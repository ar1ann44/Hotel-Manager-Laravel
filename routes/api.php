<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

// Todas estas rutas empiezan con /api (ejemplo: /api/habitaciones)
Route::get('/habitaciones', [ApiController::class, 'habitaciones']);
Route::get('/disponibilidad', [ApiController::class, 'disponibilidad']);
Route::get('/reservaciones/{folio}', [ApiController::class, 'reservacion']);
