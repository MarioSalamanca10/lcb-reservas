<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudGeneral;
use App\Models\SolicitudTransporte;
use Illuminate\Support\Facades\DB;

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

            // 1. Creamos el Eje Central (Ticket Padre)
            $solicitud = SolicitudGeneral::create([
                'correo_solicitante' => auth()->user()->email,
                'titulo_evento' => $request->titulo,
                'estado_global' => 'Aprobado' // El padre nace aprobado
            ]);

            // 2. Creamos el ticket de Transporte vinculado al padre
            SolicitudTransporte::create([
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
                'estado_transporte' => 'Agendado' // Se envía directo a la bandeja
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }

        // Redirigimos al profesor a su panel de "Mis Solicitudes"
        return redirect()->route('reservas.index')->with('success', '¡Su solicitud de transporte ha sido enviada a Coordinación exitosamente!');
    }
}