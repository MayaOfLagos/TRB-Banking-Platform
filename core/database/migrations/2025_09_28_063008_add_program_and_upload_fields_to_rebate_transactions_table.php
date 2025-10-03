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
        Schema::table('rebate_transactions', function (Blueprint $table) {
            $table->foreignId('rebate_program_id')->nullable()->after('rebate_category_id')->constrained('rebate_programs')->onDelete('cascade');
            $table->foreignId('product_upload_id')->nullable()->after('rebate_program_id')->constrained('product_uploads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rebate_transactions', function (Blueprint $table) {
            $table->dropForeign(['rebate_program_id']);
            $table->dropForeign(['product_upload_id']);
            $table->dropColumn(['rebate_program_id', 'product_upload_id']);
        });
    }
};
