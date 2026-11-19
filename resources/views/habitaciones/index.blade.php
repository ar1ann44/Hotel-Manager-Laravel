@extends('layouts.app')

@section('title', 'Habitaciones')

@section('content')
<div class="border-b border-[#D8D0C0] pb-6 mb-6 flex justify-between items-end">
    <div>
        <span class="text-xs uppercase tracking-widest text-[#9E4731] font-semibold">Módulo 04 · 客房</span>
        <h1 class="text-4xl font-serif mt-1">Habitaciones</h1>
        <p class="text-sm text-[#5C6672] mt-1">Catálogo de habitaciones y tarifas; desde aquí se marca la limpieza y el mantenimiento.</p>
    </div>
    <span class="text-4xl font-serif text-[#244E8A] hidden sm:block">客房</span>
</div>

<!-- Tipos y tarifas con conversión Frankfurter API -->
<div class="flex justify-between items-center mb-3">
    <h2 class="text-lg font-serif">Tipos y tarifas <span class="text-xs text-[#9E4731]">房型房价</span></h2>
    <span class="text-xs text-[#5C6672]">Conversión informativa: <strong>{{ $tasas['fuente'] }}</strong></span>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    @foreach(['Estándar' => ['precio' => 1200, 'cap' => 2], 'Doble' => ['precio' => 1800, 'cap' => 4], 'Suite' => ['precio' => 3200, 'cap' => 4]] as $tipoNombre => $info)
        @php
            $habEjemplo = $todas->where('tipo', $tipoNombre)->first();
            $precio = $habEjemplo ? $habEjemplo->precio_noche : $info['precio'];
            $cap = $habEjemplo ? $habEjemplo->capacidad : $info['cap'];
            $count = $todas->where('tipo', $tipoNombre)->count();
        @endphp
        <div class="bg-[#244E8A] text-white p-5">
            <h3 class="text-2xl font-serif">{{ $tipoNombre }}</h3>
            <p class="text-sm mt-2">${{ number_format($precio, 0) }} MXN / noche · hasta {{ $cap }} personas</p>
            <p class="text-xs text-[#DCE6F5] mt-1">
                ≈ ${{ number_format($precio * $tasas['USD'], 2) }} USD · €{{ number_format($precio * $tasas['EUR'], 2) }} EUR
            </p>
            <p class="text-xs opacity-80 mt-2">{{ $count }} habitaciones en catálogo</p>
        </div>
    @endforeach
</div>

<!-- Formulario exclusivo de Administrador: Crear nueva habitación -->
@if(auth()->user()->esAdmin())
    <details class="bg-[#FAF8F3] border border-[#9E4731] p-4 mb-8">
        <summary class="cursor-pointer text-sm font-semibold text-[#9E4731]">
            + Agregar nueva habitación al catálogo (Solo Administrador)
        </summary>
        <form method="POST" action="{{ route('habitaciones.store') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 mt-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-semibold mb-1">Número</label>
                <input type="text" name="numero" placeholder="Ej. 305" required class="w-full bg-white border border-[#C8BFA8] px-2.5 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Piso</label>
                <input type="number" name="piso" value="3" min="1" max="10" required class="w-full bg-white border border-[#C8BFA8] px-2.5 py-1.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Tipo</label>
                <select name="tipo" class="w-full bg-white border border-[#C8BFA8] px-2.5 py-1.5 text-sm">
                    <option value="Estándar">Estándar</option>
                    <option value="Doble">Doble</option>
                    <option value="Suite">Suite</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Capacidad / Precio MXN</label>
                <div class="flex gap-1">
                    <input type="number" name="capacidad" value="2" min="1" max="8" required class="w-1/3 bg-white border border-[#C8BFA8] px-2 py-1.5 text-sm">
                    <input type="number" name="precio_noche" value="1200" step="50" required class="w-2/3 bg-white border border-[#C8BFA8] px-2 py-1.5 text-sm">
                </div>
            </div>
            <button type="submit" class="bg-[#1E3A3A] text-white py-1.5 px-3 text-sm">Guardar</button>
        </form>
    </details>
@endif

