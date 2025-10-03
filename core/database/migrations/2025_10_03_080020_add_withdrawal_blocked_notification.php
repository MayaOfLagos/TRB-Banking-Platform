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
        $emailBody = '<div>Dear {{fullname}},</div>
<div><br></div>
<div>Your withdrawal request has been blocked due to account restrictions.</div>
<div><br></div>
<div><strong>Transaction Details:</strong></div>
<div>Amount: {{amount}} {{site_currency}}</div>
<div>Method: {{method_name}}</div>
<div>Transaction ID: {{trx}}</div>
<div>Date: {{time}}</div>
<div><br></div>
<div><strong>Restriction Status:</strong> {{status}}</div>
<div><br></div>
<div><strong>Reason:</strong></div>
<div>{{reason}}</div>
<div><br></div>
<div>Please contact our support team if you have any questions regarding this restriction.</div>
<div><br></div>
<div>Best regards,<br>{{site_name}} Team</div>';

        $smsBody = 'Dear {{fullname}}, Your withdrawal request for {{amount}} {{site_currency}} has been blocked. Status: {{status}}. Reason: {{reason}}. Please contact support.';

        $pushBody = 'Your withdrawal request for {{amount}} {{site_currency}} has been blocked. Status: {{status}}. Please check your email for details.';

        DB::table('notification_templates')->insert([
            'act' => 'WITHDRAWAL_BLOCKED',
            'name' => 'Withdrawal Blocked',
            'subject' => 'Withdrawal Request Blocked - {{status}}',
            'email_body' => $emailBody,
            'sms_body' => $smsBody,
            'push_body' => $pushBody,
            'shortcodes' => json_encode(['status', 'reason', 'amount', 'method_name', 'trx', 'time', 'fullname', 'site_currency', 'site_name']),
            'email_status' => 1,
            'sms_status' => 1,
            'push_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('notification_templates')->where('act', 'WITHDRAWAL_BLOCKED')->delete();
    }
};
