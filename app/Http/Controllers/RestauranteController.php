<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudRestaurante;
use App\Mail\NotificacionEstadoServicio;
use Illuminate\Support\Facades\Mail;
use App\Exports\RestauranteExport;
use Maatwebsite\Excel\Facades\Excel;

class RestauranteController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user();
        if (!in_array($usuario->rol, ['admin', 'gerencia_academica', 'gerencia_administrativa', 'gerencia_operativa'])) {
            abort(403);
        }

        $query = SolicitudRestaurante::with(['solicitud.reservasFisicas.espacio']);

        if ($usuario->rol === 'gerencia_academica') { $query->where('aprobador_id', 'Gerencia Académica'); }
        elseif ($usuario->rol === 'gerencia_administrativa') { $query->where('aprobador_id', 'Gerencia Administrativa'); }
        elseif ($usuario->rol === 'gerencia_operativa') { $query->where('aprobador_id', 'Gerencia Operativa'); }

        if ($request->filled('fecha')) { $query->whereDate('fecha_hora_evento', $request->fecha); }
        if ($request->filled('estado')) { $query->where('estado_restaurante', $request->estado); }

        $restaurantes = $query->orderBy('fecha_hora_evento', 'asc')->paginate(10);
        return view('admin.restaurante.index', compact('restaurantes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado_restaurante' => 'required|in:Aprobado,Rechazado',
            'respuesta_cocina' => 'required|string'
        ]);

        $rest = SolicitudRestaurante::with('solicitud')->findOrFail($id);
        $rest->update([
            'estado_restaurante' => $request->estado_restaurante,
            'respuesta_cocina' => $request->respuesta_cocina
        ]);

        // 🚀 BREVO: NOTIFICACIÓN AL DOCENTE
        $datosEstado = [
            'titulo' => $rest->solicitud->titulo_evento,
            'servicio' => 'Alimentación y Comidas (Gerencia)',
            'estado' => $request->estado_restaurante,
            'respuesta' => $request->respuesta_cocina
        ];
        Mail::to($rest->solicitud->correo_solicitante)->send(new NotificacionEstadoServicio($datosEstado));

        return redirect()->route('admin.restaurante.index')->with('success', 'Presupuesto evaluado correctamente.');
    }

    public function export(Request $request)
    {
        return Excel::download(new RestauranteExport($request->fecha, $request->estado, $request->solicitante), 'Reporte_Restaurante.xlsx');
    }
}