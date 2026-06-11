@extends('layouts.app')
@section('title', 'Evaluar Servicio')

@section('content')
@php
    // Detectamos qué módulo estamos evaluando
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

        <form action="{{ route('reservas.encuesta.store', $solicitud->id) }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="modulo_evaluado" value="{{ $modulo }}">

            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 text-center">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Calificación General *</label>
                <div class="flex justify-center gap-2 flex-row-reverse estrellas-interactivas">
                    <input type="radio" id="star5" name="calificacion_general" value="5" class="peer hidden" required />
                    <label for="star5" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>

                    <input type="radio" id="star4" name="calificacion_general" value="4" class="peer hidden" />
                    <label for="star4" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>

                    <input type="radio" id="star3" name="calificacion_general" value="3" class="peer hidden" />
                    <label for="star3" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>

                    <input type="radio" id="star2" name="calificacion_general" value="2" class="peer hidden" />
                    <label for="star2" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>

                    <input type="radio" id="star1" name="calificacion_general" value="1" class="peer hidden" />
                    <label for="star1" class="text-5xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors">★</label>
                </div>
            </div>

            @if($modulo == 'Espacios')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 text-center">Limpieza (1-5)</label>
                    <input type="number" name="respuestas_detalladas[limpieza]" min="1" max="5" required class="w-full bg-gray-50 border-none rounded-lg p-2 focus:ring-2 focus:ring-blue-500 font-black text-gray-700 text-center">
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 text-center">Equipos (1-5)</label>
                    <input type="number" name="respuestas_detalladas[equipos]" min="1" max="5" required class="w-full bg-gray-50 border-none rounded-lg p-2 focus:ring-2 focus:ring-blue-500 font-black text-gray-700 text-center">
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 text-center">Puntualidad (1-5)</label>
                    <input type="number" name="respuestas_detalladas[puntualidad]" min="1" max="5" required class="w-full bg-gray-50 border-none rounded-lg p-2 focus:ring-2 focus:ring-blue-500 font-black text-gray-700 text-center">
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
    /* Magia CSS para que al pasar el mouse sobre una estrella, se iluminen todas las anteriores */
    .estrellas-interactivas input:checked ~ label { color: #fbbf24; }
    .estrellas-interactivas label:hover,
    .estrellas-interactivas label:hover ~ label { color: #fbbf24; }
</style>
@endsection