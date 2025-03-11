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
        Schema::create('prod_discounts', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Fecha de descuento
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prod_discounts');
    }
};
