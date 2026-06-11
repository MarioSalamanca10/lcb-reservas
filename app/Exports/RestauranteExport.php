<?php

namespace App\Exports;

use App\Models\SolicitudRestaurante;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class RestauranteExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $fecha;

    public function __construct($fecha)
    {
        $this->fecha = $fecha;
    }

    public function query()
    {
        // Traemos también la información del espacio y la ENCUESTA de restaurante
        $query = SolicitudRestaurante::query()->with([
            'solicitud.reservaFisica.espacio',
            'solicitud.encuestaRestaurante'
        ]);

        if ($this->fecha) { $query->whereDate('fecha_hora_evento', $this->fecha); }

        return $query->orderBy('fecha_hora_evento', 'asc');
    }

    public function headings(): array
    {
        return [
            'ID', 'Solicitante', 'Evento', 'Lugar de Entrega', 'Fecha y Hora', 
            'Total Personas', 'Servicios / Menú', 'Instrucciones Docente', 
            'Estado Producción', 'Novedades Chef',
            // --- NUEVAS COLUMNAS DE ENCUESTA ---
            'Calificación General (1-5)',
            'Calidad de Comida', // Ajusta al nombre de tu llave JSON
            'Puntualidad Entrega', // Ajusta al nombre de tu llave JSON
            'Observaciones del Servicio'
        ];
    }

    public function map($rest): array
    {
        $encuesta = $rest->solicitud ? $rest->solicitud->encuestaRestaurante : null;
        // Traductor JSON
        $respuestas = [];
        if ($encuesta && $encuesta->respuestas_detalladas) {
            $respuestas = is_string($encuesta->respuestas_detalladas) ? json_decode($encuesta->respuestas_detalladas, true) : $encuesta->respuestas_detalladas;
        }

        return [
            $rest->id,
            $rest->solicitud->correo_solicitante ?? 'N/A',
            $rest->solicitud->titulo_evento ?? 'Sin título',
            $rest->solicitud->reservaFisica->espacio->nombre ?? 'Recogen en Cocina',
            Carbon::parse($rest->fecha_hora_evento)->format('d/m/Y h:i A'),
            $rest->num_asistentes,
            is_array($rest->servicio_requerido) ? implode(', ', $rest->servicio_requerido) : $rest->servicio_requerido,
            $rest->detalles_solicitud ?? '',
            $rest->estado_restaurante,
            $rest->respuesta_cocina ?? '',

            // --- DATA DE LA ENCUESTA ---
            $encuesta->calificacion_general ?? 'Sin evaluar',
            $respuestas['calidad_comida'] ?? 'N/A', // Ajusta a tu JSON real
            $respuestas['puntualidad'] ?? 'N/A',    // Ajusta a tu JSON real
            $encuesta->observaciones ?? 'N/A'
        ];
    }
}