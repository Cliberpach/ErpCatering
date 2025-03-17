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
        Schema::create('platos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('calorias');
            $table->string('proteinas');
            $table->string('carbohidratos');
            $table->string('grasas');
            $table->string('peso');
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->decimal('costo', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platos');
    }
};
