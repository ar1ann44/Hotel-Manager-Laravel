<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | PENGLAI Hotel Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F5F1E8] text-[#1E3A3A] min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="w-full max-w-md bg-[#FAF8F3] border border-[#D8D0C0] p-8 shadow-sm">
        <div class="text-center border-b border-[#D8D0C0] pb-6 mb-6">
            <p class="text-xs tracking-[0.25em] text-[#9E4731] uppercase font-semibold">Sistema de Gestión Hotelera</p>
            <h1 class="text-4xl font-serif tracking-widest text-[#1E3A3A] mt-2">PENGLAI</h1>
            <p class="text-xs text-[#6B7280] mt-1">蓬莱客栈 · Control de Habitaciones y Reservaciones</p>
        </div>

        @if ($errors->any())
            <div class="bg-[#FDECEA] border border-[#9E4731] text-[#9E4731] px-4 py-3 text-sm mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold mb-1">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="admin@penglai.com"
                    class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm focus:outline-none focus:border-[#244E8A]">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold mb-1">Contraseña</label>
                <input type="password" name="password" required
                    placeholder="••••••••"
                    class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm focus:outline-none focus:border-[#244E8A]">
            </div>

            <button type="submit"
                class="w-full bg-[#244E8A] hover:bg-[#1B3B6B] text-white py-2.5 text-sm font-medium tracking-wide transition">
                Entrar al sistema
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-[#D8D0C0] text-xs text-[#5C6672] space-y-1">
            <p class="font-semibold text-[#1E3A3A]">Cuentas de demostración:</p>
            <p>• <strong>Admin:</strong> admin@penglai.com / admin123</p>
            <p>• <strong>Recepción:</strong> recepcion@penglai.com / recepcion123</p>
        </div>
    </div>
</body>
</html>