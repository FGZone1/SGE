<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estacionamiento extends Model
{
    use HasFactory;
    protected $table = 'estacionamientos';
    protected $primaryKey = 'patente_vehiculo'; // Define la clave primaria
    public $incrementing = false; // La clave primaria no es autoincremental
    protected $keyType = 'string'; // Tipo de la clave primaria
    protected $fillable = [
        'patente_vehiculo',
        'dni_usuario',
        'estado',
        'tiempo'
    ];
    
    const CREATED_AT = 'creado';

    const UPDATED_AT = 'actualizado';
    
    protected $dates = ['creado', 'actualizado'];
    protected $hidden = [
        'creado',
        'actualizado',];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'patente_vehiculo', 'patente');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'dni_usuario', 'dni');
    }
}
