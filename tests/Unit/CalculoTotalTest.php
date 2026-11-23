<?php

namespace Tests\Unit;

use App\Models\Reservacion;
use PHPUnit\Framework\TestCase;

/**
 * Prueba 1: el total de la reservación se calcula bien.
 */
class CalculoTotalTest extends TestCase
{
    public function test_el_total_es_precio_por_numero_de_noches(): void
    {
        // Del 1 al 3 de diciembre son 2 noches: 2 x 1200 = 2400
        $total = Reservacion::calcularTotal(1200, '2026-12-01', '2026-12-03');

        $this->assertEquals(2400, $total);
    }

    public function test_siempre_se_cobra_al_menos_una_noche(): void
    {
        // Entrada y salida el mismo día: se cobra 1 noche
        $total = Reservacion::calcularTotal(3200, '2026-12-01', '2026-12-01');

        $this->assertEquals(3200, $total);
    }
}
