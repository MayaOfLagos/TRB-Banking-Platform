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
        Schema::table('general_settings', function (Blueprint $table) {
            $table->text('contact_address')->nullable()->after('idle_time_threshold');
            $table->string('contact_phone', 50)->nullable()->after('contact_address');
            $table->string('contact_email', 100)->nullable()->after('contact_phone');
            $table->string('social_facebook')->nullable()->after('contact_email');
            $table->string('social_twitter')->nullable()->after('social_facebook');
            $table->string('social_instagram')->nullable()->after('social_twitter');
            $table->string('social_linkedin')->nullable()->after('social_instagram');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'contact_address',
                'contact_phone',
                'contact_email',
                'social_facebook',
                'social_twitter',
                'social_instagram',
                'social_linkedin'
            ]);
        });
    }
};
