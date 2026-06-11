<?php

namespace App\Exports;

use App\Models\ReservaFisica;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class EspaciosExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $fecha, $torre_id, $docente;

    public function __construct($fecha, $torre_id, $docente)
    {
        $this->fecha = $fecha;
        $this->torre_id = $torre_id;
        $this->docente = $docente;
    }

    public function query()
    {
        // Traemos todo el ecosistema, incluyendo la nueva encuesta
        $query = ReservaFisica::query()->with([
            'espacio.torre', 
            'solicitud.transporte', 
            'solicitud.restaurante', 
            'solicitud.encuestaEspacio'
        ]);

        if ($this->fecha) { $query->whereDate('fecha_inicio', $this->fecha); }
        if ($this->torre_id) { $query->whereHas('espacio', function($q) { $q->where('torre_id', $this->torre_id); }); }
        if ($this->docente) {
            $query->where(function($q) {
                $q->where('correo_docente', 'like', '%' . $this->docente . '%')
                  ->orWhereHas('solicitud', function($sq) { $sq->where('correo_solicitante', 'like', '%' . $this->docente . '%'); });
            });
        }

        return $query->orderBy('fecha_inicio', 'asc');
    }

    public function headings(): array
    {
        return [
            'ID Reserva', 'Docente', 'Evento', 'Bloque', 'Espacio', 'Fecha', 'Horario', 
            'Bus', 'Comida', 'Calificación (1-5)', 
            'Pregunta 1', 'Pregunta 2', 'Observaciones' // Reemplaza Pregunta 1 y 2 por tus criterios reales
        ];
    }

    public function map($reserva): array
    {
        $encuesta = $reserva->solicitud ? $reserva->solicitud->encuestaEspacio : null;
        // Traductor JSON
        $respuestas = [];
        if ($encuesta && $encuesta->respuestas_detalladas) {
            $respuestas = is_string($encuesta->respuestas_detalladas) ? json_decode($encuesta->respuestas_detalladas, true) : $encuesta->respuestas_detalladas;
        }

        return [
            $reserva->id,
            $reserva->correo_docente ?? ($reserva->solicitud->correo_solicitante ?? 'N/A'),
            $reserva->titulo ?? ($reserva->solicitud->titulo_evento ?? 'General'),
            $reserva->espacio->torre->nombre ?? 'Sin Bloque',
            $reserva->espacio->nombre ?? 'N/A',
            Carbon::parse($reserva->fecha_inicio)->format('d/m/Y'),
            $reserva->hora_inicio . ' - ' . $reserva->hora_fin,
            ($reserva->solicitud && $reserva->solicitud->transporte) ? 'SÍ' : 'NO',
            ($reserva->solicitud && $reserva->solicitud->restaurante) ? 'SÍ' : 'NO',
            
            // DATOS DE LA ENCUESTA
            $encuesta->calificacion_general ?? 'Sin evaluar',
            $respuestas['limpieza'] ?? 'N/A', // OJO: Cambia 'limpieza' por la llave JSON real que guardas
            $respuestas['equipos'] ?? 'N/A',  // OJO: Cambia 'equipos' por la llave JSON real que guardas
            $encuesta->observaciones ?? 'N/A'
        ];
    }
}