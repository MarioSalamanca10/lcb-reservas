@extends('layouts.app')
@section('title', 'Auditoría Restaurante')

@section('content')
<div class="w-full">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-black text-zinc-800">Panel de Restaurante</h1>
            <p class="text-zinc-500 text-sm mt-1">Aprobación masiva y control de servicios de alimentación.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 shadow-sm rounded-xl font-bold">✅ {{ session('success') }}</div>
    @endif

    <!-- BARRA DE FILTROS COMPACTA -->
    <div class="bg-white p-3 rounded-xl shadow-sm border border-zinc-200 mb-6 flex flex-wrap gap-3 items-center">
        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 pl-2">Filtros:</span>
        <form action="{{ route('admin.restaurante.index') }}" method="GET" class="flex flex-wrap gap-2 w-full sm:w-auto">
            <input type="text" name="fecha" value="{{ request('fecha') }}" placeholder="Fecha..." class="calendario-lcb bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#FFDE00] w-32">
            
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

    <!-- TABLA DE DATOS ERP -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                <thead class="bg-zinc-800 text-zinc-100 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="p-4">Información del Evento</th>
                        <th class="p-4">Detalles del Servicio</th>
                        <th class="p-4">Dietas y Requerimientos</th>
                        <th class="p-4 w-1/4">Decisión Gerencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($restaurantes as $rest)
                        @php
                            $estado = $rest->estado_restaurante;
                            // COHERENCIA DE COLOR EN TODA LA FILA
                            $rowColor = $estado == 'Aprobado' ? 'bg-green-50/50' : ($estado == 'Rechazado' ? 'bg-red-50/50' : 'bg-white');
                            $badgeColor = $estado == 'Aprobado' ? 'bg-green-200 text-green-800' : ($estado == 'Rechazado' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800');
                        @endphp

                        <tr class="{{ $rowColor }} hover:bg-zinc-50 transition-colors group">
                            
                            <!-- COLUMNA 1: INFO -->
                            <td class="p-4 align-top">
                                <span class="px-2 py-1 rounded text-[10px] font-black tracking-widest {{ $badgeColor }} mb-2 inline-block">
                                    {{ $estado }}
                                </span>
                                <h3 class="font-black text-zinc-800 text-base leading-tight">{{ $rest->solicitud->titulo_evento ?? 'Sin título' }}</h3>
                                <p class="text-xs text-yellow-600 font-bold mt-1">{{ $rest->solicitud->correo_solicitante }}</p>
                                <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                                    <b>Asistentes:</b> <span class="bg-zinc-200 text-zinc-700 px-1.5 py-0.5 rounded text-[10px] font-black">{{ $rest->num_asistentes }}</span>
                                </p>
                            </td>

                            <!-- COLUMNA 2: DETALLES -->
                            <td class="p-4 align-top">
                                <div class="bg-white/60 p-2 rounded-lg border border-zinc-200/50">
                                    <p class="text-xs font-bold text-zinc-700">📍 {{ $rest->solicitud->reservaFisica->espacio->nombre ?? 'Entrega en área del solicitante' }}</p>
                                    @if($rest->solicitud->reservaFisica)
                                        <p class="text-[10px] font-bold text-zinc-400 mt-0.5">{{ $rest->solicitud->reservaFisica->espacio->torre->nombre ?? '' }}</p>
                                    @endif
                                </div>
                                <p class="text-xs text-zinc-600 mt-2"><b>Fecha/Hora:</b> {{ \Carbon\Carbon::parse($rest->fecha_hora_evento)->format('d/m/Y h:i A') }}</p>
                            </td>

                            <!-- COLUMNA 3: DIETAS -->
                            <td class="p-4 align-top text-xs text-zinc-600">
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @if(is_array($rest->servicio_requerido))
                                        @foreach($rest->servicio_requerido as $srv) 
                                            <span class="bg-white border border-zinc-300 px-2 py-0.5 rounded text-[10px] font-bold text-zinc-600 shadow-sm">{{ $srv }}</span> 
                                        @endforeach
                                    @endif
                                </div>
                                @if($rest->detalles_solicitud)
                                    <p class="italic border-l-2 border-zinc-300 pl-2 mt-1">{{ $rest->detalles_solicitud }}</p>
                                @else
                                    <p class="text-[10px] text-zinc-400">Sin detalles adicionales.</p>
                                @endif
                            </td>

                            <!-- COLUMNA 4: DECISIÓN Y BLOQUEO (INMUTABILIDAD) -->
                            <td class="p-4 align-top bg-zinc-50/50">
                                @if($estado == 'Pendiente')
                                    <!-- Aún no se ha decidido: Mostrar Formulario -->
                                    <form action="{{ route('admin.restaurante.update', $rest->id) }}" method="POST" class="flex flex-col gap-2" onsubmit="return confirm('¿Está seguro de su decisión? Una vez guardado, este registro quedará bloqueado.');">
                                        @csrf @method('PATCH')
                                        
                                        <select name="estado_restaurante" class="w-full bg-white border border-zinc-300 rounded-md px-2 py-1.5 text-xs font-bold text-zinc-700 focus:ring-1 focus:ring-[#FFDE00]" required>
                                            <option value="" disabled selected>Tomar decisión...</option>
                                            <option value="Aprobado">✅ Aprobar (Enviar a Cocina)</option>
                                            <option value="Rechazado">❌ Rechazar</option>
                                        </select>

                                        <textarea name="respuesta_cocina" rows="2" placeholder="Justificación o instrucciones..." class="w-full bg-white border border-zinc-300 rounded-md px-2 py-1.5 text-xs focus:ring-1 focus:ring-[#FFDE00]" required></textarea>
                                        
                                        <button type="submit" class="w-full bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest py-2 rounded-md hover:bg-yellow-500 hover:text-zinc-900 transition-colors shadow-sm">
                                            Guardar y Sellar
                                        </button>
                                    </form>
                                @else
                                    <!-- Decisión tomada: UI Bloqueada de Solo Lectura -->
                                    <div class="bg-white border {{ $estado == 'Aprobado' || $estado == 'Finalizado' ? 'border-green-200' : 'border-red-200' }} rounded-lg p-3 text-center shadow-sm opacity-80 cursor-not-allowed">
                                        <span class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">Registro Sellado</span>
                                        <span class="inline-block px-3 py-1 rounded text-xs font-bold {{ $estado == 'Aprobado' || $estado == 'Finalizado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $estado }}
                                        </span>
                                        <p class="text-[10px] text-zinc-500 mt-2 italic leading-tight">{{ $rest->respuesta_cocina }}</p>
                                    </div>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-zinc-500 font-bold">No hay pedidos de restaurante que coincidan con los filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- LINKS DE PAGINACIÓN -->
        @if($restaurantes->hasPages())
            <div class="p-4 border-t border-zinc-200 bg-zinc-50">
                {{ $restaurantes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection