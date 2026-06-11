@extends('layouts.app')
@section('content')

<div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 sm:mb-10 text-left">
        <h1 class="text-3xl sm:text-4xl font-black text-[#626366] tracking-tight">Agendar Espacio</h1>
        <p class="text-gray-500 text-base sm:text-lg mt-2">Gestiona el entorno educativo del Liceo de forma inteligente.</p>
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

    @if(session('error_reserva'))
        <div class="bg-[#FFDE00]/20 border-l-4 border-[#FFDE00] text-[#626366] p-4 mb-6 shadow-sm rounded-2xl font-bold flex items-center gap-3">
            <span class="text-2xl">⚠️</span> {{ session('error_reserva') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        
        <div class="w-full lg:w-2/5 bg-white p-6 rounded-3xl shadow-xl border border-gray-100 h-fit order-2 lg:order-1 sticky top-6">
            <div id="preview-card">
                <div class="relative overflow-hidden rounded-2xl mb-6 shadow-inner bg-gray-100 aspect-video">
                    <img id="preview-imagen" src="https://via.placeholder.com/800x600?text=Selecciona+un+lugar" class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-110">
                    <div class="absolute top-3 right-3 bg-[#4EAA68] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-md">Vista Previa</div>
                </div>
                <h2 id="preview-titulo" class="text-3xl font-black text-[#626366] leading-tight">Seleccione un Espacio</h2>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-2xl mr-3">👥</span>
                        <span id="preview-capacidad" class="text-base font-black text-gray-700">-- Personas</span>
                    </div>
                    <p id="preview-descripcion" class="text-sm text-gray-500 leading-relaxed italic p-5 border-l-4 border-[#4EAA68]/50 bg-gray-50 rounded-r-xl">La descripción de los recursos y la dotación del espacio aparecerá aquí tan pronto seleccione una opción.</p>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-3/5 bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-gray-100 order-1 lg:order-2">
            <form action="{{ route('reservas.store') }}" method="POST" class="space-y-8" id="form-reserva">
                @csrf 
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Asunto o Título del Evento *</label>
                        <input type="text" name="titulo" required value="{{ old('titulo') }}" placeholder="Ej: Clase Especial, Reunión de Padres..." class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-bold transition-all text-lg shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ubicación (Bloque / Torre) *</label>
                        <select id="torre-select" required class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-bold shadow-inner cursor-pointer appearance-none">
                            <option value="">Selecciona Bloque...</option>
                            @foreach($torres as $torre)
                                <option value="{{ $torre->id }}">{{ $torre?->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Espacio Específico *</label>
                        <select id="espacio-select" name="espacio_id" required class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-bold shadow-inner cursor-pointer appearance-none">
                            <option value="">Primero elige un bloque...</option>
                            @foreach($espacios as $espacio)
                                <option value="{{ $espacio->id }}" data-torre="{{ $espacio->torre_id }}" data-nombre="{{ $espacio?->nombre }}" data-capacidad="{{ $espacio->capacidad_personas }}" data-descripcion="{{ $espacio->descripcion }}" data-imagen="{{ $espacio->imagen_url ? asset($espacio->imagen_url) : '' }}" class="hidden">
                                    {{ $espacio?->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50/50 p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-[#4EAA68]/20 text-[#4EAA68] rounded-2xl flex items-center justify-center text-xl font-bold shadow-inner">📅</div>
                            <h3 class="text-xl font-black text-[#626366]">Horario y Disponibilidad</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Frecuencia *</label>
                                <select name="frecuencia" id="frecuencia" class="w-full bg-white border border-gray-200 focus:ring-2 focus:ring-[#4EAA68] rounded-xl p-3.5 text-sm font-bold text-[#626366] shadow-sm cursor-pointer">
                                    <option value="unica" {{ old('frecuencia') == 'unica' ? 'selected' : '' }}>Única vez / Días seguidos</option>
                                    <option value="semanal" {{ old('frecuencia') == 'semanal' ? 'selected' : '' }}>Todos los días a la semana</option>
                                    <option value="quincenal" {{ old('frecuencia') == 'quincenal' ? 'selected' : '' }}>Cada 15 días</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha Inicio *</label>
                                <input type="text" name="fecha_inicio" id="fecha_inicio" required value="{{ old('fecha_inicio') }}" placeholder="Día del evento..." class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm cursor-pointer text-[#4EAA68]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha Fin Límite</label>
                                <input type="text" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" placeholder="Solo si son varios días..." class="w-full bg-white border border-gray-200 rounded-xl p-3.5 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm cursor-pointer">
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm relative">
                            
                            <div id="capa-bloqueo-tiempo" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center rounded-2xl transition-opacity duration-300">
                                <span class="text-3xl mb-2">☝️</span>
                                <p class="text-xs font-black text-gray-500 uppercase tracking-widest text-center px-4">Seleccione primero el Espacio y la Fecha <br>para habilitar las horas</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Hora Inicio</label>
                                    <input type="text" name="hora_inicio" id="hora_inicio" required value="{{ old('hora_inicio', '07:00') }}" placeholder="07:00 AM" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 text-sm font-black text-center focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Hora Fin</label>
                                    <input type="text" name="hora_fin" id="hora_fin" required value="{{ old('hora_fin', '08:00') }}" placeholder="08:00 AM" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 text-sm font-black text-center focus:ring-2 focus:ring-[#4EAA68] cursor-pointer">
                                </div>
                            </div>

                            <div class="relative w-full h-8 bg-gray-100 rounded-lg shadow-inner mb-2 border border-gray-200 overflow-hidden" id="contenedor-barra">
                                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAlIiBoZWlnaHQ9IjEwMCUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGxpbmUgeDE9IjEwMCUiIHkxPSIwIiB4Mj0iMTAwJSIgeTI9IjEwMCUiIHN0cm9rZT0iI2U1ZTdlYiIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-50 pointer-events-none"></div>
                                <div id="bloques-rojos"></div>
                                <div id="time-bar" class="absolute top-0 h-full bg-[#4EAA68]/90 border-x-2 border-[#4EAA68] transition-all duration-300 flex items-center justify-center shadow-md z-10" style="left: 0%; width: 10%;">
                                    <span class="text-white text-[9px] font-black opacity-0 md:opacity-100 transition-opacity">MI RESERVA</span>
                                </div>
                            </div>
                            <div class="flex justify-between text-[9px] font-black text-gray-400 px-1 mb-5">
                                <span>7 AM</span><span>8</span><span>9</span><span>10</span><span>11</span><span>12 PM</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5 PM</span>
                            </div>

                            <div class="flex items-center justify-center border-t border-gray-100 pt-5">
                                <button type="button" id="btn-comprobar" class="bg-gray-800 text-white px-6 py-3 rounded-xl text-sm font-black hover:bg-gray-700 transition-colors shadow-md flex items-center gap-2 w-full sm:w-auto justify-center">
                                    <span>🔍</span> Validar Disponibilidad
                                </button>
                            </div>

                            <div id="msg-exito" class="hidden mt-4 bg-green-50 border border-green-200 px-4 py-3 rounded-xl shadow-sm transition-all duration-300 flex items-center justify-center gap-2">
                                <span class="text-xl">✅</span> 
                                <p class="text-green-700 text-sm font-black uppercase tracking-wide">¡Todos los horarios están disponibles!</p>
                            </div>

                            <div id="msg-choque" class="hidden mt-4 bg-red-50 border border-red-200 px-4 py-3 rounded-xl shadow-sm transition-all duration-300">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xl">❌</span> 
                                    <p class="text-red-700 text-sm font-black uppercase tracking-wide">Choque de Horarios Detectado</p>
                                </div>
                                <p class="text-xs text-red-600 font-bold mb-2">Las siguientes fechas ya se encuentran ocupadas en ese rango:</p>
                                <ul id="lista-conflictos" class="list-disc list-inside text-xs text-red-800 font-medium pl-6 space-y-1"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observaciones del Espacio</label>
                        <textarea name="observaciones" rows="5" placeholder="Disposición de las mesas, requerimientos especiales..." class="w-full bg-gray-50 border-none focus:ring-2 focus:ring-[#4EAA68] rounded-2xl p-4 text-[#626366] font-medium transition-all shadow-inner resize-none">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="bg-gray-50/80 p-5 rounded-2xl border border-gray-100 shadow-inner">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xl">✨</span>
                            <h3 class="text-sm font-black text-[#626366] uppercase tracking-wide">Recursos Extra</h3>
                        </div>
                        @php
                            $recursos = [
                                'Himnos (Col/Bog/Lcb)', 'Atril / Mesa P.', 'Bandera de Col',
                                'Micrófono/Sonido', 'VideoBeam/TV', 'Portátil',
                                'Logística Parqueadero', 'Refuerzo Vigilancia', 'Tienda Escolar'
                            ];
                            $oldRecursos = old('recursos_adicionales', []);
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            @foreach($recursos as $item)
                            <label class="flex items-center space-x-3 cursor-pointer group bg-white p-2 rounded-xl border border-gray-100 hover:border-[#4EAA68]/50 shadow-sm transition">
                                <input type="checkbox" name="recursos_adicionales[]" value="{{ $item }}" {{ in_array($item, $oldRecursos) ? 'checked' : '' }} class="h-4 w-4 text-[#4EAA68] border-gray-300 rounded focus:ring-[#4EAA68]">
                                <span class="text-gray-600 text-xs font-bold group-hover:text-[#4EAA68] transition">{{ $item }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-6 pt-4">
                    <div class="bg-slate-50 border border-slate-200 p-6 rounded-3xl shadow-sm transition-all relative overflow-hidden">
                        <label class="flex items-center cursor-pointer group relative z-10">
                            <div class="relative">
                                <input type="checkbox" id="check_transporte" name="requiere_transporte" value="1" class="sr-only" {{ old('requiere_transporte') ? 'checked' : '' }}>
                                <div id="bg_transporte" class="block bg-gray-300 w-14 h-8 rounded-full transition-colors duration-300 shadow-inner"></div>
                                <div id="dot_transporte" class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 shadow-md flex items-center justify-center text-[10px]">❌</div>
                            </div>
                            <div class="ml-4 font-black text-[#626366] flex items-center gap-2 group-hover:text-[#4EAA68] transition-colors text-base sm:text-lg">
                                <span class="text-2xl">🚌</span> ¿Desea incluir Transporte externo?
                            </div>
                        </label>

                        <div id="panel_transporte" class="hidden mt-6 pt-6 border-t border-slate-200 relative z-10">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Responsable</label>
                                    <input type="text" name="trans_responsable" value="{{ old('trans_responsable') }}" placeholder="Nombre completo" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Celular</label>
                                    <input type="text" name="trans_celular" value="{{ old('trans_celular') }}" placeholder="Ej: 3001234567" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Área que Solicita</label>
                                    <select name="trans_area" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm">
                                        <option value="">Seleccione...</option>
                                        <option value="Rectoría" {{ old('trans_area') == 'Rectoría' ? 'selected' : '' }}>Rectoría</option>
                                        <option value="Vicerrectoría" {{ old('trans_area') == 'Vicerrectoría' ? 'selected' : '' }}>Vicerrectoría</option>
                                        <option value="Dirección Académica" {{ old('trans_area') == 'Dirección Académica' ? 'selected' : '' }}>Dirección Académica</option>
                                        <option value="Dirección Preescolar" {{ old('trans_area') == 'Dirección Preescolar' ? 'selected' : '' }}>Dirección Preescolar</option>
                                        <option value="Gerencia Operativa" {{ old('trans_area') == 'Gerencia Operativa' ? 'selected' : '' }}>Gerencia Operativa</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-5">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Salida</label>
                                    <input type="text" id="trans_salida" name="trans_salida" value="{{ old('trans_salida') }}" placeholder="Asociado al evento..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm cursor-pointer">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Regreso</label>
                                    <input type="text" id="trans_regreso" name="trans_regreso" value="{{ old('trans_regreso') }}" placeholder="Asociado al evento..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm cursor-pointer transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">N° Estudiantes</label>
                                    <input type="number" min="0" name="trans_estudiantes" value="{{ old('trans_estudiantes', 0) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">N° Adultos</label>
                                    <input type="number" min="0" name="trans_adultos" value="{{ old('trans_adultos', 0) }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Configuración Viaje</label>
                                    @php $t_nec = old('trans_necesidades', []); @endphp
                                    <div class="grid grid-cols-2 gap-2 mt-1">
                                        <label class="flex items-center space-x-2 text-xs font-bold text-gray-600 bg-white p-2 rounded-lg border border-gray-100 shadow-sm cursor-pointer hover:border-[#4EAA68]"><input type="checkbox" id="chk_solo_ida" name="trans_necesidades[]" value="Servicio solo ida" {{ in_array('Servicio solo ida', $t_nec) ? 'checked' : '' }} class="rounded text-[#4EAA68] focus:ring-[#4EAA68] w-4 h-4"><span>Solo Ida</span></label>
                                        <label class="flex items-center space-x-2 text-xs font-bold text-gray-600 bg-white p-2 rounded-lg border border-gray-100 shadow-sm cursor-pointer hover:border-[#4EAA68]"><input type="checkbox" id="chk_ida_vuelta" name="trans_necesidades[]" value="Servicio ida y vuelta" {{ empty($t_nec) || in_array('Servicio ida y vuelta', $t_nec) ? 'checked' : '' }} class="rounded text-[#4EAA68] focus:ring-[#4EAA68] w-4 h-4"><span>Ida y Vuelta</span></label>
                                        <label class="flex items-center space-x-2 text-xs font-bold text-gray-600 bg-white p-2 rounded-lg border border-gray-100 shadow-sm cursor-pointer hover:border-[#4EAA68]"><input type="checkbox" id="chk_con_moni" name="trans_necesidades[]" value="Con Monitora" {{ in_array('Con Monitora', $t_nec) ? 'checked' : '' }} class="rounded text-[#4EAA68] focus:ring-[#4EAA68] w-4 h-4"><span>Con Monitora</span></label>
                                        <label class="flex items-center space-x-2 text-xs font-bold text-gray-600 bg-white p-2 rounded-lg border border-gray-100 shadow-sm cursor-pointer hover:border-[#4EAA68]"><input type="checkbox" id="chk_sin_moni" name="trans_necesidades[]" value="Sin Monitora" {{ in_array('Sin Monitora', $t_nec) ? 'checked' : '' }} class="rounded text-[#4EAA68] focus:ring-[#4EAA68] w-4 h-4"><span>Sin Monitora</span></label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Destino</label>
                                    <input type="text" name="trans_dir_destino" value="{{ old('trans_dir_destino') }}" placeholder="Lugar a visitar" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Lugar de Regreso</label>
                                    <input type="text" id="trans_dir_regreso" name="trans_dir_regreso" value="{{ old('trans_dir_regreso', 'Liceo de Colombia Bilingüe') }}" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#4EAA68] shadow-sm transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50/40 border border-[#FFDE00]/50 p-6 rounded-3xl shadow-sm transition-all relative overflow-hidden">
                        <label class="flex items-center cursor-pointer group relative z-10">
                            <div class="relative">
                                <input type="checkbox" id="check_restaurante" name="requiere_restaurante" value="1" class="sr-only" {{ old('requiere_restaurante') ? 'checked' : '' }}>
                                <div id="bg_restaurante" class="block bg-gray-300 w-14 h-8 rounded-full transition-colors duration-300 shadow-inner"></div>
                                <div id="dot_restaurante" class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 shadow-md flex items-center justify-center text-[10px]">❌</div>
                            </div>
                            <div class="ml-4 font-black text-[#626366] flex items-center gap-2 group-hover:text-yellow-600 transition-colors text-base sm:text-lg">
                                <span class="text-2xl">🍽️</span> ¿Desea incluir servicio de Alimentación?
                            </div>
                        </label>

                        <div id="panel_restaurante" class="hidden mt-6 pt-6 border-t border-[#FFDE00]/40 relative z-10">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fecha y Hora de Entrega</label>
                                    <input type="text" id="rest_fecha_hora" name="rest_fecha_hora" value="{{ old('rest_fecha_hora') }}" placeholder="Asociado al evento..." class="w-full bg-white border border-yellow-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#FFDE00] shadow-sm cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Cantidad de Asistentes</label>
                                    <input type="number" min="1" name="rest_asistentes" value="{{ old('rest_asistentes') }}" placeholder="Ej: 40" class="w-full bg-white border border-yellow-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#FFDE00] shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">¿Quién Aprueba el Presupuesto?</label>
                                    <select name="rest_aprobador_id" class="w-full bg-white border border-yellow-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-[#FFDE00] shadow-sm text-yellow-900">
                                        <option value="">Seleccione Gerencia...</option>
                                        <option value="Gerencia Académica" {{ old('rest_aprobador_id') == 'Gerencia Académica' ? 'selected' : '' }}>Gerencia Académica</option>
                                        <option value="Gerencia Administrativa" {{ old('rest_aprobador_id') == 'Gerencia Administrativa' ? 'selected' : '' }}>Gerencia Administrativa</option>
                                        <option value="Gerencia Operativa" {{ old('rest_aprobador_id') == 'Gerencia Operativa' ? 'selected' : '' }}>Gerencia Operativa</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipo de Servicio (Múltiple)</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @php 
                                        $servicios_rest = ['Desayuno', 'Onces', 'Pasabocas', 'Almuerzo', 'Cena', 'Estación de café']; 
                                        $old_rest = old('rest_servicios', []);
                                    @endphp
                                    @foreach($servicios_rest as $srv)
                                    <label class="flex items-center space-x-2 text-sm font-bold text-gray-700 bg-white p-2.5 rounded-xl border border-yellow-100 shadow-sm hover:border-yellow-400 transition cursor-pointer">
                                        <input type="checkbox" name="rest_servicios[]" value="{{ $srv }}" {{ in_array($srv, $old_rest) ? 'checked' : '' }} class="rounded w-4 h-4 text-yellow-500 focus:ring-[#FFDE00]">
                                        <span>{{ $srv }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dietas Especiales y Detalles</label>
                                <textarea name="rest_detalles" rows="2" placeholder="Especificar si hay vegetarianos, alérgicos..." class="w-full bg-white border border-yellow-200 rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-[#FFDE00] shadow-sm">{{ old('rest_detalles') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex justify-end pt-8 border-t border-gray-200">
                    <button type="submit" id="btn-submit" disabled class="w-full sm:w-auto bg-[#4EAA68] text-white font-black text-lg py-4 px-10 rounded-2xl transition-all shadow-[0_8px_30px_rgb(78,170,104,0.4)] opacity-50 cursor-not-allowed grayscale flex items-center justify-center gap-3">
                        <span>🚀</span> Procesar y Agendar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. LÓGICA DE ESPACIOS E IMAGEN PREVIA ---
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
            resetValidacion(); 
        });

        espacioSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if(opt && opt.value != "") {
                document.getElementById('preview-titulo').innerText = opt.dataset.nombre;
                document.getElementById('preview-capacidad').innerText = opt.dataset.capacidad + " Personas";
                document.getElementById('preview-descripcion').innerText = opt.dataset.descripcion || 'Sin descripción disponible.';
                document.getElementById('preview-imagen').src = opt.dataset.imagen || 'https://via.placeholder.com/800x600?text=LCB+Reservas';
            }
            resetValidacion();
            consultarDisponibilidad(); 
        });

        // --- 2. CONFIGURACIÓN DE FECHAS (FLATPICKR) ---
        const selectFrecuencia = document.getElementById('frecuencia');
        if (selectFrecuencia) { selectFrecuencia.addEventListener('change', resetValidacion); }

        const fpConfig = { locale: "es", disable: [ function(date) { return (date.getDay() === 0); } ] };

        const fpTransSalida = flatpickr("#trans_salida", { ...fpConfig, enableTime: true, dateFormat: "Y-m-d h:i K" });
        const fpTransRegreso = flatpickr("#trans_regreso", { ...fpConfig, enableTime: true, dateFormat: "Y-m-d h:i K" });
        const fpRestaurante = flatpickr("#rest_fecha_hora", { ...fpConfig, enableTime: true, dateFormat: "Y-m-d h:i K" });
        
        const fpFin = flatpickr("#fecha_fin", { 
            ...fpConfig, 
            dateFormat: "Y-m-d",
            onChange: function() { resetValidacion(); }
        });

        flatpickr("#fecha_inicio", {
            ...fpConfig,
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                let fechaLimite = new Date(selectedDates[0]);
                fechaLimite.setDate(fechaLimite.getDate() + 3); 

                fpFin.set('minDate', dateStr);
                fpTransSalida.set('minDate', dateStr); fpTransSalida.set('maxDate', fechaLimite);
                fpTransRegreso.set('minDate', dateStr); fpTransRegreso.set('maxDate', fechaLimite);
                fpRestaurante.set('minDate', dateStr); fpRestaurante.set('maxDate', fechaLimite);
                
                resetValidacion();
                consultarDisponibilidad(); 
            }
        });

        // --- 3. BARRA DE TIEMPO Y BOTÓN DE VALIDACIÓN ---
        const timeConfig = {
            enableTime: true, noCalendar: true, dateFormat: "H:i", altInput: true, altFormat: "h:i K",
            minTime: "07:00", maxTime: "17:00", minuteIncrement: 15,
            onChange: function() { resetValidacion(); actualizarBarraTiempoVisual(); }
        };

        flatpickr("#hora_inicio", timeConfig);
        flatpickr("#hora_fin", timeConfig);
        
        const timeBar = document.getElementById('time-bar');
        const bloquesRojosDiv = document.getElementById('bloques-rojos');
        const msgChoque = document.getElementById('msg-choque');
        const msgExito = document.getElementById('msg-exito');
        const btnSubmit = document.getElementById('btn-submit');
        const btnComprobar = document.getElementById('btn-comprobar');
        const capaBloqueo = document.getElementById('capa-bloqueo-tiempo');
        
        let reservasOcupadas = []; 

        function horaAMinutos(horaStr) {
            let parts = horaStr.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        }

        // Apaga el botón obligando a validar de nuevo
        function resetValidacion() {
            msgExito.classList.add('hidden');
            msgChoque.classList.add('hidden');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed', 'grayscale');
            btnSubmit.classList.remove('hover:-translate-y-1');

            let espacio = document.getElementById('espacio-select').value;
            let fecha = document.getElementById('fecha_inicio').value;
            if(espacio && fecha) {
                capaBloqueo.classList.add('hidden');
                btnComprobar.disabled = false;
                btnComprobar.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                capaBloqueo.classList.remove('hidden');
                btnComprobar.disabled = true;
                btnComprobar.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        function consultarDisponibilidad() {
            let espacio = document.getElementById('espacio-select').value;
            let fInicio = document.getElementById('fecha_inicio').value;
            let fFin = document.getElementById('fecha_fin').value;
            let frec = document.getElementById('frecuencia') ? document.getElementById('frecuencia').value : 'unica';
            
            if(!espacio || !fInicio) return;

            // Uso del helper URL de Blade para que no se pierda en XAMPP
            let baseUrl = "{{ url('/reservas/disponibilidad') }}";
            let urlApi = `${baseUrl}?espacio_id=${espacio}&fecha_inicio=${fInicio}&fecha_fin=${fFin}&frecuencia=${frec}`;

            fetch(urlApi)
                .then(response => {
                    if(!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    reservasOcupadas = data;
                    dibujarBloquesRojos();
                })
                .catch(err => console.error("Error consultando disponibilidad:", err));
        }

        function dibujarBloquesRojos() {
            bloquesRojosDiv.innerHTML = '';
            const minMins = 7 * 60; 
            const totalMins = (17 * 60) - minMins; 
            
            reservasOcupadas.forEach(res => {
                let startM = horaAMinutos(res.hora_inicio);
                let endM = horaAMinutos(res.hora_fin);
                if(endM <= minMins || startM >= 17*60) return;
                
                let leftPercent = ((startM - minMins) / totalMins) * 100;
                let widthPercent = ((endM - startM) / totalMins) * 100;
                
                let divRojo = document.createElement('div');
                divRojo.className = 'absolute top-0 h-full bg-red-400/80 border-x border-red-500 z-0 opacity-80';
                divRojo.style.left = leftPercent + '%'; divRojo.style.width = widthPercent + '%';
                bloquesRojosDiv.appendChild(divRojo);
            });
        }

        function actualizarBarraTiempoVisual() {
            let start = document.getElementById('hora_inicio').value;
            let end = document.getElementById('hora_fin').value;
            if(!start || !end) return;

            let startMins = horaAMinutos(start); let endMins = horaAMinutos(end);
            const minMins = 7 * 60; const totalMins = (17 * 60) - minMins;

            if(endMins <= startMins) { timeBar.style.width = '0%'; return; }

            let leftPercent = ((startMins - minMins) / totalMins) * 100;
            let widthPercent = ((endMins - startMins) / totalMins) * 100;
            timeBar.style.left = leftPercent + '%'; timeBar.style.width = widthPercent + '%';
            timeBar.classList.replace('bg-red-600/90', 'bg-[#4EAA68]/90');
        }

        // EVENTO DEL BOTÓN EXPLÍCITO DE VALIDAR
        btnComprobar.addEventListener('click', function() {
            let start = document.getElementById('hora_inicio').value;
            let end = document.getElementById('hora_fin').value;
            if(!start || !end) return;

            let startMins = horaAMinutos(start); 
            let endMins = horaAMinutos(end);
            if(endMins <= startMins) {
                alert("La hora de finalización debe ser posterior a la hora de inicio.");
                return;
            }

            let originalText = this.innerHTML;
            this.innerHTML = '<span>⏳</span> Validando...';
            this.disabled = true;

            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;

                let hayChoque = false;
                let fechasConChoque = new Set(); 

                reservasOcupadas.forEach(res => {
                    let resStart = horaAMinutos(res.hora_inicio);
                    let resEnd = horaAMinutos(res.hora_fin);
                    if(startMins < resEnd && endMins > resStart) {
                        hayChoque = true;
                        fechasConChoque.add(res.fecha_inicio);
                    }
                });

                if(hayChoque) {
                    timeBar.classList.replace('bg-[#4EAA68]/90', 'bg-red-600/90');
                    msgExito.classList.add('hidden');
                    msgChoque.classList.remove('hidden');
                    
                    const listaConflictos = document.getElementById('lista-conflictos');
                    listaConflictos.innerHTML = '';
                    fechasConChoque.forEach(fecha => {
                        let li = document.createElement('li');
                        let partes = fecha.split('-');
                        let fechaObj = new Date(partes[0], partes[1] - 1, partes[2]);
                        li.innerText = fechaObj.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                        listaConflictos.appendChild(li);
                    });

                } else {
                    msgChoque.classList.add('hidden');
                    msgExito.classList.remove('hidden');
                    
                    // ¡SE ENCIENDE EL BOTÓN PRINCIPAL!
                    btnSubmit.disabled = false;
                    btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed', 'grayscale');
                    btnSubmit.classList.add('hover:-translate-y-1');
                }
            }, 600); 
        });

        // --- 4. MAGIA DEL BUS ---
        const chkIda = document.getElementById('chk_solo_ida');
        const chkIdaVuelta = document.getElementById('chk_ida_vuelta');
        const inputDirRegreso = document.getElementById('trans_dir_regreso');
        const inputFechaRegreso = document.getElementById('trans_regreso');

        function toggleRegreso() {
            if(chkIda.checked) {
                chkIdaVuelta.checked = false;
                inputDirRegreso.disabled = true; inputDirRegreso.classList.add('bg-gray-200', 'text-gray-400');
                inputFechaRegreso.disabled = true;
                if(inputFechaRegreso.nextElementSibling) {
                    inputFechaRegreso.nextElementSibling.disabled = true;
                    inputFechaRegreso.nextElementSibling.classList.add('bg-gray-200', 'text-gray-400');
                }
                fpTransRegreso.clear();
            } else {
                inputDirRegreso.disabled = false; inputDirRegreso.classList.remove('bg-gray-200', 'text-gray-400');
                inputFechaRegreso.disabled = false;
                if(inputFechaRegreso.nextElementSibling) {
                    inputFechaRegreso.nextElementSibling.disabled = false;
                    inputFechaRegreso.nextElementSibling.classList.remove('bg-gray-200', 'text-gray-400');
                }
            }
        }

        chkIda.addEventListener('change', toggleRegreso);
        chkIdaVuelta.addEventListener('change', function() { if(this.checked) chkIda.checked = false; toggleRegreso(); });

        const chkConMoni = document.getElementById('chk_con_moni');
        const chkSinMoni = document.getElementById('chk_sin_moni');
        chkConMoni.addEventListener('change', function() { if(this.checked) chkSinMoni.checked = false; });
        chkSinMoni.addEventListener('change', function() { if(this.checked) chkConMoni.checked = false; });

        // --- 5. INTERRUPTORES PRINCIPALES ---
        const checks = [
            { chk: 'check_transporte', panel: 'panel_transporte', bg: 'bg_transporte', dot: 'dot_transporte', color: 'bg-[#4EAA68]' },
            { chk: 'check_restaurante', panel: 'panel_restaurante', bg: 'bg_restaurante', dot: 'dot_restaurante', color: 'bg-[#FFDE00]' }
        ];

        checks.forEach(c => {
            const chk = document.getElementById(c.chk);
            const panel = document.getElementById(c.panel);
            const bg = document.getElementById(c.bg);
            const dot = document.getElementById(c.dot);

            chk.addEventListener('change', function() {
                if (this.checked) {
                    panel.classList.remove('hidden'); bg.classList.replace('bg-gray-300', c.color); 
                    dot.style.transform = 'translateX(100%)'; dot.innerHTML = '✅';
                } else {
                    panel.classList.add('hidden'); bg.classList.replace(c.color, 'bg-gray-300');
                    dot.style.transform = 'translateX(0)'; dot.innerHTML = '❌';
                }
            });
            if(chk.checked) chk.dispatchEvent(new Event('change'));
        });

        // --- 6. PERSISTENCIA (RECUPERACIÓN DE DATOS) ---
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

        resetValidacion();
        actualizarBarraTiempoVisual();
        if(oldEspacioId) { consultarDisponibilidad(); }
    });
</script>
@endsection