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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Código único
            $table->string('name');
            $table->text('imagen')->nullable(); // Imagen del producto
            $table->text('imagmolde')->nullable(); // Imagen del producto
            $table->string('description'); // Nombre del producto
            $table->boolean("is_producible")->default(true); // Imagen del producto
            $table->foreignId('line_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Relación con categorías
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
