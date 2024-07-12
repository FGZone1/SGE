<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('dni', 8)->unsigned()->primary();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('domicilio')->nullable();
            $table->string('email')->unique();
            $table->date('fecha_nacimiento');
            $table->string('contraseña');
            $table->decimal('saldo', 10, 2)->default(0.00);
            $table->timestamp('creado')->nullable();
            $table->timestamp('actualizado')->nullable();
            $table->timestamps(0); // Mantener los timestamps para revertir cambios
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
