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
        Schema::create('product_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rebate_category_id')->nullable()->constrained('rebate_categories')->onDelete('set null');
            $table->string('product_name')->index(); // Product name
            $table->decimal('amount', 15, 2); // Product amount/price
            $table->integer('quantity')->default(1); // Product quantity
            $table->string('image_path')->nullable(); // Optional product image path
            $table->string('image_thumbnail_path')->nullable(); // Thumbnail path
            $table->string('status')->default('pending')->index(); // pending, approved, rejected, rewarded
            $table->decimal('calculated_rebate', 15, 2)->default(0.00); // Auto-calculated rebate
            $table->decimal('admin_rebate_override', 15, 2)->nullable(); // Admin can override rebate amount
            $table->decimal('final_rebate_amount', 15, 2)->default(0.00); // Final rebate amount awarded
            $table->text('admin_notes')->nullable(); // Admin verification notes
            $table->text('rejection_reason')->nullable(); // Reason for rejection
            $table->string('file_hash')->nullable(); // Hash for duplicate detection
            $table->json('metadata')->nullable(); // Additional product metadata
            $table->string('ip_address', 45)->nullable(); // User's IP for fraud tracking
            $table->timestamp('verified_at')->nullable(); // When product was verified
            $table->timestamp('rewarded_at')->nullable(); // When rebate was awarded
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who verified
            $table->timestamps();
            $table->softDeletes(); // Soft deletes for audit trail
            
            // Indexes for performance and fraud prevention
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('file_hash');
            $table->index('ip_address');
            $table->index(['user_id', 'created_at']); // For rate limiting checks
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_uploads');
    }
};
