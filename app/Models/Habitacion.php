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
}