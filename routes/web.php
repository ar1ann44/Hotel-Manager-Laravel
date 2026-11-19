<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\HuespedController;
use App\Http\Controllers\ReservacionController;
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

    // Módulo 02: Reservaciones (Registro, Check-in, Check-out, Cancelar)
    Route::get('/reservaciones', [ReservacionController::class, 'index'])->name('reservaciones.index');
    Route::post('/reservaciones', [ReservacionController::class, 'store'])->name('reservaciones.store');
    Route::patch('/reservaciones/{reservacion}/check-in', [ReservacionController::class, 'checkIn'])->name('reservaciones.checkin');
    Route::patch('/reservaciones/{reservacion}/check-out', [ReservacionController::class, 'checkOut'])->name('reservaciones.checkout');
    Route::patch('/reservaciones/{reservacion}/cancelar', [ReservacionController::class, 'cancelar'])->name('reservaciones.cancelar');

    // Módulo 03: Huéspedes
    Route::get('/huespedes', [HuespedController::class, 'index'])->name('huespedes.index');
    Route::post('/huespedes', [HuespedController::class, 'store'])->name('huespedes.store');

    // Módulo 04: Habitaciones
    Route::get('/habitaciones', [HabitacionController::class, 'index'])->name('habitaciones.index');
    Route::patch('/habitaciones/{habitacion}/estado', [HabitacionController::class, 'cambiarEstado'])->name('habitaciones.estado');

    // Acciones exclusivas de Administrador
    Route::middleware('role:admin')->group(function () {
        Route::post('/habitaciones', [HabitacionController::class, 'store'])->name('habitaciones.store');
        Route::put('/habitaciones/{habitacion}', [HabitacionController::class, 'update'])->name('habitaciones.update');
    });
});