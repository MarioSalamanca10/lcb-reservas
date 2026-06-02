<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liceo de Colombia - Reservas</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-blue-900 min-h-screen flex items-center justify-center">

    <div class="bg-white p-10 rounded-2xl shadow-2xl max-w-md w-full text-center">
        <!-- Aquí puedes poner el logo del colegio después -->
        <div class="w-24 h-24 bg-blue-100 rounded-full mx-auto flex items-center justify-center mb-6">
            <span class="text-4xl">🏫</span>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Portal de Reservas</h1>
        <p class="text-gray-500 mb-8">Liceo de Colombia Bilingüe</p>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('login.microsoft') }}" class="w-full flex items-center justify-center bg-white border border-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 transition shadow-sm">
            <!-- Icono de Microsoft -->
            <svg class="w-5 h-5 mr-3" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg"><path d="m10 0h-10v10h10zm11 0h-10v10h10zm-11 11h-10v10h10zm11 0h-10v10h10z" fill="#f25022"/><path d="m10 0h-10v10h10z" fill="#f25022"/><path d="m21 0h-10v10h10z" fill="#7fba00"/><path d="m10 11h-10v10h10z" fill="#00a4ef"/><path d="m21 11h-10v10h10z" fill="#ffb900"/></svg>
            Ingresar con Microsoft
        </a>
    </div>

</body>
</html>