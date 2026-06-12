@extends('layouts.app')
@section('title', 'Gestión de Usuarios')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-black text-zinc-800 tracking-tight">Gestión de Usuarios y Roles</h1>
            <p class="text-zinc-500 text-sm mt-1">Administración de permisos del personal sincronizado con Microsoft Azure AD.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 shadow-sm rounded-xl font-bold">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-3 rounded-xl shadow-sm border border-zinc-200 mb-6">
        <form action="{{ route('admin.usuarios.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o correo..." class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-sm font-bold focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-zinc-800 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-zinc-700 transition-colors shadow-sm">Buscar</button>
            @if(request()->has('buscar'))
                <a href="{{ route('admin.usuarios.index') }}" class="bg-zinc-100 text-zinc-500 px-4 py-2 rounded-lg text-sm font-bold hover:bg-zinc-200 transition-colors">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-800 text-zinc-100 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="p-4 w-1/3">Usuario (Azure AD)</th>
                        <th class="p-4 w-1/3">Rol Actual</th>
                        <th class="p-4 w-1/3 text-right">Modificar Permisos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @foreach($usuarios as $user)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="p-4 align-middle">
                                <h3 class="font-black text-zinc-800 text-base">{{ $user->name }}</h3>
                                <p class="text-xs text-blue-600 font-bold mt-0.5">{{ $user->email }}</p>
                            </td>
                            <td class="p-4 align-middle">
                                @php
                                    $rolColor = $user->rol == 'admin' ? 'bg-purple-100 text-purple-800 border-purple-200' : 
                                                ($user->rol == 'docente' ? 'bg-zinc-100 text-zinc-600 border-zinc-200' : 'bg-blue-100 text-blue-800 border-blue-200');
                                @endphp
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border shadow-sm {{ $rolColor }}">
                                    {{ $rolesDisponibles[$user->rol] ?? $user->rol }}
                                </span>
                            </td>
                            <td class="p-4 align-middle text-right">
                                <form action="{{ route('admin.usuarios.updateRole', $user->id) }}" method="POST" class="flex items-center justify-end gap-2" onsubmit="return confirm('¿Confirma el cambio de rol para este usuario?');">
                                    @csrf @method('PATCH')
                                    <select name="rol" class="bg-white border border-zinc-300 rounded-md px-2 py-1.5 text-xs font-bold text-zinc-700 focus:ring-2 focus:ring-blue-500 w-48">
                                        @foreach($rolesDisponibles as $key => $label)
                                            <option value="{{ $key }}" {{ $user->rol == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-bold hover:bg-blue-700 transition-colors shadow-sm" {{ $user->id == auth()->id() ? 'disabled title="No puedes cambiar tu propio rol"' : '' }}>
                                        Actualizar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($usuarios->hasPages())
            <div class="p-4 border-t border-zinc-200 bg-zinc-50">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection