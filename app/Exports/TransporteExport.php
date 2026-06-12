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
    protected $fecha, $estado, $solicitante;

    public function __construct($fecha = null, $estado = null, $solicitante = null) {
        $this->fecha = $fecha; $this->estado = $estado; $this->solicitante = $solicitante;
    }

    public function query() {
        $query = SolicitudTransporte::query()->with(['solicitud.encuestaTransporte']);
        if ($this->fecha) { $query->whereDate('fecha_hora_servicio', $this->fecha); }
        if ($this->estado) { $query->where('estado_transporte', $this->estado); }
        if ($this->solicitante) {
            $query->whereHas('solicitud', function($q) { $q->where('correo_solicitante', 'like', '%' . $this->solicitante . '%'); });
        }
        return $query->orderBy('fecha_hora_servicio', 'asc');
    }

    public function headings(): array {
        return [
            'ID', 'Responsable', 'Celular', 'Área', 'Destino', 'Salida', 'Regreso',
            'Estudiantes', 'Adultos', 'Necesidades', 'Estado', 'Placas/Logística',
            'Nota Encuesta (1-5)', 'Comentarios del Docente'
        ];
    }

    public function map($trans): array {
        $encuesta = $trans->solicitud ? $trans->solicitud->encuestaTransporte : null;

        return [
            $trans->id,
            $trans->nombre_responsable,
            $trans->celular_responsable,
            $trans->area_solicitante ?? 'N/A',
            $trans->direccion_destino,
            Carbon::parse($trans->fecha_hora_servicio)->format('d/m/Y h:i A'),
            $trans->fecha_hora_regreso ? Carbon::parse($trans->fecha_hora_regreso)->format('d/m/Y h:i A') : 'Solo Ida',
            $trans->num_estudiantes,
            $trans->num_adultos,
            is_array($trans->necesidades_servicio) ? implode(', ', $trans->necesidades_servicio) : $trans->necesidades_servicio,
            $trans->estado_transporte,
            $trans->respuesta_coordinador ?? 'N/A',
            $encuesta->calificacion_general ?? 'Sin evaluar',
            $encuesta->observaciones ?? 'N/A'
        ];
    }
}