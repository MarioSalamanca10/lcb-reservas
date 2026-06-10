<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liceo de Colombia - Portal Operativo</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-white flex h-screen overflow-hidden antialiased">

    <div class="w-full md:w-1/2 lg:w-1/3 flex flex-col justify-between px-8 py-10 md:px-12 lg:px-16 relative z-10 bg-white shadow-[10px_0_30px_rgba(0,0,0,0.05)] overflow-y-auto">
        
        <div class="mt-8 flex flex-col items-center md:items-start text-center md:text-left">
            <img src="{{ asset('images/LOGOLCB_sinbanderin.png') }}" alt="LCB Logo" class="h-20 w-auto mb-10 drop-shadow-sm">
            
            <h1 class="text-3xl font-black text-zinc-900 tracking-tight mb-2">Portal de Servicios</h1>
            <p class="text-zinc-500 text-sm font-medium">Gestión integral de espacios físicos, rutas escolares y alimentación del Liceo de Colombia Bilingüe.</p>
        </div>

        <div class="mt-12 mb-12 flex-1 flex flex-col justify-center">
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 mb-6 rounded-xl font-bold flex items-center gap-3 text-sm shadow-sm">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 text-center shadow-inner mb-6">
                <span class="text-5xl mb-4 block opacity-80">🔐</span>
                <p class="text-sm text-zinc-600 font-medium mb-4">Acceso exclusivo para personal administrativo y docente autorizado.</p>
                
                <a href="{{ route('login.microsoft') }}" class="w-full flex items-center justify-center bg-white border border-zinc-300 text-zinc-800 font-bold py-4 px-4 rounded-xl hover:bg-zinc-50 hover:border-blue-400 hover:shadow-lg hover:shadow-blue-500/10 transition-all group">
                    <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg">
                        <path d="m10 0h-10v10h10zm11 0h-10v10h10zm-11 11h-10v10h10zm11 0h-10v10h10z" fill="#f25022"/>
                        <path d="m10 0h-10v10h10z" fill="#f25022"/>
                        <path d="m21 0h-10v10h10z" fill="#7fba00"/>
                        <path d="m10 11h-10v10h10z" fill="#00a4ef"/>
                        <path d="m21 11h-10v10h10z" fill="#ffb900"/>
                    </svg>
                    Ingresar con Cuenta Escolar
                </a>
            </div>

            <p class="text-xs text-zinc-400 text-center font-medium px-4">Al ingresar, aceptas las políticas de uso de los sistemas de información institucionales.</p>
        </div>

        <div class="mt-auto pt-8 border-t border-zinc-100">
            <p class="text-[10px] text-zinc-400 font-bold text-center">© {{ date('Y') }} Liceo de Colombia Bilingüe. IT Dept.</p>
        </div>
    </div>

    <div class="hidden md:block md:w-1/2 lg:w-2/3 relative bg-zinc-900 overflow-hidden shadow-inner">
        
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center opacity-50 transition-transform duration-[30s] hover:scale-110"></div>
        
        <div class="absolute inset-0 bg-gradient-to-br from-[#18181b]/95 via-[#18181b]/70 to-[#4EAA68]/80 mix-blend-multiply"></div>
        
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>

        <div class="absolute inset-0 flex flex-col items-center justify-center p-12 text-center">
            <div class="bg-black/30 backdrop-blur-md border border-white/10 p-8 rounded-3xl">
                <h2 class="text-4xl lg:text-5xl font-black text-white tracking-tight mb-4 drop-shadow-lg">
                    Sincronización <span class="text-[#4EAA68]">Total</span>
                </h2>
                <p class="text-zinc-200 text-base lg:text-lg max-w-lg leading-relaxed font-medium">
                    Una plataforma diseñada para optimizar los recursos físicos, logísticos y de bienestar del Liceo de Colombia Bilingüe.
                </p>
            </div>
        </div>
    </div>

</body>
</html>