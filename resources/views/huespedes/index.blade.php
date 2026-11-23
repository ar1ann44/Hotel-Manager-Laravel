@extends('layouts.app')

@section('title', 'Huéspedes')

@section('content')
<div class="border-b border-[#D8D0C0] pb-6 mb-6 flex justify-between items-end">
    <div>
        <span class="text-xs uppercase tracking-widest text-[#9E4731] font-semibold">Módulo 03 · 宾客</span>
        <h1 class="text-4xl font-serif mt-1">Huéspedes</h1>
        <p class="text-sm text-[#5C6672] mt-1">Directorio de huéspedes: datos de contacto, identificación, historial de estancias y situación actual.</p>
    </div>
    <span class="text-4xl font-serif text-[#244E8A] hidden sm:block">宾客</span>
</div>

<!-- Tarjetas métricas -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-[#FAF8F3] border border-[#D8D0C0] p-4">
        <p class="text-xs uppercase text-[#5C6672]">Registrados <span class="text-[#9E4731]">宾客</span></p>
        <p class="text-3xl font-serif font-bold mt-1">{{ $totalRegistrados }}</p>
    </div>
    <div class="bg-[#FAF8F3] border border-[#D8D0C0] p-4">
        <p class="text-xs uppercase text-[#5C6672]">En casa <span class="text-[#9E4731]">在住</span></p>
        <p class="text-3xl font-serif font-bold mt-1">{{ $enCasaCount }}</p>
    </div>
    <div class="bg-[#FAF8F3] border border-[#D8D0C0] p-4">
        <p class="text-xs uppercase text-[#5C6672]">Con reservación <span class="text-[#9E4731]">已预订</span></p>
        <p class="text-3xl font-serif font-bold mt-1">{{ $conReservaCount }}</p>
    </div>
</div>

<!-- Alta rápida de huésped -->
<details class="bg-[#FAF8F3] border border-[#D8D0C0] p-4 mb-6">
    <summary class="cursor-pointer text-sm font-semibold text-[#244E8A]">+ Registrar nuevo huésped manualmente</summary>
    <form method="POST" action="{{ route('huespedes.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-4">
        @csrf
        <input type="text" name="nombre_completo" placeholder="Nombre completo" required class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
        <select name="tipo_documento" class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
            <option value="INE">INE</option>
            <option value="Pasaporte">Pasaporte</option>
            <option value="Licencia">Licencia</option>
            <option value="Otro">Otro</option>
        </select>
        <input type="text" name="documento_identidad" placeholder="No. Identificación" required class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
        <input type="text" name="telefono" placeholder="Teléfono" required class="bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
        <div class="flex gap-2">
            <input type="email" name="email" placeholder="Correo" class="w-full bg-white border border-[#C8BFA8] px-3 py-2 text-sm">
            <button type="submit" class="bg-[#244E8A] text-white px-4 py-2 text-sm">Guardar</button>
        </div>
    </form>
</details>

<!-- Directorio y buscador -->
<div class="bg-[#FAF8F3] border border-[#D8D0C0] p-6">
    <div class="flex flex-wrap justify-between items-end gap-4 mb-4">
        <h2 class="text-xl font-serif">Directorio <span class="text-xs text-[#9E4731]">宾客名录</span></h2>
        <form method="GET" action="{{ route('huespedes.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ $busqueda }}" placeholder="Nombre, correo o ID..."
                class="bg-white border border-[#C8BFA8] px-3 py-1.5 text-xs w-60">
            <button type="submit" class="bg-[#244E8A] text-white px-3 py-1.5 text-xs">Buscar</button>
            @if($busqueda !== '')
                <a href="{{ route('huespedes.index') }}" class="border border-[#C8BFA8] bg-white px-2.5 py-1.5 text-xs">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-[#244E8A] text-white">
                    <th class="p-3">ID</th>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Correo</th>
                    <th class="p-3">Teléfono</th>
                    <th class="p-3">Identificación</th>
                    <th class="p-3">Estancias</th>
                    <th class="p-3">Situación actual</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E6DEC8] bg-white">
                @forelse($huespedes as $h)
                    @php
                        $enCasa = $h->reservaciones->firstWhere('estado', 'en_casa');
                        $confirmada = $h->reservaciones->firstWhere('estado', 'confirmada');
                        $folios = $h->reservaciones->pluck('folio')->implode(', ');
                    @endphp
                    <tr>
                        <td class="p-3 font-bold">{{ $h->codigo_huesped }}</td>
                        <td class="p-3 font-medium">{{ $h->nombre_completo }}</td>
                        <td class="p-3 text-[#5C6672]">{{ $h->email ?? '—' }}</td>
                        <td class="p-3">{{ $h->telefono }}</td>
                        <td class="p-3">{{ $h->tipo_documento }} <span class="text-[#5C6672]">({{ $h->documento_identidad }})</span></td>
                        <td class="p-3">
                            <strong>{{ $h->reservaciones->count() }} estancia(s)</strong>
                            <span class="block text-[10px] text-[#5C6672]">{{ $folios ?: 'Sin historial' }}</span>
                        </td>
                        <td class="p-3">
                            @if($enCasa)
                                <span class="px-2 py-0.5 bg-[#244E8A] text-white">
                                    En casa · Hab. {{ $enCasa->habitacion->numero }}
                                </span>
                            @elseif($confirmada)
                                <span class="px-2 py-0.5 bg-[#F5E1D3] text-[#9E4731]">
                                    Reserva · {{ \Carbon\Carbon::parse($confirmada->fecha_entrada)->format('d M') }}
                                </span>
                            @else
                                <span class="text-[#5C6672]">Sin estancia activa</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-[#5C6672]">No se encontraron huéspedes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $huespedes->links() }}</div>
</div>
@endsection