<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;
    protected $table = 'usuarios';
    protected $primaryKey = 'dni';
   // public $timestamps = false; // Desactivar timestamps automáticos
    
    const CREATED_AT = 'creado';

    const UPDATED_AT = 'actualizado';
    
    protected $dates = ['creado', 'actualizado'];
   
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'domicilio',
        'email',
        'fecha_nacimiento',
        'contraseña',
        'saldo',
        ];
        protected $hidden = [
        'contraseña',
        'creado',
        'actualizado',];
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'dni_usuario', 'dni');
    }
    
}
