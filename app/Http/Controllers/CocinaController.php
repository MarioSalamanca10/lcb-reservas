<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudRestaurante;
use App\Exports\RestauranteExport;
use Maatwebsite\Excel\Facades\Excel;

class CocinaController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->rol, ['admin', 'cocina'])) {
            abort(403, 'Acceso exclusivo de producción para el área de cocina.');
        }

        $query = SolicitudRestaurante::whereIn('estado_restaurante', ['Aprobado', 'Finalizado'])
            ->with(['solicitud.reservasFisicas.espacio.torre']);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_hora_evento', $request->fecha);
        }

        $pedidos = $query->orderBy('fecha_hora_evento', 'asc')->paginate(12);

        return view('cocina.index', compact('pedidos'));
    }

    public function export(Request $request)
    {
        return Excel::download(new RestauranteExport(
            $request->fecha, 
            'Aprobado', 
            $request->solicitante
        ), 'Planilla_Cocina_Produccion.xlsx');
    }
}