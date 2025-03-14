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
        Schema::create('prod_designs', function (Blueprint $table) {
            $table->id();
            $table->datetime('date'); // Fecha de emisión
            $table->string('bitrix'); // Referencia de pago
            $table->string('type'); // Referencia de pago
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con operadores
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prod_designs');
    }
};
