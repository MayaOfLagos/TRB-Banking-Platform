<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable push notifications for BAL_ADD and BAL_SUB templates
        DB::table('notification_templates')
            ->whereIn('act', ['BAL_ADD', 'BAL_SUB'])
            ->update(['push_status' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally disable push notifications for BAL_ADD and BAL_SUB templates
        DB::table('notification_templates')
            ->whereIn('act', ['BAL_ADD', 'BAL_SUB'])
            ->update(['push_status' => 0]);
    }
};
