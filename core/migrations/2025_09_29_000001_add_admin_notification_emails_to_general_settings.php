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
        // Add admin notification emails column to general_settings table
        if (Schema::hasTable('general_settings')) {
            Schema::table('general_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('general_settings', 'admin_notification_emails')) {
                    $table->text('admin_notification_emails')->nullable()->after('email_from');
                }
            });
            
            // Set default admin notification emails
            DB::table('general_settings')->update([
                'admin_notification_emails' => 'admin@yourdomain.com,support@yourdomain.com'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('general_settings')) {
            Schema::table('general_settings', function (Blueprint $table) {
                if (Schema::hasColumn('general_settings', 'admin_notification_emails')) {
                    $table->dropColumn('admin_notification_emails');
                }
            });
        }
    }
};