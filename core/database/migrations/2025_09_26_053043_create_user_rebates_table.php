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
        Schema::create('user_rebates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_earned', 15, 2)->default(0.00); // Total rebates earned
            $table->decimal('current_balance', 15, 2)->default(0.00); // Current available balance
            $table->decimal('total_redeemed', 15, 2)->default(0.00); // Total amount redeemed
            $table->decimal('pending_amount', 15, 2)->default(0.00); // Pending rebate amount
            $table->integer('current_tier')->default(1); // User's current rebate tier
            $table->timestamp('last_earned_at')->nullable(); // Last rebate earned date
            $table->timestamp('last_redeemed_at')->nullable(); // Last redemption date
            $table->json('tier_history')->nullable(); // History of tier changes
            $table->json('statistics')->nullable(); // Additional user statistics
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index(['user_id', 'current_balance']);
            $table->index('current_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_rebates');
    }
};
