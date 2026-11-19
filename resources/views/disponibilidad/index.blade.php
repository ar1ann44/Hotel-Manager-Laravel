@extends('layouts.app')

@section('title', 'Disponibilidad y ocupación')

@section('content')
<div class="border-b border-[#D8D0C0] pb-6 mb-6 flex justify-between items-end">
    <div>
        <span class="text-xs uppercase tracking-widest text-[#9E4731] font-semibold">Módulo 01 · 房态</span>
        <h1 class="text-4xl font-serif mt-1">Disponibilidad y ocupación</h1>
        <p class="text-sm text-[#5C6672] mt-1">Estado de cada habitación hoy y búsqueda de habitaciones libres para unas fechas.</p>
    </div>
    <span class="text-4xl font-serif text-[#244E8A] hidden sm:block">房态</span>
</div>

<!-- Tarjetas resumen -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-[#FAF8F3] border border-[#D8D0C0] p-4">
        <p class="text-xs uppercase text-[#5C6672]">Ocupación</p>
        <p class="text-3xl font-serif font-bold mt-1">{{ $porcentajeOcupacion }}%</p>
        <p class="text-xs text-[#5C6672] mt-1">{{ $ocupadas }} de {{ $vendibles }} habitaciones vendibles</p>
    </div>
    <div class="bg-[#FAF8F3] border border-[#D8D0C0] p-4">
        <p class="text-xs uppercase text-[#5C6672]">Disponibles</p>
        <p class="text-3xl font-serif font-bold mt-1">{{ $disponiblesHoy }}</p>
        <p class="text-xs text-[#5C6672] mt-1">listas para asignar hoy</p>
    </div>
    <div class="bg-[#FAF8F3] border border-[#D8D0C0] p-4">
        <p class="text-xs uppercase text-[#5C6672]">Reservadas hoy</p>
        <p class="text-3xl font-serif font-bold mt-1">{{ $reservadasHoy }}</p>
        <p class="text-xs text-[#5C6672] mt-1">huéspedes por llegar</p>
    </div>
    <div class="bg-[#FAF8F3] border border-[#D8D0C0] p-4">
        <p class="text-xs uppercase text-[#5C6672]">Limpieza / Mant.</p>
        <p class="text-3xl font-serif font-bold mt-1">{{ $limpiezaCount }} / {{ $mantenimientoCount }}</p>
        <p class="text-xs text-[#5C6672] mt-1">fuera de venta temporal</p>
    </div>
</div>

<!-- Mapa de ocupación por piso -->
<div class="bg-[#FAF8F3] border border-[#D8D0C0] p-6 mb-8">
    <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
        <h2 class="text-xl font-serif">Mapa de ocupación <span class="text-xs text-[#9E4731]">房态图</span></h2>
        <div class="flex flex-wrap gap-2 text-[11px]">
            <span class="px-2 py-0.5 bg-[#DCE6F5] text-[#1E3A3A]">Disponible</span>
            <span class="px-2 py-0.5 bg-[#244E8A] text-white">Ocupada</span>
            <span class="px-2 py-0.5 bg-[#F5E1D3] text-[#9E4731]">Reservada</span>
            <span class="px-2 py-0.5 bg-[#F3E5C2] text-[#7A5901]">Limpieza</span>
            <span class="px-2 py-0.5 bg-[#E2DFD7] text-[#4B5563]">Mantenimiento</span>
        </div>
    </div>

    @foreach($todasHabitaciones->groupBy('piso') as $piso => $habsPiso)
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-3 items-stretch">
            <div class="flex flex-col justify-center text-xs text-[#5C6672] font-semibold">
                <span>Piso {{ $piso }}</span>
                <span class="text-[11px] text-[#9E4731]">{{ $piso }}楼</span>
            </div>
            @foreach($habsPiso as $hab)
                @php
                    $cardClass = match($hab->estado) {
                        'disponible' => 'bg-[#DCE6F5] text-[#1E3A3A] border-[#B8CBE8]',
                        'ocupada' => 'bg-[#244E8A] text-white border-[#1B3B6B]',
                        'reservada' => 'bg-[#F5E1D3] text-[#1E3A3A] border-[#E0BFA8]',
                        'limpieza' => 'bg-[#F3E5C2] text-[#1E3A3A] border-[#DFC78E]',
                        'mantenimiento' => 'bg-[#E2DFD7] text-[#4B5563] border-[#C8C3B8]',
                    };
                    $reservaActiva = $hab->reservaciones->first();
                @endphp
                <div class="border p-3 {{ $cardClass }}">
                    <p class="text-xl font-serif font-bold">{{ $hab->numero }}</p>
                    <p class="text-[11px] opacity-85">{{ $hab->tipo }}</p>
                    <p class="text-xs font-semibold capitalize mt-1">{{ $hab->estado }}</p>
                    @if($reservaActiva)
                        <p class="text-[10px] mt-1 opacity-90">
                            {{ $reservaActiva->huesped->nombre_completo }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>

<!-- Formulario Consultar Disponibilidad -->
<div class="bg-[#FAF8F3] border border-[#D8D0C0] p-6">
    <h2 class="text-xl font-serif mb-4">Consultar disponibilidad <span class="text-xs text-[#9E4731]">查询空房</span></h2>
    <form method="GET" action="{{ route('disponibilidad.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <input type="hidden" name="consultar" value="1">
        <div>
            <label class="block text-xs font-semibold mb-1">Llegada</label>
            <input type="date" name="fecha_entrada" value="{{ $fechaEntrada }}" required
                class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Salida</label>
            <input type="date" name="fecha_salida" value="{{ $fechaSalida }}" required
                class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Tipo de habitación</label>
            <select name="tipo" class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
                <option value="Todas" {{ $tipo === 'Todas' ? 'selected' : '' }}>Todas</option>
                <option value="Estándar" {{ $tipo === 'Estándar' ? 'selected' : '' }}>Estándar</option>
                <option value="Doble" {{ $tipo === 'Doble' ? 'selected' : '' }}>Doble</option>
                <option value="Suite" {{ $tipo === 'Suite' ? 'selected' : '' }}>Suite</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Huéspedes</label>
            <input type="number" name="capacidad" value="{{ $capacidad }}" min="1" max="6" required
                class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-[#244E8A] hover:bg-[#1B3B6B] text-white py-2 px-4 text-sm font-medium">
            Consultar
        </button>
    </form>

    @if(!is_null($habitacionesLibres))
        <div class="mt-6 pt-6 border-t border-[#D8D0C0]">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-serif text-lg">
                    Resultados disponibles ({{ $habitacionesLibres->count() }} habitaciones)
                </h3>
                <span class="text-[11px] text-[#5C6672]">Divisas: {{ $tasas['fuente'] }}</span>
            </div>

            @if($habitacionesLibres->isEmpty())
                <p class="text-sm text-[#9E4731]">No hay habitaciones disponibles para ese rango de fechas y capacidad.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($habitacionesLibres as $libre)
                        @php
                            $noches = \Carbon\Carbon::parse($fechaEntrada)->diffInDays(\Carbon\Carbon::parse($fechaSalida));
                            $totalMxn = $libre->precio_noche * max(1, $noches);
                            $totalUsd = number_format($totalMxn * $tasas['USD'], 2);
                            $totalEur = number_format($totalMxn * $tasas['EUR'], 2);
                        @endphp
                        <div class="bg-white border border-[#B8CBE8] p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-serif font-bold">Hab. {{ $libre->numero }}</span>
                                    <span class="text-xs px-2 py-0.5 bg-[#DCE6F5]">{{ $libre->tipo }}</span>
                                </div>
                                <p class="text-xs text-[#5C6672] mt-1">Piso {{ $libre->piso }} · Hasta {{ $libre->capacidad }} personas</p>
                                <p class="text-sm font-bold text-[#1E3A3A] mt-3">
                                    ${{ number_format($totalMxn, 2) }} MXN <span class="text-xs font-normal">({{ $noches }} noches)</span>
                                </p>
                                <p class="text-[11px] text-[#244E8A]">
                                    ≈ ${{ $totalUsd }} USD · €{{ $totalEur }} EUR
                                </p>
                            </div>
                            <a href="{{ route('reservaciones.index', ['habitacion_id' => $libre->id, 'fecha_entrada' => $fechaEntrada, 'fecha_salida' => $fechaSalida, 'capacidad' => $capacidad]) }}"
                               class="mt-4 block text-center bg-[#244E8A] text-white py-1.5 text-xs font-medium hover:bg-[#1B3B6B]">
                                Seleccionar para reservar
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
@endsection