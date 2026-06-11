@extends('layouts.app')
@section('title', 'Tablero de Cocina')

@section('content')
<div class="w-full">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-black text-zinc-800">Tablero de Producción (Cocina)</h1>
            <p class="text-zinc-500 text-sm mt-1">Órdenes de servicio, dietas especiales y cronograma de entregas.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-xl font-bold">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-3 rounded-xl shadow-sm border border-zinc-200 mb-6 flex flex-wrap gap-3 items-center">
        <form action="{{ route('cocina.index') }}" method="GET" class="flex flex-wrap gap-2 w-full sm:w-auto">
            <input type="text" name="fecha" value="{{ request('fecha') }}" placeholder="Filtrar Día..." class="calendario-lcb bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-orange-500 w-32 cursor-pointer">
            
            <button type="submit" class="bg-zinc-800 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-zinc-700 transition-colors shadow-sm">
                Filtrar Pizarra
            </button>
            
            @if(request()->has('fecha'))
                <a href="{{ route('cocina.index') }}" class="text-zinc-400 hover:text-red-500 text-xs font-bold px-2 py-1.5 transition-colors">Limpiar Filtro</a>
            @endif
            <button type="submit" formaction="{{ route('cocina.export') }}" class="bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-green-700 transition-colors flex items-center gap-1 shadow-sm">
                <span>📊</span> Excel
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                <thead class="bg-zinc-800 text-zinc-100 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="p-4 w-1/5">Hora de Entrega</th>
                        <th class="p-4 w-1/4">Lugar y Evento</th>
                        <th class="p-4">Menú y Dietas Especiales</th>
                        <th class="p-4 w-1/5 text-center">Estado de Producción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($pedidos as $pedido)
                        @php
                            $estado = $pedido->estado_restaurante;
                            // Colores según estado (Aprobado = Verde / Finalizado = Gris)
                            $rowColor = $estado == 'Aprobado' ? 'bg-green-50/40' : 'bg-zinc-50/50';
                        @endphp

                        <tr class="{{ $rowColor }} hover:bg-zinc-50 transition-colors group">
                            
                            <td class="p-4 align-top">
                                <div class="bg-white border {{ $estado == 'Finalizado' ? 'border-zinc-200 opacity-60' : 'border-green-200' }} p-3 rounded-xl text-center shadow-sm">
                                    <p class="text-xs font-black {{ $estado == 'Finalizado' ? 'text-zinc-400' : 'text-green-600' }} uppercase">
                                        {{ \Carbon\Carbon::parse($pedido->fecha_hora_evento)->format('M d') }}
                                    </p>
                                    <p class="text-xl font-black {{ $estado == 'Finalizado' ? 'text-zinc-600' : 'text-zinc-800' }}">
                                        {{ \Carbon\Carbon::parse($pedido->fecha_hora_evento)->format('h:i A') }}
                                    </p>
                                </div>
                            </td>

                            <td class="p-4 align-top {{ $estado == 'Finalizado' ? 'opacity-60' : '' }}">
                                <p class="font-black text-zinc-800 text-base leading-tight">{{ $pedido->solicitud->titulo_evento }}</p>
                                <p class="text-xs font-bold text-zinc-600 mt-2 flex items-center gap-1">
                                    📍 {{ $pedido->solicitud->reservaFisica->espacio->nombre ?? 'Recogen en Cocina' }}
                                </p>
                                <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-1">
                                    Solicita: {{ $pedido->solicitud->correo_solicitante }}
                                </p>
                            </td>

                            <td class="p-4 align-top {{ $estado == 'Finalizado' ? 'opacity-60' : '' }}">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="bg-orange-100 text-orange-800 px-2 py-0.5 rounded text-[10px] font-black border border-orange-200">
                                        👥 {{ $pedido->num_asistentes }} PAX
                                    </span>
                                    @if(is_array($pedido->servicio_requerido))
                                        @foreach($pedido->servicio_requerido as $srv) 
                                            <span class="bg-white border border-zinc-300 px-2 py-0.5 rounded text-[10px] font-bold text-zinc-600 shadow-sm">{{ $srv }}</span> 
                                        @endforeach
                                    @endif
                                </div>
                                
                                @if($pedido->detalles_solicitud)
                                    <p class="text-sm text-zinc-700 italic border-l-2 border-orange-400 pl-2 mt-2 bg-white/60 p-2 rounded-r-lg">
                                        <b>Notas:</b> {{ $pedido->detalles_solicitud }}
                                    </p>
                                @endif
                                
                                @if($pedido->respuesta_cocina)
                                    <p class="text-xs text-blue-800 bg-blue-50/80 p-2 rounded-lg mt-2 border border-blue-100 font-medium">
                                        <b>Gerencia:</b> {{ $pedido->respuesta_cocina }}
                                    </p>
                                @endif
                            </td>

                            <td class="p-4 align-top text-center bg-zinc-50/50">
                                @if($estado == 'Aprobado')
                                    
                                    <form action="{{ route('cocina.finalizar', $pedido->id) }}" method="POST" onsubmit="return confirm('¿Confirmas que este pedido ya fue entregado/servido?');">
                                        @csrf @method('PATCH')
                                        <textarea name="observaciones_finales" rows="1" placeholder="Novedades (Opcional)" class="w-full bg-white border border-zinc-300 rounded text-[10px] px-2 py-1.5 mb-2 focus:ring-1 focus:ring-orange-500 font-medium"></textarea>
                                        <button type="submit" class="w-full bg-orange-500 text-white text-[10px] font-black uppercase tracking-widest py-2 rounded-md hover:bg-orange-600 transition-colors shadow-md active:scale-95">
                                            Marcar Entregado
                                        </button>
                                    </form>
                                @elseif($estado == 'Finalizado')
                                    <div class="opacity-70 cursor-not-allowed">
                                        <span class="px-3 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest shadow-sm bg-zinc-800 text-white block w-full text-center">
                                            🏁 Servicio Finalizado
                                        </span>
                                        <p class="text-[9px] text-zinc-500 mt-2 font-bold leading-tight">Registro archivado y bloqueado por el sistema.</p>
                                    </div>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-zinc-500 font-bold">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-4xl mb-3 opacity-50">👨‍🍳</span>
                                    <p>No hay pedidos pendientes de entrega en la pizarra.</p>
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
@endsection