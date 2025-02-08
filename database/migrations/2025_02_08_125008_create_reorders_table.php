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
        Schema::create('reorders', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Fecha de emisión
            $table->foreignId('error_order_id')->constrained('error_orders')->onDelete('cascade'); 
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade'); 
            $table->foreignId('operator_id')->constrained('operators')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reorders');
    }
};
