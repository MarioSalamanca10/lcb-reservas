@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 text-left">
        <h1 class="text-3xl sm:text-4xl font-black text-[#626366]">Bandeja de Transporte</h1>
        <p class="text-gray-500 text-lg mt-2">Planificación y asignación de vehículos.</p>
    </div>

    @if(session('success'))
        <div class="bg-[#4EAA68]/20 border-l-4 border-[#4EAA68] text-[#4EAA68] p-4 mb-6 shadow-sm rounded-2xl font-bold">✅ {{ session('success') }}</div>
    @endif

    <div class="space-y-6">
        @forelse($transportes as $trans)
            <div class="bg-white rounded-3xl shadow-md border-l-8 border-[#4EAA68] overflow-hidden">
                <div class="bg-slate-50 p-4 sm:p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-[#626366]">{{ $trans->solicitud->titulo_evento ?? 'Sin título' }}</h3>
                        <p class="text-sm text-gray-500 font-medium mt-1">Solicita: <span class="text-[#4EAA68]">{{ $trans->solicitud->correo_solicitante }}</span></p>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase bg-green-100 text-green-700">
                        VIAJE {{ $trans->estado_transporte }}
                    </span>
                </div>

                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><p class="text-gray-400 font-bold uppercase text-[10px]">Responsable</p><p class="font-semibold text-gray-700">{{ $trans->nombre_responsable }} ({{ $trans->celular_responsable }})</p></div>
                            <div><p class="text-gray-400 font-bold uppercase text-[10px]">Área</p><p class="font-semibold text-gray-700">{{ $trans->area_solicitante }}</p></div>
                            <div><p class="text-gray-400 font-bold uppercase text-[10px]">Pasajeros</p><p class="font-semibold text-gray-700">{{ $trans->num_estudiantes }} Est. | {{ $trans->num_adultos }} Adult.</p></div>
                            <div><p class="text-gray-400 font-bold uppercase text-[10px]">Ruta</p><p class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($trans->fecha_hora_servicio)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($trans->fecha_hora_regreso)->format('d/m/Y H:i') }}</p></div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-sm font-medium text-gray-600">📍 <b>Origen:</b> {{ $trans->direccion_recogida }}</p>
                            <p class="text-sm font-medium text-gray-600 mt-1">🚩 <b>Destino:</b> {{ $trans->direccion_destino }}</p>
                        </div>
                        @if($trans->observaciones || $trans->necesidades_servicio)
                        <div>
                            <p class="text-gray-400 font-bold uppercase text-[10px]">Necesidades</p>
                            <p class="text-sm font-bold text-gray-700">{{ is_array($trans->necesidades_servicio) ? implode(', ', $trans->necesidades_servicio) : '' }}</p>
                            <p class="text-sm text-gray-500 italic mt-1">{{ $trans->observaciones }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                        <form action="{{ route('admin.transporte.update', $trans->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Asignación de Vehículo / Notas internas</label>
                            <textarea name="respuesta_coordinador" rows="4" placeholder="Ej: Bus placa ABC-123 asignado. Conductor: Juan Pérez. Tel: 300..." class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#4EAA68] mb-4">{{ $trans->respuesta_coordinador }}</textarea>
                            <button type="submit" class="w-full bg-[#4EAA68] text-white font-black py-3 rounded-xl hover:bg-green-600 transition-colors">Guardar Datos Logísticos</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10"><p class="text-gray-500 font-bold mt-4">No hay viajes programados.</p></div>
        @endforelse
    </div>
</div>
@endsection