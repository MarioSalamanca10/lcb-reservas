@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Gestión de Reservas</h1>
            <p class="text-gray-500 mt-2 italic">Control total de la operación logística del Liceo.</p>
        </div>
        <div class="bg-blue-50 px-6 py-3 rounded-2xl border border-blue-100">
            <span class="text-blue-600 font-bold text-sm">Total: {{ $reservas->total() }} registros</span>
        </div>
    </div>

    <form action="{{ route('admin.reservas.index') }}" method="GET" class="bg-white/70 backdrop-blur-md p-6 rounded-3xl shadow-2xl border border-white/50 mb-10 flex flex-wrap gap-6 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Filtrar por Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="w-full bg-gray-50 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Bloque / Torre</label>
            <select name="torre_id" class="w-full bg-gray-50 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las Torres</option>
                @foreach($torres as $torre)
                    <option value="{{ $torre->id }}" {{ request('torre_id') == $torre->id ? 'selected' : '' }}>{{ $torre->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Docente (Email)</label>
            <input type="text" name="docente" value="{{ request('docente') }}" placeholder="buscar@lcb.edu.co" class="w-full bg-gray-50 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 hover:scale-105 transition-all">Filtrar</button>
            <a href="{{ route('admin.reservas.index') }}" class="bg-gray-100 text-gray-500 px-6 py-3 rounded-xl font-bold hover:bg-gray-200 transition-all">Limpiar</a>
        </div>
    </form>

    <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-tighter">Información del Evento</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-tighter">Ubicación Exacta</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-tighter text-center">Horario</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-tighter">Recursos</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-tighter text-right">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reservas as $reserva)
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <td class="p-6">
                        <p class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $reserva->titulo }}</p>
                        <p class="text-xs text-gray-400 font-medium">{{ $reserva->correo_docente }}</p>
                    </td>
                    <td class="p-6">
                        <span class="text-sm font-bold text-gray-700 block">{{ $reserva->espacio->nombre }}</span>
                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-md uppercase font-black">
                            {{ $reserva->espacio->torre->nombre ?? 'Sin Torre' }}
                        </span>
                    </td>
                    <td class="p-6 text-center">
                        <p class="text-sm font-black text-gray-800">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}</p>
                        <p class="text-[11px] text-blue-500 font-bold uppercase">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</p>
                    </td>
                    <td class="p-6">
                        <div class="flex flex-wrap gap-1">
                            @if($reserva->recursos_adicionales)
                                @foreach($reserva->recursos_adicionales as $item)
                                    <span class="text-[9px] bg-blue-100 text-blue-600 px-2 py-1 rounded-lg font-bold">{{ $item }}</span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-300 italic">Ninguno</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-6 text-right">
                        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                             <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" onsubmit="return confirm('¿Cancelar esta reserva?')">
                                @csrf @method('DELETE')
                                <button class="bg-red-50 text-red-500 p-2 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                             </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-20 text-center">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" class="w-16 h-16 mx-auto opacity-20 mb-4">
                        <p class="text-gray-400 font-medium">No se encontraron reservas para esta búsqueda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $reservas->appends(request()->query())->links() }}
    </div>
</div>
@endsection