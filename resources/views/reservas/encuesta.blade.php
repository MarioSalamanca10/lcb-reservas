@extends('layouts.app')
@section('title', 'Evaluar Servicio')

@section('content')
@php
    $modulo = request('modulo', 'Espacios');
    $icon = $modulo == 'Transporte' ? '🚌' : ($modulo == 'Restaurante' ? '🍽️' : '🏢');
@endphp

<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-gray-100">
        
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-4 shadow-sm border border-blue-100">
                {{ $icon }}
            </div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Evaluar {{ $modulo }}</h1>
            <p class="text-gray-500 text-sm mt-2">Califica tu experiencia. Tu opinión nos ayuda a mejorar los servicios del Liceo.</p>
        </div>

        <form action="{{ route('reservas.encuesta.store', $solicitud->id) }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="modulo_evaluado" value="{{ $modulo }}">

            @if($modulo == 'Espacios')
                <input type="hidden" name="calificacion_general" value="5"> <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 border border-gray-200 p-5 rounded-2xl text-center shadow-sm">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Limpieza</label>
                        <div class="rating-group flex flex-row-reverse justify-center">
                            <input type="radio" id="l5" name="respuestas_detalladas[limpieza]" value="5" class="peer hidden" required><label for="l5" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="l4" name="respuestas_detalladas[limpieza]" value="4" class="peer hidden"><label for="l4" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="l3" name="respuestas_detalladas[limpieza]" value="3" class="peer hidden"><label for="l3" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="l2" name="respuestas_detalladas[limpieza]" value="2" class="peer hidden"><label for="l2" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="l1" name="respuestas_detalladas[limpieza]" value="1" class="peer hidden"><label for="l1" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                        </div>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 p-5 rounded-2xl text-center shadow-sm">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Equipos (TV/Video)</label>
                        <div class="rating-group flex flex-row-reverse justify-center">
                            <input type="radio" id="e5" name="respuestas_detalladas[equipos]" value="5" class="peer hidden" required><label for="e5" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="e4" name="respuestas_detalladas[equipos]" value="4" class="peer hidden"><label for="e4" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="e3" name="respuestas_detalladas[equipos]" value="3" class="peer hidden"><label for="e3" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="e2" name="respuestas_detalladas[equipos]" value="2" class="peer hidden"><label for="e2" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="e1" name="respuestas_detalladas[equipos]" value="1" class="peer hidden"><label for="e1" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                        </div>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 p-5 rounded-2xl text-center shadow-sm">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Puntualidad</label>
                        <div class="rating-group flex flex-row-reverse justify-center">
                            <input type="radio" id="p5" name="respuestas_detalladas[puntualidad]" value="5" class="peer hidden" required><label for="p5" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="p4" name="respuestas_detalladas[puntualidad]" value="4" class="peer hidden"><label for="p4" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="p3" name="respuestas_detalladas[puntualidad]" value="3" class="peer hidden"><label for="p3" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="p2" name="respuestas_detalladas[puntualidad]" value="2" class="peer hidden"><label for="p2" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                            <input type="radio" id="p1" name="respuestas_detalladas[puntualidad]" value="1" class="peer hidden"><label for="p1" class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 text-center shadow-sm">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Calificación General del Servicio *</label>
                    <div class="rating-group flex justify-center gap-2 flex-row-reverse">
                        <input type="radio" id="star5" name="calificacion_general" value="5" class="peer hidden" required><label for="star5" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                        <input type="radio" id="star4" name="calificacion_general" value="4" class="peer hidden"><label for="star4" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                        <input type="radio" id="star3" name="calificacion_general" value="3" class="peer hidden"><label for="star3" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                        <input type="radio" id="star2" name="calificacion_general" value="2" class="peer hidden"><label for="star2" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                        <input type="radio" id="star1" name="calificacion_general" value="1" class="peer hidden"><label for="star1" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observaciones / Comentarios Adicionales</label>
                <textarea name="observaciones" rows="4" placeholder="¿Qué tal estuvo el servicio?..." class="w-full bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-xl p-4 text-gray-600 font-medium transition-all shadow-inner resize-none"></textarea>
            </div>

            <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-black text-sm uppercase tracking-widest py-4 rounded-xl transition-all shadow-lg hover:-translate-y-1 active:scale-95">
                Enviar Evaluación
            </button>
        </form>
    </div>
</div>

<style>
    /* CSS para iluminar las estrellas de izquierda a derecha sin JavaScript */
    .rating-group input:checked ~ label { color: #fbbf24; }
    .rating-group label:hover,
    .rating-group label:hover ~ label { color: #fbbf24; }
</style>
@endsection