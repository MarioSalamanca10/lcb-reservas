@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Gestión de Espacios Físicos</h1>
            <p class="text-gray-500 text-lg mt-2">Administra el inventario de aulas, auditorios y salas.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('torres.index') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 font-black py-3 px-6 rounded-xl transition-all shadow-sm flex items-center gap-2">
                <span class="text-xl">🏗️</span> Gestionar Torres
            </a>

            <a href="{{ route('espacios.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-6 rounded-xl transition-all shadow-xl hover:shadow-blue-500/40 flex items-center gap-2">
                <span class="text-xl">+</span> Nuevo Espacio
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-2xl font-medium shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-gradient-to-r from-blue-900 to-slate-800 rounded-3xl p-8 mb-10 shadow-2xl relative overflow-hidden border border-blue-400/20">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-white">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-3xl">🚀</span>
                    <h2 class="text-2xl font-black tracking-tight">Carga Masiva de Espacios</h2>
                </div>
                <p class="text-blue-200 text-sm">Sube tu archivo .CSV. El sistema creará las Torres y Espacios en un segundo.</p>
            </div>

            <form action="{{ route('espacios.importar') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3 bg-white/10 p-2 rounded-2xl backdrop-blur-md border border-white/20">
                @csrf
                <input type="file" name="archivo" accept=".csv" required class="text-sm text-gray-300 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all">
                
                <button type="submit" class="bg-green-500 hover:bg-green-400 text-green-900 font-black py-3 px-6 rounded-xl transition shadow-lg shadow-green-500/30 flex items-center gap-2">
                    Importar
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-wider">Espacio / Recurso</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-wider">Ubicación (Torre/Bloque)</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-wider text-center">Aforo</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-wider text-center">Estado</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($espacios as $espacio)
                <tr class="hover:bg-gray-50/50 transition group">
                    <td class="p-6 font-bold text-gray-800">{{ $espacio->nombre }}</td>
                    <td class="p-6">
                        <span class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-semibold border border-blue-100">
                            {{ $espacio->torre->nombre ?? 'Sin Torre' }}
                        </span>
                    </td>
                    <td class="p-6 text-center font-bold text-gray-600">{{ $espacio->capacidad_personas }}</td>
                    <td class="p-6 text-center">
                        @if($espacio->activo)
                            <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-bold">Activo</span>
                        @else
                            <span class="bg-gray-100 text-gray-400 text-xs px-3 py-1 rounded-full font-bold">Inactivo</span>
                        @endif
                    </td>
                    <td class="p-6 text-right">
                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('espacios.edit', $espacio->id) }}" class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-600 hover:text-white transition" title="Editar">
                                ✏️
                            </a>
                            <form action="{{ route('espacios.destroy', $espacio->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este espacio? Esta acción no se puede deshacer.')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-500 p-2 rounded-lg hover:bg-red-500 hover:text-white transition" title="Eliminar">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-16 text-center text-gray-400 italic font-medium">
                        La base de datos está vacía. Selecciona tu archivo .csv arriba o crea un espacio manualmente.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection