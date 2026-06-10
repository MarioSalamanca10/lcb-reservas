@extends('layouts.app')
@section('title', 'Inicio - Panel Principal')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="bg-zinc-900 rounded-[2rem] p-8 md:p-12 mb-10 relative overflow-hidden shadow-2xl border border-zinc-800">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-[#4EAA68] rounded-full blur-[80px] opacity-30 pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-3">
                    Hola, <span class="text-[#4EAA68]">{{ explode(' ', auth()->user()->name)[0] ?? 'Docente' }}</span> 👋
                </h1>
                <p class="text-zinc-400 text-base md:text-lg max-w-2xl font-medium leading-relaxed">
                    Bienvenido al centro de operaciones. ¿Qué necesitas para tu próximo evento o clase? Gestiona tus espacios, transporte y alimentación desde un solo lugar.
                </p>
            </div>
        </div>
    </div>

    <h2 class="text-xl font-black text-zinc-800 mb-6 flex items-center gap-2 px-2">
        <span class="text-2xl">⚡</span> ¿Qué deseas solicitar hoy?
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        
        <a href="{{ route('reservas.create') }}" class="group bg-white rounded-3xl p-8 border border-zinc-200 shadow-sm hover:shadow-xl hover:border-indigo-300 transition-all duration-300 relative overflow-hidden flex flex-col items-center text-center hover:-translate-y-1">
            <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform shadow-inner">🏢</div>
            <h3 class="text-xl font-black text-zinc-800 mb-2">Agendar Espacio</h3>
            <p class="text-sm text-zinc-500 font-medium px-2">Reserva auditorios, canchas o salones especiales para tus clases o eventos.</p>
            <div class="mt-6 text-xs font-bold text-indigo-600 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">Iniciar Solicitud &rarr;</div>
        </a>

        <a href="{{ route('reservas.create') }}" class="group bg-white rounded-3xl p-8 border border-zinc-200 shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 relative overflow-hidden flex flex-col items-center text-center hover:-translate-y-1">
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform shadow-inner">🚌</div>
            <h3 class="text-xl font-black text-zinc-800 mb-2">Pedir Transporte</h3>
            <p class="text-sm text-zinc-500 font-medium px-2">Programa buses y vehículos para salidas pedagógicas o eventos externos.</p>
            <div class="mt-6 text-xs font-bold text-blue-600 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">Iniciar Solicitud &rarr;</div>
        </a>

        <a href="{{ route('reservas.create') }}" class="group bg-white rounded-3xl p-8 border border-zinc-200 shadow-sm hover:shadow-xl hover:border-orange-300 transition-all duration-300 relative overflow-hidden flex flex-col items-center text-center hover:-translate-y-1">
            <div class="w-20 h-20 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center text-4xl mb-6 group-hover:scale-110 transition-transform shadow-inner">🍽️</div>
            <h3 class="text-xl font-black text-zinc-800 mb-2">Alimentación</h3>
            <p class="text-sm text-zinc-500 font-medium px-2">Solicita refrigerios, almuerzos o dietas especiales para tus invitados.</p>
            <div class="mt-6 text-xs font-bold text-orange-600 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">Iniciar Solicitud &rarr;</div>
        </a>
        
    </div>

    <a href="{{ route('reservas.index') }}" class="group bg-gradient-to-r from-zinc-800 to-zinc-900 rounded-[2rem] p-8 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col md:flex-row items-center justify-between gap-6 border border-zinc-700 hover:border-zinc-500 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
        
        <div class="flex items-center gap-6 relative z-10">
            <div class="w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center text-3xl group-hover:rotate-12 transition-transform border border-white/5">📭</div>
            <div>
                <h3 class="text-xl font-black text-white mb-1">Rastrear mis Solicitudes</h3>
                <p class="text-sm text-zinc-400 font-medium">Revisa si Gerencia o Logística ya aprobaron o asignaron recursos a tus pedidos.</p>
            </div>
        </div>
        <div class="relative z-10 bg-white/10 backdrop-blur-sm border border-white/20 text-white px-6 py-3 rounded-xl font-bold text-sm group-hover:bg-white group-hover:text-zinc-900 transition-colors whitespace-nowrap shadow-sm">
            Ver Historial &rarr;
        </div>
    </a>

</div>
@endsection