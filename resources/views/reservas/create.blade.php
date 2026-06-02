@extends('layouts.app')
@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 sm:mb-10 text-left">
        <h1 class="text-3xl sm:text-4xl font-black text-[#626366] tracking-tight">Agendar Espacio</h1>
        <p class="text-gray-500 text-base sm:text-lg mt-2">Gestiona el entorno educativo del Liceo de forma inteligente.</p>
    </div>

    @if(session('error_reserva'))
        <div class="bg-[#FFDE00]/20 border-l-4 border-[#FFDE00] text-[#626366] p-4 mb-6 shadow-sm rounded-2xl font-bold">
            ⚠️ {{ session('error_reserva') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        <div class="w-full lg:w-1/3 bg-white p-6 rounded-3xl shadow-xl border border-gray-100 h-fit order-2 lg:order-1">
            <div id="preview-card">
                <div class="relative overflow-hidden rounded-2xl mb-6 shadow-inner bg-gray-100 aspect-video">
                    <img id="preview-imagen" src="https://via.placeholder.com/600x400?text=Selecciona+un+lugar" class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-110">
                    <div class="absolute top-3 right-3 bg-[#4EAA68] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-md">Vista Previa</div>
                </div>
                <h2 id="preview-titulo" class="text-2xl font-bold text-[#626366]">Lugar</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center text-gray-600 bg-gray-50 p-3 rounded-xl">
                        <span class="text-xl mr-3">👥</span>
                        <span id="preview-capacidad" class="text-sm font-semibold">- Personas</span>
                    </div>
                    <p id="preview-descripcion" class="text-sm text-gray-500 leading-relaxed italic p-3 border-l-4 border-[#4EAA68]/50 bg-gray-50 rounded-r-xl">La descripción aparecerá aquí.</p>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-2/3 bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 order-1 lg:order-2">
            <form action="{{ route('reservas.store') }}" method="POST" class="space-y-6 sm:space-y-8">
                @csrf 
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Asunto o Título</label>
                    <input type="text" name="titulo" required value="{{ old('titulo') }}" placeholder="Ej: Clase de Robótica - 10B" class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-medium transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ubicación (Bloque)</label>
                        <select id="torre-select" class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-medium appearance-none">
                            <option value="">Selecciona Bloque...</option>
                            @foreach($torres as $torre)
                                <option value="{{ $torre->id }}">{{ $torre->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Espacio Específico</label>
                        <select id="espacio-select" name="espacio_id" required class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-medium appearance-none">
                            <option value="">Primero elige una torre...</option>
                            @foreach($espacios as $espacio)
                                <option value="{{ $espacio->id }}" data-torre="{{ $espacio->torre_id }}" data-nombre="{{ $espacio->nombre }}" data-capacidad="{{ $espacio->capacidad_personas }}" data-descripcion="{{ $espacio->descripcion }}" data-imagen="{{ $espacio->imagen_url ? asset($espacio->imagen_url) : '' }}" class="hidden">
                                    {{ $espacio->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50/50 p-4 sm:p-6 rounded-3xl border border-gray-100 shadow-inner">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-[#4EAA68]/20 text-[#4EAA68] rounded-xl flex items-center justify-center font-bold">📅</div>
                        <h3 class="text-lg sm:text-xl font-bold text-[#626366]">Horario del Evento</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 items-end">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Frecuencia</label>
                            <select name="frecuencia" class="w-full bg-white border border-gray-200 focus:ring-2 focus:ring-[#4EAA68] rounded-xl p-3 text-sm font-bold text-[#626366]">
                                <option value="unica" {{ old('frecuencia') == 'unica' ? 'selected' : '' }}>Única vez / Días seguidos</option>
                                @if(auth()->user()->rol === 'admin')
                                    <option value="semanal" {{ old('frecuencia') == 'semanal' ? 'selected' : '' }}>Semanal (Admin)</option>
                                    <option value="quincenal" {{ old('frecuencia') == 'quincenal' ? 'selected' : '' }}>Quincenal (Admin)</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha Inicio</label>
                            <input type="text" name="fecha_inicio" id="fecha_inicio" required value="{{ old('fecha_inicio') }}" placeholder="Selecciona..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha Fin Límite</label>
                            <input type="text" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" placeholder="Opcional..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Hora Inicia</label>
                            <input type="text" name="hora_inicio" id="hora_inicio" required value="{{ old('hora_inicio', '08:00') }}" placeholder="00:00" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Hora Finaliza</label>
                            <input type="text" name="hora_fin" id="hora_fin" required value="{{ old('hora_fin', '09:00') }}" placeholder="00:00" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Observaciones de la Sala</label>
                    <textarea name="observaciones" rows="2" placeholder="Especifique cómo requiere la organización del espacio..." class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-medium transition-all">{{ old('observaciones') }}</textarea>
                </div>

                <div class="bg-gray-50/50 p-4 sm:p-6 rounded-3xl border border-gray-100 shadow-inner">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-[#4EAA68]/20 text-[#4EAA68] rounded-xl flex items-center justify-center font-bold">✨</div>
                        <h3 class="text-lg sm:text-xl font-bold text-[#626366]">Recursos Adicionales</h3>
                    </div>
                    @php
                        $recursos = [
                            'Himno del Colegio', 'Himno Nacional', 'Himno de Bogotá',
                            'Atril', 'Bandera de Colombia', 'Baños torre 6',
                            'Disposición de las sillas', 'Mantel', 'Mesa Principal',
                            'Micrófono', 'Organización Parqueadero', 'Portatil',
                            'Refuerzo de Vigilancia', 'Sonido', 'Tienda Escolar', 'VideoBeam'
                        ];
                        $oldRecursos = old('recursos_adicionales', []);
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6 text-sm">
                        @foreach($recursos as $item)
                        <label class="flex items-center space-x-3 cursor-pointer group hover:bg-white p-2 rounded-xl transition">
                            <input type="checkbox" name="recursos_adicionales[]" value="{{ $item }}" {{ in_array($item, $oldRecursos) ? 'checked' : '' }} class="h-5 w-5 text-[#4EAA68] border-none bg-gray-200 rounded focus:ring-[#4EAA68]">
                            <span class="text-gray-600 group-hover:text-[#4EAA68] group-hover:font-medium transition">{{ $item }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-6 mt-6">
                    
                    <div class="bg-slate-50 border border-slate-200 p-5 sm:p-6 rounded-3xl shadow-sm transition-all">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" id="check_transporte" name="requiere_transporte" value="1" class="sr-only" {{ old('requiere_transporte') ? 'checked' : '' }}>
                                <div id="bg_transporte" class="block bg-gray-300 w-14 h-8 rounded-full transition-colors duration-300 shadow-inner"></div>
                                <div id="dot_transporte" class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 shadow-md flex items-center justify-center text-[10px]">❌</div>
                            </div>
                            <div class="ml-4 font-bold text-[#626366] flex items-center gap-2 group-hover:text-[#4EAA68] transition-colors text-sm sm:text-base">
                                <span class="text-xl sm:text-2xl">🚌</span> ¿Desea incluir Transporte para este evento?
                            </div>
                        </label>

                        <div id="panel_transporte" class="hidden mt-6 pt-6 border-t border-slate-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Responsable de Solicitud</label>
                                    <input type="text" name="trans_responsable" value="{{ old('trans_responsable') }}" placeholder="Nombre completo" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Celular del Responsable</label>
                                    <input type="text" name="trans_celular" value="{{ old('trans_celular') }}" placeholder="Ej: 3001234567" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Área que Solicita</label>
                                    <select name="trans_area" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                                        <option value="">Seleccione un área...</option>
                                        <option value="Rectoría">Rectoría</option>
                                        <option value="Vicerrectoría">Vicerrectoría</option>
                                        <option value="Dirección Académica">Dirección Académica</option>
                                        <option value="Dirección Preescolar">Dirección Preescolar</option>
                                        <option value="CA Ciencias Naturales">CA Ciencias Naturales</option>
                                        <option value="Gerencia Operativa">Gerencia Operativa</option>
                                        <option value="Coordinación de Transporte">Coordinación de Transporte</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">N° Estudiantes</label>
                                        <input type="number" min="0" name="trans_estudiantes" value="{{ old('trans_estudiantes', 0) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">N° Adultos</label>
                                        <input type="number" min="0" name="trans_adultos" value="{{ old('trans_adultos', 0) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Salida</label>
                                    <input type="text" id="trans_salida" name="trans_salida" value="{{ old('trans_salida') }}" placeholder="Sincronizado..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Regreso</label>
                                    <input type="text" id="trans_regreso" name="trans_regreso" value="{{ old('trans_regreso') }}" placeholder="Sincronizado..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dir. Recogida</label>
                                    <input type="text" name="trans_dir_recogida" value="{{ old('trans_dir_recogida', 'Liceo de Colombia Bilingüe') }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dir. Destino</label>
                                    <input type="text" name="trans_dir_destino" value="{{ old('trans_dir_destino') }}" placeholder="Lugar a visitar" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dir. de Regreso</label>
                                    <input type="text" name="trans_dir_regreso" value="{{ old('trans_dir_regreso', 'Liceo de Colombia Bilingüe') }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">
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
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observaciones Adicionales Transporte</label>
                                <textarea name="trans_observaciones" rows="2" placeholder="Ej: Necesitamos buses con baúl grande para equipos de Ciencias..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68]">{{ old('trans_observaciones') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50/40 border border-[#FFDE00]/50 p-5 sm:p-6 rounded-3xl shadow-sm transition-all">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" id="check_restaurante" name="requiere_restaurante" value="1" class="sr-only" {{ old('requiere_restaurante') ? 'checked' : '' }}>
                                <div id="bg_restaurante" class="block bg-gray-300 w-14 h-8 rounded-full transition-colors duration-300 shadow-inner"></div>
                                <div id="dot_restaurante" class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 shadow-md flex items-center justify-center text-[10px]">❌</div>
                            </div>
                            <div class="ml-4 font-bold text-[#626366] flex items-center gap-2 group-hover:text-yellow-600 transition-colors text-sm sm:text-base">
                                <span class="text-xl sm:text-2xl">🍽️</span> ¿Desea incluir servicio de Restaurante?
                            </div>
                        </label>

                        <div id="panel_restaurante" class="hidden mt-6 pt-6 border-t border-[#FFDE00]/40">
                            <div class="bg-white p-4 rounded-xl border border-yellow-200 mb-4 flex items-start gap-3">
                                <span class="text-xl">⚠️</span>
                                <p class="text-xs text-[#626366] font-bold leading-relaxed">Todo servicio de restaurante entra a estado "Pendiente" y requiere autorización de Gerencia para ser procesado por cocina.</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora del Evento</label>
                                    <input type="text" id="rest_fecha_hora" name="rest_fecha_hora" value="{{ old('rest_fecha_hora') }}" placeholder="Sincronizado..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#FFDE00] cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">N° Asistentes</label>
                                    <input type="number" min="1" name="rest_asistentes" value="{{ old('rest_asistentes') }}" placeholder="Ej: 40" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#FFDE00]">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">¿Quién aprueba?</label>
                                    <select name="rest_aprobador_id" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#FFDE00]">
                                        <option value="">(Se asignará a Gerencia en la nube)</option>
                                        </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Servicios Requeridos (Múltiple)</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @php $servicios_rest = ['Desayuno', 'Onces', 'Pasabocas', 'Brunch', 'Almuerzo', 'Cena', 'Estación de café']; @endphp
                                    @foreach($servicios_rest as $srv)
                                    <label class="flex items-center space-x-2 text-sm text-gray-600">
                                        <input type="checkbox" name="rest_servicios[]" value="{{ $srv }}" class="rounded text-yellow-500 focus:ring-[#FFDE00]">
                                        <span>{{ $srv }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Detalles de su Solicitud</label>
                                <textarea name="rest_detalles" rows="2" placeholder="Ej: Especificar dietas, o si el refrigerio incluye capuccino vainilla, empanadas, pizza, etc..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#FFDE00]">{{ old('rest_detalles') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="w-full sm:w-auto bg-[#4EAA68] hover:bg-[#3d8c55] text-white font-black py-4 px-10 rounded-2xl transition-all shadow-xl hover:shadow-[#4EAA68]/40 active:scale-95">
                        Agendar Solicitud Completa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. LÓGICA DE ESPACIOS (Vista Previa) ---
        const torreSelect = document.getElementById('torre-select');
        const espacioSelect = document.getElementById('espacio-select');
        const options = Array.from(espacioSelect.querySelectorAll('option'));
        
        torreSelect.addEventListener('change', function() {
            const torreId = this.value;
            if(!window.isAutoLoading) espacioSelect.value = ""; 
            options.forEach(opt => {
                if(opt.dataset.torre == torreId || opt.value == "") opt.classList.remove('hidden');
                else opt.classList.add('hidden');
            });
        });

        espacioSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if(opt && opt.value != "") {
                document.getElementById('preview-titulo').innerText = opt.dataset.nombre;
                document.getElementById('preview-capacidad').innerText = opt.dataset.capacidad + " Personas";
                document.getElementById('preview-descripcion').innerText = opt.dataset.descripcion || 'Sin descripción disponible.';
                document.getElementById('preview-imagen').src = opt.dataset.imagen || 'https://via.placeholder.com/600x400?text=LCB+Reservas';
            }
        });

        // --- 2. CONFIGURACIÓN GLOBAL DE FLATPICKR (SINCRONIZACIÓN) ---
        const fpConfig = {
            locale: "es",
            disable: [ function(date) { return (date.getDay() === 0); } ] // Bloquea Domingos
        };

        const fpTransSalida = flatpickr("#trans_salida", { ...fpConfig, enableTime: true, dateFormat: "Y-m-d H:i" });
        const fpTransRegreso = flatpickr("#trans_regreso", { ...fpConfig, enableTime: true, dateFormat: "Y-m-d H:i" });
        const fpRestaurante = flatpickr("#rest_fecha_hora", { ...fpConfig, enableTime: true, dateFormat: "Y-m-d H:i" });
        const fpFin = flatpickr("#fecha_fin", { ...fpConfig, dateFormat: "Y-m-d" });

        // Instancia Principal que empuja la restricción a las demás
        flatpickr("#fecha_inicio", {
            ...fpConfig,
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                // Sincroniza la fecha mínima de todo el resto del formulario para evitar errores humanos
                fpFin.set('minDate', dateStr);
                fpTransSalida.set('minDate', dateStr);
                fpTransRegreso.set('minDate', dateStr);
                fpRestaurante.set('minDate', dateStr);
                
                if(document.getElementById('fecha_fin').value && document.getElementById('fecha_fin').value < dateStr) {
                    fpFin.setDate(dateStr);
                }
            }
        });

        flatpickr("#hora_inicio, #hora_fin", { enableTime: true, noCalendar: true, dateFormat: "H:i", minTime: "07:00", maxTime: "17:00", time_24hr: false, minuteIncrement: 15 });

        // --- 3. LÓGICA DE EXCLUSIÓN PARA CHECKBOXES DE TRANSPORTE ---
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

        // --- 4. MAGIA DE PERSISTENCIA PARA RECARGAS CON ERROR ---
        const oldEspacioId = "{{ old('espacio_id') }}";
        if (oldEspacioId) {
            window.isAutoLoading = true;
            const opt = options.find(o => o.value === oldEspacioId);
            if (opt) {
                torreSelect.value = opt.dataset.torre;
                torreSelect.dispatchEvent(new Event('change'));
                espacioSelect.value = oldEspacioId;
                espacioSelect.dispatchEvent(new Event('change'));
            }
            window.isAutoLoading = false;
        }

        // --- 5. ANIMACIONES DE LOS INTERRUPTORES ---
        const checkTransporte = document.getElementById('check_transporte');
        const panelTransporte = document.getElementById('panel_transporte');
        const bgTransporte = document.getElementById('bg_transporte');
        const dotTransporte = document.getElementById('dot_transporte');

        checkTransporte.addEventListener('change', function() {
            if (this.checked) {
                panelTransporte.classList.remove('hidden');
                bgTransporte.classList.replace('bg-gray-300', 'bg-[#4EAA68]'); 
                dotTransporte.style.transform = 'translateX(100%)';
                dotTransporte.innerHTML = '✅';
            } else {
                panelTransporte.classList.add('hidden');
                bgTransporte.classList.replace('bg-[#4EAA68]', 'bg-gray-300');
                dotTransporte.style.transform = 'translateX(0)';
                dotTransporte.innerHTML = '❌';
            }
        });

        const checkRestaurante = document.getElementById('check_restaurante');
        const panelRestaurante = document.getElementById('panel_restaurante');
        const bgRestaurante = document.getElementById('bg_restaurante');
        const dotRestaurante = document.getElementById('dot_restaurante');

        checkRestaurante.addEventListener('change', function() {
            if (this.checked) {
                panelRestaurante.classList.remove('hidden');
                bgRestaurante.classList.replace('bg-gray-300', 'bg-[#FFDE00]'); 
                dotRestaurante.style.transform = 'translateX(100%)';
                dotRestaurante.innerHTML = '✅';
            } else {
                panelRestaurante.classList.add('hidden');
                bgRestaurante.classList.replace('bg-[#FFDE00]', 'bg-gray-300');
                dotRestaurante.style.transform = 'translateX(0)';
                dotRestaurante.innerHTML = '❌';
            }
        });

        if(checkTransporte.checked) checkTransporte.dispatchEvent(new Event('change'));
        if(checkRestaurante.checked) checkRestaurante.dispatchEvent(new Event('change'));
    });
</script>
@endsection