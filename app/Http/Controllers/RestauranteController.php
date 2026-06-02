<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudRestaurante;

class RestauranteController extends Controller
{
    public function index()
    {
        // Candado: Solo entran el Super Admin o Gerencia de Restaurante
        if (!in_array(auth()->user()->rol, ['admin', 'admin_restaurante'])) {
            abort(403, 'Acceso Denegado. Este panel es exclusivo de Gerencia de Restaurante.');
        }

        $restaurantes = SolicitudRestaurante::with(['solicitud', 'solicitud.reservaFisica.espacio.torre'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.restaurante.index', compact('restaurantes'));
    }

    public function update(Request $request, $id)
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

        return back()->with('success', '¡Decisión guardada y procesada!');
    }
}