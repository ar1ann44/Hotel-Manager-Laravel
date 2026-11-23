<?php

namespace Tests\Feature;

use App\Models\Habitacion;
use App\Models\Huesped;
use App\Models\Reservacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prueba 2: una habitación con reservación activa no aparece como disponible
 * en esas fechas, pero sí en fechas que no se cruzan.
 */
class DisponibilidadTest extends TestCase
{
    use RefreshDatabase;

    public function test_habitacion_reservada_no_aparece_disponible_en_esas_fechas(): void
    {
        $usuario = User::create([
            'name' => 'Recepción', 'email' => 'recepcion@test.com',
            'password' => 'secreto123', 'rol' => 'recepcionista',
        ]);
        $habitacion = Habitacion::create([
            'numero' => '101', 'piso' => 1, 'tipo' => 'Doble',
            'capacidad' => 2, 'precio_noche' => 1800, 'estado' => 'reservada',
        ]);
        $huesped = Huesped::create([
            'nombre_completo' => 'Huésped de Prueba', 'tipo_documento' => 'INE',
            'documento_identidad' => 'ABC123', 'telefono' => '6461234567',
        ]);

        // Reservación del 10 al 15 de diciembre
        Reservacion::create([
            'folio' => 'R-TEST', 'habitacion_id' => $habitacion->id,
            'huesped_id' => $huesped->id, 'user_id' => $usuario->id,
            'fecha_entrada' => '2026-12-10', 'fecha_salida' => '2026-12-15',
            'numero_huespedes' => 2, 'total_mxn' => 9000, 'estado' => 'confirmada',
        ]);

        // Del 12 al 14 se cruza con la reservación: NO debe estar disponible
        $libres = Habitacion::disponibles('2026-12-12', '2026-12-14', 2);
        $this->assertFalse($libres->contains('id', $habitacion->id));

        // Del 20 al 22 no se cruza: SÍ debe estar disponible
        $libres = Habitacion::disponibles('2026-12-20', '2026-12-22', 2);
        $this->assertTrue($libres->contains('id', $habitacion->id));
    }
}
