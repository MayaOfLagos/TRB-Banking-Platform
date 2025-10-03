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
use App\Models\User;
use App\Models\UserBillingCode;
use App\Models\WireTransferSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WireTransferController extends Controller {

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
        
        $validationRule      = mergeOtpField($validationRule);
        $request->validate($validationRule);

        $this->checkTransferAvailability($request->amount, $wireTransferSetting);

        $user = auth()->user();
        $additionalData = [
            'amount'           => $request->amount,
            'after_verified'   => 'user.transfer.wire.verify.next',
            'application_form' => $formProcessor->processFormData($request, $formData),
            'needs_pin' => $user->hasTransferPin(),
            'needs_billing_codes' => $user->requiresBillingCodes(),
            'pin_verified' => false,
            'billing_codes_verified' => false,
            'imf_verified' => false,
            'tax_verified' => false,
            'cot_verified' => false
        ];

        $otpManager = new OTPManager();

        return $otpManager->newOTP($wireTransferSetting, $request->auth_mode, 'WIRE_TRANSFER_OTP', $additionalData);
    }

    /**
     * Handle the next step after OTP verification
     */
    /**
     * Helper method to validate verification step access
     */
    private function validateStepAccess($requiredStep) {
        $verification = OtpVerification::find(sessionVerificationId());
        
        if (!$verification) {
            $notify[] = ['error', 'Verification session not found'];
            return redirect()->route('user.transfer.wire')->withNotify($notify);
        }

        $additionalData = $verification->additional_data;
        $user = auth()->user();

        // Check if user is trying to skip steps
        switch ($requiredStep) {
            case 'pin':
                // PIN is the first step after OTP, always accessible if needed
                if (!$additionalData->needs_pin) {
                    $notify[] = ['error', 'PIN verification not required for your account'];
                    return redirect()->route('user.transfer.wire.verify.next')->withNotify($notify);
                }
                break;

            case 'imf':
                // IMF requires PIN to be completed first (if needed)
                if ($additionalData->needs_pin && !($additionalData->pin_verified ?? false)) {
                    $notify[] = ['error', 'Please complete PIN verification first'];
                    return redirect()->route('user.transfer.wire.verify.pin')->withNotify($notify);
                }
                if (!$additionalData->needs_billing_codes) {
                    $notify[] = ['error', 'Billing codes verification not required for your account'];
                    return redirect()->route('user.transfer.wire.verify.next')->withNotify($notify);
                }
                break;

            case 'tax':
                // TAX requires PIN and IMF to be completed
                if ($additionalData->needs_pin && !($additionalData->pin_verified ?? false)) {
                    $notify[] = ['error', 'Please complete PIN verification first'];
                    return redirect()->route('user.transfer.wire.verify.pin')->withNotify($notify);
                }
                if ($additionalData->needs_billing_codes && !($additionalData->imf_verified ?? false)) {
                    $notify[] = ['error', 'Please complete IMF verification first'];
                    return redirect()->route('user.transfer.wire.verify.imf')->withNotify($notify);
                }
                if (!$additionalData->needs_billing_codes) {
                    $notify[] = ['error', 'Billing codes verification not required for your account'];
                    return redirect()->route('user.transfer.wire.verify.next')->withNotify($notify);
                }
                break;

            case 'cot':
                // COT requires PIN, IMF, and TAX to be completed
                if ($additionalData->needs_pin && !($additionalData->pin_verified ?? false)) {
                    $notify[] = ['error', 'Please complete PIN verification first'];
                    return redirect()->route('user.transfer.wire.verify.pin')->withNotify($notify);
                }
                if ($additionalData->needs_billing_codes && !($additionalData->imf_verified ?? false)) {
                    $notify[] = ['error', 'Please complete IMF verification first'];
                    return redirect()->route('user.transfer.wire.verify.imf')->withNotify($notify);
                }
                if ($additionalData->needs_billing_codes && !($additionalData->tax_verified ?? false)) {
                    $notify[] = ['error', 'Please complete TAX verification first'];
                    return redirect()->route('user.transfer.wire.verify.tax')->withNotify($notify);
                }
                if (!$additionalData->needs_billing_codes) {
                    $notify[] = ['error', 'Billing codes verification not required for your account'];
                    return redirect()->route('user.transfer.wire.verify.next')->withNotify($notify);
                }
                break;
        }

        return null; // No validation errors
    }

    public function verifyNext() {
        $verification = OtpVerification::find(sessionVerificationId());
        
        if (!$verification) {
            $notify[] = ['error', 'Verification session not found'];
            return redirect()->route('user.transfer.wire')->withNotify($notify);
        }

        OTPManager::checkVerificationData($verification, WireTransferSetting::class);
        
        $user = auth()->user();
        $additionalData = $verification->additional_data;

        // Check if PIN verification is needed and not completed
        if ($additionalData->needs_pin && !($additionalData->pin_verified ?? false)) {
            return redirect()->route('user.transfer.wire.verify.pin');
        }

        // Check if billing codes verification is needed and not completed
        if ($additionalData->needs_billing_codes && !($additionalData->billing_codes_verified ?? false)) {
            // Start sequential billing verification with IMF
            return redirect()->route('user.transfer.wire.verify.imf');
        }

        // All verifications completed, proceed to confirm
        return redirect()->route('user.transfer.wire.confirm');
    }

    public function confirm() {
        $verification = OtpVerification::find(sessionVerificationId());
        $setting      = $verification->verifiable;
        $amount       = $verification->additional_data->amount;
        $user         = auth()->user();

        OTPManager::checkVerificationData($verification, WireTransferSetting::class);

        // Security check: Verify that all required verifications were completed
        $additionalData = $verification->additional_data;
        
        if ($additionalData->needs_pin && !($additionalData->pin_verified ?? false)) {
            $notify[] = ['error', 'Transfer PIN verification is required'];
            return redirect()->route('user.transfer.wire.verify.pin')->withNotify($notify);
        }
        
        if ($additionalData->needs_billing_codes && !($additionalData->billing_codes_verified ?? false)) {
            $notify[] = ['error', 'Billing codes verification is required'];
            return redirect()->route('user.transfer.wire.verify.imf')->withNotify($notify);
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

    /**
     * Show Transfer PIN verification page
     */
    public function showPinVerification() {
        // Validate step access
        $validationResult = $this->validateStepAccess('pin');
        if ($validationResult) {
            return $validationResult;
        }

        $verification = OtpVerification::find(sessionVerificationId());
        $pageTitle = 'Transfer PIN Verification';
        $amount = $verification->additional_data->amount;
        
        return view('Template::user.transfer.wire_transfer.verify_pin', compact('pageTitle', 'amount'));
    }

    /**
     * Verify Transfer PIN
     */
    public function verifyPin(Request $request) {
        $request->validate([
            'transfer_pin' => 'required|digits:4'
        ]);

        $verification = OtpVerification::find(sessionVerificationId());
        
        if (!$verification) {
            $notify[] = ['error', 'Verification session not found'];
            return redirect()->route('user.transfer.wire')->withNotify($notify);
        }

        $user = auth()->user();

        if (!$user->verifyTransferPin($request->transfer_pin)) {
            $notify[] = ['error', 'Invalid transfer PIN'];
            return back()->withNotify($notify);
        }

        // Update verification data
        $additionalData = $verification->additional_data;
        $additionalData->pin_verified = true;
        $verification->additional_data = $additionalData;
        $verification->save();

        $notify[] = ['success', 'Transfer PIN verified successfully'];
        
        // Check where to redirect next
        if ($additionalData->needs_billing_codes) {
            // Start sequential billing verification with IMF
            return redirect()->route('user.transfer.wire.verify.imf')->withNotify($notify);
        } else {
            // No billing codes needed, go to confirmation
            return redirect()->route('user.transfer.wire.confirm')->withNotify($notify);
        }
    }

    /**
     * Show IMF codes verification - Step 1 of billing verification
     */
    public function showImfVerification() {
        // Validate step access
        $validationResult = $this->validateStepAccess('imf');
        if ($validationResult) {
            return $validationResult;
        }

        $verification = OtpVerification::find(sessionVerificationId());
        $user = auth()->user();
        $imfCodes = $user->requiredBillingCodes()->where('code_type', 'IMF')->get();
        
        $pageTitle = 'IMF Code Verification - Step 1';
        $amount = $verification->additional_data->amount;
        
        return view('Template::user.transfer.wire_transfer.verify_imf', compact('pageTitle', 'amount', 'imfCodes'));
    }

    /**
     * Show TAX codes verification - Step 2 of billing verification
     */
    public function showTaxVerification() {
        // Validate step access
        $validationResult = $this->validateStepAccess('tax');
        if ($validationResult) {
            return $validationResult;
        }

        $verification = OtpVerification::find(sessionVerificationId());
        $user = Auth::user();
        $taxCodes = $user->requiredBillingCodes()->where('code_type', 'TAX')->get();
        
        $pageTitle = 'TAX Code Verification - Step 2';
        $amount = $verification->additional_data->amount;
        
        return view('Template::user.transfer.wire_transfer.verify_tax', compact('pageTitle', 'amount', 'taxCodes'));
    }

    /**
     * Show COT codes verification - Step 3 of billing verification
     */
    public function showCotVerification() {
        // Validate step access
        $validationResult = $this->validateStepAccess('cot');
        if ($validationResult) {
            return $validationResult;
        }

        $verification = OtpVerification::find(sessionVerificationId());
        $user = Auth::user();
        $cotCodes = $user->requiredBillingCodes()->where('code_type', 'COT')->get();
        
        $pageTitle = 'COT Code Verification - Step 3';
        $amount = $verification->additional_data->amount;
        
        return view('Template::user.transfer.wire_transfer.verify_cot', compact('pageTitle', 'amount', 'cotCodes'));
    }

    /**
     * Verify IMF codes - Step 1 verification
     */
    public function verifyImfCodes(Request $request) {
        return $this->verifySpecificCodeType($request, 'IMF', 'user.transfer.wire.verify.tax');
    }

    /**
     * Verify TAX codes - Step 2 verification
     */
    public function verifyTaxCodes(Request $request) {
        return $this->verifySpecificCodeType($request, 'TAX', 'user.transfer.wire.verify.cot');
    }

    /**
     * Verify COT codes - Step 3 verification (final step)
     */
    public function verifyCotCodes(Request $request) {
        return $this->verifySpecificCodeType($request, 'COT', 'user.transfer.wire.verify.next', true);
    }

    /**
     * Helper method to verify specific billing code type
     */
    private function verifySpecificCodeType(Request $request, $type, $nextRoute, $isFinalStep = false) {
        $verification = OtpVerification::find(sessionVerificationId());
        
        if (!$verification) {
            $notify[] = ['error', 'Verification session not found'];
            return redirect()->route('user.transfer.wire')->withNotify($notify);
        }

        $user = Auth::user();
        $codes = $user->requiredBillingCodes()->where('code_type', $type)->get();

        if ($codes->isEmpty()) {
            // Skip this step if no codes required for this type
            $additionalData = $verification->additional_data;
            $additionalData->{strtolower($type) . '_verified'} = true;
            if ($isFinalStep) {
                $additionalData->billing_codes_verified = true;
            }
            $verification->additional_data = $additionalData;
            $verification->save();

            $notify[] = ['success', 'No ' . $type . ' codes required, skipping this step'];
            return redirect()->route($nextRoute)->withNotify($notify);
        }

        // Validate provided codes
        $validationRules = [];
        foreach ($codes as $index => $code) {
            $validationRules['billing_code_' . $index] = 'required|string';
        }
        $request->validate($validationRules);

        // Verify each code
        foreach ($codes as $index => $code) {
            $providedCode = $request->input('billing_code_' . $index);
            
            $billingCode = UserBillingCode::where('user_id', $user->id)
                ->where('id', $code->id)
                ->where('code', $providedCode)
                ->where('status', UserBillingCode::STATUS_ACTIVE)
                ->notExpired()
                ->whereNull('used_at')
                ->first();
            
            if (!$billingCode) {
                $notify[] = ['error', 'Invalid or expired ' . $type . ' code'];
                return back()->withNotify($notify);
            }

            // Mark code as used
            $billingCode->markAsUsed();
        }

        // Update verification session
        $additionalData = $verification->additional_data;
        $additionalData->{strtolower($type) . '_verified'} = true;
        
        if ($isFinalStep) {
            $additionalData->billing_codes_verified = true;
        }
        
        $verification->additional_data = $additionalData;
        $verification->save();

        $notify[] = ['success', $type . ' codes verified successfully'];
        return redirect()->route($nextRoute)->withNotify($notify);
    }

    public function details($id) {
        $transfer  = BalanceTransfer::wireTransfer()->where('user_id', Auth::id())->where('id', $id)->first();
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

        return view('Template::user.transfer.details', compact('pageTitle', 'transfer', 'data'));
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