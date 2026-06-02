@extends('layouts.app')

@section('title', 'Mis Reservas')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#626366]">Historial de Reservas</h1>
            <p class="text-gray-500">Gestiona tus solicitudes de espacios físicos.</p>
        </div>
        <a href="{{ route('reservas.create') }}" class="bg-[#4EAA68] text-white px-4 py-2 rounded-xl font-bold hover:bg-[#3d8c55] shadow-md transition-colors">
            + Nueva Reserva
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-[#4EAA68] text-green-800 p-4 mb-6 shadow-sm rounded-xl flex items-center font-medium">
            <svg class="w-5 h-5 mr-2 text-[#4EAA68]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-[#FFDE00]/20 border-l-4 border-[#FFDE00] text-[#626366] p-4 mb-6 shadow-sm rounded-xl flex items-center font-bold">
            <svg class="w-5 h-5 mr-2 text-[#FFDE00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('reservas.index') }}" method="GET" class="bg-white p-4 rounded-2xl shadow-sm mb-6 flex flex-wrap gap-4 items-end border border-gray-100">
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#4EAA68] p-3">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Espacio</label>
            <input type="text" name="espacio" placeholder="Ej. Ágora" value="{{ request('espacio') }}" class="bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#4EAA68] p-3">
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">
            Filtrar
        </button>
        <a href="{{ route('reservas.index') }}" class="text-blue-600 text-sm hover:underline mb-3 ml-2 font-medium">Limpiar</a>
    </form>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Evento</th>
                        <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Espacio</th>
                        <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Fecha y Hora</th>
                        <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Extras</th>
                        <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reservas as $reserva)
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="p-5 font-semibold text-gray-800">{{ $reserva->titulo }}</td>
                        <td class="p-5 text-[#4EAA68] font-bold">{{ $reserva->espacio->nombre }}</td>
                        <td class="p-5 text-center">
                            <span class="block font-bold text-gray-700">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</span>
                            <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-1 rounded-md mt-1 inline-block">
                                {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('h:i A') }}
                            </span>
                        </td>
                        <td class="p-5 text-xs">
                            @if($reserva->recursos_adicionales && is_array($reserva->recursos_adicionales))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($reserva->recursos_adicionales as $extra)
                                        <span class="bg-gray-100 border border-gray-200 px-2 py-1 rounded-md text-gray-600 font-medium">{{ $extra }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 italic">Ninguno</span>
                            @endif
                        </td>
                        <td class="p-5 text-right align-middle">
                            @php
                                $fechaHoy = now()->format('Y-m-d');
                                $horaActual = now()->format('H:i:s');
                                $yaFinalizo = false;

                                if ($reserva->fecha_fin < $fechaHoy) {
                                    $yaFinalizo = true;
                                } elseif ($reserva->fecha_fin == $fechaHoy && $reserva->hora_fin < $horaActual) {
                                    $yaFinalizo = true;
                                }
                            @endphp

                            @if($yaFinalizo)
                                @if(!$reserva->encuesta_completada)
                                    <a href="{{ route('reservas.encuesta.create', $reserva->id) }}" class="bg-[#FFDE00] hover:bg-yellow-400 text-[#626366] font-black py-2 px-4 rounded-xl shadow-md transition-all inline-block animate-bounce text-xs">
                                        📋 Evaluar Espacio
                                    </a>
                                @else
                                    <span class="text-[#4EAA68] font-bold bg-[#4EAA68]/10 px-3 py-2 rounded-xl text-xs flex items-center justify-end w-fit ml-auto border border-[#4EAA68]/20">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Evaluado
                                    </span>
                                @endif
                            @else
                                <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Seguro quieres cancelar esta reserva? El espacio quedará libre de inmediato.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg font-bold text-xs transition-colors">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-400 italic">No se encontraron reservas con esos criterios.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection