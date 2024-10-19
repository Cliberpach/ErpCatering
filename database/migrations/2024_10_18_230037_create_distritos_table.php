<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('distritos', function (Blueprint $table) {
            $table->char('id', 6)->primary();
            $table->char('departamento_id', 2);
            $table->foreign('departamento_id')->references('id')->on('departamentos')->onDelete('cascade');
            $table->string('departamento');
            $table->char('provincia_id', 4);
            $table->foreign('provincia_id')->references('id')->on('provincias')->onDelete('cascade');
            $table->string('provincia');
            $table->string('nombre');
            $table->string('nombre_legal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distritos');
    }
};
