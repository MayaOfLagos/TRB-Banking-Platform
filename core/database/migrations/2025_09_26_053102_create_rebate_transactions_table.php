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
        Schema::create('rebate_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rebate_category_id')->constrained('rebate_categories')->onDelete('cascade');
            $table->string('transaction_type')->index(); // Type: wire_transfer, deposit, product_upload, etc.
            $table->string('reference_id')->nullable()->index(); // Reference to original transaction
            $table->string('reference_type')->nullable(); // Type of referenced model
            $table->decimal('original_amount', 15, 2)->default(0.00); // Original transaction amount
            $table->decimal('rebate_rate', 5, 2)->default(0.00); // Applied rebate rate
            $table->decimal('rebate_amount', 15, 2)->default(0.00); // Calculated rebate amount
            $table->decimal('tier_multiplier', 3, 2)->default(1.00); // Applied tier multiplier
            $table->decimal('final_amount', 15, 2)->default(0.00); // Final rebate amount after multipliers
            $table->string('status')->default('pending')->index(); // pending, processed, failed, reversed
            $table->text('description')->nullable(); // Transaction description
            $table->json('metadata')->nullable(); // Additional transaction metadata
            $table->string('ip_address', 45)->nullable(); // User's IP address for fraud tracking
            $table->timestamp('processed_at')->nullable(); // When rebate was processed
            $table->timestamps();
            $table->softDeletes(); // Soft deletes for audit trail
            
            // Indexes for performance and security
            $table->index(['user_id', 'status']);
            $table->index(['transaction_type', 'created_at']);
            $table->index(['reference_id', 'reference_type']);
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rebate_transactions');
    }
};
