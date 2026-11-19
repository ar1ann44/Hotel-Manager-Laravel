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
        Schema::create('huespedes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_huesped', 20)->unique()->nullable();
            $table->string('nombre_completo', 100);
            $table->enum('tipo_documento', ['INE', 'Pasaporte', 'Licencia', 'Otro'])->default('INE');
            $table->string('documento_identidad', 50);
            $table->string('telefono', 25);
            $table->string('email', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huespedes');
    }
};
