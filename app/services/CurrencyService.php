<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    // Monedas a las que se pueden convertir las tarifas
    const MONEDAS = [
        'USD' => 'Dólar estadounidense',
        'EUR' => 'Euro',
        'CAD' => 'Dólar canadiense',
        'GBP' => 'Libra esterlina',
        'JPY' => 'Yen japonés',
        'CNY' => 'Yuan chino',
    ];

    // Tasas aproximadas por si la API no responde
    const TASAS_RESPALDO = [
        'USD' => 0.050,
        'EUR' => 0.046,
        'CAD' => 0.070,
        'GBP' => 0.039,
        'JPY' => 7.6,
        'CNY' => 0.36,
    ];

    /**
     * Obtiene cuánto vale 1 peso mexicano en cada moneda usando la API de Frankfurter.
     * Si la API externa falla o no hay internet, usa tasas de respaldo.
     */
    public function obtenerTasasDesdeMxn(): array
    {
        return Cache::remember('frankfurter_tasas_mxn', now()->addHours(6), function () {
            try {
                $response = Http::timeout(4)->get('https://api.frankfurter.dev/v1/latest', [
                    'from' => 'MXN',
                    'to' => implode(',', array_keys(self::MONEDAS)),
                ]);

                if ($response->successful() && isset($response->json()['rates'])) {
                    return array_merge(self::TASAS_RESPALDO, $response->json()['rates'], [
                        'fecha' => $response->json()['date'] ?? today()->toDateString(),
                        'fuente' => 'En vivo (Frankfurter API)',
                    ]);
                }
            } catch (\Throwable $e) {
                // Respaldo silencioso si no hay conexión
            }

            return array_merge(self::TASAS_RESPALDO, [
                'fecha' => today()->toDateString(),
                'fuente' => 'Tasa referencial local (API no disponible)',
            ]);
        });
    }
}
