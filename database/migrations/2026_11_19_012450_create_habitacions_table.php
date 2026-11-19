<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 10)->unique();
            $table->integer('piso');
            $table->enum('tipo', ['Estándar', 'Doble', 'Suite']);
            $table->integer('capacidad');
            $table->decimal('precio_noche', 10, 2);
            $table->enum('estado', [
                'disponible',
                'ocupada',
                'reservada',
                'limpieza',
                'mantenimiento'
            ])->default('disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
