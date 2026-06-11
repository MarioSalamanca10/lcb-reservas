<?php

namespace App\Exports;

use App\Models\SolicitudTransporte;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class TransporteExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $fecha;

    public function __construct($fecha)
    {
        $this->fecha = $fecha;
    }

    public function query()
    {
        return SolicitudTransporte::query()->with(['solicitud.encuestaTransporte']);
    }

    public function headings(): array
    {
        return [
            'ID', 'Responsable', 'Celular', 'Área', 'Destino', 'Salida', 'Regreso',
            'Estudiantes', 'Adultos', 'Necesidades', 'Estado', 'Novedades Logística',
            // --- NUEVAS COLUMNAS DE ENCUESTA ---
            'Calificación General (1-5)',
            'Puntualidad', // Ajusta a tu llave JSON
            'Estado del Vehículo', // Ajusta a tu llave JSON
            'Observaciones de Ruta'
        ];
    }

    public function map($trans): array
    {
        $encuesta = $trans->solicitud ? $trans->solicitud->encuestaTransporte : null;
        // Traductor JSON
        $respuestas = [];
        if ($encuesta && $encuesta->respuestas_detalladas) {
            $respuestas = is_string($encuesta->respuestas_detalladas) ? json_decode($encuesta->respuestas_detalladas, true) : $encuesta->respuestas_detalladas;
        }

        return [
            $trans->id,
            $trans->responsable,
            $trans->celular,
            $trans->area_solicitante ?? 'N/A',
            $trans->direccion_destino,
            Carbon::parse($trans->fecha_hora_salida)->format('d/m/Y h:i A'),
            $trans->fecha_hora_regreso ? Carbon::parse($trans->fecha_hora_regreso)->format('d/m/Y h:i A') : 'Solo Ida',
            $trans->num_estudiantes,
            $trans->num_adultos,
            is_array($trans->necesidades_servicio) ? implode(', ', $trans->necesidades_servicio) : $trans->necesidades_servicio,
            $trans->estado_transporte,
            $trans->respuesta_coordinador ?? '',

            // --- DATA DE LA ENCUESTA ---
            $encuesta->calificacion_general ?? 'Sin evaluar',
            $respuestas['puntualidad'] ?? 'N/A', // Ajusta a tu JSON real
            $respuestas['vehiculo'] ?? 'N/A',    // Ajusta a tu JSON real
            $encuesta->observaciones ?? 'N/A'
        ];
    }
}