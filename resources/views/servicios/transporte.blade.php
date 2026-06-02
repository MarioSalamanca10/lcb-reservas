@extends('layouts.app')
@section('title', 'Solicitar Transporte')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 text-left">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-[#4EAA68]/10 text-[#4EAA68] mb-4">
            <span class="text-2xl">🚌</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-[#626366] tracking-tight">Solicitar Transporte</h1>
        <p class="text-gray-500 text-base sm:text-lg mt-2">Gestione la asignación de vehículos para salidas pedagógicas o rutas especiales.</p>
    </div>

    @if(session('success'))
        <div class="bg-[#4EAA68]/20 border-l-4 border-[#4EAA68] text-[#4EAA68] p-4 mb-6 shadow-sm rounded-2xl font-bold">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100">
        <form action="{{ route('servicios.transporte.store') }}" method="POST" class="space-y-8">
            @csrf 
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Motivo de la salida o Título del evento</label>
                <input type="text" name="titulo" required value="{{ old('titulo') }}" placeholder="Ej: Salida Pedagógica Maloka - Grado 5A" class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-medium transition-all">
            </div>

            <div class="bg-slate-50 border border-slate-200 p-6 rounded-3xl">
                <h3 class="text-lg font-bold text-[#626366] mb-4 flex items-center gap-2">
                    <span class="text-[#4EAA68]">👤</span> Datos del Responsable
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nombre Completo</label>
                        <input type="text" name="trans_responsable" required value="{{ old('trans_responsable', auth()->user()->name) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Celular de Contacto</label>
                        <input type="text" name="trans_celular" required value="{{ old('trans_celular') }}" placeholder="Ej: 3001234567" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Área que Solicita</label>
                        <select name="trans_area" required class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                            <option value="">Seleccione un área...</option>
                            <option value="Rectoría">Rectoría</option>
                            <option value="Vicerrectoría">Vicerrectoría</option>
                            <option value="Dirección Académica">Dirección Académica</option>
                            <option value="Dirección Preescolar">Dirección Preescolar</option>
                            <option value="CA Ciencias Naturales">CA Ciencias Naturales</option>
                            <option value="Gerencia Operativa">Gerencia Operativa</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">N° Estudiantes</label>
                            <input type="number" min="0" name="trans_estudiantes" required value="{{ old('trans_estudiantes', 0) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">N° Adultos</label>
                            <input type="number" min="0" name="trans_adultos" required value="{{ old('trans_adultos', 0) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50/30 border border-green-100 p-6 rounded-3xl">
                <h3 class="text-lg font-bold text-[#626366] mb-4 flex items-center gap-2">
                    <span class="text-[#4EAA68]">📍</span> Logística del Viaje
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Salida</label>
                        <input type="text" id="trans_salida" name="trans_salida" required value="{{ old('trans_salida') }}" placeholder="Seleccione..." class="calendario-hora w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Regreso</label>
                        <input type="text" id="trans_regreso" name="trans_regreso" required value="{{ old('trans_regreso') }}" placeholder="Seleccione..." class="calendario-hora w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dir. Recogida</label>
                        <input type="text" name="trans_dir_recogida" required value="{{ old('trans_dir_recogida', 'Liceo de Colombia Bilingüe') }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dir. Destino</label>
                        <input type="text" name="trans_dir_destino" required value="{{ old('trans_dir_destino') }}" placeholder="Lugar a visitar" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dir. de Regreso</label>
                        <input type="text" name="trans_dir_regreso" required value="{{ old('trans_dir_regreso', 'Liceo de Colombia Bilingüe') }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Necesidades del Servicio</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <label class="flex items-center space-x-2 text-sm text-gray-600"><input type="checkbox" id="chk_solo_ida" name="trans_necesidades[]" value="Servicio solo ida" class="rounded text-[#4EAA68] focus:ring-[#4EAA68]"><span>Servicio solo ida</span></label>
                        <label class="flex items-center space-x-2 text-sm text-gray-600"><input type="checkbox" id="chk_ida_vuelta" name="trans_necesidades[]" value="Servicio ida y vuelta" class="rounded text-[#4EAA68] focus:ring-[#4EAA68]"><span>Servicio ida y vuelta</span></label>
                        <label class="flex items-center space-x-2 text-sm text-gray-600"><input type="checkbox" id="chk_con_moni" name="trans_necesidades[]" value="Con Monitora" class="rounded text-[#4EAA68] focus:ring-[#4EAA68]"><span>Con Monitora</span></label>
                        <label class="flex items-center space-x-2 text-sm text-gray-600"><input type="checkbox" id="chk_sin_moni" name="trans_necesidades[]" value="Sin Monitora" class="rounded text-[#4EAA68] focus:ring-[#4EAA68]"><span>Sin Monitora</span></label>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observaciones Adicionales</label>
                    <textarea name="trans_observaciones" rows="2" placeholder="Ej: Necesitamos buses con baúl grande para equipos de Ciencias..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">{{ old('trans_observaciones') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="w-full sm:w-auto bg-[#4EAA68] hover:bg-green-600 text-white font-black py-4 px-10 rounded-2xl transition-all shadow-xl hover:shadow-[#4EAA68]/40 active:scale-95">
                    Enviar Solicitud a Transporte
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calendarios sincronizados
        const fpRegreso = flatpickr("#trans_regreso", { 
            locale: "es", enableTime: true, dateFormat: "Y-m-d H:i", disable: [ function(date) { return (date.getDay() === 0); } ]
        });

        flatpickr("#trans_salida", { 
            locale: "es", enableTime: true, dateFormat: "Y-m-d H:i", minDate: "today", disable: [ function(date) { return (date.getDay() === 0); } ],
            onChange: function(selectedDates, dateStr, instance) {
                fpRegreso.set('minDate', dateStr);
            }
        });

        // Checkboxes excluyentes
        const chkIda = document.getElementById('chk_solo_ida');
        const chkIdaVuelta = document.getElementById('chk_ida_vuelta');
        const chkConMoni = document.getElementById('chk_con_moni');
        const chkSinMoni = document.getElementById('chk_sin_moni');

        if(chkIda && chkIdaVuelta) {
            chkIda.addEventListener('change', function() { if(this.checked) chkIdaVuelta.checked = false; });
            chkIdaVuelta.addEventListener('change', function() { if(this.checked) chkIda.checked = false; });
        }
        if(chkConMoni && chkSinMoni) {
            chkConMoni.addEventListener('change', function() { if(this.checked) chkSinMoni.checked = false; });
            chkSinMoni.addEventListener('change', function() { if(this.checked) chkConMoni.checked = false; });
        }
    });
</script>
@endsection