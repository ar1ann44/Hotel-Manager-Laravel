<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mejora de rendimiento: índices en las columnas que más se usan para buscar
 * reservaciones (por estado y por rango de fechas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservaciones', function (Blueprint $table) {
            $table->index('estado');
            $table->index(['habitacion_id', 'estado', 'fecha_entrada', 'fecha_salida'], 'reservaciones_disponibilidad_index');
        });
    }

    public function down(): void
    {
        Schema::table('reservaciones', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex('reservaciones_disponibilidad_index');
        });
    }
};
