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
            $table->foreign('supervisor_id')->references('id')->on('colaboradores');

            $table->string('nombre',260);

            $table->decimal('costo', 20, 2)->unsigned();
            $table->decimal('avance_costo', 20, 2)->unsigned();
            $table->decimal('diferencia', 20, 2);

            $table->decimal('avance',16,2)->unsigned()->default(0);
            
            $table->char('departamento_id', 2);
            $table->char('provincia_id', 4);
            $table->char('distrito_id', 6);

            $table->string('departamento_nombre',140);
            $table->string('provincia_nombre',140);
            $table->string('distrito_nombre',140);
            $table->string('ubigeo',20);

            $table->string('direccion',200);

            $table->enum('estado', ['PENDIENTE', 'EN PROCESO','FINALIZADO','ANULADO'])->default('PENDIENTE');
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
