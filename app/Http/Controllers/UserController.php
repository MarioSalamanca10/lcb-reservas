<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Solo el Super Admin puede entrar aquí
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso Exclusivo para el Super Administrador del Sistema.');
        }

        $query = User::query();

        if ($request->filled('buscar')) {
            $query->where('name', 'like', '%' . $request->buscar . '%')
                  ->orWhere('email', 'like', '%' . $request->buscar . '%');
        }

        $usuarios = $query->orderBy('name', 'asc')->paginate(15);
        
        $rolesDisponibles = [
            'docente' => 'Docente / Empleado',
            'admin_espacios' => 'Auditor de Espacios',
            'admin_transporte' => 'Coordinador de Transporte',
            'gerencia_academica' => 'Gerencia Académica',
            'gerencia_administrativa' => 'Gerencia Administrativa',
            'gerencia_operativa' => 'Gerencia Operativa',
            'cocina' => 'Chef / Cocina',
            'admin' => 'Super Administrador'
        ];

        return view('admin.usuarios.index', compact('usuarios', 'rolesDisponibles'));
    }

    public function updateRole(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403);
        }

        $request->validate(['rol' => 'required|string']);
        
        $user = User::findOrFail($id);
        $user->update(['rol' => $request->rol]);

        return back()->with('success', 'Rol actualizado exitosamente para ' . $user->name);
    }
}