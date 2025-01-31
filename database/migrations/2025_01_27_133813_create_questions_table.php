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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('text'); // Texto de la pregunta
            $table->enum('type', ['string', 'integer', 'list']); // Tipo de dato de la pregunta
            $table->text('options')->nullable(); // Opciones en caso de que sea lista
            $table->boolean('is_required')->default(false); // Si es obligatoria
            $table->foreignId('category_id')->constrained('question_categories')->onDelete('cascade'); // Relación con categorías
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
