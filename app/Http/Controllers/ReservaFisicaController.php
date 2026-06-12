<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudGeneral;
use App\Models\ReservaFisica;
use App\Models\Espacio;
use App\Models\Torre;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NuevaSolicitudRecibida;

class ReservaFisicaController extends Controller
{
    public function create()
    {
        $usuario = auth()->user();
        
        $fechaHoy = now()->format('Y-m-d');
        $horaActual = now()->format('H:i:s');

        $pendientesEncuesta = ReservaFisica::where('encuesta_completada', false)
            ->whereHas('solicitud', function($query) use ($usuario) {
                $query->where('correo_solicitante', $usuario->email);
            })
            ->where(function ($query) use ($fechaHoy, $horaActual) {
                $query->where('fecha_fin', '<', $fechaHoy)
                      ->orWhere(function ($q) use ($fechaHoy, $horaActual) {
                          $q->where('fecha_fin', '=', $fechaHoy)
                            ->where('hora_fin', '<', $horaActual);
                      });
            })
            ->exists();

        if ($pendientesEncuesta) {
            return redirect()->route('reservas.index')
                ->with('error', 'LCB Reservas: Tienes reuniones finalizadas pendientes de evaluar. Completa la encuesta para agendar un nuevo espacio.');
        }

        $espacios = Espacio::where('activo', 1)->get();
        $torres = Torre::all();

        return view('reservas.create', compact('espacios', 'torres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'espacio_id' => 'required|exists:espacios,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => [
                'nullable', 'date', 'after_or_equal:fecha_inicio',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->fecha_inicio) {
                        $diasDiferencia = Carbon::parse($request->fecha_inicio)->diffInDays($value);
                        if ($request->frecuencia == 'semanal' && $diasDiferencia < 7) {
                            $fail('Para un evento semanal, la Fecha Fin debe ser mínimo 7 días después.');
                        }
                        if ($request->frecuencia == 'quincenal' && $diasDiferencia < 14) {
                            $fail('Para un evento quincenal, la Fecha Fin debe ser mínimo 14 días después.');
                        }
                    }
                }
            ],
            'hora_inicio' => 'required|date_format:H:i|after_or_equal:07:00|before_or_equal:17:00',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio|before_or_equal:17:00',
            'frecuencia' => 'nullable|string|in:unica,semanal,quincenal'
        ]);

        $frecuencia = $request->frecuencia ?? 'unica';
        $fechaFin = $request->fecha_fin ?? $request->fecha_inicio;
        $horaInicio = $request->hora_inicio . ':00';
        $horaFin = $request->hora_fin . ':00';

        $fechasAProcesar = [];
        $fechaActualLoop = Carbon::parse($request->fecha_inicio);
        $fechaLimite = Carbon::parse($fechaFin);

        while ($fechaActualLoop->lte($fechaLimite)) {
            if (!$fechaActualLoop->isSunday()) {
                $fechasAProcesar[] = $fechaActualLoop->format('Y-m-d');
            }

            if ($frecuencia === 'semanal') {
                $fechaActualLoop->addDays(7);
            } elseif ($frecuencia === 'quincenal') {
                $fechaActualLoop->addDays(14);
            } else {
                $fechaActualLoop->addDay();
            }
        }

        $díasChocan = [];
        foreach ($fechasAProcesar as $fechaActual) {
            $choque = ReservaFisica::where('espacio_id', $request->espacio_id)
                ->where('fecha_inicio', '<=', $fechaActual)
                ->where('fecha_fin', '>=', $fechaActual)
                ->where(function ($query) use ($horaInicio, $horaFin) {
                    $query->where('hora_inicio', '<', $horaFin)
                          ->where('hora_fin', '>', $horaInicio);
                })
                ->exists();

            if ($choque) {
                $díasChocan[] = Carbon::parse($fechaActual)->format('d/m/Y');
            }
        }

        if (!empty($díasChocan)) {
            $msjError = 'El espacio ya está ocupado en estas fechas: ' . implode(', ', $díasChocan);
            return back()->withInput()->with('error_reserva', $msjError);
        }

        try {
            DB::beginTransaction();

            $solicitud = SolicitudGeneral::create([
                'correo_solicitante' => auth()->user()->email,
                'titulo_evento' => $request->titulo,
                'estado_global' => 'Aprobado' 
            ]);

            if ($request->filled('requiere_transporte')) {
                \App\Models\SolicitudTransporte::create([
                    'solicitud_id' => $solicitud->id,
                    'nombre_responsable' => $request->trans_responsable,
                    'celular_responsable' => $request->trans_celular,
                    'area_solicitante' => $request->trans_area,
                    'fecha_hora_servicio' => $request->trans_salida,
                    'direccion_recogida' => $request->trans_dir_recogida,
                    'direccion_destino' => $request->trans_dir_destino,
                    'direccion_regreso' => $request->trans_dir_regreso,
                    'fecha_hora_regreso' => $request->trans_regreso,
                    'num_estudiantes' => $request->trans_estudiantes ?? 0,
                    'num_adultos' => $request->trans_adultos ?? 0,
                    'necesidades_servicio' => $request->trans_necesidades ?? [],
                    'observaciones' => $request->trans_observaciones,
                    'estado_transporte' => 'Pendiente'
                ]);
            }

            if ($request->filled('requiere_restaurante')) {
                \App\Models\SolicitudRestaurante::create([
                    'solicitud_id' => $solicitud->id,
                    'fecha_hora_evento' => $request->rest_fecha_hora,
                    'num_asistentes' => $request->rest_asistentes,
                    'servicio_requerido' => $request->rest_servicios ?? [],
                    'detalles_solicitud' => $request->rest_detalles,
                    'aprobador_id' => $request->filled('rest_aprobador_id') ? $request->rest_aprobador_id : null, 
                    'estado_restaurante' => 'Pendiente'
                ]);
            }

            foreach ($fechasAProcesar as $fechaActual) {
                ReservaFisica::create([
                    'solicitud_id' => $solicitud->id,
                    'espacio_id' => $request->espacio_id,
                    'fecha_inicio' => $fechaActual,
                    'fecha_fin' => $fechaActual,
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin,
                    'recursos_adicionales' => $request->recursos_adicionales ?? [],
                    'observaciones' => $request->observaciones,
                    'encuesta_completada' => false
                ]);
            }

            // --- INICIO ENVÍO DE CORREOS BREVO ---
            $datosCorreo = [
                'titulo' => $request->titulo,
                'servicio' => 'Reserva de Espacio Físico',
                'solicitante_nombre' => auth()->user()->name,
                'solicitante_correo' => auth()->user()->email,
                'fecha' => Carbon::parse($request->fecha_inicio)->format('d/m/Y') . ' a las ' . $request->hora_inicio,
                'detalles' => 'Frecuencia: ' . $frecuencia . ' (Abarca ' . count($fechasAProcesar) . ' día/s)'
            ];

            Mail::to(auth()->user()->email)->send(new NuevaSolicitudRecibida($datosCorreo, 'docente'));

            $admins = \App\Models\User::whereIn('rol', ['admin', 'admin_espacios'])->pluck('email');
            if($admins->count() > 0) {
                Mail::to($admins)->send(new NuevaSolicitudRecibida($datosCorreo, 'admin'));
            }
            // --- FIN ENVÍO DE CORREOS BREVO ---

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error_reserva', 'Error crítico: ' . $e->getMessage()); 
        }

        return redirect()->route('reservas.index')->with('success', '¡El evento ha sido agendado exitosamente con todos sus servicios!');
    }

    public function index(Request $request)
    {
        $usuario = auth()->user();
        
        $query = SolicitudGeneral::with(['reservasFisicas.espacio.torre', 'transporte', 'restaurante']);

        if ($usuario->rol !== 'admin') {
            $query->where('correo_solicitante', $usuario->email);
        }

        if ($request->filled('categoria')) {
            if ($request->categoria === 'espacios') {
                $query->has('reservasFisicas');
            } elseif ($request->categoria === 'transporte') {
                $query->has('transporte');
            } elseif ($request->categoria === 'restaurante') {
                $query->has('restaurante');
            }
        }

        if ($request->filled('estado')) {
            $estado = $request->estado;
            $query->where(function($q) use ($estado) {
                $q->whereHas('transporte', function($t) use ($estado) { $t->where('estado_transporte', $estado); })
                  ->orWhereHas('restaurante', function($r) use ($estado) { $r->where('estado_restaurante', $estado); });
            });
        }
        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(10); 

        return view('reservas.index', compact('solicitudes'));
    }

    public function destroy($id)
    {
        $usuario = auth()->user();
        $solicitud = SolicitudGeneral::with(['reservasFisicas', 'transporte', 'restaurante'])->findOrFail($id);

        if ($usuario->rol !== 'admin' && $usuario->email !== $solicitud->correo_solicitante) {
            return back()->with('error', 'No tienes permiso para cancelar esta solicitud.');
        }

        $fechaEvento = null;
        if ($solicitud->reservasFisicas->isNotEmpty()) {
            $fechaEvento = $solicitud->reservasFisicas->min('fecha_inicio');
        } elseif ($solicitud->transporte) {
            $fechaEvento = $solicitud->transporte->fecha_hora_servicio;
        } elseif ($solicitud->restaurante) {
            $fechaEvento = $solicitud->restaurante->fecha_hora_evento;
        }

        if ($fechaEvento && $usuario->rol !== 'admin') {
            $horasFaltantes = \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($fechaEvento), false);
            if ($horasFaltantes < 24) {
                return back()->with('error', 'No puedes cancelar una solicitud con menos de 24 horas de anticipación. Por favor, contacta directamente a Gerencia.');
            }
        }

        if ($solicitud->reservasFisicas()->exists()) $solicitud->reservasFisicas()->delete();
        if ($solicitud->transporte) $solicitud->transporte()->delete();
        if ($solicitud->restaurante) $solicitud->restaurante()->delete();
        $solicitud->delete();

        return back()->with('success', 'La solicitud ha sido cancelada exitosamente en todos sus módulos.');
    }

    public function showEncuestaForm(Request $request, $id)
    {
        $solicitud = SolicitudGeneral::with(['reservasFisicas', 'transporte', 'restaurante'])->findOrFail($id);

        if ($solicitud->correo_solicitante !== auth()->user()->email) {
            return redirect()->route('reservas.index')->with('error', 'LCB Reservas: No tienes permiso para evaluar esta solicitud.');
        }

        $modulo = $request->query('modulo', 'Espacios');
        $fechaFin = null;
        $horaFin = null;

        if ($modulo == 'Espacios' && $solicitud->reservasFisicas->isNotEmpty()) {
            $ultimaReserva = $solicitud->reservasFisicas->sortByDesc('fecha_fin')->first();
            $fechaFin = $ultimaReserva->fecha_fin;
            $horaFin = $ultimaReserva->hora_fin;
        } elseif ($modulo == 'Transporte' && $solicitud->transporte) {
            $fechaFinObj = \Carbon\Carbon::parse($solicitud->transporte->fecha_hora_regreso ?? $solicitud->transporte->fecha_hora_servicio);
            $fechaFin = $fechaFinObj->format('Y-m-d');
            $horaFin = $fechaFinObj->format('H:i:s');
        } elseif ($modulo == 'Restaurante' && $solicitud->restaurante) {
            $fechaFinObj = \Carbon\Carbon::parse($solicitud->restaurante->fecha_hora_evento);
            $fechaFin = $fechaFinObj->format('Y-m-d');
            $horaFin = $fechaFinObj->addHour()->format('H:i:s');
        } else {
            return redirect()->route('reservas.index')->with('error', 'El servicio que intentas evaluar no existe.');
        }

        $fechaHoy = now()->format('Y-m-d');
        $horaActual = now()->format('H:i:s');
        $aunNoTermina = false;

        if ($fechaFin > $fechaHoy) {
            $aunNoTermina = true; 
        } elseif ($fechaFin == $fechaHoy && $horaFin >= $horaActual) {
            $aunNoTermina = true; 
        }

        if ($aunNoTermina) {
            return redirect()->route('reservas.index')->with('error', 'LCB Reservas: Todavía no puedes evaluar el servicio de ' . $modulo . ' porque no ha finalizado.');
        }

        return view('reservas.encuesta', compact('solicitud', 'modulo'));
    }

    public function storeEncuesta(Request $request, $id)
    {
        $request->validate([
            'modulo_evaluado' => 'required|string',
            'calificacion_general' => 'required|integer|min:1|max:5',
            'observaciones' => 'nullable|string',
        ]);

        $solicitud = SolicitudGeneral::findOrFail($id);
        $detalles = []; 
        $notaFinal = $request->calificacion_general;

        if ($request->modulo_evaluado == 'Espacios') {
            $request->validate([
                'respuestas_detalladas.limpieza' => 'required|integer|min:1|max:5',
                'respuestas_detalladas.equipos' => 'required|integer|min:1|max:5',
                'respuestas_detalladas.puntualidad' => 'required|integer|min:1|max:5',
            ]);

            $detalles = [
                'limpieza' => $request->respuestas_detalladas['limpieza'],
                'equipos' => $request->respuestas_detalladas['equipos'],
                'puntualidad' => $request->respuestas_detalladas['puntualidad'],
                'evaluado_el' => now()->toDateTimeString(),
            ];
            $notaFinal = round(($detalles['limpieza'] + $detalles['equipos'] + $detalles['puntualidad']) / 3);
        }

        try {
            DB::beginTransaction();

            \App\Models\EncuestaSatisfaccion::create([
                'solicitud_id' => $solicitud->id, 
                'modulo_evaluado' => $request->modulo_evaluado,
                'calificacion_general' => $notaFinal,
                'respuestas_detalladas' => json_encode($detalles),
                'observaciones' => $request->observaciones,
            ]);

            if ($request->modulo_evaluado == 'Espacios') {
                ReservaFisica::where('solicitud_id', $solicitud->id)->update([
                    'encuesta_completada' => true
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar tu encuesta: ' . $e->getMessage());
        }

        return redirect()->route('reservas.index')->with('success', '¡Gracias por calificar el servicio de ' . $request->modulo_evaluado . '!');
    }

    public function checkDisponibilidad(Request $request)
    {
        try {
            $espacio_id = $request->espacio_id;
            $fecha_inicio = $request->fecha_inicio;
            $fecha_fin = $request->fecha_fin ?: $fecha_inicio;
            $frecuencia = $request->frecuencia ?? 'unica';

            if (!$espacio_id || !$fecha_inicio) {
                return response()->json([]);
            }

            $fechasInvolucradas = [];
            $currentDate = \Carbon\Carbon::parse($fecha_inicio);
            $endDate = \Carbon\Carbon::parse($fecha_fin);

            if ($currentDate->diffInDays($endDate) > 365) {
                $endDate = $currentDate->copy()->addDays(365);
            }

            while ($currentDate->lte($endDate)) {
                $fechasInvolucradas[] = $currentDate->format('Y-m-d');
                if ($frecuencia === 'semanal') {
                    $currentDate->addDays(7);
                } elseif ($frecuencia === 'quincenal') {
                    $currentDate->addDays(14);
                } else {
                    $currentDate->addDay();
                }
            }

            $reservas = ReservaFisica::where('espacio_id', $espacio_id)
                ->where(function($q) use ($fechasInvolucradas) {
                    foreach($fechasInvolucradas as $f) {
                        $q->orWhereDate('fecha_inicio', $f);
                    }
                })
                ->get(['fecha_inicio', 'hora_inicio', 'hora_fin']);

            $reservas->transform(function($reserva) {
                $reserva->fecha_inicio = \Carbon\Carbon::parse($reserva->fecha_inicio)->format('Y-m-d');
                $reserva->hora_inicio = \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i');
                $reserva->hora_fin = \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i');
                return $reserva;
            });

            return response()->json($reservas);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}