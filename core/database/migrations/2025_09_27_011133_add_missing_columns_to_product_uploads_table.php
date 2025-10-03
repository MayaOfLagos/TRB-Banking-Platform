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
        Schema::table('product_uploads', function (Blueprint $table) {
            $table->foreignId('rebate_program_id')->nullable()->after('rebate_category_id')->constrained('rebate_programs')->onDelete('set null');
            $table->string('receipt_image')->nullable()->after('rebate_program_id');
            $table->decimal('purchase_amount', 15, 2)->after('receipt_image');
            $table->date('purchase_date')->after('purchase_amount');
            $table->string('store_name')->after('purchase_date');
            $table->text('description')->nullable()->after('store_name');
            $table->string('submission_ip', 45)->nullable()->after('description');
            $table->text('user_agent')->nullable()->after('submission_ip');
            $table->foreignId('rebate_transaction_id')->nullable()->after('user_agent')->constrained('rebate_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_uploads', function (Blueprint $table) {
            $table->dropForeign(['rebate_program_id']);
            $table->dropForeign(['rebate_transaction_id']);
            $table->dropColumn([
                'rebate_program_id',
                'receipt_image',
                'purchase_amount',
                'purchase_date',
                'store_name',
                'description',
                'submission_ip',
                'user_agent',
                'rebate_transaction_id'
            ]);
        });
    }
};
