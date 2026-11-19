@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="border-b border-[#D8D0C0] pb-8 mb-8">
    <p class="text-xs tracking-[0.25em] text-[#9E4731] uppercase font-semibold">Sistema de Gestión Hotelera · 酒店管理系统</p>
    <h1 class="text-5xl font-serif tracking-widest text-[#1E3A3A] mt-2">PENGLAI</h1>
    <p class="text-xs tracking-widest uppercase text-[#5C6672] mt-1">Hotel · Control de Habitaciones y Reservaciones</p>
    <p class="text-sm text-[#374151] mt-4 max-w-xl">
        <strong>Objetivo:</strong> controlar las habitaciones y reservaciones de la propiedad en un solo lugar — desde la consulta de disponibilidad hasta la salida del huésped.
    </p>
    <div class="mt-6 flex gap-3">
        <a href="{{ route('disponibilidad.index') }}" class="bg-[#244E8A] text-white px-5 py-2.5 text-sm font-medium hover:bg-[#1B3B6B]">
            Iniciar demostración
        </a>
        <a href="{{ route('disponibilidad.index') }}" class="border border-[#1E3A3A] px-5 py-2.5 text-sm font-medium hover:bg-[#E6DEC8]">
            Ver ocupación de hoy
        </a>
    </div>
</div>

<!-- Flujo de demostración principal -->
<div class="bg-[#FAF8F3] border border-[#D8D0C0] p-6 mb-8">
    <h2 class="text-lg font-serif mb-4">Demostración principal <span class="text-xs text-[#9E4731]">演示流程</span></h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
        <a href="{{ route('disponibilidad.index') }}" class="border border-[#9E4731] bg-white p-3 hover:bg-[#F5F1E8]">
            <p class="font-bold text-[#1E3A3A]">1. Consultar disponibilidad</p>
            <p class="text-[#9E4731] mt-1">Disponibilidad · SIGUIENTE</p>
        </a>
        <a href="{{ route('reservaciones.index') }}" class="border border-[#D8D0C0] bg-white p-3 hover:bg-[#F5F1E8]">
            <p class="font-bold text-[#1E3A3A]">2. Registrar reservación</p>
            <p class="text-[#5C6672] mt-1">Reservaciones</p>
        </a>
        <a href="{{ route('reservaciones.index') }}" class="border border-[#D8D0C0] bg-white p-3 hover:bg-[#F5F1E8]">
            <p class="font-bold text-[#1E3A3A]">3. Check-in</p>
            <p class="text-[#5C6672] mt-1">Ingreso del huésped</p>
        </a>
        <a href="{{ route('reservaciones.index') }}" class="border border-[#D8D0C0] bg-white p-3 hover:bg-[#F5F1E8]">
            <p class="font-bold text-[#1E3A3A]">4. Check-out</p>
            <p class="text-[#5C6672] mt-1">Salida y limpieza</p>
        </a>
    </div>
</div>

<!-- Módulos del sistema -->
<h2 class="text-lg font-serif mb-4">Módulos del sistema <span class="text-xs text-[#9E4731]">系统模块</span></h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('disponibilidad.index') }}" class="bg-[#FAF8F3] border border-[#D8D0C0] p-5 hover:border-[#244E8A] flex flex-col justify-between">
        <div>
            <span class="text-[11px] uppercase text-[#9E4731] font-semibold">Módulo 01</span>
            <h3 class="text-xl font-serif mt-1">Disponibilidad y ocupación</h3>
            <p class="text-xs text-[#5C6672] mt-2">Mapa de ocupación por piso y consulta de habitaciones libres por fechas, tipo y huéspedes.</p>
        </div>
        <p class="text-xs font-semibold text-[#244E8A] mt-4">Ocupación hoy {{ $porcentaje }}% →</p>
    </a>

    <a href="{{ route('reservaciones.index') }}" class="bg-[#FAF8F3] border border-[#D8D0C0] p-5 hover:border-[#244E8A] flex flex-col justify-between">
        <div>
            <span class="text-[11px] uppercase text-[#9E4731] font-semibold">Módulo 02</span>
            <h3 class="text-xl font-serif mt-1">Reservaciones</h3>
            <p class="text-xs text-[#5C6672] mt-2">Registro de reservaciones, check-in, check-out y cancelaciones con su total.</p>
        </div>
        <p class="text-xs font-semibold text-[#244E8A] mt-4">{{ $reservasActivas }} activas · {{ $llegadasHoy }} por llegar →</p>
    </a>

    <a href="{{ route('huespedes.index') }}" class="bg-[#FAF8F3] border border-[#D8D0C0] p-5 hover:border-[#244E8A] flex flex-col justify-between">
        <div>
            <span class="text-[11px] uppercase text-[#9E4731] font-semibold">Módulo 03</span>
            <h3 class="text-xl font-serif mt-1">Huéspedes</h3>
            <p class="text-xs text-[#5C6672] mt-2">Directorio de huéspedes con contacto, identificación, estancias y situación actual.</p>
        </div>
        <p class="text-xs font-semibold text-[#244E8A] mt-4">{{ $totalHuespedes }} registrados · {{ $enCasa }} en casa →</p>
    </a>

    <a href="{{ route('habitaciones.index') }}" class="bg-[#FAF8F3] border border-[#D8D0C0] p-5 hover:border-[#244E8A] flex flex-col justify-between">
        <div>
            <span class="text-[11px] uppercase text-[#9E4731] font-semibold">Módulo 04</span>
            <h3 class="text-xl font-serif mt-1">Habitaciones</h3>
            <p class="text-xs text-[#5C6672] mt-2">Catálogo de habitaciones y tarifas; limpieza, mantenimiento y conversión de divisas.</p>
        </div>
        <p class="text-xs font-semibold text-[#244E8A] mt-4">{{ $totalHabitaciones }} habitaciones · {{ $mantenimiento }} en mantenimiento →</p>
    </a>
</div>
@endsection