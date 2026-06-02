<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudTransporte;
use App\Models\SolicitudRestaurante;

class AprobacionController extends Controller
{
    /**
     * Muestra el panel principal (Torre de Control) con todas las solicitudes de logística.
     */
    public function index()
    {
        // Traemos los transportes ordenados de lo más reciente a lo más antiguo
        // Usamos 'with' para traer los datos del ticket padre y del salón asignado
        $transportes = SolicitudTransporte::with(['solicitud', 'solicitud.reservaFisica.espacio.torre'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Hacemos lo mismo con el restaurante
        $restaurantes = SolicitudRestaurante::with(['solicitud', 'solicitud.reservaFisica.espacio.torre'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.aprobaciones.index', compact('transportes', 'restaurantes'));
    }

    /**
     * Actualiza el estado (Aprobar/Rechazar) de un ticket de Transporte
     */
    public function updateTransporte(Request $request, $id)
    {
        $request->validate([
            'estado_transporte' => 'required|in:Aprobado,Rechazado,Pendiente',
            'respuesta_coordinador' => 'nullable|string'
        ]);

        $transporte = SolicitudTransporte::findOrFail($id);
        
        $transporte->update([
            'estado_transporte' => $request->estado_transporte,
            'respuesta_coordinador' => $request->respuesta_coordinador
        ]);

        return back()->with('success', '¡El estado del transporte ha sido actualizado correctamente!');
    }

    /**
     * Actualiza el estado (Aprobar/Rechazar) de un ticket de Restaurante
     */
    public function updateRestaurante(Request $request, $id)
    {
        $request->validate([
            'estado_restaurante' => 'required|in:Aprobado,Rechazado,Pendiente',
            'respuesta_cocina' => 'nullable|string'
        ]);

        $restaurante = SolicitudRestaurante::findOrFail($id);
        
        $restaurante->update([
            'estado_restaurante' => $request->estado_restaurante,
            'respuesta_cocina' => $request->respuesta_cocina
        ]);

        return back()->with('success', '¡El estado del servicio de restaurante ha sido actualizado!');
    }
}