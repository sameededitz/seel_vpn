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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('discount_percent')->default(0);

            $table->enum('type', ['single_use', 'multi_use'])->default('single_use'); // NEW
            $table->integer('max_uses')->nullable(); // NULL means unlimited for multi-use
            $table->integer('uses_count')->default(0); // Track total uses

            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promo_code_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('promo_code_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete(); // Purchase where promo was used

            $table->timestamp('used_at')->default(now());

            $table->unique(['promo_code_id', 'user_id']); // Prevent duplicate usage by same user

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_code_user');
        Schema::dropIfExists('promo_codes');
    }
};
