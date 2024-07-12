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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->string('patente');
            $table->integer('dni_usuario', 8)->unsigned();
            $table->timestamp('creado')->nullable();
            $table->timestamp('actualizado')->nullable();

            // Clave primaria
            $table->primary('patente');
            
            // Clave foránea
            $table->foreign('dni_usuario')->references('dni')->on('usuarios')->onDelete('cascade');

            // Restricción de unicidad compuesta
            $table->unique(['dni_usuario', 'patente']);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
