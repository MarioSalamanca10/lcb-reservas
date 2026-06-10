<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspacioRecursoController;
use App\Http\Controllers\ReservaFisicaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TorreController;

Route::get('/', function () {
    return view('welcome');
})->name('login');

// ==========================================
// RUTAS PÚBLICAS (No requieren sesión)
// ==========================================
Route::get('/login/microsoft', [AuthController::class, 'redirect'])->name('login.microsoft');
Route::get('/auth/microsoft/callback', [AuthController::class, 'callback']);

// 🚨 PUERTA TRASERA DE DESARROLLO (SELECTOR DE ROLES) 🚨
Route::get('/dev-login/{role?}', function ($role = 'admin') {
    $email = $role . '@localhost.com'; // Crea correos como admin_transporte@localhost.com
    $user = \App\Models\User::firstOrCreate(
        ['email' => $email],
        ['name' => 'Usuario ' . strtoupper($role), 'rol' => $role, 'password' => bcrypt('123456')]
    );

    auth()->login($user);
    return redirect()->route('reservas.create')->with('success', "Logueado exitosamente con rol: $role");
});

// ==========================================
// ZONA PROTEGIDA: Solo usuarios logueados
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rutas Generales de Reservas (Para Docentes y Admin)
    Route::get('/mis-reservas', [ReservaFisicaController::class, 'index'])->name('reservas.index');
    Route::get('/reservas/nueva', [ReservaFisicaController::class, 'create'])->name('reservas.create');
    Route::post('/reservas', [ReservaFisicaController::class, 'store'])->name('reservas.store');
    Route::delete('/reservas/{reserva}', [ReservaFisicaController::class, 'destroy'])->name('reservas.destroy');

    // Rutas para Servicios Independientes (Docentes/Usuarios)
    Route::get('/servicios/transporte/nuevo', [\App\Http\Controllers\ServicioAdicionalController::class, 'createTransporte'])->name('servicios.transporte.create');
    Route::post('/servicios/transporte', [\App\Http\Controllers\ServicioAdicionalController::class, 'storeTransporte'])->name('servicios.transporte.store');

    Route::get('/servicios/restaurante/nuevo', [\App\Http\Controllers\ServicioAdicionalController::class, 'createRestaurante'])->name('servicios.restaurante.create');
    Route::post('/servicios/restaurante', [\App\Http\Controllers\ServicioAdicionalController::class, 'storeRestaurante'])->name('servicios.restaurante.store');
    
    // Rutas para la Encuesta Obligatoria
    Route::get('/reservas/{reserva}/encuesta', [ReservaFisicaController::class, 'showEncuestaForm'])->name('reservas.encuesta.create');
    Route::post('/reservas/{reserva}/encuesta', [ReservaFisicaController::class, 'storeEncuesta'])->name('reservas.encuesta.store');

    // ==========================================
    // ZONA LOGÍSTICA: Transporte y Restaurante
    // (La seguridad está dentro de cada controlador)
    // ==========================================
    Route::get('/admin/transporte', [\App\Http\Controllers\TransporteController::class, 'index'])->name('admin.transporte.index');
    Route::patch('/admin/transporte/{id}', [\App\Http\Controllers\TransporteController::class, 'update'])->name('admin.transporte.update');

    Route::get('/admin/restaurante', [\App\Http\Controllers\RestauranteController::class, 'index'])->name('admin.restaurante.index');
    Route::patch('/admin/restaurante/{id}', [\App\Http\Controllers\RestauranteController::class, 'update'])->name('admin.restaurante.update');

    // RUTA EXCLUSIVA PARA EL STAFF DE COCINA

    Route::get('/cocina/tablero', [\App\Http\Controllers\CocinaController::class, 'index'])->name('cocina.index');
    Route::patch('/cocina/{id}/finalizar', [\App\Http\Controllers\CocinaController::class, 'finalizar'])->name('cocina.finalizar'); 


    // ==========================================
    // ZONA VIP SÚPER ADMIN: Configuración del Sistema
    // ==========================================
    Route::middleware([\App\Http\Middleware\CheckAdmin::class])->group(function () {

        Route::post('/espacios/importar', [\App\Http\Controllers\EspacioRecursoController::class, 'importar'])->name('espacios.importar');

        // Rutas del Tablero y Reservas Generales
        Route::get('/admin/dashboard', [EspacioRecursoController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/reservas', [ReservaFisicaController::class, 'adminIndex'])->name('admin.reservas.index');

        // PANEL INFORMATIVO PARA ADMIN ESPACIOS
        Route::get('/admin/reservas', [\App\Http\Controllers\AdminReservaEspacioController::class, 'index'])->name('admin.reservas.index');

        // Rutas de Torres
        Route::get('/torres', [TorreController::class, 'index'])->name('torres.index');
        Route::post('/torres', [TorreController::class, 'store'])->name('torres.store');
        Route::delete('/torres/{torre}', [TorreController::class, 'destroy'])->name('torres.destroy');

        // Rutas de Espacios
        Route::get('/espacios', [EspacioRecursoController::class, 'index'])->name('espacios.index');
        Route::get('/espacios/crear', [EspacioRecursoController::class, 'create'])->name('espacios.create');
        Route::post('/espacios', [EspacioRecursoController::class, 'store'])->name('espacios.store');
        Route::get('/espacios/{espacio}/editar', [EspacioRecursoController::class, 'edit'])->name('espacios.edit');
        Route::put('/espacios/{espacio}', [EspacioRecursoController::class, 'update'])->name('espacios.update');
        Route::delete('/espacios/{espacio}', [EspacioRecursoController::class, 'destroy'])->name('espacios.destroy');
    });

});