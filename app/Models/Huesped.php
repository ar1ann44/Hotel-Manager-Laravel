<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Huesped extends Model
{
    protected $table = 'huespedes';

    protected $fillable = [
        'codigo_huesped', 'nombre_completo', 'tipo_documento',
        'documento_identidad', 'telefono', 'email'
    ];

    public function reservaciones()
    {
        return $this->hasMany(Reservacion::class, 'huesped_id');
    }
}