<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudTransporte;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionEstadoServicio;
use App\Exports\TransporteExport;
use Maatwebsite\Excel\Facades\Excel;

class TransporteController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->rol, ['admin', 'admin_transporte'])) {
            abort(403, 'Acceso Denegado.');
        }

        $query = \App\Models\SolicitudTransporte::with(['solicitud', 'solicitud.reservaFisica.espacio.torre']);

        if ($request->filled('estado')) $query->where('estado_transporte', $request->estado);
        if ($request->filled('fecha')) $query->whereDate('fecha_hora_servicio', $request->fecha);
        if ($request->filled('solicitante')) {
            $query->whereHas('solicitud', function($q) use ($request) {
                $q->where('correo_solicitante', 'like', '%' . $request->solicitante . '%');
            });
        }

        // EL SECRETO DEL RENDIMIENTO: paginate() en lugar de get()
        $transportes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.transporte.index', compact('transportes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado_transporte' => 'required|string',
            'respuesta_coordinador' => 'nullable|string'
        ]);

        $transporte = SolicitudTransporte::findOrFail($id);
        $transporte->update([
            'estado_transporte' => $request->estado_transporte,
            'respuesta_coordinador' => $request->respuesta_coordinador
        ]);

        // --- INICIO ENVÍO DE CORREO ---
        try {
            $correoDocente = $transporte->solicitud->correo_solicitante;
            
            $datosCorreo = [
                'titulo' => $transporte->solicitud->titulo_evento,
                'servicio' => 'Transporte y Rutas',
                'estado' => $transporte->estado_transporte,
                'notas' => $transporte->respuesta_coordinador
            ];

            Mail::to($correoDocente)->send(new NotificacionEstadoServicio($datosCorreo));
        } catch (\Exception $e) {
            // Si el correo falla (ej. sin internet), no se cae el sistema, solo registra el error en los logs
            \Illuminate\Support\Facades\Log::error('Error enviando correo de transporte: ' . $e->getMessage());
        }
        // --- FIN ENVÍO DE CORREO ---

        return back()->with('success', '¡Estado y datos logísticos del transporte actualizados correctamente!');
    }
    public function exportarExcel(Request $request)
    {
        return Excel::download(new TransporteExport(
            $request->fecha, 
            $request->estado, 
            $request->solicitante
        ), 'Planilla_Rutas_LCB.xlsx');
    }
}