<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations to support individual rebate tracking
     */
    public function up(): void
    {
        // Add individual rebate tracking fields to rebate_transactions
        Schema::table('rebate_transactions', function (Blueprint $table) {
            $table->foreignId('rebate_program_id')->nullable()->after('rebate_category_id')
                ->constrained('rebate_programs')->onDelete('set null');
            $table->foreignId('product_upload_id')->nullable()->after('reference_type')
                ->constrained('product_uploads')->onDelete('set null');
            $table->decimal('purchase_amount', 15, 2)->nullable()->after('original_amount');
            $table->text('review_notes')->nullable()->after('description');
            $table->string('rejected_by')->nullable()->after('review_notes');
            $table->timestamp('approved_at')->nullable()->after('processed_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
        });

        // Add status change tracking
        Schema::create('rebate_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rebate_transaction_id')->constrained('rebate_transactions')->onDelete('cascade');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->string('changed_by')->nullable(); // admin user ID or 'system'
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['rebate_transaction_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rebate_status_changes');
        
        Schema::table('rebate_transactions', function (Blueprint $table) {
            $table->dropForeign(['rebate_program_id']);
            $table->dropForeign(['product_upload_id']);
            $table->dropColumn([
                'rebate_program_id',
                'product_upload_id', 
                'purchase_amount',
                'review_notes',
                'rejected_by',
                'approved_at',
                'rejected_at'
            ]);
        });
    }
};