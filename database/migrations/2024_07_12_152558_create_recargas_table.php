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
        Schema::create('recargas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cuit_comercio', 11)->unsigned();
            $table->integer('dni_usuario', 8)->unsigned();
            $table->decimal('importe', 10, 2);
            $table->timestamp('creado')->nullable();
            $table->timestamp('actualizado')->nullable();
            $table->foreign('cuit_comercio')->references('cuit')->on('comercios')->onDelete('cascade');
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
        Schema::dropIfExists('recargas');
    }
};
