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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Relación con pedidos
            $table->string('model'); // Nombre personalizado del ítem
            $table->string('name')->nullable(); // Nombre personalizado del ítem
            $table->string('number')->nullable(); // Número personalizado
            $table->string('other')->nullable(); // Otros datos personalizados
            $table->foreignId('size_id')->constrained('sizes')->onDelete('cascade'); // Relación con talles
            $table->integer('quantity')->default(1); // Cantidad
            $table->integer('price'); // Precio
            $table->integer('subtotal'); // Precio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
