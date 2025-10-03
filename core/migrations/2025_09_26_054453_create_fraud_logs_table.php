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
        Schema::create('fraud_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_upload_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('fraud_score')->default(0);
            $table->json('flags')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('additional_data')->nullable();
            $table->enum('risk_level', ['minimal', 'low', 'medium', 'high'])->default('minimal');
            $table->enum('action_taken', ['none', 'flagged', 'review_required', 'blocked'])->default('none');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('review_notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'fraud_score']);
            $table->index(['created_at', 'risk_level']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_logs');
    }
};
