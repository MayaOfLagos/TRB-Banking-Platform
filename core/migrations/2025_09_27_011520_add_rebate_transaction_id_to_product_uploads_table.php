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
            $table->foreignId('rebate_transaction_id')->nullable()->after('user_agent')->constrained('rebate_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_uploads', function (Blueprint $table) {
            $table->dropForeign(['rebate_transaction_id']);
            $table->dropColumn('rebate_transaction_id');
        });
    }
};
