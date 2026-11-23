<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PENGLAI') | Sistema de Gestión Hotelera</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuentes del prototipo de diseño -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,500;6..96,700&family=IBM+Plex+Sans:wght@400;500;600&family=Noto+Serif+SC:wght@500;700&family=Ma+Shan+Zheng&display=swap" rel="stylesheet">
</head>
<body class="bg-[#F5F1E8] text-[#1E3A3A] min-h-screen flex flex-col font-sans">
    <!-- Barra superior -->
    <header class="bg-[#FAF8F3] border-b border-[#D8D0C0] px-6 md:px-10 py-3.5 flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('inicio') }}" class="font-serif text-xl tracking-[0.2em] font-bold text-[#1E3A3A]">
                PENGLAI
            </a>
            <span class="text-xs text-[#9E4731]">蓬莱客栈</span>
            <span class="text-[11px] px-2 py-0.5 bg-[#E6DEC8] text-[#1E3A3A] uppercase font-semibold">
                {{ auth()->user()->rol }}
            </span>
        </div>

        <nav class="flex flex-wrap items-center gap-1 text-sm">
            <a href="{{ route('inicio') }}"
               class="px-3 py-1.5 {{ request()->routeIs('inicio') ? 'bg-[#244E8A] text-white' : 'hover:bg-[#E6DEC8]' }}">
                Inicio
            </a>
            <a href="{{ route('disponibilidad.index') }}"
               class="px-3 py-1.5 {{ request()->routeIs('disponibilidad.*') ? 'bg-[#244E8A] text-white' : 'hover:bg-[#E6DEC8]' }}">
                Disponibilidad
            </a>
            <a href="{{ route('reservaciones.index') }}"
               class="px-3 py-1.5 {{ request()->routeIs('reservaciones.*') ? 'bg-[#244E8A] text-white' : 'hover:bg-[#E6DEC8]' }}">
                Reservaciones
            </a>
            <a href="{{ route('huespedes.index') }}"
               class="px-3 py-1.5 {{ request()->routeIs('huespedes.*') ? 'bg-[#244E8A] text-white' : 'hover:bg-[#E6DEC8]' }}">
                Huéspedes
            </a>
            <a href="{{ route('habitaciones.index') }}"
               class="px-3 py-1.5 {{ request()->routeIs('habitaciones.*') ? 'bg-[#244E8A] text-white' : 'hover:bg-[#E6DEC8]' }}">
                Habitaciones
            </a>
        </nav>

        <div class="flex items-center gap-3 text-xs">
            <span class="text-[#5C6672]">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="border border-[#1E3A3A] px-2.5 py-1 hover:bg-[#1E3A3A] hover:text-white transition">
                    Salir
                </button>
            </form>
        </div>
    </header>

    <!-- Mensajes de alerta -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
        @if(session('success'))
            <div class="bg-[#E6F4EA] border border-[#2E7D32] text-[#1B5E20] px-4 py-3 text-sm mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-[#FDECEA] border border-[#9E4731] text-[#9E4731] px-4 py-3 text-sm mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-[#244E8A] text-[#FAF8F3] text-center text-xs py-3 tracking-widest uppercase">
        PENGLAI · Control de Habitaciones y Reservaciones · 蓬莱客栈
    </footer>
</body>
</html>