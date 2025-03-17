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
        Schema::create('plato_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plato_id')->constrained()->onDelete('cascade'); // Relaciona con la tabla 'platos'
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade'); // Relaciona con la tabla 'productos'
            $table->string('producto'); // Nombre del producto (puede ser redundante con el 'producto_id')
            $table->float('cantidad', 8, 2);
            $table->string('unidad_medida'); // Unidad de medida
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plato_detalles');
    }
};
