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
        Schema::create('productiondets', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Fecha de producción
            $table->foreignId('production_id')->constrained('productions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Relación con productos
            $table->integer('quantity'); // Cantidad producida
            $table->integer('valid_amount'); // Cantidad producida
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
        Schema::dropIfExists('productiondets');
    }
};
