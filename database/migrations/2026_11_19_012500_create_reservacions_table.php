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
        Schema::create('reservaciones', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->foreignId('habitacion_id')->constrained('habitaciones')->onDelete('restrict');
            $table->foreignId('huesped_id')->constrained('huespedes')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->date('fecha_entrada');
            $table->date('fecha_salida');
            $table->integer('numero_huespedes')->default(1);
            $table->decimal('total_mxn', 10, 2);
            $table->enum('estado', [
                'confirmada',
                'en_casa',
                'finalizada',
                'cancelada'
            ])->default('confirmada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservaciones');
    }
};
