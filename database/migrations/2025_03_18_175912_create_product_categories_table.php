<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo crear la tabla si no existe
        if (!Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            
            // Insertar algunas categorías por defecto
            DB::table('product_categories')->insert([
                ['name' => 'Categoría 1', 'description' => 'Categoría por defecto 1', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Categoría 2', 'description' => 'Categoría por defecto 2', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Categoría 3', 'description' => 'Categoría por defecto 3', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
