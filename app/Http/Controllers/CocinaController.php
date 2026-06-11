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
            abort(403, 'Acceso Denegado.');
        }

        // COCINA SOLO VE LO APROBADO O LO QUE YA FINALIZARON ELLOS MISMOS
        $query = SolicitudRestaurante::with(['solicitud', 'solicitud.reservaFisica.espacio.torre'])
            ->whereIn('estado_restaurante', ['Aprobado', 'Finalizado']);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_hora_evento', $request->fecha);
        }

        $pedidos = $query->orderBy('fecha_hora_evento', 'asc')->paginate(15);

        return view('cocina.index', compact('pedidos'));
    }

    // NUEVA FUNCIÓN: El Chef marca el pedido como entregado
    public function finalizar(Request $request, $id)
    {
        $request->validate([
            'observaciones_finales' => 'nullable|string'
        ]);

        $pedido = SolicitudRestaurante::findOrFail($id);
        
        $pedido->update([
            'estado_restaurante' => 'Finalizado',
            // Concatenamos las notas de Gerencia con las notas finales del Chef
            'respuesta_cocina' => $pedido->respuesta_cocina . ' | [Nota Chef]: ' . ($request->observaciones_finales ?? 'Entregado sin novedades.')
        ]);

        return back()->with('success', '¡Pedido marcado como Finalizado y bloqueado en el sistema!');
    }

    public function exportarExcel(Request $request)
    {
        return Excel::download(new RestauranteExport($request->fecha), 'Planilla_Produccion_Cocina.xlsx');
    }
}