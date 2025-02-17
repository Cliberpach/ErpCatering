<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('proyecto_regimen', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('proyecto_id');
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            
            $table->unsignedBigInteger('colaborador_id')->nullable();
            $table->foreign('colaborador_id')->references('id')->on('colaboradores')->onDelete('set null');
            
            $table->unsignedBigInteger('regimen_id')->nullable();
            $table->foreign('regimen_id')->references('id')->on('regimenes')->onDelete('set null');
            
            $table->unsignedBigInteger('horario_id')->nullable();
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('set null');
            
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyecto_regimen');
    }
};
