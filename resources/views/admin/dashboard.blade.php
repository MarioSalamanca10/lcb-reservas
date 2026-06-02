@extends('layouts.app')

@section('title', 'Panel de Control')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-8">Resumen General del Colegio</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-600">
            <p class="text-sm font-bold text-gray-400 uppercase">Total Espacios</p>
            <p class="text-3xl font-black text-gray-800">{{ $totalEspacios }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-600">
            <p class="text-sm font-bold text-gray-400 uppercase">Reservas Totales</p>
            <p class="text-3xl font-black text-gray-800">{{ $totalReservas }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500">
            <p class="text-sm font-bold text-gray-400 uppercase">Reservas para Hoy</p>
            <p class="text-3xl font-black text-gray-800">{{ $reservasHoy }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-700 mb-4">Últimas Solicitudes Recibidas</h2>
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold text-gray-400 uppercase border-b border-gray-100">
                    <th class="pb-3">Docente</th>
                    <th class="pb-3">Espacio</th>
                    <th class="pb-3">Fecha Evento</th>
                    <th class="pb-3 text-right">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($ultimasReservas as $reserva)
                <tr>
                    <td class="py-4">
                        <p class="font-medium text-gray-800">{{ $reserva->titulo }}</p>
                        <p class="text-xs text-gray-400">{{ $reserva->correo_docente }}</p>
                    </td>
                    <td class="py-4 text-blue-600 font-medium">{{ $reserva->espacio->nombre }}</td>
                    <td class="py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d M, Y') }}</td>
                    <td class="py-4 text-right">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Confirmada</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection