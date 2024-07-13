<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recarga extends Model
{
    use HasFactory;
    protected $table = 'recargas';
  
    public $incrementing = false; // No es autoincremental
    protected $keyType = 'int'; // Tipo de clave primaria

    protected $fillable = [
        'id',
        'dni_usuario',
        'cuit_comercio',
        'patente',
        'importe',
    ];

    const CREATED_AT = 'creado';

    const UPDATED_AT = 'actualizado';
    
    protected $dates = ['creado', 'actualizado'];
    protected $hidden = [
        'creado',
        'actualizado',];
}
