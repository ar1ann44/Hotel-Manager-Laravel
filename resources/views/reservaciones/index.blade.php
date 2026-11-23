@extends('layouts.app')

@section('title', 'Reservaciones')

@section('content')
<div class="border-b border-[#D8D0C0] pb-6 mb-6 flex justify-between items-end">
    <div>
        <span class="text-xs uppercase tracking-widest text-[#9E4731] font-semibold">Módulo 02 · 预订</span>
        <h1 class="text-4xl font-serif mt-1">Reservaciones</h1>
        <p class="text-sm text-[#5C6672] mt-1">Registro de nuevas reservaciones y control de llegadas y salidas: check-in, check-out y cancelación.</p>
    </div>
    <span class="text-4xl font-serif text-[#244E8A] hidden sm:block">预订</span>
</div>

<!-- Formulario para registrar nueva reservación -->
<div class="bg-[#FAF8F3] border border-[#D8D0C0] p-6 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-serif">Registrar reservación <span class="text-xs text-[#9E4731]">登记预订</span></h2>
        <a href="{{ route('disponibilidad.index') }}" class="text-xs bg-[#244E8A] text-white px-3 py-1.5 hover:bg-[#1B3B6B]">
            Consultar disponibilidad
        </a>
    </div>

    <form method="POST" action="{{ route('reservaciones.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1">Habitación</label>
                <select name="habitacion_id" required class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
                    <option value="">Selecciona habitación...</option>
                    @foreach($habitaciones as $hab)
                        <option value="{{ $hab->id }}" {{ old('habitacion_id', $preseleccion['habitacion_id']) == $hab->id ? 'selected' : '' }}>
                            Hab. {{ $hab->numero }} · {{ $hab->tipo }} (${{ number_format($hab->precio_noche, 0) }} MXN / max {{ $hab->capacidad }}p)
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Llegada</label>
                <input type="date" name="fecha_entrada" value="{{ old('fecha_entrada', $preseleccion['fecha_entrada']) }}" required
                    class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Salida</label>
                <input type="date" name="fecha_salida" value="{{ old('fecha_salida', $preseleccion['fecha_salida']) }}" required
                    class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Número de huéspedes</label>
                <input type="number" name="numero_huespedes" value="{{ old('numero_huespedes', $preseleccion['capacidad']) }}" min="1" max="8" required
                    class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
            </div>
        </div>

        <!-- Selección o alta rápida de huésped -->
        <div class="border-t border-[#E6DEC8] pt-4">
            <div class="flex gap-6 text-xs font-semibold mb-3">
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="radio" name="modo_huesped" value="existente" checked onclick="toggleHuesped('existente')">
                    Seleccionar huésped registrado
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="radio" name="modo_huesped" value="nuevo" onclick="toggleHuesped('nuevo')">
                    Registrar huésped nuevo
                </label>
            </div>

            <div id="bloque-existente">
                <select name="huesped_id" class="w-full md:w-1/2 bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
                    <option value="">Elige un huésped del directorio...</option>
                    @foreach($huespedes as $h)
                        <option value="{{ $h->id }}" {{ old('huesped_id') == $h->id ? 'selected' : '' }}>
                            {{ $h->codigo_huesped }} — {{ $h->nombre_completo }} ({{ $h->tipo_documento }}: {{ $h->documento_identidad }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="bloque-nuevo" class="hidden grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="nombre_completo" placeholder="Nombre completo" value="{{ old('nombre_completo') }}"
                    class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
                <select name="tipo_documento" class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
                    <option value="INE">INE</option>
                    <option value="Pasaporte">Pasaporte</option>
                    <option value="Licencia">Licencia</option>
                    <option value="Otro">Otro</option>
                </select>
                <input type="text" name="documento_identidad" placeholder="No. Identificación" value="{{ old('documento_identidad') }}"
                    class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
                <input type="text" name="telefono" placeholder="Teléfono (ej. 646 555 0199)" value="{{ old('telefono') }}"
                    class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
                <input type="email" name="email" placeholder="Correo (opcional)" value="{{ old('email') }}"
                    class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-[#244E8A] hover:bg-[#1B3B6B] text-white px-6 py-2 text-sm font-medium">
                Guardar reservación
            </button>
        </div>
    </form>
</div>

<!-- Tabla de Registro de Reservaciones -->
<div class="bg-[#FAF8F3] border border-[#D8D0C0] p-6">
    <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
        <h2 class="text-xl font-serif">Registro de reservaciones <span class="text-xs text-[#9E4731]">预订记录</span></h2>
        <div class="flex flex-wrap gap-1.5 text-xs">
            <a href="{{ route('reservaciones.index', ['estado' => 'todas']) }}" class="px-3 py-1 border {{ $filtroEstado === 'todas' ? 'bg-[#1E3A3A] text-white' : 'bg-white' }}">
                Todas · {{ $conteos->sum() }}
            </a>
            @foreach(['confirmada' => 'Confirmada', 'en_casa' => 'En casa', 'finalizada' => 'Finalizada', 'cancelada' => 'Cancelada'] as $key => $label)
                <a href="{{ route('reservaciones.index', ['estado' => $key]) }}" class="px-3 py-1 border {{ $filtroEstado === $key ? 'bg-[#1E3A3A] text-white' : 'bg-white' }}">
                    {{ $label }} · {{ $conteos[$key] ?? 0 }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-[#244E8A] text-white">
                    <th class="p-3">Folio</th>
                    <th class="p-3">Huésped</th>
                    <th class="p-3">Hab.</th>
                    <th class="p-3">Llegada</th>
                    <th class="p-3">Salida</th>
                    <th class="p-3">Noches</th>
                    <th class="p-3">Total (MXN / USD)</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E6DEC8] bg-white">
                @forelse($reservaciones as $res)
                    @php
                        $noches = max(1, \Carbon\Carbon::parse($res->fecha_entrada)->diffInDays(\Carbon\Carbon::parse($res->fecha_salida)));
                        $usd = number_format($res->total_mxn * $tasas['USD'], 2);
                    @endphp
                    <tr>
                        <td class="p-3 font-bold">{{ $res->folio }}</td>
                        <td class="p-3 font-medium">{{ $res->huesped->nombre_completo }}</td>
                        <td class="p-3">{{ $res->habitacion->numero }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($res->fecha_entrada)->format('d M Y') }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($res->fecha_salida)->format('d M Y') }}</td>
                        <td class="p-3">{{ $noches }}</td>
                        <td class="p-3">
                            <strong>${{ number_format($res->total_mxn, 0) }} MXN</strong>
                            <span class="block text-[10px] text-[#5C6672]">≈ ${{ $usd }} USD</span>
                        </td>
                        <td class="p-3">
                            @if($res->estado === 'en_casa')
                                <span class="px-2 py-0.5 bg-[#244E8A] text-white">En casa</span>
                            @elseif($res->estado === 'confirmada')
                                <span class="px-2 py-0.5 bg-[#F5E1D3] text-[#9E4731]">Confirmada</span>
                            @elseif($res->estado === 'finalizada')
                                <span class="px-2 py-0.5 bg-[#E6DEC8] text-[#1E3A3A]">Finalizada</span>
                            @else
                                <span class="px-2 py-0.5 bg-[#FDECEA] text-[#9E4731]">Cancelada</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <div class="flex gap-1.5">
                                @if($res->estado === 'confirmada')
                                    <form method="POST" action="{{ route('reservaciones.checkin', $res) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="bg-[#244E8A] text-white px-2.5 py-1 hover:bg-[#1B3B6B]">Check-in</button>
                                    </form>
                                    <form method="POST" action="{{ route('reservaciones.cancelar', $res) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="border border-[#C8BFA8] px-2.5 py-1 hover:bg-[#FDECEA]">Cancelar</button>
                                    </form>
                                @elseif($res->estado === 'en_casa')
                                    <form method="POST" action="{{ route('reservaciones.checkout', $res) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="bg-[#9E4731] text-white px-2.5 py-1 hover:bg-[#7A3524]">Check-out</button>
                                    </form>
                                @else
                                    <span class="text-[#9CA3AF]">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-4 text-center text-[#5C6672]">No hay reservaciones en esta categoría.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $reservaciones->links() }}</div>
</div>

<script>
function toggleHuesped(modo) {
    document.getElementById('bloque-existente').classList.toggle('hidden', modo !== 'existente');
    document.getElementById('bloque-nuevo').classList.toggle('hidden', modo !== 'nuevo');
}
</script>
@endsection