@extends('layouts.app')

@section('title', 'Gestión de Torres')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900">Gestión de Bloques y Torres</h1>
        <p class="text-gray-500 mt-2">Administra los edificios o ubicaciones generales del Liceo.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-1 bg-white p-6 rounded-2xl shadow-lg border border-gray-100 h-fit">
            <h2 class="font-bold text-gray-800 mb-4">Nueva Ubicación</h2>
            <form action="{{ route('torres.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nombre del Bloque/Torre</label>
                    <input type="text" name="nombre" required placeholder="Ej. Bloque Preescolar" class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Descripción (Opcional)</label>
                    <textarea name="descripcion" rows="2" class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-md">
                    Guardar Torre
                </button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase">Nombre</th>
                        <th class="p-4 text-xs font-bold text-gray-400 uppercase text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($torres as $torre)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <p class="font-bold text-gray-800">{{ $torre->nombre }}</p>
                            <p class="text-xs text-gray-400">{{ $torre->descripcion }}</p>
                        </td>
                        <td class="p-4 text-right">
                            <form action="{{ route('torres.destroy', $torre->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta torre? Los espacios que la tengan asignada quedarán huérfanos.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-1 rounded-lg">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="p-8 text-center text-gray-400 italic">No hay torres registradas. Agrega la primera en el formulario.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection