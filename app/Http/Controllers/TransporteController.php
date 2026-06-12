<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudTransporte;
use App\Mail\NotificacionEstadoServicio;
use Illuminate\Support\Facades\Mail;
use App\Exports\TransporteExport;
use Maatwebsite\Excel\Facades\Excel;

class TransporteController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->rol, ['admin', 'admin_transporte'])) {
            abort(403);
        }

        $query = SolicitudTransporte::with('solicitud');

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_hora_servicio', $request->fecha);
        }
        if ($request->filled('estado')) {
            $query->where('estado_transporte', $request->estado);
        }
        if ($request->filled('solicitante')) {
            $query->whereHas('solicitud', function($q) use ($request) {
                $q->where('correo_solicitante', 'like', '%' . $request->solicitante . '%');
            });
        }

        $transportes = $query->orderBy('fecha_hora_servicio', 'asc')->paginate(10);
        return view('admin.transporte.index', compact('transportes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado_transporte' => 'required|in:Aprobado,Rechazado',
            'respuesta_coordinador' => 'required|string'
        ]);

        $trans = SolicitudTransporte::with('solicitud')->findOrFail($id);
        $trans->update([
            'estado_transporte' => $request->estado_transporte,
            'respuesta_coordinador' => $request->respuesta_coordinador
        ]);

        // 🚀 BREVO: NOTIFICACIÓN AL DOCENTE
        $datosEstado = [
            'titulo' => $trans->solicitud->titulo_evento,
            'servicio' => 'Logística de Transporte y Rutas',
            'estado' => $request->estado_transporte,
            'respuesta' => $request->respuesta_coordinador
        ];
        Mail::to($trans->solicitud->correo_solicitante)->send(new NotificacionEstadoServicio($datosEstado));

        return redirect()->route('admin.transporte.index')->with('success', 'Ruta gestionada y sellada con éxito.');
    }

    public function export(Request $request)
    {
        return Excel::download(new TransporteExport($request->fecha, $request->estado, $request->solicitante), 'Reporte_Transporte.xlsx');
    }
}