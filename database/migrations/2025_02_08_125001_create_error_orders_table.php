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
        Schema::create('error_orders', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Fecha de emisión
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); 
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade'); 
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); 
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade'); 
            $table->integer('item'); // Número de ítem con error
            $table->string('obs_det')->nullable(); // Observaciones
            $table->string('obs_error')->nullable(); // Observaciones
            $table->boolean('tela')->default(false); // Se usó tela
            $table->integer('quantity'); // Cantidad afectada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_orders');
    }
};
