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
        Schema::create('order_item_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('orderitems')->onDelete('cascade'); // Relación con ítems
            $table->foreignId('reference_id')->constrained('orderreferences')->onDelete('cascade'); // Relación con referencias
            $table->foreignId('size_id')->constrained('sizes')->onDelete('cascade'); // Relación con talles
            $table->integer('price'); // Precio de este producto en el ítem
            $table->integer('quantity')->default(1); // Cantidad de este producto en el ítem
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_products');
    }
};
