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
    protected $fecha, $estado, $solicitante;

    public function __construct($fecha = null, $estado = null, $solicitante = null) {
        $this->fecha = $fecha; $this->estado = $estado; $this->solicitante = $solicitante;
    }

    public function query() {
        $query = SolicitudRestaurante::query()->with(['solicitud.reservaFisicas.espacio', 'solicitud.encuestaRestaurante']);
        if ($this->fecha) { $query->whereDate('fecha_hora_evento', $this->fecha); }
        if ($this->estado) { $query->where('estado_restaurante', $this->estado); }
        if ($this->solicitante) {
            $query->whereHas('solicitud', function($q) { $q->where('correo_solicitante', 'like', '%' . $this->solicitante . '%'); });
        }
        return $query->orderBy('fecha_hora_evento', 'asc');
    }

    public function headings(): array {
        return [
            'ID', 'Solicitante', 'Evento', 'Lugar de Entrega', 'Fecha y Hora', 
            'Total Personas', 'Menú', 'Dietas/Alergias', 'Estado Producción', 'Novedades Chef/Gerencia',
            'Nota Encuesta (1-5)', 'Comentarios del Docente'
        ];
    }

    public function map($rest): array {
        $encuesta = $rest->solicitud ? $rest->solicitud->encuestaRestaurante : null;
        $primeraReserva = $rest->solicitud ? $rest->solicitud->reservasFisicas->first() : null;

        return [
            $rest->id,
            $rest->solicitud->correo_solicitante ?? 'N/A',
            $rest->solicitud->titulo_evento ?? 'Sin título',
            $primeraReserva->espacio->nombre ?? 'Recogen en Cocina',
            Carbon::parse($rest->fecha_hora_evento)->format('d/m/Y h:i A'),
            $rest->num_asistentes,
            is_array($rest->servicio_requerido) ? implode(', ', $rest->servicio_requerido) : $rest->servicio_requerido,
            $rest->detalles_solicitud ?? 'Ninguna',
            $rest->estado_restaurante,
            $rest->respuesta_cocina ?? 'N/A',
            $encuesta->calificacion_general ?? 'Sin evaluar',
            $encuesta->observaciones ?? 'N/A'
        ];
    }
}