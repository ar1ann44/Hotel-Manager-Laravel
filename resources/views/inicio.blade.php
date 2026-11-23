@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<style>
    /* Fuentes y marcos del prototipo de diseño */
    .fuente-titulo { font-family: 'Bodoni Moda', serif; }
    .fuente-texto  { font-family: 'IBM Plex Sans', sans-serif; }
    .fuente-china  { font-family: 'Noto Serif SC', serif; }
    .fuente-pincel { font-family: 'Ma Shan Zheng', serif; }
    .marco-doble   { border: 1px solid #1F5FA8; outline: 1px solid #8FB8E0; outline-offset: -7px; }
</style>

@php
    // Tarjetas de los módulos
    $modulos = [
        ['glifo' => '态', 'num' => 'MÓDULO 01', 'titulo' => 'Disponibilidad y ocupación', 'ruta' => 'disponibilidad.index',
         'desc' => 'Mapa de ocupación por piso y consulta de habitaciones libres por fechas, tipo y huéspedes.',
         'dato' => "Ocupación hoy {$porcentaje}%"],
        ['glifo' => '订', 'num' => 'MÓDULO 02', 'titulo' => 'Reservaciones', 'ruta' => 'reservaciones.index',
         'desc' => 'Registro de reservaciones, check-in, check-out y cancelaciones con su total.',
         'dato' => "{$reservasActivas} activas · {$llegadasHoy} llegan hoy"],
        ['glifo' => '宾', 'num' => 'MÓDULO 03', 'titulo' => 'Huéspedes', 'ruta' => 'huespedes.index',
         'desc' => 'Directorio de huéspedes con contacto, identificación, estancias y situación actual.',
         'dato' => "{$totalHuespedes} registrados · {$enCasa} en casa"],
        ['glifo' => '房', 'num' => 'MÓDULO 04', 'titulo' => 'Habitaciones', 'ruta' => 'habitaciones.index',
         'desc' => 'Catálogo de habitaciones y tarifas; limpieza y mantenimiento de cada una.',
         'dato' => "{$totalHabitaciones} habitaciones · {$mantenimiento} en mantenimiento"],
    ];
@endphp

<div class="fuente-texto text-[#22262B]">

    <!-- Portada -->
    <section class="py-8 mb-8 flex flex-col lg:flex-row items-center gap-8">
        <div class="flex-1 flex flex-col gap-5">
            <p class="text-[13px] tracking-[0.28em] font-semibold text-[#B23A17]">
                SISTEMA DE GESTIÓN HOTELERA · <span class="fuente-china">酒店管理系统</span>
            </p>
            <h1 class="fuente-titulo font-bold text-[#1E4D4A] text-7xl md:text-9xl leading-none">PENGLAI</h1>
            <p class="fuente-titulo font-medium text-lg tracking-[0.3em]">HOTEL · CONTROL DE HABITACIONES Y RESERVACIONES</p>

            <div class="flex gap-7 items-start">
                <!-- Nombre en vertical con sello rojo -->
                <div class="hidden sm:flex flex-col items-center gap-2.5">
                    <p class="fuente-pincel text-[34px] leading-tight" style="writing-mode: vertical-rl;">蓬莱客栈</p>
                    <span class="fuente-china w-[34px] h-[34px] bg-[#B23A17] text-[#F6F0E0] grid place-items-center text-[13px]"
                          style="outline: 1px solid #F6F0E0; outline-offset: -4px;">印</span>
                </div>

                <div class="flex flex-col gap-4 max-w-xl">
                    <p class="text-[17px] leading-relaxed text-[#33373D]">
                        <strong class="text-[#1E4D4A]">Objetivo:</strong> controlar las habitaciones y reservaciones de la propiedad en un solo lugar — desde la consulta de disponibilidad hasta la salida del huésped.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('disponibilidad.index') }}"
                           class="min-h-12 inline-flex items-center px-6 bg-[#1F5FA8] hover:bg-[#123E73] text-white font-semibold tracking-wide">
                            Iniciar demostración
                        </a>
                        <a href="{{ route('disponibilidad.index') }}"
                           class="min-h-12 inline-flex items-center px-6 border border-[#1F5FA8] text-[#123E73] font-medium hover:bg-[#FBF8EF]">
                            Ver ocupación de hoy
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linterna (imagen tomada del prototipo de diseño) -->
        <img src="{{ asset('img/linterna.svg') }}" alt="Linterna china de porcelana azul y blanca"
             class="w-full max-w-[300px] h-auto">
    </section>

    <!-- Módulos del sistema -->
    <section class="flex flex-col gap-4">
        <h2 class="fuente-titulo font-bold text-4xl text-[#1E4D4A]">
            Módulos del sistema <span class="fuente-china text-lg font-medium text-[#B23A17]">系统模块</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($modulos as $modulo)
                <a href="{{ route($modulo['ruta']) }}"
                   class="marco-doble bg-[#FBF8EF] px-6 py-7 flex flex-col gap-3 min-h-[280px] hover:bg-white">
                    <div class="flex justify-between items-start">
                        <span class="fuente-pincel w-[72px] h-[72px] rounded-full bg-[#1F5FA8] text-white grid place-items-center text-[40px]"
                              style="outline: 2px solid #8FB8E0; outline-offset: 3px;">{{ $modulo['glifo'] }}</span>
                        <span class="text-xs tracking-[0.2em] text-[#B23A17] font-semibold">{{ $modulo['num'] }}</span>
                    </div>
                    <span class="fuente-titulo font-bold text-[26px] text-[#1E4D4A] leading-tight">{{ $modulo['titulo'] }}</span>
                    <span class="text-[15px] leading-relaxed text-[#33373D]">{{ $modulo['desc'] }}</span>
                    <span class="mt-auto text-sm font-semibold text-[#123E73] border-t border-[#D6CDB4] pt-3">{{ $modulo['dato'] }} →</span>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
