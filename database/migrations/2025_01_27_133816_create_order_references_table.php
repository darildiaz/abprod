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
        Schema::create('order_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Relación con órdenes
           // $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade'); // Relación con OrderItem
            $table->integer('item'); // Código de referencia
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Relación con productos
            $table->foreignId('size_id')->constrained()->onDelete('cascade'); // Relación con talles/sizes
            $table->integer('quantity'); // Cantidad total
            $table->integer('price'); // Precio predefinido
            $table->integer('discount')->default(0); // Descuento aplicado
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_references');
    }
};
