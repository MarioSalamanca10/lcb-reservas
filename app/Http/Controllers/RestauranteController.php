<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudRestaurante;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionEstadoServicio;
use App\Exports\RestauranteExport;
use Maatwebsite\Excel\Facades\Excel;

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

        // --- INICIO ENVÍO DE CORREO (EL RELEVO) ---
        try {
            $estadoNuevo = $request->estado_restaurante;
            
            // 1. Correo normal para el Docente notificando el cambio
            $datosCorreo = [
                'titulo' => $restaurante->solicitud->titulo_evento,
                'servicio' => 'Alimentación / Restaurante',
                'estado' => $estadoNuevo,
                'notas' => $request->respuesta_cocina
            ];
            \Illuminate\Support\Facades\Mail::to($restaurante->solicitud->correo_solicitante)
                ->send(new \App\Mail\NotificacionEstadoServicio($datosCorreo));

            // 2. ALERTA A LA COCINA (Solo si se aprueba un evento)
            if ($estadoNuevo === 'Aprobado') {
                // Buscamos los correos de todo el personal que tenga el rol 'cocina'
                $correosCocina = \App\Models\User::where('rol', 'cocina')->pluck('email');
                
                if ($correosCocina->count() > 0) {
                    $datosCocina = $datosCorreo;
                    // Le ponemos un título llamativo (con banderas) para que la cocina no lo ignore
                    $datosCocina['titulo'] = '🚨 NUEVO PEDIDO APROBADO: ' . $restaurante->solicitud->titulo_evento;
                    \Illuminate\Support\Facades\Mail::to($correosCocina)
                        ->send(new \App\Mail\NotificacionEstadoServicio($datosCocina));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando correo de restaurante: ' . $e->getMessage());
        }
        // --- FIN ENVÍO DE CORREO ---

        return back()->with('success', '¡Decisión e instrucciones de cocina guardadas exitosamente!');
    }

    public function exportarExcel(Request $request)
    {
        // Reutilizamos el mismo molde, pero le cambiamos el nombre al archivo a descargar
        return Excel::download(new RestauranteExport($request->fecha), 'Auditoria_Gerencia_Restaurante.xlsx');
    }
}