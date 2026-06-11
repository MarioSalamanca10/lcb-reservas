@extends('layouts.app')
@section('title', 'Solicitar Alimentación')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 text-center">
        <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 shadow-sm">🍽️</div>
        <h1 class="text-3xl font-black text-[#626366] tracking-tight">Solicitar Alimentación</h1>
        <p class="text-gray-500 text-sm mt-2">Gestione refrigerios, almuerzos o servicios de café para reuniones que NO requieran reservar un salón.</p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm rounded-2xl font-bold flex items-center gap-3">
            <span class="text-2xl">⚠️</span> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.02] pointer-events-none"></div>

        <form action="{{ route('servicios.restaurante.store') }}" method="POST" class="space-y-8 relative z-10">
            @csrf 
            
            <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 shadow-sm">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Motivo del Evento / Título</label>
                <input type="text" name="titulo" required value="{{ old('titulo') }}" placeholder="Ej: Comité Directivo Semanal, Desayuno de Trabajo..." class="w-full bg-white border border-gray-200 focus:ring-2 focus:ring-[#FFDE00] rounded-xl p-4 text-[#626366] font-bold transition-all shadow-sm text-lg">
            </div>

            <div class="bg-yellow-50/40 p-6 rounded-2xl border border-[#FFDE00]/50 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Entrega</label>
                        <input type="text" id="rest_fecha_hora" name="rest_fecha_hora" required value="{{ old('rest_fecha_hora') }}" placeholder="Click para agendar..." class="w-full bg-white border border-yellow-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-[#FFDE00] shadow-sm cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Cantidad de Asistentes</label>
                        <input type="number" min="1" name="rest_asistentes" required value="{{ old('rest_asistentes') }}" placeholder="Ej: 40" class="w-full bg-white border border-yellow-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-[#FFDE00] shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">¿Quién Aprueba el Presupuesto?</label>
                        <select name="rest_aprobador_id" required class="w-full bg-white border border-yellow-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-[#FFDE00] shadow-sm text-yellow-900 cursor-pointer">
                            <option value="">Seleccione Gerencia...</option>
                            <option value="Gerencia Académica" {{ old('rest_aprobador_id') == 'Gerencia Académica' ? 'selected' : '' }}>Gerencia Académica</option>
                            <option value="Gerencia Administrativa" {{ old('rest_aprobador_id') == 'Gerencia Administrativa' ? 'selected' : '' }}>Gerencia Administrativa</option>
                            <option value="Gerencia Operativa" {{ old('rest_aprobador_id') == 'Gerencia Operativa' ? 'selected' : '' }}>Gerencia Operativa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tipo de Servicio (Múltiple)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php 
                            $servicios_rest = ['Desayuno', 'Onces', 'Pasabocas', 'Almuerzo', 'Cena', 'Estación de café']; 
                            $old_rest = old('rest_servicios', []);
                        @endphp
                        @foreach($servicios_rest as $srv)
                        <label class="flex items-center space-x-3 text-sm font-bold text-gray-700 bg-white p-3 rounded-xl border border-yellow-100 shadow-sm hover:border-yellow-400 transition cursor-pointer">
                            <input type="checkbox" name="rest_servicios[]" value="{{ $srv }}" {{ in_array($srv, $old_rest) ? 'checked' : '' }} class="rounded w-4 h-4 text-yellow-500 focus:ring-[#FFDE00]">
                            <span>{{ $srv }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dietas Especiales, Lugar de Entrega y Detalles</label>
                    <textarea name="rest_detalles" rows="3" placeholder="Ej: Entregar en la sala de profesores. 2 almuerzos vegetarianos. El refrigerio debe incluir fruta..." class="w-full bg-white border border-yellow-200 rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-[#FFDE00] shadow-sm resize-none">{{ old('rest_detalles') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="w-full sm:w-auto bg-[#FFDE00] hover:bg-[#e6c800] text-yellow-900 font-black text-base py-4 px-10 rounded-2xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                    <span>🍽️</span> Enviar Pedido a Cocina
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#rest_fecha_hora", {
            locale: "es",
            disable: [ function(date) { return (date.getDay() === 0); } ],
            enableTime: true, 
            dateFormat: "Y-m-d h:i K",
            minDate: "today"
        });
    });
</script>

@endsection