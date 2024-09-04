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
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('users');

            $table->string('nombre',260);

            $table->decimal('costo', 20, 2)->unsigned();
            $table->decimal('avance_costo', 20, 2)->unsigned();
            $table->decimal('diferencia', 20, 2);
            $table->enum('estado', ['ACTIVO', 'ANULADO','FINALIZADO'])->default('ACTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
