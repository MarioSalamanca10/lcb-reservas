@extends('layouts.app')
@section('title', 'Auditoría de Reservas')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('success'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl font-bold mb-6 flex items-center gap-3 shadow-sm">
            <span class="text-xl">🗑️</span> {{ session('success') }}
        </div>
    @endif
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Auditoría de Espacios</h1>
            <p class="text-gray-500 mt-2 italic">Monitoreo de ocupación, logística asociada y encuestas de satisfacción.</p>
        </div>
        <div class="bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100 shadow-sm">
            <span class="text-blue-600 font-bold text-sm">Total: {{ $reservas->total() }} eventos programados</span>
        </div>
    </div>

    <form action="{{ route('admin.reservas.index') }}" method="GET" class="bg-white/70 backdrop-blur-md p-6 rounded-3xl shadow-lg border border-gray-100 mb-10 flex flex-wrap gap-6 items-end">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Filtrar por Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="flex-1 min-w-[150px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Bloque / Torre</label>
            <select id="filtro-torre" name="torre_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las Torres</option>
                @foreach($torres as $torre)
                    <option value="{{ $torre->id }}" {{ request('torre_id') == $torre->id ? 'selected' : '' }}>{{ $torre->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Salón Específico</label>
            <select id="filtro-espacio" name="espacio_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los Salones</option>
                @foreach($espacios as $espacio)
                    <option value="{{ $espacio->id }}" data-torre="{{ $espacio->torre_id }}" {{ request('espacio_id') == $espacio->id ? 'selected' : '' }} class="{{ request('torre_id') && request('torre_id') != $espacio->torre_id ? 'hidden' : '' }}">
                        {{ $espacio->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Docente (Email)</label>
            <input type="text" name="docente" value="{{ request('docente') }}" placeholder="buscar@lcb.edu.co" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-xl font-bold shadow-md hover:bg-blue-700 transition-colors">Filtrar</button>
            @if(request()->has('fecha') || request()->has('torre_id') || request()->has('espacio_id') || request()->has('docente'))
                <a href="{{ route('admin.reservas.index') }}" class="bg-gray-100 text-gray-500 px-5 py-3 rounded-xl font-bold hover:bg-gray-200 transition-colors">Limpiar</a>
            @endif
            <button type="submit" formaction="{{ route('admin.reservas.export') }}" class="bg-green-600 text-white px-5 py-3 rounded-xl font-bold shadow-md hover:bg-green-700 transition-colors flex items-center gap-2">
                <span>📊</span> Excel
            </button>
        </div>
    </form>

    <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Información del Evento</th>
                        <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ubicación y Horario</th>
                        <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Extras</th>
                        <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reservas as $reserva)
                    <tr class="hover:bg-blue-50/40 transition-colors group">
                        
                        <td class="p-5 align-middle">
                            <p class="font-black text-gray-900 group-hover:text-blue-700 transition-colors text-base leading-tight mb-1">{{ $reserva->titulo ?? $reserva->solicitud->titulo_evento ?? 'Reserva General' }}</p>
                            <p class="text-xs text-blue-600 font-bold">{{ $reserva->correo_docente ?? $reserva->solicitud->correo_solicitante ?? 'N/A' }}</p>
                        </td>

                        <td class="p-5 align-middle">
                            <div class="flex items-center gap-3">
                                <div>
                                    <span class="text-sm font-black text-gray-800 block">{{ $reserva->espacio->nombre ?? 'N/A' }}</span>
                                    <span class="text-[10px] text-gray-500 uppercase font-black tracking-widest">{{ $reserva->espacio?->torre?->nombre ?? 'Sin Bloque' }}</span>
                                </div>
                                <div class="border-l border-gray-200 pl-3">
                                    <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</p>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="p-5 align-middle text-center">
                            <div class="flex justify-center gap-1">
                                @if($reserva->solicitud && $reserva->solicitud->transporte)
                                    <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs shadow-sm" title="Requiere Bus">🚌</span>
                                @endif
                                @if($reserva->solicitud && $reserva->solicitud->restaurante)
                                    <span class="w-6 h-6 bg-yellow-100 text-yellow-700 rounded-full flex items-center justify-center text-xs shadow-sm" title="Tiene Comida">🍽️</span>
                                @endif
                                @if($reserva->solicitud && $reserva->solicitud->encuestaEspacio)
                                    <span class="w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs shadow-sm" title="Encuesta Respondida">⭐</span>
                                @endif
                            </div>
                        </td>

                        <td class="p-5 align-middle text-right">
                            <div class="flex items-center justify-end gap-2">
                                
                                <button type="button" onclick="abrirModal('{{ $reserva->id }}')" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-700 transition-colors shadow-sm">
                                    Ver Detalles
                                </button>
                                
                                <form action="{{ route('admin.reservas.destroy', $reserva->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de cancelar/eliminar este registro del sistema?');" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-red-50 text-red-600 px-3 py-2 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors border border-red-200" title="Cancelar Reserva">
                                        ❌
                                    </button>
                                </form>
                            </div>

                            <div id="modal-{{ $reserva->id }}" class="fixed inset-0 z-[100] flex items-center justify-center px-4 bg-black/60 backdrop-blur-sm hidden text-left" style="margin: 0;">
                                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
                                    
                                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                        <h3 class="text-xl font-black text-gray-800">Detalles de la Reserva</h3>
                                        <button type="button" onclick="cerrarModal('{{ $reserva->id }}')" class="text-gray-400 hover:text-red-500 transition-colors text-3xl font-bold leading-none">&times;</button>
                                    </div>

                                    <div class="p-6 space-y-6 overflow-y-auto">
                                        <div>
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 border-b border-gray-100 pb-1">Información General</p>
                                            <h4 class="text-lg font-bold text-blue-700">{{ $reserva->titulo ?? $reserva->solicitud->titulo_evento ?? 'Sin título' }}</h4>
                                            <p class="text-sm text-gray-600"><b>Solicita:</b> {{ $reserva->correo_docente ?? $reserva->solicitud->correo_solicitante ?? 'N/A' }}</p>
                                            <p class="text-sm text-gray-600 mt-2"><b>Observaciones del Docente:</b></p>
                                            <p class="text-sm bg-gray-50 p-3 rounded-lg border border-gray-100 mt-1 italic">{{ $reserva->observaciones ?? 'Sin observaciones especiales.' }}</p>
                                            <p class="text-sm text-gray-600 mt-3"><b>Recursos Físicos Solicitados:</b></p>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @if($reserva->recursos_adicionales)
                                                    @foreach($reserva->recursos_adicionales as $item)
                                                        <span class="text-[10px] bg-gray-100 border border-gray-200 text-gray-600 px-2 py-1 rounded font-bold">{{ $item }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Ninguno</span>
                                                @endif
                                            </div>
                                        </div>

                                        @php 
                                            $encuesta = $reserva->solicitud ? $reserva->solicitud->encuestaEspacio : null; 
                                            $respuestas = [];
                                            if ($encuesta && $encuesta->respuestas_detalladas) {
                                                // Truco para decodificar JSON si viene como texto desde la DB
                                                $respuestas = is_string($encuesta->respuestas_detalladas) 
                                                    ? json_decode($encuesta->respuestas_detalladas, true) 
                                                    : $encuesta->respuestas_detalladas;
                                            }
                                        @endphp

                                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-200 pb-1 flex items-center gap-2">
                                                <span>⭐</span> Resultados de Encuesta
                                            </p>
                                            
                                            @if($encuesta)
                                                <div class="flex items-center gap-2 mb-4">
                                                    <span class="text-xs font-black text-gray-800 uppercase">Nota Global:</span>
                                                    <div class="flex text-lg">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <span class="leading-none {{ $i <= $encuesta->calificacion_general ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3 mb-4">
                                                    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                                                        <span class="text-[10px] text-gray-500 font-bold uppercase block mb-1">Limpieza / Organización</span>
                                                        <span class="text-sm text-gray-800 font-black">{{ $respuestas['limpieza'] ?? 'N/A' }}{{ isset($respuestas['limpieza']) ? '/5' : '' }}</span>
                                                    </div>
                                                    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                                                        <span class="text-[10px] text-gray-500 font-bold uppercase block mb-1">Equipos (TV/Video)</span>
                                                        <span class="text-sm text-gray-800 font-black">{{ $respuestas['equipos'] ?? 'N/A' }}{{ isset($respuestas['equipos']) ? '/5' : '' }}</span>
                                                    </div>
                                                    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                                                        <span class="text-[10px] text-gray-500 font-bold uppercase block mb-1">Puntualidad</span>
                                                        <span class="text-sm text-gray-800 font-black">{{ $respuestas['puntualidad'] ?? 'N/A' }}{{ isset($respuestas['puntualidad']) ? '/5' : '' }}</span>
                                                    </div>
                                                </div>

                                                @if($encuesta->observaciones)
                                                    <div class="bg-blue-50/60 p-3 rounded-xl border border-blue-100">
                                                        <span class="text-[10px] font-black text-blue-400 uppercase block mb-1">Observaciones del Docente:</span>
                                                        <p class="text-xs text-gray-700 italic leading-relaxed">"{{ $encuesta->observaciones }}"</p>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-center py-4">
                                                    <span class="text-2xl mb-2 block opacity-50">⏳</span>
                                                    <p class="text-xs text-gray-500 font-bold uppercase">Aún no evaluado por el docente</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 text-right">
                                        <button type="button" onclick="cerrarModal('{{ $reserva->id }}')" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-bold hover:bg-gray-300 transition-colors">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
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

<script>
    // JS PARA EL FILTRO CASCADA (TORRE -> SALÓN)
    document.getElementById('filtro-torre').addEventListener('change', function() {
        const torreId = this.value;
        const espacioSelect = document.getElementById('filtro-espacio');
        espacioSelect.value = "";
        Array.from(espacioSelect.querySelectorAll('option')).forEach(opt => {
            if (opt.value === "") return;
            if (torreId === "" || opt.dataset.torre == torreId) {
                opt.classList.remove('hidden');
            } else {
                opt.classList.add('hidden');
            }
        });
    });

    // JS INFALIBLE PARA LOS MODALES
    function abrirModal(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
    }
    
    function cerrarModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
    }
</script>
@endsection