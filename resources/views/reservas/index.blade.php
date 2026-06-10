@extends('layouts.app')
@section('title', 'Mis Solicitudes')

@section('content')
<div class="w-full">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-black text-zinc-800 tracking-tight">Mis Solicitudes</h1>
            <p class="text-zinc-500 text-sm mt-1">Historial y estado de tus reservas, rutas y alimentación.</p>
        </div>
        
        <a href="{{ route('reservas.create') }}" class="bg-[#4EAA68] hover:bg-green-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-green-500/30 transition-transform active:scale-95 flex items-center gap-2">
            <span>+</span> Nueva Solicitud
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 rounded-xl font-bold flex items-center gap-3 text-sm">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 mb-6 rounded-xl font-bold flex items-center gap-3 text-sm">⚠️ {{ session('error') }}</div>
    @endif

    <div class="bg-white p-3 rounded-xl shadow-sm border border-zinc-200 mb-6 flex flex-wrap gap-3 items-center">
        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 pl-2">Filtros:</span>
        <form action="{{ route('reservas.index') }}" method="GET" class="flex flex-wrap gap-2 w-full sm:w-auto">
            <select name="categoria" class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#4EAA68]">
                <option value="">Todas las Categorías</option>
                <option value="espacios" {{ request('categoria') == 'espacios' ? 'selected' : '' }}>🏢 Espacios</option>
                <option value="transporte" {{ request('categoria') == 'transporte' ? 'selected' : '' }}>🚌 Transporte</option>
                <option value="restaurante" {{ request('categoria') == 'restaurante' ? 'selected' : '' }}>🍽️ Restaurante</option>
            </select>
            
            <select name="estado" class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-bold text-zinc-600 focus:ring-1 focus:ring-[#4EAA68]">
                <option value="">Todos los Estados</option>
                <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>⏳ Pendientes</option>
                <option value="Aprobado" {{ request('estado') == 'Aprobado' ? 'selected' : '' }}>✅ Aprobados</option>
                <option value="Rechazado" {{ request('estado') == 'Rechazado' ? 'selected' : '' }}>❌ Rechazados</option>
            </select>
            
            <button type="submit" class="bg-zinc-800 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-zinc-700 transition-colors">Filtrar</button>
            @if(request()->has('categoria') || request()->has('estado'))
                <a href="{{ route('reservas.index') }}" class="text-zinc-400 hover:text-red-500 text-xs font-bold px-2 py-1.5 transition-colors">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                <thead class="bg-zinc-800 text-zinc-100 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="p-4 w-1/3">Asunto y Fecha de Creación</th>
                        <th class="p-4 w-1/2">Servicios Solicitados y Estado</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($solicitudes as $solicitud)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            
                            <td class="p-4 align-top">
                                <h3 class="font-black text-zinc-800 text-base leading-tight">{{ $solicitud->titulo_evento }}</h3>
                                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-2">
                                    Enviado: {{ $solicitud->created_at->format('d/m/Y h:i A') }}
                                </p>
                            </td>

                            <td class="p-4 align-top">
                                <div class="flex flex-col gap-2">
                                    
                                    @if($solicitud->reservaFisica)
                                        <div class="flex items-start sm:items-center flex-col sm:flex-row gap-2 bg-indigo-50/50 p-3 rounded-lg border border-indigo-100">
                                            <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-[10px] font-black uppercase whitespace-nowrap shadow-sm">🏢 Confirmado</span>
                                            <div class="text-xs text-indigo-900 leading-tight">
                                                <b>{{ $solicitud->reservaFisica->espacio->nombre ?? 'N/A' }}</b> 
                                                ({{ \Carbon\Carbon::parse($solicitud->reservaFisica->fecha_inicio)->format('d/m/y H:i') }})
                                            </div>
                                        </div>
                                    @endif

                                    @if($solicitud->transporte)
                                        @php 
                                            $estT = $solicitud->transporte->estado_transporte;
                                            $colT = $estT == 'Pendiente' ? 'yellow' : (in_array($estT, ['Agendado', 'Aprobado']) ? 'green' : 'red');
                                        @endphp
                                        <div class="flex flex-col gap-2 bg-{{ $colT }}-50/50 p-3 rounded-lg border border-{{ $colT }}-200">
                                            <div class="flex items-start sm:items-center flex-col sm:flex-row gap-2">
                                                <span class="bg-{{ $colT }}-100 text-{{ $colT }}-700 px-2 py-1 rounded text-[10px] font-black uppercase whitespace-nowrap shadow-sm">🚌 {{ $estT }}</span>
                                                <div class="text-xs text-{{ $colT }}-900 leading-tight">
                                                    <b>Destino:</b> {{ $solicitud->transporte->direccion_destino }} 
                                                    ({{ \Carbon\Carbon::parse($solicitud->transporte->fecha_hora_servicio)->format('d/m/y H:i') }})
                                                </div>
                                            </div>
                                            @if($solicitud->transporte->respuesta_coordinador)
                                                <div class="bg-white p-2 rounded-md text-[11px] text-{{ $colT }}-800 border border-{{ $colT }}-100 italic shadow-sm mt-1">
                                                    <b>Logística responde:</b> {{ $solicitud->transporte->respuesta_coordinador }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($solicitud->restaurante)
                                        @php 
                                            $estR = $solicitud->restaurante->estado_restaurante;
                                            // Si está finalizado por cocina, lo pintamos de un gris azulado bonito.
                                            $colR = $estR == 'Pendiente' ? 'yellow' : ($estR == 'Aprobado' ? 'green' : ($estR == 'Finalizado' ? 'blue' : 'red'));
                                        @endphp
                                        <div class="flex flex-col gap-2 bg-{{ $colR }}-50/50 p-3 rounded-lg border border-{{ $colR }}-200">
                                            <div class="flex items-start sm:items-center flex-col sm:flex-row gap-2">
                                                <span class="bg-{{ $colR }}-100 text-{{ $colR }}-700 px-2 py-1 rounded text-[10px] font-black uppercase whitespace-nowrap shadow-sm">🍽️ {{ $estR }}</span>
                                                <div class="text-xs text-{{ $colR }}-900 leading-tight">
                                                    <b>Alimentación:</b> {{ $solicitud->restaurante->num_asistentes }} Personas
                                                    ({{ \Carbon\Carbon::parse($solicitud->restaurante->fecha_hora_evento)->format('d/m/y H:i') }})
                                                </div>
                                            </div>
                                            
                                            @if($solicitud->restaurante->respuesta_cocina)
                                                @php
                                                    $notaCompleta = $solicitud->restaurante->respuesta_cocina;
                                                    $notaChef = null;
                                                    
                                                    // Si el string contiene la marca del Chef, cortamos el mensaje y guardamos solo esa parte.
                                                    if(str_contains($notaCompleta, '[Nota Chef]: ')) {
                                                        $partes = explode('[Nota Chef]: ', $notaCompleta);
                                                        $notaChef = trim($partes[1]);
                                                    }
                                                @endphp
                                                
                                                @if($notaChef)
                                                    <div class="bg-white p-2 rounded-md text-[11px] text-blue-800 border border-blue-200 italic shadow-sm mt-1">
                                                        <b>👨‍🍳 Chef responde:</b> {{ $notaChef }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </td>

                            <td class="p-4 align-top text-right">
                                <div class="flex flex-col items-end gap-2">
                                    <form action="{{ route('reservas.destroy', $solicitud->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar TODA esta solicitud?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-white border border-red-200 text-red-500 hover:bg-red-50 hover:text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center justify-end gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Cancelar
                                        </button>
                                    </form>
                                    
                                    @if($solicitud->reservaFisica && !$solicitud->reservaFisica->encuesta_completada && \Carbon\Carbon::parse($solicitud->reservaFisica->fecha_fin)->isPast())
                                        <a href="{{ route('reservas.encuesta.create', $solicitud->reservaFisica->id) }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-colors shadow-sm">
                                            ⭐ Evaluar
                                        </a>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-4xl grayscale opacity-30 mb-3">📭</span>
                                    <p class="text-zinc-500 font-bold text-sm">Aún no tienes solicitudes registradas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($solicitudes->hasPages())
            <div class="p-4 border-t border-zinc-200 bg-zinc-50">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection