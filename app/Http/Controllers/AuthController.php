<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. Envía al usuario a la página de Microsoft
    public function redirect()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    // 2. Microsoft nos devuelve al usuario aquí después de iniciar sesión
    public function callback()
    {
        try {
            // Obtenemos los datos de Microsoft
            $microsoftUser = Socialite::driver('microsoft')->user();

            // Buscamos si el usuario ya existe en nuestra base de datos, si no, lo creamos
            $user = User::updateOrCreate([
                'email' => $microsoftUser->getEmail(),
            ], [
                'name' => $microsoftUser->getName(),
                'azure_id' => $microsoftUser->getId(),
                // Le ponemos una contraseña aleatoria gigante porque el login real lo hace Microsoft
                'password' => bcrypt(Str::random(24)) 
            ]);

            // Iniciamos sesión en Laravel con este usuario
            Auth::login($user);

            // Magia de Roles: Si eres admin, te mando a los espacios. Si es profe, a su formulario.
            if ($user->rol === 'admin') {
                return redirect()->route('espacios.index');
            } else {
                return redirect()->route('reservas.create');
            }

        } catch (\Exception $e) {
            // Si el profe cancela o hay un error, lo devolvemos al inicio
            return redirect('/')->with('error', 'Hubo un problema iniciando sesión con Microsoft.');
        }
    }

    // 3. Función para cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
