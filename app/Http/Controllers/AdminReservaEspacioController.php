<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReservaFisica;
use App\Exports\EspaciosExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminReservaEspacioController extends Controller
{
    public function index(Request $request)
    {
        // --- CANDADO MANUAL SEGURO ---
        if (!in_array(auth()->user()->rol, ['admin', 'admin_espacios'])) {
            abort(403, 'Acceso Denegado. Panel exclusivo para Auditoría de Espacios.');
        }

        // Traemos las reservas con TODO su ecosistema asociado
        $query = ReservaFisica::with(['espacio.torre', 'solicitud.transporte', 'solicitud.restaurante']);

        // --- FILTROS ---
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_inicio', $request->fecha);
        }
        
        if ($request->filled('torre_id')) {
            $query->whereHas('espacio', function($q) use ($request) {
                $q->where('torre_id', $request->torre_id);
            });
        }

        // NUEVO FILTRO POR ESPACIO
        if ($request->filled('espacio_id')) {
            $query->where('espacio_id', $request->espacio_id);
        }
        
        if ($request->filled('docente')) {
            $query->where(function($q) use ($request) {
                $q->where('correo_docente', 'like', '%' . $request->docente . '%')
                  ->orWhereHas('solicitud', function($sq) use ($request) {
                      $sq->where('correo_solicitante', 'like', '%' . $request->docente . '%');
                  });
            });
        }

        // Paginación a 15 registros
        $reservas = $query->orderBy('fecha_inicio', 'asc')->paginate(15);
        $torres = \App\Models\Torre::all();
        // ESTA ES LA LÍNEA QUE FALTABA:
        $espacios = \App\Models\Espacio::all(); 

        return view('admin.reservas.index', compact('reservas', 'torres', 'espacios'));
    }

    public function exportarExcel(Request $request)
    {
        // Pasa los filtros de la URL directamente a la clase exportadora
        return Excel::download(new EspaciosExport(
            $request->fecha, 
            $request->torre_id, 
            $request->docente
        ), 'Reporte_Auditoria_Espacios.xlsx');
    }

    public function destroy($id)
    {
        $reserva = ReservaFisica::findOrFail($id);
        
        // Al eliminar la Solicitud General padre, se eliminará en cascada 
        // la reserva física, transporte, restaurante y encuestas asociadas.
        if ($reserva->solicitud_id) {
            \App\Models\SolicitudGeneral::destroy($reserva->solicitud_id);
        } else {
            // Por si es una reserva antigua que no tiene ticket padre
            $reserva->delete();
        }

        return redirect()->route('admin.reservas.index')
                         ->with('success', 'Reserva y servicios asociados cancelados correctamente.');
    }
}