<!-- Filtros de inventario -->
<div class="flex flex-wrap justify-between items-center gap-3 mb-4">
    <h2 class="text-lg font-serif">Inventario <span class="text-xs text-[#9E4731]">客房清单</span></h2>
    <div class="flex flex-wrap gap-1.5 text-xs">
        <a href="{{ route('habitaciones.index', ['estado' => 'todas']) }}" class="px-3 py-1 border {{ $filtroEstado === 'todas' ? 'bg-[#1E3A3A] text-white' : 'bg-white' }}">
            Todas · {{ $todas->count() }}
        </a>
        @foreach(['disponible', 'ocupada', 'reservada', 'limpieza', 'mantenimiento'] as $est)
            <a href="{{ route('habitaciones.index', ['estado' => $est]) }}" class="px-3 py-1 border capitalize {{ $filtroEstado === $est ? 'bg-[#1E3A3A] text-white' : 'bg-white' }}">
                {{ $est }} · {{ $todas->where('estado', $est)->count() }}
            </a>
        @endforeach
    </div>
</div>

<!-- Tarjetas de habitaciones -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($habitaciones as $hab)
        <div class="bg-[#FAF8F3] border border-[#D8D0C0] p-4 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start">
                    <span class="text-2xl font-serif font-bold">{{ $hab->numero }}</span>
                    <span class="text-[11px] px-2 py-0.5 uppercase font-semibold bg-[#E6DEC8]">{{ $hab->estado }}</span>
                </div>
                <p class="text-sm font-semibold mt-1">{{ $hab->tipo }}</p>
                <p class="text-xs text-[#5C6672] mt-1">
                    Piso {{ $hab->piso }} · {{ $hab->capacidad }} personas · ${{ number_format($hab->precio_noche, 0) }} MXN/noche
                </p>
                <p class="text-[11px] text-[#244E8A]">
                    ≈ ${{ number_format($hab->precio_noche * $tasas['USD'], 2) }} USD · €{{ number_format($hab->precio_noche * $tasas['EUR'], 2) }} EUR
                </p>

                @if($hab->reservaciones->first())
                    <p class="text-xs text-[#9E4731] mt-2">
                        Huésped: {{ $hab->reservaciones->first()->huesped->nombre_completo }}
                    </p>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-[#E6DEC8] space-y-2">
                @if($hab->estado === 'limpieza')
                    <form method="POST" action="{{ route('habitaciones.estado', $hab) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="disponible">
                        <button type="submit" class="w-full bg-[#1E3A3A] text-white py-1.5 text-xs">Marcar como limpia</button>
                    </form>
                @endif

                @if($hab->estado === 'mantenimiento')
                    <form method="POST" action="{{ route('habitaciones.estado', $hab) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="disponible">
                        <button type="submit" class="w-full bg-[#244E8A] text-white py-1.5 text-xs">Habilitar</button>
                    </form>
                @elseif(!in_array($hab->estado, ['ocupada', 'reservada']))
                    <form method="POST" action="{{ route('habitaciones.estado', $hab) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="mantenimiento">
                        <button type="submit" class="w-full border border-[#C8BFA8] bg-white hover:bg-[#E6DEC8] py-1.5 text-xs">
                            Enviar a mantenimiento
                        </button>
                    </form>
                @endif

                @if(auth()->user()->esAdmin())
                    <details class="text-xs pt-1">
                        <summary class="cursor-pointer text-[#244E8A] underline">Editar tarifa / tipo (Admin)</summary>
                        <form method="POST" action="{{ route('habitaciones.update', $hab) }}" class="mt-2 space-y-1.5">
                            @csrf @method('PUT')
                            <select name="tipo" class="w-full border px-2 py-1 bg-white">
                                <option value="Estándar" {{ $hab->tipo === 'Estándar' ? 'selected' : '' }}>Estándar</option>
                                <option value="Doble" {{ $hab->tipo === 'Doble' ? 'selected' : '' }}>Doble</option>
                                <option value="Suite" {{ $hab->tipo === 'Suite' ? 'selected' : '' }}>Suite</option>
                            </select>
                            <div class="flex gap-1">
                                <input type="number" name="capacidad" value="{{ $hab->capacidad }}" class="w-1/3 border px-2 py-1 bg-white">
                                <input type="number" name="precio_noche" value="{{ $hab->precio_noche }}" class="w-2/3 border px-2 py-1 bg-white">
                            </div>
                            <button type="submit" class="w-full bg-[#244E8A] text-white py-1">Actualizar</button>
                        </form>
                    </details>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection