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

            // ==========================================
            // 1. CREAR EL TICKET PADRE (Una sola vez)
            // ==========================================
            $solicitud = SolicitudGeneral::create([
                'correo_solicitante' => auth()->user()->email,
                'titulo_evento' => $request->titulo,
                'estado_global' => 'Aprobado' 
            ]);

            // ==========================================
            // 2. CREAR TRANSPORTE Y RESTAURANTE (Una sola vez)
            // ==========================================
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

            // ==========================================
            // 3. CREAR LOS DÍAS DE RESERVA EN EL BUCLE (Hijos)
            // ==========================================
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
        
        // Llamamos al Ticket Padre y le pedimos que traiga a todos los hijos (Notar el plural en reservasFisicas)
        $query = SolicitudGeneral::with(['reservasFisicas.espacio.torre', 'transporte', 'restaurante']);

        // Si NO es Super Admin, filtramos para que solo vea sus propios tickets
        if ($usuario->rol !== 'admin') {
            $query->where('correo_solicitante', $usuario->email);
        }

        // --- SISTEMA DE FILTROS ---
        if ($request->filled('categoria')) {
            if ($request->categoria === 'espacios') {
                $query->has('reservasFisicas'); // Cambio a Plural
            } elseif ($request->categoria === 'transporte') {
                $query->has('transporte');
            } elseif ($request->categoria === 'restaurante') {
                $query->has('restaurante');
            }
        }

        if ($request->filled('estado')) {
            $estado = $request->estado;
            // Busca si ALGÚN hijo tiene ese estado
            $query->where(function($q) use ($estado) {
                $q->whereHas('transporte', function($t) use ($estado) { $t->where('estado_transporte', $estado); })
                  ->orWhereHas('restaurante', function($r) use ($estado) { $r->where('estado_restaurante', $estado); });
            });
        }
        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(10); 

        return view('reservas.index', compact('solicitudes'));
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

    public function destroy($id)
    {
        $usuario = auth()->user();
        // Llamamos a reservasFisicas en plural
        $solicitud = SolicitudGeneral::with(['reservasFisicas', 'transporte', 'restaurante'])->findOrFail($id);

        if ($usuario->rol !== 'admin' && $usuario->email !== $solicitud->correo_solicitante) {
            return back()->with('error', 'No tienes permiso para cancelar esta solicitud.');
        }

        // Regla de Negocio: Validar tiempo (Mínimo 24 horas de anticipación)
        // Buscamos la fecha más próxima de las reservas
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

        // Borrado en Cascada manual por seguridad (Borra hijos y luego padre)
        if ($solicitud->reservasFisicas()->exists()) $solicitud->reservasFisicas()->delete();
        if ($solicitud->transporte) $solicitud->transporte()->delete();
        if ($solicitud->restaurante) $solicitud->restaurante()->delete();
        $solicitud->delete();

        return back()->with('success', 'La solicitud ha sido cancelada exitosamente en todos sus módulos.');
    }

    public function showEncuestaForm(Request $request, $id)
    {
        // 1. Buscamos el Ticket Padre (Solicitud General) con todos sus hijos
        $solicitud = \App\Models\SolicitudGeneral::with(['reservasFisicas', 'transporte', 'restaurante'])->findOrFail($id);

        // 2. Seguridad: Solo el creador de la solicitud puede evaluarla
        if ($solicitud->correo_solicitante !== auth()->user()->email) {
            return redirect()->route('reservas.index')->with('error', 'LCB Reservas: No tienes permiso para evaluar esta solicitud.');
        }

        // 3. Identificamos qué botón presionó el usuario (Si no manda nada, asumimos que es Espacios)
        $modulo = $request->query('modulo', 'Espacios');
        
        $fechaFin = null;
        $horaFin = null;

        // 4. Lógica de Tiempos Dinámica (Calcula a qué hora termina el evento según el servicio)
        if ($modulo == 'Espacios' && $solicitud->reservasFisicas->isNotEmpty()) {
            // Si el evento dura varios días, buscamos el último día para habilitar la encuesta
            $ultimaReserva = $solicitud->reservasFisicas->sortByDesc('fecha_fin')->first();
            $fechaFin = $ultimaReserva->fecha_fin;
            $horaFin = $ultimaReserva->hora_fin;

        } elseif ($modulo == 'Transporte' && $solicitud->transporte) {
            // Si es un viaje de ida y vuelta, usamos el regreso. Si es solo ida, usamos la salida.
            $fechaFinObj = \Carbon\Carbon::parse($solicitud->transporte->fecha_hora_regreso ?? $solicitud->transporte->fecha_hora_servicio);
            $fechaFin = $fechaFinObj->format('Y-m-d');
            $horaFin = $fechaFinObj->format('H:i:s');

        } elseif ($modulo == 'Restaurante' && $solicitud->restaurante) {
            // Usamos la hora a la que se entregó la comida
            $fechaFinObj = \Carbon\Carbon::parse($solicitud->restaurante->fecha_hora_evento);
            $fechaFin = $fechaFinObj->format('Y-m-d');
            // Le sumamos 1 hora a la entrega para que puedan comer antes de evaluar
            $horaFin = $fechaFinObj->addHour()->format('H:i:s'); 

        } else {
            return redirect()->route('reservas.index')->with('error', 'El servicio que intentas evaluar no existe en esta solicitud.');
        }

        // 5. Validación Estricta de Tiempo
        $fechaHoy = now()->format('Y-m-d');
        $horaActual = now()->format('H:i:s');
        $aunNoTermina = false;

        if ($fechaFin > $fechaHoy) {
            $aunNoTermina = true; 
        } elseif ($fechaFin == $fechaHoy && $horaFin >= $horaActual) {
            $aunNoTermina = true; 
        }

        if ($aunNoTermina) {
            return redirect()->route('reservas.index')
                ->with('error', 'LCB Reservas: Todavía no puedes evaluar el servicio de ' . $modulo . '. Debes esperar a que finalice.');
        }

        // 6. Si ya pasó la hora, le mostramos el súper formulario de estrellas
        return view('reservas.encuesta', compact('solicitud', 'modulo'));
    }

    public function storeEncuesta(Request $request, $id)
    {
        $request->validate([
            'modulo_evaluado' => 'required|string',
            'calificacion_general' => 'required|integer|min:1|max:5',
            'observaciones' => 'nullable|string',
        ]);

        // 🚀 TRUCO AUTO-REPARABLE: Si Laravel está pasando el ID del Salón por error de ruta, buscamos el ID del Ticket Padre.
        $reserva = \App\Models\ReservaFisica::find($id);
        $solicitudId = $reserva ? $reserva->solicitud_id : $id;

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

            // Promediamos
            $notaFinal = round(($detalles['limpieza'] + $detalles['equipos'] + $detalles['puntualidad']) / 3);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Insertamos apuntando al ID Padre correcto siempre
            \Illuminate\Support\Facades\DB::table('encuestas_satisfaccion')->insert([
                'solicitud_id' => $solicitudId, 
                'modulo_evaluado' => $request->modulo_evaluado,
                'calificacion_general' => $notaFinal,
                'respuestas_detalladas' => json_encode($detalles),
                'observaciones' => $request->observaciones,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($request->modulo_evaluado == 'Espacios') {
                \App\Models\ReservaFisica::where('solicitud_id', $solicitudId)->update([
                    'encuesta_completada' => true
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
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

            // Tope máximo de 1 año para evitar ciclos infinitos
            if ($currentDate->diffInDays($endDate) > 365) {
                $endDate = $currentDate->copy()->addDays(365);
            }

            // Calculamos exactamente qué días caen en la recurrencia
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

            // Usamos whereDate que es indestructible contra el formato DATETIME de MySQL
            $reservas = \App\Models\ReservaFisica::where('espacio_id', $espacio_id)
                ->where(function($q) use ($fechasInvolucradas) {
                    foreach($fechasInvolucradas as $f) {
                        $q->orWhereDate('fecha_inicio', $f);
                    }
                })
                ->get(['fecha_inicio', 'hora_inicio', 'hora_fin']);

            // Limpiamos los datos antes de enviarlos a JavaScript para asegurar compatibilidad
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