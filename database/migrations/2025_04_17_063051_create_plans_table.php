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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('original_price', 8, 2); // Added original_price
            $table->decimal('discount_price', 8, 2)->nullable(); // Added discount_price
            $table->integer('duration');
            $table->enum('duration_unit', ['day', 'week', 'month', 'year'])->default('day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
