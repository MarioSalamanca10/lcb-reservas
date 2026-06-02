<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudGeneral;
use App\Models\SolicitudTransporte;
use App\Models\SolicitudRestaurante;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ServicioAdicionalController extends Controller
{
    public function createTransporte()
    {
        return view('servicios.transporte');
    }

    public function createRestaurante()
    {
        return view('servicios.restaurante');
    }

    /**
     * Guarda una solicitud exclusiva de transporte
     */
    public function storeTransporte(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'trans_responsable' => 'required|string',
            'trans_salida' => 'required|date',
            'trans_regreso' => 'required|date|after_or_equal:trans_salida',
            'trans_dir_destino' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $solicitud = SolicitudGeneral::create([
                'correo_solicitante' => auth()->user()->email,
                'titulo_evento' => $request->titulo,
                'estado_global' => 'Aprobado'
            ]);

            $transporte = SolicitudTransporte::create([
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
                'estado_transporte' => 'Pendiente' // Asegúrate de que nazca Pendiente
            ]);

            DB::commit();

            // --- INICIO ENVÍO DE CORREOS DE CREACIÓN ---
            $datosCorreo = [
                'titulo' => $request->titulo,
                'servicio' => 'Transporte y Rutas',
                'solicitante_nombre' => auth()->user()->name,
                'solicitante_correo' => auth()->user()->email,
                'fecha' => \Carbon\Carbon::parse($request->trans_salida)->format('d/m/Y h:i A'),
                'detalles' => 'Destino: ' . $request->trans_dir_destino . ' (' . ($request->trans_estudiantes + $request->trans_adultos) . ' pasajeros)'
            ];

            // 1. Correo al Docente
            Mail::to(auth()->user()->email)->send(new NuevaSolicitudRecibida($datosCorreo, 'docente'));

            // 2. Correo a los Administradores de Transporte y Super Admins
            $adminsTransporte = User::whereIn('rol', ['admin', 'admin_transporte'])->pluck('email');
            if($adminsTransporte->count() > 0) {
                Mail::to($adminsTransporte)->send(new NuevaSolicitudRecibida($datosCorreo, 'admin'));
            }
            // --- FIN ENVÍO DE CORREOS ---

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al procesar: ' . $e->getMessage());
        }

        // Redirigimos al profesor a su panel de "Mis Solicitudes"
        return redirect()->route('reservas.index')->with('success', '¡Su solicitud de transporte ha sido enviada a Coordinación exitosamente!');
    }

    /**
     * Guarda una solicitud exclusiva de Restaurante
     */
    public function storeRestaurante(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'rest_fecha_hora' => 'required|date',
            'rest_asistentes' => 'required|integer|min:1',
            'rest_servicios' => 'required|array|min:1', // Obliga a marcar al menos un checkbox
        ]);

        try {
            DB::beginTransaction();

            $solicitud = SolicitudGeneral::create([
                'correo_solicitante' => auth()->user()->email,
                'titulo_evento' => $request->titulo,
                'estado_global' => 'Aprobado'
            ]);

            $restaurante = SolicitudRestaurante::create([
                'solicitud_id' => $solicitud->id,
                'fecha_hora_evento' => $request->rest_fecha_hora,
                'num_asistentes' => $request->rest_asistentes,
                'servicio_requerido' => $request->rest_servicios,
                'detalles_solicitud' => $request->rest_detalles,
                'estado_restaurante' => 'Pendiente'
            ]);

            DB::commit();

            // --- INICIO ENVÍO DE CORREOS DE CREACIÓN ---
            $datosCorreo = [
                'titulo' => $request->titulo,
                'servicio' => 'Alimentación / Restaurante',
                'solicitante_nombre' => auth()->user()->name,
                'solicitante_correo' => auth()->user()->email,
                'fecha' => \Carbon\Carbon::parse($request->rest_fecha_hora)->format('d/m/Y h:i A'),
                'detalles' => 'Para ' . $request->rest_asistentes . ' personas. Servicios: ' . implode(', ', $request->rest_servicios)
            ];

            // 1. Correo al Docente
            Mail::to(auth()->user()->email)->send(new NuevaSolicitudRecibida($datosCorreo, 'docente'));

            // 2. Correo a los Administradores de Restaurante y Super Admins
            $adminsRestaurante = User::whereIn('rol', ['admin', 'admin_restaurante'])->pluck('email');
            if($adminsRestaurante->count() > 0) {
                Mail::to($adminsRestaurante)->send(new NuevaSolicitudRecibida($datosCorreo, 'admin'));
            }
            // --- FIN ENVÍO DE CORREOS ---

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al procesar: ' . $e->getMessage());
        }

        return redirect()->route('reservas.index')->with('success', '¡Su solicitud de restaurante ha sido enviada a Gerencia para su aprobación!');
    }
}