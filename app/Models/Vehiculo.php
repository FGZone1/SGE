<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $primaryKey = 'patente';
    public $incrementing = false;
    protected $keyType = 'string';
    //public $timestamps = false; // Desactivar timestamps automáticos
    const CREATED_AT = 'creado';

    const UPDATED_AT = 'actualizado';
    
    protected $dates = ['creado', 'actualizado'];
       
    protected $fillable = [
        'patente',
        'dni_usuario',
     ];
     protected $hidden = [
        'creado',
        'actualizado',];
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'dni_usuario', 'dni');
    }
}