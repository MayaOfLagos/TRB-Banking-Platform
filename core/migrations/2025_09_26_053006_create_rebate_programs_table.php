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
        Schema::create('rebate_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Program name (e.g., "Wire Transfer Rebates")
            $table->text('description')->nullable(); // Program description
            $table->boolean('is_active')->default(true)->index(); // Active status
            $table->decimal('default_rate', 5, 2)->default(0.00); // Default rebate rate (percentage)
            $table->decimal('minimum_amount', 15, 2)->default(0.00); // Minimum transaction amount
            $table->decimal('maximum_rebate', 15, 2)->nullable(); // Maximum rebate per transaction
            $table->decimal('daily_limit', 15, 2)->nullable(); // Daily rebate limit per user
            $table->decimal('monthly_limit', 15, 2)->nullable(); // Monthly rebate limit per user
            $table->json('settings')->nullable(); // Additional program settings
            $table->timestamp('starts_at')->nullable(); // Program start date
            $table->timestamp('ends_at')->nullable(); // Program end date
            $table->timestamps();
            $table->softDeletes(); // Soft deletes for audit trail
            
            // Indexes for performance
            $table->index(['is_active', 'starts_at', 'ends_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rebate_programs');
    }
};
