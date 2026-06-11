@extends('layouts.app')
@section('title', 'Solicitar Transporte')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8 text-center">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 shadow-sm">🚌</div>
        <h1 class="text-3xl font-black text-[#626366] tracking-tight">Solicitar Transporte</h1>
        <p class="text-gray-500 text-sm mt-2">Gestione vehículos institucionales o rutas externas para salidas pedagógicas y eventos que no requieran reserva de salones.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-5 mb-6 shadow-sm rounded-2xl">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl">⚠️</span>
                <h3 class="text-red-800 font-black text-lg">Por favor corrige los siguientes errores:</h3>
            </div>
            <ul class="list-disc list-inside text-sm text-red-700 font-bold ml-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.02] pointer-events-none"></div>

        <form action="{{ route('servicios.transporte.store') }}" method="POST" class="space-y-8 relative z-10">
            @csrf 
            
            <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="text-sm font-black text-[#626366] uppercase tracking-widest mb-5 border-b border-gray-200 pb-2">1. Información General</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Motivo de la Salida / Título *</label>
                        <input type="text" name="titulo" required value="{{ old('titulo') }}" placeholder="Ej: Salida Pedagógica Museo del Oro - Curso 8A" class="w-full bg-white border border-gray-200 focus:ring-2 focus:ring-[#4EAA68] rounded-xl p-3.5 text-[#626366] font-bold transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Responsable de la Ruta *</label>
                        <input type="text" name="trans_responsable" required value="{{ old('trans_responsable') }}" placeholder="Nombre completo" class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Celular de Contacto *</label>
                        <input type="text" name="trans_celular" required value="{{ old('trans_celular') }}" placeholder="Para emergencias" class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Área que Solicita *</label>
                        <select name="trans_area" required class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm cursor-pointer">
                            <option value="">Seleccione...</option>
                            <option value="Rectoría" {{ old('trans_area') == 'Rectoría' ? 'selected' : '' }}>Rectoría</option>
                            <option value="Vicerrectoría" {{ old('trans_area') == 'Vicerrectoría' ? 'selected' : '' }}>Vicerrectoría</option>
                            <option value="Dirección Académica" {{ old('trans_area') == 'Dirección Académica' ? 'selected' : '' }}>Dirección Académica</option>
                            <option value="Dirección Preescolar" {{ old('trans_area') == 'Dirección Preescolar' ? 'selected' : '' }}>Dirección Preescolar</option>
                            <option value="Gerencia Operativa" {{ old('trans_area') == 'Gerencia Operativa' ? 'selected' : '' }}>Gerencia Operativa</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-green-50/30 p-6 rounded-2xl border border-green-100 shadow-sm">
                <h3 class="text-sm font-black text-green-700 uppercase tracking-widest mb-5 border-b border-green-200 pb-2 flex items-center gap-2">
                    <span>📍</span> 2. Itinerario y Logística
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">Lugar de Destino *</label>
                        <input type="text" name="trans_dir_destino" required value="{{ old('trans_dir_destino') }}" placeholder="Lugar a visitar" class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-green-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">Lugar de Regreso *</label>
                        <input type="text" id="trans_dir_regreso" name="trans_dir_regreso" required value="{{ old('trans_dir_regreso', 'Liceo de Colombia Bilingüe') }}" class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-green-500 shadow-sm transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">Fecha y Hora de Salida *</label>
                        <input type="text" id="trans_salida" name="trans_salida" required value="{{ old('trans_salida') }}" placeholder="Click para agendar..." class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-green-500 shadow-sm cursor-pointer text-green-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">Fecha y Hora de Regreso</label>
                        <input type="text" id="trans_regreso" name="trans_regreso" required value="{{ old('trans_regreso') }}" placeholder="Click para agendar..." class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-green-500 shadow-sm cursor-pointer transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div class="flex gap-4">
                        <div class="w-full">
                            <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">N° Estudiantes</label>
                            <input type="number" min="0" name="trans_estudiantes" required value="{{ old('trans_estudiantes', 0) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-green-500 shadow-sm text-center">
                        </div>
                        <div class="w-full">
                            <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">N° Adultos</label>
                            <input type="number" min="0" name="trans_adultos" required value="{{ old('trans_adultos', 0) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-green-500 shadow-sm text-center">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">Configuración y Acompañamiento</label>
                        @php $t_nec = old('trans_necesidades', []); @endphp
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <label class="flex items-center space-x-2 text-xs font-bold text-gray-600 bg-white p-3 rounded-lg border border-gray-100 shadow-sm w-full cursor-pointer hover:border-green-500 transition-colors"><input type="checkbox" id="chk_solo_ida" name="trans_necesidades[]" value="Servicio solo ida" {{ in_array('Servicio solo ida', $t_nec) ? 'checked' : '' }} class="rounded text-green-600 focus:ring-green-500 w-4 h-4"><span>Solo Ida</span></label>
                            <label class="flex items-center space-x-2 text-xs font-bold text-gray-600 bg-white p-3 rounded-lg border border-gray-100 shadow-sm w-full cursor-pointer hover:border-green-500 transition-colors"><input type="checkbox" id="chk_ida_vuelta" name="trans_necesidades[]" value="Servicio ida y vuelta" {{ empty($t_nec) || in_array('Servicio ida y vuelta', $t_nec) ? 'checked' : '' }} class="rounded text-green-600 focus:ring-green-500 w-4 h-4"><span>Ida y Vuelta</span></label>
                            <label class="flex items-center space-x-2 text-xs font-bold text-gray-600 bg-white p-3 rounded-lg border border-gray-100 shadow-sm w-full cursor-pointer hover:border-green-500 transition-colors"><input type="checkbox" id="chk_con_moni" name="trans_necesidades[]" value="Con Monitora" {{ in_array('Con Monitora', $t_nec) ? 'checked' : '' }} class="rounded text-green-600 focus:ring-green-500 w-4 h-4"><span>Con Monitora</span></label>
                            <label class="flex items-center space-x-2 text-xs font-bold text-gray-600 bg-white p-3 rounded-lg border border-gray-100 shadow-sm w-full cursor-pointer hover:border-green-500 transition-colors"><input type="checkbox" id="chk_sin_moni" name="trans_necesidades[]" value="Sin Monitora" {{ in_array('Sin Monitora', $t_nec) ? 'checked' : '' }} class="rounded text-green-600 focus:ring-green-500 w-4 h-4"><span>Sin Monitora</span></label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-green-600 uppercase tracking-widest mb-2">Observaciones Especiales (Transporte)</label>
                    <textarea name="trans_observaciones" rows="3" placeholder="Ej: Necesitamos buses con baúl grande para equipos de ciencias, o el bus debe esperar en una zona específica..." class="w-full bg-white border border-gray-200 focus:ring-2 focus:ring-green-500 rounded-xl p-4 text-[#626366] font-medium transition-all shadow-sm resize-none">{{ old('trans_observaciones') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="w-full sm:w-auto bg-[#4EAA68] hover:bg-green-700 text-white font-black text-lg py-4 px-10 rounded-2xl transition-all shadow-[0_8px_30px_rgb(78,170,104,0.4)] hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                    <span>🚌</span> Solicitar Vehículo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Configuración de Fechas
        const fpConfig = {
            locale: "es", disable: [ function(date) { return (date.getDay() === 0); } ],
            enableTime: true, dateFormat: "Y-m-d h:i K", minDate: "today"
        };

        const fpRegreso = flatpickr("#trans_regreso", fpConfig);
        
        flatpickr("#trans_salida", {
            ...fpConfig,
            onChange: function(selectedDates, dateStr, instance) {
                fpRegreso.set('minDate', dateStr);
            }
        });

        // 2. Magia "Solo Ida" con remoción de Required
        const chkIda = document.getElementById('chk_solo_ida');
        const chkIdaVuelta = document.getElementById('chk_ida_vuelta');
        const inputDirRegreso = document.getElementById('trans_dir_regreso');
        const inputFechaRegreso = document.getElementById('trans_regreso');

        function toggleRegreso() {
            if(chkIda.checked) {
                chkIdaVuelta.checked = false;
                
                inputDirRegreso.disabled = true; 
                inputDirRegreso.classList.add('bg-gray-200', 'text-gray-400');
                inputDirRegreso.removeAttribute('required'); // <- CLAVE
                
                inputFechaRegreso.disabled = true;
                if(inputFechaRegreso.nextElementSibling) {
                    inputFechaRegreso.nextElementSibling.disabled = true;
                    inputFechaRegreso.nextElementSibling.classList.add('bg-gray-200', 'text-gray-400');
                }
                fpRegreso.clear();
                inputFechaRegreso.removeAttribute('required'); // <- CLAVE

            } else {
                inputDirRegreso.disabled = false; 
                inputDirRegreso.classList.remove('bg-gray-200', 'text-gray-400');
                inputDirRegreso.setAttribute('required', 'required');

                inputFechaRegreso.disabled = false;
                if(inputFechaRegreso.nextElementSibling) {
                    inputFechaRegreso.nextElementSibling.disabled = false;
                    inputFechaRegreso.nextElementSibling.classList.remove('bg-gray-200', 'text-gray-400');
                }
                inputFechaRegreso.setAttribute('required', 'required');
            }
        }

        chkIda.addEventListener('change', toggleRegreso);
        chkIdaVuelta.addEventListener('change', function() { if(this.checked) chkIda.checked = false; toggleRegreso(); });

        const chkConMoni = document.getElementById('chk_con_moni');
        const chkSinMoni = document.getElementById('chk_sin_moni');
        chkConMoni.addEventListener('change', function() { if(this.checked) chkSinMoni.checked = false; });
        chkSinMoni.addEventListener('change', function() { if(this.checked) chkConMoni.checked = false; });

        // Activar si hay persistencia
        if(chkIda.checked) toggleRegreso();
    });
</script>
@endsection