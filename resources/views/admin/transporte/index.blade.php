@extends('layouts.app')
@section('title', 'Auditoría Transporte')

@section('content')
<div class="w-full">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-black text-zinc-800">Panel de Transporte</h1>
            <p class="text-zinc-500 text-sm mt-1">Gestión masiva y asignación de vehículos.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 shadow-sm rounded-xl font-bold">✅ {{ session('success') }}</div>
    @endif

    <!-- BARRA DE FILTROS COMPACTA -->
    <div class="bg-white p-3 rounded-xl shadow-sm border border-zinc-200 mb-6 flex flex-wrap gap-3 items-center">
        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 pl-2">Filtros:</span>
        <form action="{{ route('admin.transporte.index') }}" method="GET" class="flex flex-wrap gap-2 w-full sm:w-auto">
            <input type="text" name="fecha" value="{{ request('fecha') }}" placeholder="Fecha..." class="calendario-lcb bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#4EAA68] w-32">
            
            <select name="estado" class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#4EAA68]">
                <option value="">Todos los Estados</option>
                <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>⏳ Pendientes</option>
                <option value="Aprobado" {{ request('estado') == 'Aprobado' ? 'selected' : '' }}>✅ Aprobados</option>
                <option value="Rechazado" {{ request('estado') == 'Rechazado' ? 'selected' : '' }}>❌ Rechazados</option>
            </select>
            
            <input type="text" name="solicitante" value="{{ request('solicitante') }}" placeholder="Correo Docente..." class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#4EAA68] w-48">
            
            <button type="submit" class="bg-zinc-800 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-zinc-700 transition-colors">Buscar</button>
            @if(request()->has('fecha') || request()->has('estado') || request()->has('solicitante'))
                <a href="{{ route('admin.transporte.index') }}" class="text-zinc-400 hover:text-red-500 text-xs font-bold px-2 py-1.5 transition-colors">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- TABLA DE DATOS ERP -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                <thead class="bg-zinc-800 text-zinc-100 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="p-4">Información del Evento</th>
                        <th class="p-4">Logística y Ruta</th>
                        <th class="p-4">Requerimientos</th>
                        <th class="p-4 w-1/4">Gestión de Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($transportes as $trans)
                        @php
                            $estado = $trans->estado_transporte;
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
                                <h3 class="font-black text-zinc-800 text-base leading-tight">{{ $trans->solicitud->titulo_evento ?? 'Sin título' }}</h3>
                                <p class="text-xs text-[#4EAA68] font-bold mt-1">{{ $trans->solicitud->correo_solicitante }}</p>
                                <p class="text-xs text-zinc-500 mt-1"><b>Responsable:</b> {{ $trans->nombre_responsable }} ({{ $trans->celular_responsable }})</p>
                            </td>

                            <!-- COLUMNA 2: LOGÍSTICA -->
                            <td class="p-4 align-top">
                                <div class="bg-white/60 p-2 rounded-lg border border-zinc-200/50">
                                    <p class="text-xs font-bold text-zinc-700">📍 {{ $trans->direccion_recogida }}</p>
                                    <p class="text-xs font-bold text-zinc-700 mt-1">🚩 {{ $trans->direccion_destino }}</p>
                                </div>
                                <p class="text-xs text-zinc-600 mt-2"><b>Salida:</b> {{ \Carbon\Carbon::parse($trans->fecha_hora_servicio)->format('d/m/Y h:i A') }}</p>
                                <p class="text-xs text-zinc-600"><b>Regreso:</b> {{ \Carbon\Carbon::parse($trans->fecha_hora_regreso)->format('d/m/Y h:i A') }}</p>
                                <p class="text-[10px] font-bold text-zinc-400 uppercase mt-1">{{ $trans->num_estudiantes }} Estudiantes | {{ $trans->num_adultos }} Adultos</p>
                            </td>

                            <!-- COLUMNA 3: REQUERIMIENTOS -->
                            <td class="p-4 align-top text-xs text-zinc-600">
                                <p class="font-bold text-zinc-800">{{ is_array($trans->necesidades_servicio) ? implode(', ', $trans->necesidades_servicio) : 'Ninguna' }}</p>
                                @if($trans->observaciones)
                                    <p class="mt-2 italic border-l-2 border-zinc-300 pl-2">{{ $trans->observaciones }}</p>
                                @endif
                            </td>

                            <!-- COLUMNA 4: EDICIÓN CONTINUA -->
                            <td class="p-4 align-top bg-zinc-50/50">
                                <form action="{{ route('admin.transporte.update', $trans->id) }}" method="POST" class="flex flex-col gap-2">
                                    @csrf @method('PATCH')
                                    
                                    <select name="estado_transporte" class="w-full bg-white border border-zinc-300 rounded-md px-2 py-1.5 text-xs font-bold text-zinc-700 focus:ring-1 focus:ring-[#4EAA68]">
                                        <option value="Pendiente" {{ $estado == 'Pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                        <option value="Aprobado" {{ $estado == 'Aprobado' ? 'selected' : '' }}>✅ Aprobado</option>
                                        <option value="Rechazado" {{ $estado == 'Rechazado' ? 'selected' : '' }}>❌ Rechazado</option>
                                    </select>

                                    <textarea name="respuesta_coordinador" rows="2" placeholder="Notas/Asignación interna..." class="w-full bg-white border border-zinc-300 rounded-md px-2 py-1.5 text-xs focus:ring-1 focus:ring-[#4EAA68]">{{ $trans->respuesta_coordinador }}</textarea>
                                    
                                    <button type="submit" class="w-full bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest py-2 rounded-md hover:bg-[#4EAA68] transition-colors">
                                        Guardar
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-zinc-500 font-bold">No hay solicitudes que coincidan con los filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- LINKS DE PAGINACIÓN -->
        @if($transportes->hasPages())
            <div class="p-4 border-t border-zinc-200 bg-zinc-50">
                {{ $transportes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection