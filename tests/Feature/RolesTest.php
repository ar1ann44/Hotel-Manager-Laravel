<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prueba 3: solo el administrador puede crear habitaciones.
 */
class RolesTest extends TestCase
{
    use RefreshDatabase;

    private array $nuevaHabitacion = [
        'numero' => '501', 'piso' => 5, 'tipo' => 'Suite',
        'capacidad' => 4, 'precio_noche' => 3500,
    ];

    public function test_recepcionista_no_puede_crear_habitaciones(): void
    {
        $recepcionista = User::create([
            'name' => 'Recepción', 'email' => 'recepcion@test.com',
            'password' => 'secreto123', 'rol' => 'recepcionista',
        ]);

        $respuesta = $this->actingAs($recepcionista)->post('/habitaciones', $this->nuevaHabitacion);

        $respuesta->assertStatus(403);
        $this->assertDatabaseMissing('habitaciones', ['numero' => '501']);
    }

    public function test_administrador_si_puede_crear_habitaciones(): void
    {
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'secreto123', 'rol' => 'admin',
        ]);

        $respuesta = $this->actingAs($admin)->post('/habitaciones', $this->nuevaHabitacion);

        $respuesta->assertRedirect();
        $this->assertDatabaseHas('habitaciones', ['numero' => '501']);
    }
}
