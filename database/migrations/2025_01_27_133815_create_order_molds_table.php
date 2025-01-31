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
        Schema::create('order_molds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id') // Clave foránea a la tabla orders
                ->constrained('orders') // Referencia a la tabla 'orders'
                ->onDelete('cascade');
            $table->string('title'); // Título del molde
            $table->text('imagen')->nullable(); // Imagen del molde
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_molds');
    }
};
