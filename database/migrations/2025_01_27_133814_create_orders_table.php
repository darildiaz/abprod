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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('bitrix_id')->nullable(); // Número de pedido
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade'); // Relación con clientes
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // Relación con vendedores
            $table->string('reference_name'); // Nombre de referencia
            $table->date('issue_date'); // Fecha de emisión
            $table->date('delivery_date'); // Fecha de entrega
            $table->date('shipping_date')->nullable(); // Fecha de entrega
            $table->date('completion_date')->nullable(); // Fecha de entrega
            $table->integer('total'); // Total del pedido
            $table->foreignId('classification_id')->constrained('question_categories')->onDelete('cascade'); // Clasificación
            $table->integer('status')->default(0); // Estado del pedido
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
