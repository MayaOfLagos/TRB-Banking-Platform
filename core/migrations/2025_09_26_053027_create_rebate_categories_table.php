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
        Schema::create('rebate_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rebate_program_id')->constrained('rebate_programs')->onDelete('cascade');
            $table->string('name')->index(); // Category name (e.g., "Wire Transfers", "Deposits")
            $table->string('code')->unique(); // Unique code (e.g., "WIRE_TRANSFER", "DEPOSIT")
            $table->text('description')->nullable(); // Category description
            $table->decimal('rebate_rate', 5, 2)->default(0.00); // Rebate rate for this category
            $table->decimal('minimum_amount', 15, 2)->default(0.00); // Minimum transaction amount
            $table->decimal('maximum_rebate', 15, 2)->nullable(); // Maximum rebate per transaction
            $table->integer('daily_transaction_limit')->nullable(); // Max transactions per day
            $table->decimal('daily_rebate_limit', 15, 2)->nullable(); // Daily rebate limit
            $table->json('tier_multipliers')->nullable(); // Multipliers for different user tiers
            $table->json('settings')->nullable(); // Additional category settings
            $table->boolean('is_active')->default(true)->index(); // Active status
            $table->timestamps();
            $table->softDeletes(); // Soft deletes for audit trail
            
            // Indexes for performance
            $table->index(['rebate_program_id', 'is_active']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rebate_categories');
    }
};
