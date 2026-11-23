<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reservacion extends Model
{
    protected $table = 'reservaciones';

    protected $fillable = [
        'folio', 'habitacion_id', 'huesped_id', 'user_id',
        'fecha_entrada', 'fecha_salida', 'numero_huespedes',
        'total_mxn', 'estado'
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function huesped()
    {
        return $this->belongsTo(Huesped::class, 'huesped_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Total a pagar = precio por noche x número de noches.
     * Siempre se cobra al menos 1 noche.
     */
    public static function calcularTotal(float $precioNoche, string $fechaEntrada, string $fechaSalida): float
    {
        $noches = Carbon::parse($fechaEntrada)->diffInDays(Carbon::parse($fechaSalida));

        return $precioNoche * max(1, $noches);
    }
}
