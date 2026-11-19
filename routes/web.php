<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\HabitacionController;
use App\Models\Habitacion;
use App\Models\Huesped;
use App\Models\Reservacion;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $totalHabitaciones = Habitacion::count();
        $ocupadas = Habitacion::where('estado', 'ocupada')->count();
        $vendibles = Habitacion::whereNotIn('estado', ['mantenimiento', 'limpieza'])->count();
        $porcentaje = $vendibles > 0 ? round(($ocupadas / $vendibles) * 100) : 0;
        $mantenimiento = Habitacion::where('estado', 'mantenimiento')->count();
        $reservasActivas = Reservacion::whereIn('estado', ['confirmada', 'en_casa'])->count();
        $llegadasHoy = Reservacion::where('estado', 'confirmada')->count();
        $totalHuespedes = Huesped::count();
        $enCasa = Reservacion::where('estado', 'en_casa')->count();

        return view('inicio', compact(
            'totalHabitaciones', 'porcentaje', 'mantenimiento',
            'reservasActivas', 'llegadasHoy', 'totalHuespedes', 'enCasa'
        ));
    })->name('inicio');

    // Módulo 01: Disponibilidad y ocupación
    Route::get('/disponibilidad', [DisponibilidadController::class, 'index'])->name('disponibilidad.index');

    // Módulo 04: Habitaciones
    Route::get('/habitaciones', [HabitacionController::class, 'index'])->name('habitaciones.index');
    Route::patch('/habitaciones/{habitacion}/estado', [HabitacionController::class, 'cambiarEstado'])->name('habitaciones.estado');

    // Acciones exclusivas de Administrador sobre habitaciones
    Route::middleware('role:admin')->group(function () {
        Route::post('/habitaciones', [HabitacionController::class, 'store'])->name('habitaciones.store');
        Route::put('/habitaciones/{habitacion}', [HabitacionController::class, 'update'])->name('habitaciones.update');
    });

    // Placeholders temporales para el menú superior (Siguiente paso)
    Route::get('/reservaciones', fn() => redirect()->route('disponibilidad.index'))->name('reservaciones.index');
    Route::get('/huespedes', fn() => redirect()->route('disponibilidad.index'))->name('huespedes.index');
});