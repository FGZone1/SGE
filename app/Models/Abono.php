<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    use HasFactory;
   
    protected $primaryKey = 'cuit_comercio'; // Define cuit como clave primaria
    public $incrementing = false; // No es autoincremental
    protected $keyType = 'int'; // Tipo de clave primaria
    protected $table = 'abonos_comercios'; 
    protected $fillable = [
        'cuit_comercio',
        'fecha_desde',
        'fecha_hasta',
        'importe',
    ];
    protected $dates = ['creado', 'actualizado'];
    protected $hidden = [
        'creado',
        'actualizado',];
}
