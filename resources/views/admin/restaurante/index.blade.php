@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 text-left">
        <h1 class="text-3xl sm:text-4xl font-black text-[#626366]">Panel de Restaurante</h1>
        <p class="text-gray-500 text-lg mt-2">Aprobación y autorización de cocina.</p>
    </div>

    @if(session('success'))
        <div class="bg-[#4EAA68]/20 border-l-4 border-[#4EAA68] text-[#4EAA68] p-4 mb-6 shadow-sm rounded-2xl font-bold">✅ {{ session('success') }}</div>
    @endif

    <div class="space-y-6">
        @forelse($restaurantes as $rest)
            <div class="bg-white rounded-3xl shadow-md border-l-8 border-[#FFDE00] overflow-hidden">
                <div class="bg-yellow-50/30 p-4 sm:p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-[#626366]">{{ $rest->solicitud->titulo_evento ?? 'Sin título' }}</h3>
                        <p class="text-sm text-gray-500 font-medium mt-1">Solicita: <span class="text-yellow-600">{{ $rest->solicitud->correo_solicitante }}</span></p>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase {{ $rest->estado_restaurante == 'Aprobado' ? 'bg-green-100 text-green-700' : ($rest->estado_restaurante == 'Rechazado' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $rest->estado_restaurante }}
                    </span>
                </div>

                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><p class="text-gray-400 font-bold uppercase text-[10px]">Fecha Servicio</p><p class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($rest->fecha_hora_evento)->format('d/m/Y H:i') }}</p></div>
                            <div><p class="text-gray-400 font-bold uppercase text-[10px]">Cantidad</p><p class="font-semibold text-gray-700">{{ $rest->num_asistentes }} Personas</p></div>
                            <div class="col-span-2"><p class="text-gray-400 font-bold uppercase text-[10px]">Ubicación</p><p class="font-semibold text-gray-700">{{ $rest->solicitud->reservaFisica->espacio->nombre ?? 'N/A' }}</p></div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-100 p-4 rounded-xl">
                            <p class="text-yellow-600 font-bold uppercase text-[10px] mb-2">Servicios Solicitados</p>
                            <div class="flex flex-wrap gap-2">
                                @if(is_array($rest->servicio_requerido))
                                    @foreach($rest->servicio_requerido as $srv) <span class="bg-white border border-yellow-200 px-3 py-1 rounded-full text-xs font-bold shadow-sm">{{ $srv }}</span> @endforeach
                                @endif
                            </div>
                            @if($rest->detalles_solicitud)
                            <div class="mt-3 pt-3 border-t border-yellow-200"><p class="text-gray-400 font-bold uppercase text-[10px]">Dietas / Detalles</p><p class="text-sm text-gray-600 italic">{{ $rest->detalles_solicitud }}</p></div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-yellow-50/30 p-5 rounded-2xl border border-yellow-100 h-fit">
                        <form action="{{ route('admin.restaurante.update', $rest->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Decisión Gerencia</label>
                            <select name="estado_restaurante" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#FFDE00] font-bold text-gray-700 mb-4">
                                <option value="Pendiente" {{ $rest->estado_restaurante == 'Pendiente' ? 'selected' : '' }}>⏳ Mantener Pendiente</option>
                                <option value="Aprobado" {{ $rest->estado_restaurante == 'Aprobado' ? 'selected' : '' }}>✅ Autorizar a Cocina</option>
                                <option value="Rechazado" {{ $rest->estado_restaurante == 'Rechazado' ? 'selected' : '' }}>❌ Rechazar</option>
                            </select>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Instrucciones</label>
                            <textarea name="respuesta_cocina" rows="2" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#FFDE00] mb-4">{{ $rest->respuesta_cocina }}</textarea>
                            <button type="submit" class="w-full bg-[#FFDE00] text-[#626366] font-black py-3 rounded-xl hover:bg-yellow-400 transition-colors">Guardar Decisión</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10"><p class="text-gray-500 font-bold mt-4">No hay pedidos de restaurante.</p></div>
        @endforelse
    </div>
</div>
@endsection