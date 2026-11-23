<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    protected $table = 'habitaciones';

    protected $fillable = [
        'numero', 'piso', 'tipo', 'capacidad', 'precio_noche', 'estado'
    ];

    public function reservaciones()
    {
        return $this->hasMany(Reservacion::class, 'habitacion_id');
    }

    /**
     * Habitaciones libres entre dos fechas para cierto número de personas.
     * Una habitación está libre si no está en mantenimiento y no tiene
     * una reservación activa (confirmada o en casa) que se cruce con esas fechas.
     */
    public static function disponibles(string $fechaEntrada, string $fechaSalida, int $capacidad, string $tipo = 'Todas')
    {
        $query = self::where('estado', '!=', 'mantenimiento')
            ->where('capacidad', '>=', $capacidad)
            ->whereDoesntHave('reservaciones', function ($q) use ($fechaEntrada, $fechaSalida) {
                $q->whereIn('estado', ['confirmada', 'en_casa'])
                  ->where('fecha_entrada', '<', $fechaSalida)
                  ->where('fecha_salida', '>', $fechaEntrada);
            });

        if ($tipo !== 'Todas') {
            $query->where('tipo', $tipo);
        }

        return $query->orderBy('piso')->orderBy('numero')->get();
    }
}
