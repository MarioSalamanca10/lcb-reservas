@extends('layouts.app')
@section('title', 'Tablero de Cocina')

@section('content')
<div class="w-full">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-black text-zinc-800 tracking-tight">Pizarra de Producción (Cocina)</h1>
            <p class="text-zinc-500 text-sm mt-1">Monitoreo informativo de órdenes de servicio, menús aprobados y dietas especiales.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-xl font-bold">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-3 rounded-xl shadow-sm border border-zinc-200 mb-6 flex flex-wrap gap-3 items-center">
        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 pl-2">Filtros:</span>
        <form action="{{ route('cocina.index') }}" method="GET" class="flex flex-wrap gap-2 w-full sm:w-auto">
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-orange-500 w-40 cursor-pointer">
            
            <button type="submit" class="bg-zinc-800 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-zinc-700 transition-colors shadow-sm">
                Filtrar Pizarra
            </button>
            
            @if(request()->has('fecha'))
                <a href="{{ route('cocina.index') }}" class="text-zinc-400 hover:text-red-500 text-xs font-bold px-2 py-1.5 transition-colors flex items-center">Limpiar Filtro</a>
            @endif
            
            <button type="submit" formaction="{{ route('cocina.export') }}" class="bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-green-700 transition-colors flex items-center gap-1 shadow-sm">
                <span>📊</span> Descargar Excel
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                <thead class="bg-zinc-800 text-zinc-100 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="p-4 w-1/6">Cronograma / Hora</th>
                        <th class="p-4 w-1/4">Lugar y Evento</th>
                        <th class="p-4">Menú Solicitado</th>
                        <th class="p-4 w-1/6 text-center">Porciones (PAX)</th>
                        <th class="p-4 w-1/6 text-right">Detalles</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($pedidos as $pedido)
                        @php
                            $estado = $pedido->estado_restaurante;
                            $rowColor = $estado == 'Finalizado' ? 'bg-zinc-50/70 opacity-70' : 'bg-white';
                            $servicios = is_string($pedido->servicio_requerido) ? json_decode($pedido->servicio_requerido, true) : $pedido->servicio_requerido;
                            $primeraReserva = $pedido->solicitud?->reservasFisicas?->first();
                        @endphp

                        <tr class="{{ $rowColor }} hover:bg-orange-50/40 transition-colors group">
                            
                            <td class="p-4 align-middle">
                                <div class="bg-zinc-100 border border-zinc-200 p-2.5 rounded-xl text-center shadow-inner max-w-[110px]">
                                    <p class="text-[10px] font-black text-zinc-500 uppercase leading-none">
                                        {{ \Carbon\Carbon::parse($pedido->fecha_hora_evento)->format('M d') }}
                                    </p>
                                    <p class="text-base font-black text-zinc-800 mt-1 leading-none">
                                        {{ \Carbon\Carbon::parse($pedido->fecha_hora_evento)->format('h:i A') }}
                                    </p>
                                </div>
                            </td>

                            <td class="p-4 align-middle">
                                <p class="font-black text-zinc-800 text-base leading-tight">{{ $pedido->solicitud?->titulo_evento ?? 'Sin título' }}</p>
                                <p class="text-xs font-bold text-zinc-600 mt-1 flex items-center gap-1">
                                    📍 {{ $primeraReserva?->espacio?->nombre ?? 'Entrega en área del solicitante' }}
                                </p>
                                @if($primeraReserva?->espacio?->torre)
                                    <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-wide ml-4">
                                        {{ $primeraReserva->espacio->torre->nombre }}
                                    </p>
                                @endif
                            </td>

                            <td class="p-4 align-middle">
                                <div class="flex flex-wrap gap-1">
                                    @if(is_array($servicios))
                                        @foreach($servicios as $srv) 
                                            <span class="bg-white border border-zinc-300 px-2.5 py-0.5 rounded text-[11px] font-bold text-zinc-700 shadow-sm">{{ $srv }}</span> 
                                        @endforeach
                                    @else
                                        <span class="text-zinc-400 italic">No especificados</span>
                                    @endif
                                </div>
                            </td>

                            <td class="p-4 align-middle text-center">
                                <span class="bg-orange-100 text-orange-800 border border-orange-200 px-3 py-1 rounded-lg text-sm font-black shadow-sm inline-block">
                                    {{ $pedido->num_asistentes }} PAX
                                </span>
                            </td>

                            <td class="p-4 align-middle text-right">
                                <button type="button" onclick="abrirModalCocina('{{ $pedido->id }}')" class="bg-zinc-800 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-zinc-700 transition-colors shadow-sm">
                                    Ver Ficha
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center text-zinc-500 font-bold">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-5xl mb-3 opacity-40">👨‍🍳</span>
                                    <p class="text-base font-black text-zinc-400">Pizarra informativa vacía</p>
                                    <p class="text-xs text-zinc-400 font-medium mt-1">No hay servicios aprobados por gerencia que coincidan con la búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pedidos->hasPages())
            <div class="p-4 border-t border-zinc-200 bg-zinc-50">
                {{ $pedidos->links() }}
            </div>
        @endif
    </div>
</div>

