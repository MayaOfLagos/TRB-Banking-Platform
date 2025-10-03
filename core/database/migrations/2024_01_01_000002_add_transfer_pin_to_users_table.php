<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferPinToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('transfer_pin', 60)->nullable()->after('password'); // Hashed transfer PIN
            $table->tinyInteger('transfer_pin_verified')->default(0)->after('transfer_pin'); // 0 = Not set, 1 = Set
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['transfer_pin', 'transfer_pin_verified']);
        });
    }
}