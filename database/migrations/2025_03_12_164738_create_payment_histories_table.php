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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Fecha de emisión
            $table->date('date_confirmation')->nullable(); // Fecha de emisión
            $table->integer('amount'); // Total del pedido
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->string('reference'); // Referencia de pago
            $table->text('image')->nullable(); // Referencia de pago
            $table->boolean('status')->default(false); // Total del pedido
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_conf_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
