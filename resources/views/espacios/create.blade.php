@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-10 border-b border-gray-100 pb-8">
        <a href="{{ route('espacios.index') }}" class="w-10 h-10 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center hover:bg-blue-100 hover:text-blue-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Crear Nuevo Espacio</h1>
            <p class="text-gray-500 mt-1 italic">Registra un nuevo salón, auditorio o zona del colegio.</p>
        </div>
    </div>

    <div class="bg-white p-10 rounded-3xl shadow-2xl border border-gray-100">
        <form action="{{ route('espacios.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-2xl mb-8">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-red-500 text-xl">⚠️</span>
                        <h3 class="font-black text-red-800">No pudimos guardar el espacio</h3>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-600 font-medium ml-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nombre del Espacio <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" required placeholder="Ej: Laboratorio de Física" class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl p-4 text-gray-800 font-medium transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Torre o Bloque <span class="text-red-500">*</span></label>
                    <select name="torre_id" required class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl p-4 text-gray-800 font-medium appearance-none">
                        <option value="">Selecciona la ubicación...</option>
                        @foreach(\App\Models\Torre::all() as $torre)
                            <option value="{{ $torre->id }}">{{ $torre->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Capacidad Máxima <span class="text-red-500">*</span></label>
                    <input type="number" name="capacidad_personas" min="1" required placeholder="Ej: 35" class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl p-4 text-gray-800 font-medium transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Descripción (Opcional)</label>
                <textarea name="descripcion" rows="3" placeholder="Detalles sobre el lugar, reglas de uso, etc." class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-blue-500 rounded-2xl p-4 text-gray-800 font-medium transition-all"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Fotografía del Espacio</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-blue-500 hover:bg-blue-50 transition-all group relative">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-blue-500 transition" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span>Sube un archivo</span>
                                <input id="file-upload" name="imagen" type="file" accept="image/*" class="sr-only">
                            </label>
                            <p class="pl-1">o arrástralo aquí</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG o WEBP hasta 2MB</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                <a href="{{ route('espacios.index') }}" class="px-8 py-4 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-4 px-10 rounded-2xl transition-all shadow-xl hover:shadow-blue-500/40 active:scale-95">
                    Guardar Espacio
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    // Este script cambia el texto "Sube un archivo" por el nombre de la foto que elegiste
    document.getElementById('file-upload').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            let fileName = e.target.files[0].name;
            let labelText = this.parentElement.querySelector('span');
            labelText.textContent = '✅ ' + fileName;
            labelText.classList.add('text-green-600');
        }
    });
</script>
@endsection