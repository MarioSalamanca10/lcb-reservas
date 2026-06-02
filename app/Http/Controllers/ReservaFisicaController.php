<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudGeneral;
use App\Models\ReservaFisica;
use App\Models\Espacio;
use App\Models\Torre;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                ->with('error', 'LCB Reservas: Tienes reuniones que ya finalizaron. Por favor, ve a "Mis Reservas" y completa la encuesta de satisfacción para poder agendar un nuevo espacio.');
        }

        $espacios = Espacio::where('activo', 1)->get();
        $torres = Torre::all();

        return view('reservas.create', compact('espacios', 'torres'));
    }

    public function store(Request $request)
    {
        // 1. Validaciones con reglas matemáticas para la Frecuencia
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

            foreach ($fechasAProcesar as $fechaActual) {
                $solicitud = SolicitudGeneral::create([
                    'correo_solicitante' => auth()->user()->email,
                    'titulo_evento' => $request->titulo,
                    'estado_global' => 'Aprobado' 
                ]);

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
                        'aprobador_id' => $request->filled('rest_aprobador_id') ? $request->rest_aprobador_id : null, // Solución al error rojo
                        'estado_restaurante' => 'Pendiente'
                    ]);
                }
            }

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
        $query = ReservaFisica::with(['solicitud', 'espacio.torre']);

        if ($usuario->rol !== 'admin') {
            $query->whereHas('solicitud', function($q) use ($usuario) {
                $q->where('correo_solicitante', $usuario->email);
            });
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_inicio', $request->fecha); 
        }
        
        if ($request->filled('espacio')) {
            $query->whereHas('espacio', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->espacio . '%');
            });
        }

        $reservas = $query->orderBy('fecha_inicio', 'desc')->get(); 

        return view('reservas.index', compact('reservas'));
    }

    public function destroy(ReservaFisica $reserva)
    {
        $usuario = auth()->user();

        if ($usuario->rol !== 'admin' && $usuario->email !== $reserva->solicitud->correo_solicitante) {
            return back()->with('error', 'No tienes permiso para cancelar esta reserva.');
        }

        $reserva->solicitud->delete();
        return back()->with('success', 'La reserva ha sido cancelada y el espacio está libre nuevamente.');
    }

    public function adminIndex(Request $request)
    {
        $query = ReservaFisica::with(['solicitud', 'espacio.torre']);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_inicio', $request->fecha);
        }

        if ($request->filled('torre_id')) {
            $query->whereHas('espacio', function($q) use ($request) {
                $q->where('torre_id', $request->torre_id);
            });
        }

        if ($request->filled('docente')) {
            $query->whereHas('solicitud', function($q) use ($request) {
                $q->where('correo_solicitante', 'like', '%' . $request->docente . '%');
            });
        }

        $reservas = $query->orderBy('fecha_inicio', 'desc')->paginate(10); 
        $torres = Torre::all();
        
        return view('admin.reservas.index', compact('reservas', 'torres'));
    }

    public function showEncuestaForm(ReservaFisica $reserva)
    {
        if ($reserva->solicitud->correo_solicitante !== auth()->user()->email) {
            return redirect()->route('reservas.index')->with('error', 'LCB Reservas: No tienes permiso para evaluar esta reserva.');
        }

        $fechaHoy = now()->format('Y-m-d');
        $horaActual = now()->format('H:i:s');
        $aunNoTermina = false;

        if ($reserva->fecha_fin > $fechaHoy) {
            $aunNoTermina = true; 
        } elseif ($reserva->fecha_fin == $fechaHoy && $reserva->hora_fin >= $horaActual) {
            $aunNoTermina = true; 
        }

        if ($aunNoTermina) {
            return redirect()->route('reservas.index')->with('error', 'LCB Reservas: Todavía no puedes evaluar esta reunión, debes esperar a que finalice la hora programada.');
        }

        return view('reservas.encuesta', compact('reserva'));
    }

    public function storeEncuesta(Request $request, ReservaFisica $reserva)
    {
        $request->validate([
            'calificacion_aseo' => 'required|integer|min:1|max:5',
            'calificacion_recursos' => 'required|integer|min:1|max:5',
            'calificacion_horarios' => 'required|integer|min:1|max:5',
            'observaciones' => 'nullable|string',
        ]);

        $detalles = [
            'calificacion_aseo' => $request->calificacion_aseo,
            'calificacion_recursos' => $request->calificacion_recursos,
            'calificacion_horarios' => $request->calificacion_horarios,
            'evaluado_el' => now()->toDateTimeString(),
        ];

        try {
            DB::beginTransaction();

            DB::table('encuestas_satisfaccion')->insert([
                'solicitud_id' => $reserva->solicitud_id,
                'modulo_evaluado' => 'Espacios',
                'calificacion_general' => round(($request->calificacion_aseo + $request->calificacion_recursos + $request->calificacion_horarios) / 3),
                'respuestas_detalladas' => json_encode($detalles),
                'observaciones' => $request->observaciones,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $reserva->update([
                'encuesta_completada' => true
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hubo un error al procesar tu encuesta. Intenta nuevamente.');
        }

        return redirect()->route('reservas.index')->with('success', '¡Gracias por tu feedback! Tus respuestas han sido guardadas y el espacio ha sido liberado exitosamente.');
    }
}