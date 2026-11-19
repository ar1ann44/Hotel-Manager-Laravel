<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    /**
     * Obtiene tasas de cambio desde MXN hacia USD y EUR usando Frankfurter API.
     * Si la API externa falla o no hay internet, usa tasas de respaldo.
     */
    public function obtenerTasasDesdeMxn(): array
    {
        return Cache::remember('frankfurter_mxn_rates', now()->addHours(6), function () {
            try {
                $response = Http::timeout(4)->get('https://api.frankfurter.app/latest', [
                    'from' => 'MXN',
                    'to' => 'USD,EUR',
                ]);

                if ($response->successful() && isset($response->json()['rates'])) {
                    $rates = $response->json()['rates'];
                    return [
                        'USD' => $rates['USD'] ?? 0.050,
                        'EUR' => $rates['EUR'] ?? 0.046,
                        'fuente' => 'En vivo (Frankfurter API)',
                    ];
                }
            } catch (\Throwable $e) {
                // Respaldo silencioso si no hay conexión
            }

            return [
                'USD' => 0.050,
                'EUR' => 0.046,
                'fuente' => 'Tasa referencial local (API no disponible)',
            ];
        });
    }
}