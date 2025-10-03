<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBillingCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_billing_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code_type', 10); // 'IMF', 'TAX', 'COT'
            $table->string('code', 50);
            $table->decimal('amount', 28, 8)->default(0); // Amount required for this code
            $table->text('description')->nullable(); // Optional description for admin
            $table->tinyInteger('status')->default(1); // 1 = Active, 0 = Inactive
            $table->tinyInteger('is_required')->default(1); // 1 = Required, 0 = Optional
            $table->datetime('expires_at')->nullable(); // Optional expiry date for the code
            $table->datetime('used_at')->nullable(); // When the code was used/resolved
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'code_type']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_billing_codes');
    }
}