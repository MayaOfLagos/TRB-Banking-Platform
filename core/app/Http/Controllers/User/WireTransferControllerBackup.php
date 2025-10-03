<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\OTPManager;
use App\Models\AdminNotification;
use App\Models\BalanceTransfer;
use App\Models\Form;
use App\Models\OtpVerification;
use App\Models\Transaction;
use App\Models\UserBillingCode;
use App\Models\WireTransferSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WireTransferControllerBackup extends Controller {

    public function wireTransfer() {
        $pageTitle = "Wire Transfer";
        $setting   = WireTransferSetting::first();

        if (!$setting) {
            $notify[] = ['error', 'The wire transfer system is not currently available'];
            return back()->withNotify($notify);
        }

        return view('Template::user.transfer.wire_transfer.form', compact('pageTitle', 'setting'));
    }

    public function transferRequest(Request $request) {
        $wireTransferSetting = WireTransferSetting::firstOrFail();
        $formProcessor       = new FormProcessor();
        $form                = Form::where('act', 'wire_transfer')->first();
        
        if (!$form) {
            $notify[] = ['error', 'Wire transfer form configuration not found. Please contact administrator.'];
            return back()->withNotify($notify);
        }
        
        $formData            = $form->form_data;
        $validationRule      = $formProcessor->valueValidation($formData);
        
        // Add billing code and transfer PIN validation rules if required
        $user = auth()->user();
        
        // Check if billing codes are enabled and user has required codes
        if ($user->requiresBillingCodes()) {
            $requiredCodes = $user->requiredBillingCodes()->get();
            foreach ($requiredCodes as $code) {
                $validationRule['billing_code_' . strtolower($code->code_type)] = 'required';
            }
            $validationRule['transfer_pin'] = 'required|digits:4';
        } elseif ($user->hasTransferPin()) {
            // Even if no billing codes, require PIN if user has one set
            $validationRule['transfer_pin'] = 'required|digits:4';
        }
        
        $validationRule      = mergeOtpField($validationRule);
        $request->validate($validationRule);

        // Verify billing codes and transfer PIN before proceeding
        if ($user->requiresBillingCodes()) {
            $this->verifyBillingCodes($request, $user);
        }
        
        if ($user->hasTransferPin() && $request->has('transfer_pin')) {
            $this->verifyTransferPin($request->transfer_pin, $user);
        }

        $this->checkTransferAvailability($request->amount, $wireTransferSetting);

        $additionalData = [
            'amount'           => $request->amount,
            'after_verified'   => 'user.transfer.wire.confirm',
            'application_form' => $formProcessor->processFormData($request, $formData),
            'billing_codes_verified' => $user->requiresBillingCodes(),
            'transfer_pin_verified' => $user->hasTransferPin()
        ];

        $otpManager = new OTPManager();

        return $otpManager->newOTP($wireTransferSetting, $request->auth_mode, 'WIRE_TRANSFER_OTP', $additionalData);
    }

    public function confirm() {
        $verification = OtpVerification::find(sessionVerificationId());
        $setting      = $verification->verifiable;
        $amount       = $verification->additional_data->amount;
        $user         = auth()->user();

        OTPManager::checkVerificationData($verification, WireTransferSetting::class);

        // Security check: Verify that billing codes and transfer PIN were properly verified
        $additionalData = $verification->additional_data;
        
        if ($user->requiresBillingCodes()) {
            if (!isset($additionalData->billing_codes_verified) || !$additionalData->billing_codes_verified) {
                $notify[] = ['error', 'Security verification failed. Billing codes verification is required.'];
                return redirect()->route('user.transfer.wire')->withNotify($notify);
            }
        }
        
        if ($user->hasTransferPin()) {
            if (!isset($additionalData->transfer_pin_verified) || !$additionalData->transfer_pin_verified) {
                $notify[] = ['error', 'Security verification failed. Transfer PIN verification is required.'];
                return redirect()->route('user.transfer.wire')->withNotify($notify);
            }
        }

        $this->checkTransferAvailability($amount, $setting);

        $charge      = $this->charge($amount, $setting);
        $finalAmount = $amount + $charge;

        $transfer                     = new BalanceTransfer();
        $transfer->user_id            = $user->id;
        $transfer->trx                = getTrx();
        $transfer->beneficiary_id     = 0;
        $transfer->amount             = $amount;
        $transfer->charge             = $charge;
        $transfer->status             = Status::TRANSFER_PENDING;
        $transfer->wire_transfer_data = $verification->additional_data->application_form;
        $transfer->save();

        $user->balance -= $finalAmount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $finalAmount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->details      = showAmount($amount) . ' ' . gs('cur_text') . ' wire transfer';
        $transaction->trx          = $transfer->trx;
        $transaction->remark       = 'wire_transfer';
        $transaction->save();

        $notify[] = ['success', 'Your wire transfer request has been submitted successfully'];

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New Wire Transfer Request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.transfers.wire');
        $adminNotification->save();

        $wireTransferAccountName = $transfer->wireTransferAccountName();
        $wireTransferAccountNumber = $transfer->wireTransferAccountNumber();

        notify($user, 'WIRE_TRANSFER_REQUEST_SEND', [
            "sender_account_number"    => $transfer->user->account_number,
            "sender_account_name"      => $transfer->user->username,
            "recipient_account_number" => @$wireTransferAccountNumber->value,
            "recipient_account_name"   => @$wireTransferAccountName->value,
            "sending_amount"           => $transfer->amount,
            "charge"                   => $transfer->charge,
            "final_amount"             => $finalAmount,
        ]);

        return to_route('user.transfer.wire.details', $transfer->id)->withNotify($notify);
    }

    public function details($id) {
        $transfer  = BalanceTransfer::wireTransfer()->where('user_id', auth()->id())->where('id', $id)->first();
        $pageTitle = 'Wire Transfer Details';

        if (!$transfer) {
            $notify[] = ['error', 'Transfer not found'];
            return to_route('user.transfer.wire')->withNotify($notify);
        }

        $data = $transfer->wire_transfer_data;

        if (request()->expectsJson()) {
            $html = view('components.view-form-data', compact('data'))->render();
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        return view('Template::user.transfer.wire_transfer.details', compact('pageTitle', 'transfer', 'data'));
    }

    /**
     * Verify billing codes for wire transfer
     */
    private function verifyBillingCodes($request, $user) {
        $requiredCodes = $user->requiredBillingCodes()->get();
        
        foreach ($requiredCodes as $code) {
            $fieldName = 'billing_code_' . strtolower($code->code_type);
            $providedCode = $request->input($fieldName);
            
            if (!$providedCode) {
                throw ValidationException::withMessages(['error' => 'Billing code for ' . $code->code_type . ' is required']);
            }
            
            // Find matching billing code
            $billingCode = UserBillingCode::where('user_id', $user->id)
                ->where('code_type', $code->code_type)
                ->where('code', $providedCode)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->whereNull('used_at')
                ->first();
            
            if (!$billingCode) {
                throw ValidationException::withMessages(['error' => 'Invalid or expired billing code for ' . $code->code_type]);
            }
        }
    }
    
    /**
     * Verify transfer PIN
     */
    private function verifyTransferPin($pin, $user) {
        if (!$user->verifyTransferPin($pin)) {
            throw ValidationException::withMessages(['error' => 'Invalid transfer PIN']);
        }
    }

    /**
     * Check if the transfer can be made with the given amount and setting
     */
    private function checkTransferAvailability($amount, $setting) {
        $charge      = $this->charge($amount, $setting);
        $finalAmount = $amount + $charge;
        $user        = auth()->user();

        if ($user->balance < $finalAmount) {
            throw ValidationException::withMessages(['error' => 'Sorry! You don\'t have sufficient balance']);
        }

        if ($amount < $setting->minimum_limit) {
            throw ValidationException::withMessages(['error' => 'Sorry minimum transfer limit is ' . showAmount($setting->minimum_limit)]);
        }

        if ($amount > $setting->maximum_limit) {
            throw ValidationException::withMessages(['error' => 'Sorry maximum transfer limit is ' . showAmount($setting->maximum_limit)]);
        }

        $todayTransfer      = BalanceTransfer::wireTransfer()->where('user_id', $user->id)->whereDate('created_at', now())->sum('amount');
        $dailyRemaining     = $setting->daily_maximum_limit - $todayTransfer;

        if ($amount > $dailyRemaining) {
            throw ValidationException::withMessages(['error' => 'Sorry! Daily transfer limit exceeded']);
        }

        $monthlyTransfer    = BalanceTransfer::wireTransfer()->where('user_id', $user->id)->whereMonth('created_at', now())->sum('amount');
        $monthlyRemaining   = $setting->monthly_maximum_limit - $monthlyTransfer;

        if ($amount > $monthlyRemaining) {
            throw ValidationException::withMessages(['error' => 'Sorry! Monthly transfer limit exceeded']);
        }

        $totalTodayTransfer    = BalanceTransfer::wireTransfer()->where('user_id', $user->id)->whereDate('created_at', now())->count();
        $totalMonthlyTransfer  = BalanceTransfer::wireTransfer()->where('user_id', $user->id)->whereMonth('created_at', now())->count();

        if ($totalTodayTransfer >= $setting->daily_total_transaction) {
            throw ValidationException::withMessages(['error' => 'Sorry! Daily transaction limit exceeded']);
        }

        if ($totalMonthlyTransfer >= $setting->monthly_total_transaction) {
            throw ValidationException::withMessages(['error' => 'Sorry! Monthly transaction limit exceeded']);
        }
    }

    /**
     * Calculate wire transfer charge
     */
    private function charge($amount, $setting) {
        return $setting->fixed_charge + ($amount * $setting->percent_charge) / 100;
    }
}