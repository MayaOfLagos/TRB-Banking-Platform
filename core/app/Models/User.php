<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, UserNotify;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
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
        'banking_profile_completed_at',
        'dial_code',
        'country_code',
        'city',
        'state',
        'zip',
        'mobile',
        'country_name',
        'address',
        'image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','ver_code','balance','kyc_data'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime',
        'date_of_birth' => 'date',
        'banking_profile_complete' => 'boolean',
        'banking_profile_completed_at' => 'datetime'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',Status::PAYMENT_INITIATE);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',Status::PAYMENT_INITIATE);
    }

    public function fdr()
    {
        return $this->hasMany(Fdr::class, 'user_id');
    }

    public function dps()
    {
        return $this->hasMany(Dps::class, 'user_id');
    }
    public function loan()
    {
        return $this->hasMany(Loan::class, 'user_id');
    }
    public function transfer()
    {
        return $this->hasMany(BalanceTransfer::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function branchStaff()
    {
        return $this->belongsTo(BranchStaff::class, 'branch_staff_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'ref_by');
    }

    public function referees()
    {
        return $this->hasMany(User::class, 'ref_by');
    }

    public function beneficiaryTypes()
    {
        return $this->morphMany(Beneficiary::class, 'beneficiary', 'beneficiary_type', 'beneficiary_id');
    }

    public function allReferees()
    {
        return $this->referees()->with('allReferees:id,ref_by,username');
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function withdrawalControl()
    {
        return $this->hasOne(WithdrawalControl::class);
    }

    public function hasWithdrawalControl()
    {
        return $this->withdrawalControl()->exists();
    }

    public function getWithdrawalStatus()
    {
        if (!$this->hasWithdrawalControl()) {
            return WithdrawalControl::STATUS_ALLOWED;
        }

        return $this->withdrawalControl->status;
    }

    public function canWithdraw()
    {
        if (!$this->hasWithdrawalControl()) {
            return true;
        }

        return $this->withdrawalControl->status === WithdrawalControl::STATUS_ALLOWED;
    }

    public function getWithdrawalBlockReason()
    {
        if (!$this->hasWithdrawalControl() || $this->canWithdraw()) {
            return null;
        }

        return $this->withdrawalControl->reason;
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn () => $this->dial_code . $this->mobile,
        );
    }

    public function formattedDateOfBirth(): Attribute
    {
        return new Attribute(
            get: fn () => $this->date_of_birth ? \Carbon\Carbon::parse($this->date_of_birth)->format('M d, Y') : null,
        );
    }

    public function age(): Attribute
    {
        return new Attribute(
            get: fn () => $this->date_of_birth ? \Carbon\Carbon::parse($this->date_of_birth)->diffInYears(now()) : null,
        );
    }

    public function bankingProfileCompletionPercentage(): Attribute
    {
        return new Attribute(
            get: function () {
                $requiredFields = [
                    'title', 'full_legal_name', 'date_of_birth', 'gender', 'nationality',
                    'account_type_preference', 'preferred_currency', 'source_of_funds', 
                    'purpose_of_account', 'employment_status', 'occupation'
                ];
                
                $completedFields = 0;
                foreach ($requiredFields as $field) {
                    if (!empty($this->$field)) {
                        $completedFields++;
                    }
                }
                
                return round(($completedFields / count($requiredFields)) * 100);
            }
        );
    }

    // SCOPES
    public function scopeProfileInComplete($query) {
        return $query->where('profile_complete', Status::NO);
    }

    public function scopeProfileCompleted($query) {
        return $query->where('profile_complete', Status::YES);
    }

    public function scopeActive($query)
    {
        return $query->where('users.status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('users.status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance','>', 0);
    }

    public function scopeBankingProfileIncomplete($query) {
        return $query->where('banking_profile_complete', false);
    }

    public function scopeBankingProfileComplete($query) {
        return $query->where('banking_profile_complete', true);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    // Billing Codes Relationship
    public function billingCodes()
    {
        return $this->hasMany(UserBillingCode::class);
    }

    public function activeBillingCodes()
    {
        return $this->hasMany(UserBillingCode::class)->active();
    }

    public function pendingBillingCodes()
    {
        return $this->hasMany(UserBillingCode::class)
                    ->active()
                    ->unused()
                    ->notExpired();
    }

    public function requiredBillingCodes()
    {
        return $this->hasMany(UserBillingCode::class)
                    ->active()
                    ->required()
                    ->unused()
                    ->notExpired();
    }

    // Rebate System Relationships
    public function rebates()
    {
        return $this->hasMany(RebateTransaction::class);
    }

    public function rebateTransactions()
    {
        return $this->hasMany(RebateTransaction::class);
    }

    public function productUploads()
    {
        return $this->hasMany(ProductUpload::class);
    }

    public function pendingRebates()
    {
        return $this->hasMany(RebateTransaction::class)->where('status', 'pending');
    }

    public function approvedRebates()
    {
        return $this->hasMany(RebateTransaction::class)->where('status', 'approved');
    }

    public function totalRebateEarnings()
    {
        return $this->hasMany(RebateTransaction::class)
                    ->where('type', 'credit')
                    ->where('status', 'completed');
    }

    public function monthlyRebateEarnings($month = null, $year = null)
    {
        $query = $this->hasMany(RebateTransaction::class)
                      ->where('type', 'credit')
                      ->where('status', 'completed');
        
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        return $query;
    }

    // Transfer PIN Methods
    public function hasTransferPin()
    {
        return !empty($this->transfer_pin) && $this->transfer_pin_verified;
    }

    public function setTransferPin($pin)
    {
        $this->transfer_pin = bcrypt($pin);
        $this->transfer_pin_verified = 1;
        $this->save();
    }

    public function verifyTransferPin($pin)
    {
        return $this->hasTransferPin() && password_verify($pin, $this->transfer_pin);
    }

    public function requiresBillingCodes()
    {
        return gs('billing_codes_enabled') && $this->requiredBillingCodes()->exists();
    }

    public function getPendingBillingAmount()
    {
        return $this->requiredBillingCodes()->sum('amount');
    }

}
