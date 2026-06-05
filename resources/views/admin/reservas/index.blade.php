@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Auditoría de Reservas Físicas</h1>
            <p class="text-gray-500 mt-2 italic">Monitoreo de ocupación, logística asociada y encuestas de satisfacción.</p>
        </div>
        <div class="bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 shadow-sm">
            <span class="text-blue-600 font-bold text-sm">Total: {{ $reservas->total() }} eventos programados</span>
        </div>
    </div>

    <form action="{{ route('admin.reservas.index') }}" method="GET" class="bg-white/70 backdrop-blur-md p-6 rounded-3xl shadow-lg border border-gray-100 mb-10 flex flex-wrap gap-6 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Filtrar por Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Bloque / Torre</label>
            <select name="torre_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las Torres</option>
                @foreach($torres as $torre)
                    <option value="{{ $torre->id }}" {{ request('torre_id') == $torre->id ? 'selected' : '' }}>{{ $torre->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Docente (Email)</label>
            <input type="text" name="docente" value="{{ request('docente') }}" placeholder="buscar@lcb.edu.co" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold shadow-md hover:bg-blue-700 transition-colors">Filtrar</button>
            @if(request()->has('fecha') || request()->has('torre_id') || request()->has('docente'))
                <a href="{{ route('admin.reservas.index') }}" class="bg-gray-100 text-gray-500 px-6 py-3 rounded-xl font-bold hover:bg-gray-200 transition-colors">Limpiar</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Información del Evento</th>
                        <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ubicación y Horario</th>
                        <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Recursos Extras</th>
                        <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Logística Amarrada y Encuestas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reservas as $reserva)
                    <tr class="hover:bg-blue-50/40 transition-colors group">
                        
                        <td class="p-5 align-top">
                            <p class="font-black text-gray-900 group-hover:text-blue-700 transition-colors text-base leading-tight mb-1">{{ $reserva->titulo ?? $reserva->solicitud->titulo_evento ?? 'Reserva General' }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Solicita:</p>
                            <p class="text-xs text-blue-600 font-bold">{{ $reserva->correo_docente ?? $reserva->solicitud->correo_solicitante ?? 'N/A' }}</p>
                        </td>

                        <td class="p-5 align-top">
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-100 inline-block mb-2">
                                <span class="text-sm font-black text-gray-800 block">{{ $reserva->espacio->nombre ?? 'N/A' }}</span>
                                <span class="text-[10px] text-gray-500 uppercase font-black tracking-widest">{{ $reserva->espacio->torre->nombre ?? 'Sin Bloque' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-black text-gray-800">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</p>
                                <p class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-black uppercase tracking-wider">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</p>
                            </div>
                        </td>

                        <td class="p-5 align-top">
                            <div class="flex flex-wrap gap-1 max-w-[150px]">
                                @if($reserva->recursos_adicionales)
                                    @foreach($reserva->recursos_adicionales as $item)
                                        <span class="text-[9px] bg-white border border-gray-200 text-gray-600 px-2 py-1 rounded font-bold shadow-sm">{{ $item }}</span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-300 italic">Ninguno</span>
                                @endif
                            </div>
                        </td>

                        <td class="p-5 align-top bg-gray-50/50">
                            
                            <div class="flex flex-wrap gap-2 mb-3">
                                @if($reserva->solicitud && $reserva->solicitud->transporte)
                                    <span class="bg-green-100 text-green-800 border border-green-200 px-2 py-1 rounded text-[10px] font-black uppercase shadow-sm" title="El docente también pidió Bus">
                                        🚌 Requiere Bus
                                    </span>
                                @endif
                                
                                @if($reserva->solicitud && $reserva->solicitud->restaurante)
                                    <span class="bg-yellow-100 text-yellow-800 border border-yellow-200 px-2 py-1 rounded text-[10px] font-black uppercase shadow-sm" title="El docente también pidió Comida">
                                        🍽️ Tiene Comida
                                    </span>
                                @endif
                                
                                @if(!$reserva->solicitud || (!$reserva->solicitud->transporte && !$reserva->solicitud->restaurante))
                                    <span class="text-[10px] text-gray-400 font-bold uppercase">Sin servicios extras</span>
                                @endif
                            </div>

                            <div class="bg-white border border-gray-200 p-2.5 rounded-lg shadow-sm">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 border-b border-gray-100 pb-1">Evaluación del Espacio</p>
                                @if($reserva->encuesta)
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="text-sm leading-none {{ $i <= $reserva->encuesta->calificacion_general ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                        @endfor
                                        <span class="text-[10px] font-black text-gray-600 ml-1">{{ $reserva->encuesta->calificacion_general }}/5</span>
                                    </div>
                                    @if($reserva->encuesta->comentarios)
                                        <p class="text-[10px] text-gray-500 italic mt-1.5 leading-tight">"{{ Str::limit($reserva->encuesta->comentarios, 55) }}"</p>
                                    @endif
                                @else
                                    <p class="text-[10px] text-gray-400 font-medium italic mt-1">Evento no evaluado por el docente.</p>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center">
                            <span class="text-5xl grayscale opacity-20 block mb-4">🏢</span>
                            <p class="text-gray-400 font-medium">No se encontraron reservas que coincidan con la búsqueda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $reservas->appends(request()->query())->links() }}
    </div>
</div>
@endsection