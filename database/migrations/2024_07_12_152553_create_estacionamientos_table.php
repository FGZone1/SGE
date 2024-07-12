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
        Schema::create('estacionamientos', function (Blueprint $table) {
            $table->id();
            $table->string('patente_vehiculo');
            $table->integer('dni_usuario', 8)->unsigned();
            $table->enum('estado', ['estacionado', 'libre']);
            $table->integer('tiempo')->unsigned();
            $table->timestamp('creado')->nullable();
            $table->timestamp('actualizado')->nullable();
            $table->foreign('patente_vehiculo')->references('patente')->on('vehiculos')->onDelete('cascade');
            $table->foreign('dni_usuario')->references('dni')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('estacionamientos');
    }
};
