<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReservaFisica;

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
        
        if ($request->filled('docente')) {
            $query->where(function($q) use ($request) {
                $q->where('correo_docente', 'like', '%' . $request->docente . '%')
                  ->orWhereHas('solicitud', function($sq) use ($request) {
                      $sq->where('correo_solicitante', 'like', '%' . $request->docente . '%');
                  });
            });
        }

        // Ordenamos las más próximas a ocurrir primero
        $reservas = $query->orderBy('fecha_inicio', 'asc')->paginate(15);
        $torres = \App\Models\Torre::all();

        return view('admin.reservas.index', compact('reservas', 'torres'));
    }
}