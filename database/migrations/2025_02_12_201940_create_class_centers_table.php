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
        Schema::create('class_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('question_categories')->onDelete('cascade'); // Relación con categorías
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade'); // Relación con centros
            $table->integer('item'); // Cantidad afectada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_centers');
    }
};
