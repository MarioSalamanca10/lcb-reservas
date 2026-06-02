<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudTransporte;

class TransporteController extends Controller
{
    public function index()
    {
        // Candado: Solo entran el Super Admin o el Coordinador de Transporte
        if (!in_array(auth()->user()->rol, ['admin', 'admin_transporte'])) {
            abort(403, 'Acceso Denegado. Este panel es exclusivo de la Coordinación de Transporte.');
        }

        $transportes = SolicitudTransporte::with(['solicitud', 'solicitud.reservaFisica.espacio.torre'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.transporte.index', compact('transportes'));
    }

    public function update(Request $request, $id)
    {
        // En transporte NO aprobamos, solo guardamos notas como "Conductor asignado"
        $transporte = SolicitudTransporte::findOrFail($id);
        $transporte->update([
            'respuesta_coordinador' => $request->respuesta_coordinador
        ]);

        return back()->with('success', '¡Datos logísticos guardados correctamente!');
    }
}