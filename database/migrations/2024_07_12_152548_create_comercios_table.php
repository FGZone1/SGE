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
        Schema::create('comercios', function (Blueprint $table) {
            $table->bigInteger('cuit', 11)->unsigned()->primary();
            $table->string('razon_social');
            $table->string('direccion')->nullable();
            $table->enum('estado', ['autorizado', 'suspendido'])->default('autorizado');
            $table->timestamp('creado')->nullable();
            $table->timestamp('actualizado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('comercios');
    }
};
