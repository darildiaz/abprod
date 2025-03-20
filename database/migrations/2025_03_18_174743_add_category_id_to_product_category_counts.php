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
        // Verificar si la tabla existe antes de modificarla
        if (Schema::hasTable('product_category_counts')) {
            // Comprobar si la columna ya existe para evitar duplicados
            if (!Schema::hasColumn('product_category_counts', 'category_id')) {
                Schema::table('product_category_counts', function (Blueprint $table) {
                    $table->unsignedBigInteger('category_id')->nullable();
                    
                    // Solo agregar la restricción de clave foránea si la tabla de categorías existe
                    if (Schema::hasTable('product_categories')) {
                        $table->foreign('category_id')
                              ->references('id')
                              ->on('product_categories')
                              ->onDelete('set null');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_category_counts') && 
            Schema::hasColumn('product_category_counts', 'category_id')) {
            
            Schema::table('product_category_counts', function (Blueprint $table) {
                // Intentar quitar la restricción de clave foránea si existe
                try {
                    $table->dropForeign(['category_id']);
                } catch (\Exception $e) {
                    // La restricción podría no existir, ignorar el error
                }
                
                $table->dropColumn('category_id');
            });
        }
    }
};
