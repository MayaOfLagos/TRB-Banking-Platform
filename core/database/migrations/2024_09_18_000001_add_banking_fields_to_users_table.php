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
        Schema::table('users', function (Blueprint $table) {
            // Banking Personal Information
            $table->string('title', 10)->nullable()->after('lastname'); // Mr, Mrs, Dr, etc.
            $table->string('full_legal_name', 255)->nullable()->after('title'); // Full name as on ID
            $table->date('date_of_birth')->nullable()->after('full_legal_name');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            $table->string('nationality', 100)->nullable()->after('gender');
            
            // Banking Account Preferences
            $table->enum('account_type_preference', [
                'savings', 
                'checking', 
                'business', 
                'premium', 
                'student', 
                'joint'
            ])->nullable()->after('nationality');
            
            $table->string('preferred_currency', 10)->default('USD')->after('account_type_preference');
            
            // Financial Information
            $table->enum('source_of_funds', [
                'employment',
                'business_income',
                'investment',
                'inheritance',
                'gift',
                'savings',
                'pension',
                'government_benefits',
                'other'
            ])->nullable()->after('preferred_currency');
            
            $table->enum('purpose_of_account', [
                'personal_banking',
                'business_operations',
                'savings_investment',
                'salary_deposit',
                'international_transfers',
                'bill_payments',
                'online_shopping',
                'other'
            ])->nullable()->after('source_of_funds');
            
            // Employment Information
            $table->enum('employment_status', [
                'employed_full_time',
                'employed_part_time',
                'self_employed',
                'business_owner',
                'unemployed',
                'student',
                'retired',
                'homemaker',
                'other'
            ])->nullable()->after('purpose_of_account');
            
            $table->string('occupation', 255)->nullable()->after('employment_status');
            
            // Banking Profile Completion Status
            $table->boolean('banking_profile_complete')->default(false)->after('occupation');
            $table->timestamp('banking_profile_completed_at')->nullable()->after('banking_profile_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'full_legal_name',
                'date_of_birth',
                'gender',
                'nationality',
                'account_type_preference',
                'preferred_currency',
                'source_of_funds',
                'purpose_of_account',
                'employment_status',
                'occupation',
                'banking_profile_complete',
                'banking_profile_completed_at'
            ]);
        });
    }
};