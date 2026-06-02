@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-0">
    <div class="flex items-center gap-4 mb-10 border-b border-gray-100 pb-8">
        <a href="{{ route('reservas.index') }}" class="w-10 h-10 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center hover:bg-[#4EAA68]/20 hover:text-[#4EAA68] transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-[#626366] tracking-tighter">Evaluación de Espacio</h1>
            <p class="text-gray-500 mt-1 italic">Tu feedback es vital para mejorar el entorno logístico del Liceo.</p>
        </div>
    </div>

    <div class="bg-[#626366] text-white p-6 rounded-3xl mb-10 flex flex-wrap gap-6 items-center shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="relative z-10">
            <p class="text-[10px] uppercase font-black tracking-widest text-[#FFDE00]">Reserva a evaluar</p>
            <h2 class="text-2xl font-bold tracking-tight mt-1">{{ $reserva->titulo }}</h2>
            <p class="text-sm text-gray-300 mt-1">📍 {{ $reserva->espacio->nombre }}</p>
        </div>
        <div class="relative z-10 bg-white/10 px-6 py-3 rounded-2xl border border-white/10 text-center ml-auto">
            <p class="text-sm font-bold">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d M, Y') }}</p>
            <p class="text-[10px] text-[#FFDE00] font-bold uppercase">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</p>
        </div>
    </div>

    <div class="bg-white p-6 md:p-10 rounded-3xl shadow-2xl border border-gray-100 relative overflow-hidden">
        <div class="flex items-center gap-3 mb-10">
            <span class="text-3xl">📋</span>
            <h3 class="text-2xl font-bold text-[#626366] tracking-tight">Responder</h3>
        </div>

        <form action="{{ route('reservas.encuesta.store', $reserva->id) }}" method="POST" class="space-y-10">
            @csrf

            <div>
                <label class="block text-gray-700 font-medium mb-4 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Califique el aseo del espacio (1:muy malo → 5:excelente)<span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 sm:gap-3 flex-wrap">
                    @foreach([1, 2, 3, 4, 5] as $val)
                    <input type="radio" name="calificacion_aseo" value="{{ $val }}" id="aseo_{{ $val }}" required class="hidden peer">
                    <label for="aseo_{{ $val }}" class="cursor-pointer border border-gray-200 p-4 rounded-xl flex items-center gap-2 group transition-all duration-300 hover:border-[#4EAA68]/50 hover:bg-[#4EAA68]/10 peer-checked:border-[#4EAA68] peer-checked:bg-[#4EAA68]/10 peer-checked:text-[#4EAA68] peer-checked:font-bold">
                        <span class="text-xl transition-transform duration-300 group-hover:scale-125 peer-checked:scale-125">⭐</span>
                        <span class="text-sm text-gray-500 peer-checked:text-[#4EAA68]">{{ $val }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-4 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    ¿Su reserva cumplió con todos los recursos solicitados? (1:muy malo → 5:excelente)<span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 sm:gap-3 flex-wrap">
                    @foreach([1, 2, 3, 4, 5] as $val)
                    <input type="radio" name="calificacion_recursos" value="{{ $val }}" id="recursos_{{ $val }}" required class="hidden peer">
                    <label for="recursos_{{ $val }}" class="cursor-pointer border border-gray-200 p-4 rounded-xl flex items-center gap-2 group transition-all duration-300 hover:border-[#4EAA68]/50 hover:bg-[#4EAA68]/10 peer-checked:border-[#4EAA68] peer-checked:bg-[#4EAA68]/10 peer-checked:text-[#4EAA68] peer-checked:font-bold">
                        <span class="text-xl transition-transform duration-300 group-hover:scale-125 peer-checked:scale-125">⭐</span>
                        <span class="text-sm text-gray-500 peer-checked:text-[#4EAA68]">{{ $val }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-4 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    ¿Su reserva cumplió los horarios solicitados? (1:muy malo → 5:excelente)<span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 sm:gap-3 flex-wrap">
                    @foreach([1, 2, 3, 4, 5] as $val)
                    <input type="radio" name="calificacion_horarios" value="{{ $val }}" id="horarios_{{ $val }}" required class="hidden peer">
                    <label for="horarios_{{ $val }}" class="cursor-pointer border border-gray-200 p-4 rounded-xl flex items-center gap-2 group transition-all duration-300 hover:border-[#4EAA68]/50 hover:bg-[#4EAA68]/10 peer-checked:border-[#4EAA68] peer-checked:bg-[#4EAA68]/10 peer-checked:text-[#4EAA68] peer-checked:font-bold">
                        <span class="text-xl transition-transform duration-300 group-hover:scale-125 peer-checked:scale-125">⭐</span>
                        <span class="text-sm text-gray-500 peer-checked:text-[#4EAA68]">{{ $val }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-4 flex items-center gap-3">Observaciones (Opcional)</label>
                <textarea name="observaciones" rows="4" placeholder="Algún detalle adicional sobre su experiencia..." class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-[#4EAA68] shadow-inner"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('reservas.index') }}" class="px-6 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold hover:bg-gray-200 transition">Cancelar</a>
                <button type="submit" class="px-8 py-3 bg-[#4EAA68] hover:bg-[#3d8c55] text-white font-black rounded-xl transition shadow-xl hover:shadow-[#4EAA68]/40 active:scale-95">
                    Enviar Evaluación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection