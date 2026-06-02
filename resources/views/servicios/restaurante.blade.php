@extends('layouts.app')
@section('title', 'Solicitar Restaurante')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 text-left">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-[#FFDE00]/20 text-yellow-600 mb-4">
            <span class="text-2xl">🍽️</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-[#626366] tracking-tight">Solicitar Restaurante</h1>
        <p class="text-gray-500 text-base sm:text-lg mt-2">Gestione la alimentación para reuniones, eventos o visitas en el Liceo.</p>
    </div>

    @if(session('success'))
        <div class="bg-[#4EAA68]/20 border-l-4 border-[#4EAA68] text-[#4EAA68] p-4 mb-6 shadow-sm rounded-2xl font-bold">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm rounded-2xl font-bold">
            ❌ {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100">
        
        <!-- Alerta de Proceso -->
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-2xl mb-8 flex items-start gap-4 shadow-inner">
            <div class="bg-white p-2 rounded-full shadow-sm"><span class="text-xl">⚠️</span></div>
            <div>
                <h4 class="font-black text-yellow-800 text-sm uppercase tracking-widest mb-1">Proceso de Autorización</h4>
                <p class="text-sm text-yellow-700 font-medium leading-relaxed">Toda solicitud de alimentación ingresa en estado <b class="font-black">Pendiente</b>. Será revisada por Gerencia Operativa, quien autorizará a Cocina su preparación. Por favor, realice su pedido con el tiempo de antelación estipulado por el Liceo.</p>
            </div>
        </div>

        <form action="{{ route('servicios.restaurante.store') }}" method="POST" class="space-y-8">
            @csrf 
            
            <!-- Título General del Evento -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Asunto o Motivo de la Solicitud</label>
                <input type="text" name="titulo" required value="{{ old('titulo') }}" placeholder="Ej: Refrigerio Junta de Profesores Ciencias" class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#FFDE00] rounded-2xl p-4 text-[#626366] font-medium transition-all">
            </div>

            <!-- Datos del Servicio -->
            <div class="bg-slate-50 border border-slate-200 p-6 rounded-3xl">
                <h3 class="text-lg font-bold text-[#626366] mb-4 flex items-center gap-2">
                    <span class="text-yellow-500">📅</span> Detalles del Servicio
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Entrega</label>
                        <input type="text" id="rest_fecha_hora" name="rest_fecha_hora" required value="{{ old('rest_fecha_hora') }}" placeholder="Seleccione cuándo necesita los alimentos..." class="calendario-hora w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#FFDE00] cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Número de Asistentes</label>
                        <input type="number" min="1" name="rest_asistentes" required value="{{ old('rest_asistentes') }}" placeholder="Ej: 15" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#FFDE00]">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Servicios Requeridos (Puede marcar varios)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @php $servicios_rest = ['Desayuno', 'Onces', 'Pasabocas', 'Brunch', 'Almuerzo', 'Cena', 'Estación de café', 'Hidratación']; @endphp
                        @foreach($servicios_rest as $srv)
                        <label class="flex items-center space-x-3 cursor-pointer group bg-white border border-gray-100 p-3 rounded-xl hover:border-[#FFDE00] transition-colors shadow-sm">
                            <input type="checkbox" name="rest_servicios[]" value="{{ $srv }}" class="h-5 w-5 text-yellow-500 border-gray-300 rounded focus:ring-[#FFDE00] transition-all">
                            <span class="text-sm font-semibold text-gray-600 group-hover:text-yellow-700 transition">{{ $srv }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dietas Especiales y Detalles Logísticos</label>
                    <textarea name="rest_detalles" rows="3" placeholder="Ej: 2 almuerzos vegetarianos, entregar en la sala de juntas, incluir servilletas extra..." class="w-full bg-white border border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-[#FFDE00] transition-all">{{ old('rest_detalles') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="w-full sm:w-auto bg-[#FFDE00] hover:bg-yellow-400 text-[#626366] font-black py-4 px-10 rounded-2xl transition-all shadow-xl hover:shadow-yellow-300/40 active:scale-95">
                    Enviar Solicitud a Gerencia
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#rest_fecha_hora", { 
            locale: "es", 
            enableTime: true, 
            dateFormat: "Y-m-d H:i", 
            minDate: "today", 
            disable: [ function(date) { return (date.getDay() === 0); } ] 
        });
    });
</script>
@endsection