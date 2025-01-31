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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Fecha de producción
            $table->foreignId('production_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade'); // Relación con centros
            $table->foreignId('operator_id')->constrained('operators')->onDelete('cascade'); // Relación con operadores
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Relación con productos
            $table->integer('quantity'); // Cantidad producida
            $table->integer('price');
            $table->boolean('pay')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
