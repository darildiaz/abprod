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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nif')->unique(); // Cédula/NIF del cliente
            $table->string('name'); // Nombre del cliente
            $table->string('address'); // Dirección del cliente
            $table->string('phone'); // Teléfono del cliente
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con vendedores
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
