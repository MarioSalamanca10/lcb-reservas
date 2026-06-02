<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudRestaurante;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionEstadoServicio;

class RestauranteController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->rol, ['admin', 'admin_restaurante'])) {
            abort(403, 'Acceso Denegado.');
        }

        $query = \App\Models\SolicitudRestaurante::with(['solicitud', 'solicitud.reservaFisica.espacio.torre']);

        if ($request->filled('estado')) $query->where('estado_restaurante', $request->estado);
        if ($request->filled('fecha')) $query->whereDate('fecha_hora_evento', $request->fecha);
        if ($request->filled('solicitante')) {
            $query->whereHas('solicitud', function($q) use ($request) {
                $q->where('correo_solicitante', 'like', '%' . $request->solicitante . '%');
            });
        }

        // PAGINACIÓN APLICADA
        $restaurantes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.restaurante.index', compact('restaurantes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado_restaurante' => 'required|string',
            'respuesta_cocina' => 'nullable|string'
        ]);

        $restaurante = SolicitudRestaurante::findOrFail($id);
        $restaurante->update([
            'estado_restaurante' => $request->estado_restaurante,
            'respuesta_cocina' => $request->respuesta_cocina
        ]);

        // --- INICIO ENVÍO DE CORREO ---
        try {
            $correoDocente = $restaurante->solicitud->correo_solicitante;
            
            $datosCorreo = [
                'titulo' => $restaurante->solicitud->titulo_evento,
                'servicio' => 'Alimentación / Restaurante',
                'estado' => $restaurante->estado_restaurante,
                'notas' => $restaurante->respuesta_cocina
            ];

            Mail::to($correoDocente)->send(new NotificacionEstadoServicio($datosCorreo));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando correo de restaurante: ' . $e->getMessage());
        }
        // --- FIN ENVÍO DE CORREO ---

        return back()->with('success', '¡Decisión e instrucciones de cocina guardadas exitosamente!');
    }
}