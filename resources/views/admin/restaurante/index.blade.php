@extends('layouts.app')
@section('title', 'Auditoría Restaurante')

@section('content')
<div class="w-full">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-black text-zinc-800">Panel de Aprobación - Restaurante</h1>
            <p class="text-zinc-500 text-sm mt-1">
                @if(auth()->user()->rol === 'admin')
                    Control global de servicios de alimentación y presupuestos del Liceo.
                @else
                    Solicitudes asignadas a tu cargo para revisión de presupuesto.
                @endif
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 shadow-sm rounded-xl font-bold">Aprobación registrada con éxito.</div>
    @endif

    <div class="bg-white p-3 rounded-xl shadow-sm border border-zinc-200 mb-6 flex flex-wrap gap-3 items-center">
        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 pl-2">Filtros:</span>
        <form action="{{ route('admin.restaurante.index') }}" method="GET" class="flex flex-wrap gap-2 w-full sm:w-auto">
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#FFDE00] w-36">
            
            <select name="estado" class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#FFDE00]">
                <option value="">Todos los Estados</option>
                <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>⏳ Pendientes</option>
                <option value="Aprobado" {{ request('estado') == 'Aprobado' ? 'selected' : '' }}>✅ Aprobados</option>
                <option value="Rechazado" {{ request('estado') == 'Rechazado' ? 'selected' : '' }}>❌ Rechazados</option>
            </select>
            
            <input type="text" name="solicitante" value="{{ request('solicitante') }}" placeholder="Correo Docente..." class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#FFDE00] w-48">
            
            <button type="submit" class="bg-zinc-800 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-zinc-700 transition-colors">Buscar</button>
            @if(request()->has('fecha') || request()->has('estado') || request()->has('solicitante'))
                <a href="{{ route('admin.restaurante.index') }}" class="text-zinc-400 hover:text-red-500 text-xs font-bold px-2 py-1.5 transition-colors">Limpiar</a>
            @endif
            <button type="submit" formaction="{{ route('admin.restaurante.export') }}" class="bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-green-700 transition-colors flex items-center gap-1 shadow-sm">
                <span>📊</span> Excel
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                <thead class="bg-zinc-800 text-zinc-100 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="p-4 w-1/4">Información del Evento</th>
                        <th class="p-4 w-1/4">Ubicación y Logística</th>
                        <th class="p-4 w-1/4">Dietas y Menú Solicitado</th>
                        <th class="p-4 w-1/4 min-w-[280px] text-center">Decisión Gerencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($restaurantes as $rest)
                        @php
                            $estado = $rest->estado_restaurante;
                            $rowColor = $estado == 'Aprobado' || $estado == 'Finalizado' ? 'bg-green-50/40' : ($estado == 'Rechazado' ? 'bg-red-50/40' : 'bg-white');
                            $badgeColor = $estado == 'Aprobado' || $estado == 'Finalizado' ? 'bg-green-200 text-green-800' : ($estado == 'Rechazado' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800');
                            
                            // Extraemos el primer día de reserva si existe un salón amarrado
                            $primeraReserva = $rest->solicitud?->reservasFisicas?->first();
                        @endphp

                        <tr class="{{ $rowColor }} hover:bg-zinc-50 transition-colors group">
                            
                            <td class="p-4 align-top">
                                <span class="px-2 py-1 rounded text-[10px] font-black tracking-widest {{ $badgeColor }} mb-2 inline-block shadow-sm">
                                    {{ $estado }}
                                </span>
                                <h3 class="font-black text-zinc-800 text-base leading-tight">{{ $rest->solicitud?->titulo_evento ?? 'Sin Título' }}</h3>
                                <p class="text-xs text-yellow-600 font-bold mt-1">{{ $rest->solicitud?->correo_solicitante }}</p>
                                <div class="mt-2 bg-zinc-100 border border-zinc-200 px-2 py-0.5 rounded inline-block">
                                    <span class="text-[9px] font-black text-zinc-500 uppercase">Asignado a:</span>
                                    <span class="text-[9px] font-black text-zinc-700 ml-1">{{ $rest->aprobador_id ?? 'No especificado' }}</span>
                                </div>
                            </td>

                            <td class="p-4 align-top">
                                <div class="bg-white/60 p-2 rounded-lg border border-zinc-200/50">
                                    <p class="text-xs font-bold text-zinc-700">📍 {{ $primeraReserva?->espacio?->nombre ?? 'Entrega en área del solicitante' }}</p>
                                    @if($primeraReserva?->espacio?->torre)
                                        <p class="text-[10px] font-bold text-zinc-400 mt-0.5">{{ $primeraReserva->espacio->torre->nombre }}</p>
                                    @endif
                                </div>
                                <p class="text-xs text-zinc-600 mt-2"><b>Fecha/Hora Entrega:</b></p>
                                <p class="text-xs font-bold text-zinc-800">{{ \Carbon\Carbon::parse($rest->fecha_hora_evento)->format('d/m/Y h:i A') }}</p>
                            </td>

                            <td class="p-4 align-top text-xs text-zinc-600">
                                <span class="bg-orange-100 text-orange-800 px-2 py-0.5 rounded text-[10px] font-black border border-orange-200 block w-fit mb-2">
                                    👥 {{ $rest->num_asistentes }} PAX
                                </span>
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @php $servicios = is_string($rest->servicio_requerido) ? json_decode($rest->servicio_requerido, true) : $rest->servicio_requerido; @endphp
                                    @if(is_array($servicios))
                                        @foreach($servicios as $srv) 
                                            <span class="bg-white border border-zinc-300 px-2 py-0.5 rounded text-[10px] font-bold text-zinc-600 shadow-sm">{{ $srv }}</span> 
                                        @endforeach
                                    @else
                                        <span class="text-zinc-400 italic">No especificados</span>
                                    @endif
                                </div>
                                @if($rest->detalles_solicitud)
                                    <div class="bg-red-50/50 border border-red-100 text-red-900 p-2 rounded-lg mt-1 text-[11px] font-medium leading-tight">
                                        <b>⚠️ Novedades/Dietas:</b> "{{ $rest->detalles_solicitud }}"
                                    </div>
                                @endif
                            </td>

                            <td class="p-4 align-top bg-zinc-50/50 min-w-[280px]">
                                @if($estado == 'Pendiente')
                                    <form action="{{ route('admin.restaurante.update', $rest->id) }}" method="POST" class="flex flex-col gap-2" onsubmit="return confirm('¿Confirmar acción presupuestal? Esta decisión bloqueará el registro.');">
                                        @csrf 
                                        @method('PATCH')
                                        
                                        <select name="estado_restaurante" class="w-full bg-white border border-zinc-300 rounded-md px-2 py-1.5 text-xs font-bold text-zinc-700 focus:ring-1 focus:ring-[#FFDE00]" required>
                                            <option value="" disabled selected>Tomar decisión...</option>
                                            <option value="Aprobado">✅ Aprobar presupuesto (Enviar a Cocina)</option>
                                            <option value="Rechazado">❌ Rechazar servicio</option>
                                        </select>

                                        <textarea name="respuesta_cocina" rows="3" placeholder="Instrucciones para la producción o motivo de rechazo..." class="w-full bg-white border border-zinc-300 rounded-md px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-[#FFDE00] resize-none" required></textarea>
                                        
                                        <button type="submit" class="w-full bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest py-2 rounded-md hover:bg-yellow-500 hover:text-zinc-900 transition-colors shadow-sm mt-1">
                                            Guardar y Sellar Registro
                                        </button>
                                    </form>
                                @else
                                    <div class="bg-white border {{ $estado == 'Aprobado' || $estado == 'Finalizado' ? 'border-green-200' : 'border-red-200' }} rounded-lg p-3 text-center shadow-sm opacity-80 cursor-not-allowed">
                                        <span class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">Registro Sellado</span>
                                        <span class="inline-block px-3 py-1 rounded text-xs font-bold {{ $estado == 'Aprobado' || $estado == 'Finalizado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $estado }}
                                        </span>
                                        <p class="text-xs text-zinc-700 mt-2 italic leading-relaxed text-left border-l-2 {{ $estado == 'Aprobado' || $estado == 'Finalizado' ? 'border-green-300' : 'border-red-300' }} pl-2">
                                            "{{ $rest->respuesta_cocina }}"
                                        </p>
                                    </div>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-zinc-500 font-bold">No hay pedidos asignados pendientes de evaluar para tu rango.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($restaurantes->hasPages())
            <div class="p-4 border-t border-zinc-200 bg-zinc-50">
                {{ $restaurantes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection