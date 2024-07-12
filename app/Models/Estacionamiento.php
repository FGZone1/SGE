<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estacionamiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'patente_vehiculo',
        'dni_usuario',
        'estado',
        'tiempo'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'patente_vehiculo', 'patente');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'dni_usuario', 'dni');
    }
}
