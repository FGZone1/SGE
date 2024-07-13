<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comercio extends Model
{
    use HasFactory;
    protected $table = 'comercios';
    protected $primaryKey = 'cuit'; // Define cuit como clave primaria
    public $incrementing = false; // No es autoincremental
    protected $keyType = 'string'; // Tipo de clave primaria
    
    
    protected $fillable = [
        'cuit',
        'razon_social',
        'direccion',
        'estado',
    ];
    const CREATED_AT = 'creado';

    const UPDATED_AT = 'actualizado';
    
    protected $dates = ['creado', 'actualizado'];
    protected $hidden = [
        'creado',
        'actualizado',];

}