@foreach($pedidos as $pedido)
    @php
        $servicios = is_string($pedido->servicio_requerido) ? json_decode($pedido->servicio_requerido, true) : $pedido->servicio_requerido;
        $primeraReserva = $pedido->solicitud?->reservasFisicas?->first();
        $encuesta = $pedido->solicitud?->encuestaRestaurante;
    @endphp

    <div id="modal-cocina-{{ $pedido->id }}" class="fixed inset-0 z-[9999] flex items-center justify-center px-4 bg-black/70 backdrop-blur-sm hidden text-left" style="margin: 0;">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[85vh] overflow-hidden flex flex-col border border-gray-200">
            
            <div class="bg-zinc-900 px-6 py-4 border-b border-zinc-800 flex justify-between items-center">
                <h3 class="text-lg font-black text-white flex items-center gap-2">
                    <span>👨‍🍳</span> Ficha de Producción #{{ $pedido->id }}
                </h3>
                <button type="button" onclick="cerrarModalCocina('{{ $pedido->id }}')" class="text-zinc-400 hover:text-red-400 transition-colors text-3xl font-bold leading-none">&times;</button>
            </div>

            <div class="p-6 space-y-5 overflow-y-auto bg-white">
                <div>
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest block mb-1">Nombre de la Orden / Evento</span>
                    <h4 class="text-xl font-black text-zinc-800 leading-snug">{{ $pedido->solicitud?->titulo_evento }}</h4>
                    <p class="text-xs text-zinc-500 mt-1">Solicitado por: <b>{{ $pedido->solicitud?->correo_solicitante }}</b></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-zinc-50 p-3 rounded-xl border border-zinc-200">
                        <span class="text-[10px] text-zinc-400 font-black uppercase tracking-wider block mb-0.5">Fecha y Hora</span>
                        <p class="text-xs font-black text-zinc-700">{{ \Carbon\Carbon::parse($pedido->fecha_hora_evento)->format('d/m/Y - h:i A') }}</p>
                    </div>
                    <div class="bg-zinc-50 p-3 rounded-xl border border-zinc-200">
                        <span class="text-[10px] text-zinc-400 font-black uppercase tracking-wider block mb-0.5">Volumen Total</span>
                        <p class="text-xs font-black text-orange-700">{{ $pedido->num_asistentes }} Porciones (PAX)</p>
                    </div>
                </div>

                <div class="bg-zinc-50 p-3 rounded-xl border border-zinc-200">
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-wider block mb-1">Lugar Exacto de Despacho</span>
                    <p class="text-xs font-bold text-zinc-800">
                        📍 {{ $primeraReserva?->espacio?->nombre ?? 'Recogen en Cocina' }}
                    </p>
                    @if($primeraReserva?->espacio?->torre)
                        <p class="text-[10px] text-zinc-500 font-bold uppercase mt-0.5 ml-4">{{ $primeraReserva->espacio->torre->nombre }}</p>
                    @endif
                </div>

                <div>
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest block mb-2">Detalle de Menú a Preparar:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @if(is_array($servicios))
                            @foreach($servicios as $srv) 
                                <span class="bg-orange-50 border border-orange-200 text-orange-800 px-3 py-1 rounded-lg text-xs font-black">{{ $srv }}</span> 
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="bg-red-50 p-4 rounded-2xl border border-red-200">
                    <span class="text-[10px] font-black text-red-600 uppercase tracking-widest block mb-1 flex items-center gap-1">⚠️ Observaciones de Alergias / Dietas:</span>
                    <p class="text-xs text-red-900 font-black leading-relaxed">
                        {{ $pedido->detalles_solicitud ?? 'Sin dietas ni especificaciones especiales registradas.' }}
                    </p>
                </div>

                @if($pedido->respuesta_cocina)
                    <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl">
                        <span class="text-[10px] font-black text-blue-500 uppercase block mb-0.5">Indicaciones de la Gerencia:</span>
                        <p class="text-xs text-zinc-700 italic">"{{ $pedido->respuesta_cocina }}"</p>
                    </div>
                @endif

                @if($encuesta)
                    <div class="bg-green-50 p-4 rounded-xl border border-green-200 mt-4">
                        <span class="text-[10px] font-black text-green-700 uppercase tracking-widest block mb-2">⭐ Evaluación del Docente</span>
                        <div class="flex items-center gap-2">
                            <div class="flex text-lg">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $encuesta->calificacion_general ? 'text-yellow-500' : 'text-gray-300' }}">★</span>
                                @endfor
                            </div>
                            <span class="text-xs font-black text-green-800">{{ $encuesta->calificacion_general }}/5</span>
                        </div>
                        @if($encuesta->observaciones)
                            <p class="text-xs text-green-800 italic mt-2 border-l-2 border-green-300 pl-2">"{{ $encuesta->observaciones }}"</p>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 mt-4 text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Servicio sin evaluar aún</p>
                    </div>
                @endif
            </div>
            
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 text-right">
                <button type="button" onclick="cerrarModalCocina('{{ $pedido->id }}')" class="bg-zinc-200 text-zinc-700 px-5 py-2 rounded-lg text-xs font-bold hover:bg-zinc-300 transition-colors">
                    Cerrar Ficha
                </button>
            </div>
        </div>
    </div>
@endforeach

<script>
    function abrirModalCocina(id) { document.getElementById('modal-cocina-' + id).classList.remove('hidden'); }
    function cerrarModalCocina(id) { document.getElementById('modal-cocina-' + id).classList.add('hidden'); }
</script>
@endsection