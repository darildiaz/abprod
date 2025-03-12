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
        Schema::create('roll_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roll_id')->constrained('rolls')->onDelete('cascade');
            $table->foreignId('error_order_id')->constrained('error_orders')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roll_errors');
    }
};
