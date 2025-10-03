<?php

namespace App\Http\Controllers\Api;

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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WireTransferController extends Controller {
    public function wireTransfer() {
        $setting = WireTransferSetting::first();
        if (!$setting) {
            $notify[] = 'The wire transfer system is not currently available';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $form = Form::where('act', 'wire_transfer')->first();
        if (!$form) {
            $notify[] = 'Wire transfer form data not found';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $notify[] = 'Wire Transfer';
        return response()->json([
            'remark'  => 'wire_transfer',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'setting' => $setting,
                'form'    => $form,
            ],
        ]);
    }

    public function transferRequest(Request $request) {

        $wireTransferSetting = WireTransferSetting::first();
        if (!$wireTransferSetting) {
            $notify[] = 'The wire transfer system is not currently available';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $formProcessor = new FormProcessor();
        $form          = Form::where('act', 'wire_transfer')->first();
        if (!$form) {
            $notify[] = 'Wire transfer form data not found';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        
        $user = auth()->user();
        $validationRules = [
            'amount' => 'required',
        ];
        
        // Add billing code and transfer PIN validation rules if required
        if ($user->requiresBillingCodes()) {
            $requiredCodes = $user->requiredBillingCodes()->get();
            foreach ($requiredCodes as $code) {
                $validationRules['billing_code_' . strtolower($code->code_type)] = 'required';
            }
        }
        
        if ($user->hasTransferPin()) {
            $validationRules['transfer_pin'] = 'required|digits:4';
        }
        
        $validator = Validator::make($request->all(), $validationRules);

        $userData = null;
        if (@$form->form_data) {
            $formData           = $form->form_data;
            $formProcessor      = new FormProcessor();
            $validationRule     = $formProcessor->valueValidation($formData);
            $validationRule     = mergeOtpField($validationRule);
            $formDataValidation = Validator::make($request->all(), $validationRule);

            if ($formDataValidation->fails()) {
                return response()->json([
                    'remark'  => 'validation_error',
                    'status'  => 'error',
                    'message' => ['error' => $formDataValidation->errors()->all()],
                ]);
            }
            $userData = $formProcessor->processFormData($request, $formData);
        }

        // Verify billing codes and transfer PIN before proceeding
        if ($user->requiresBillingCodes()) {
            $verificationResult = $this->verifyBillingCodes($request, $user);
            if ($verificationResult !== true) {
                return response()->json([
                    'remark'  => 'validation_error',
                    'status'  => 'error',
                    'message' => ['error' => [$verificationResult]],
                ]);
            }
        }
        
        if ($user->hasTransferPin() && $request->has('transfer_pin')) {
            $verificationResult = $this->verifyTransferPin($request->transfer_pin, $user);
            if ($verificationResult !== true) {
                return response()->json([
                    'remark'  => 'validation_error',
                    'status'  => 'error',
                    'message' => ['error' => [$verificationResult]],
                ]);
            }
        }

        $this->checkTransferAvailability($request->amount, $wireTransferSetting, $validator);
        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $additionalData = [
            'amount'           => $request->amount,
            'application_form' => $formProcessor->processFormData($request, $formData),
            'after_verified'   => 'api.transfer.wire.confirm',
            'billing_codes_verified' => $user->requiresBillingCodes(),
            'transfer_pin_verified' => $user->hasTransferPin()
        ];

        $otpManager = new OTPManager();
        return $otpManager->newOTP($wireTransferSetting, $request->auth_mode, 'WIRE_TRANSFER_OTP', $additionalData, true);
    }

    private function checkTransferAvailability($amount, $setting, $validator) {

        $charge      = $this->charge($amount, $setting);
        $finalAmount = $amount + $charge;
        $user        = auth()->user();

        if ($user->balance < $finalAmount) {
            return addCustomValidation($validator, 'error', 'Sorry! You don\'t have sufficient balance');
        }

        if ($amount < $setting->minimum_limit) {
            return addCustomValidation($validator, 'error', 'Sorry minimum transfer limit is ' . showAmount($setting->minimum_limit, currencyFormat:false));
        }

        if ($amount > $setting->maximum_limit) {
            return addCustomValidation($validator, 'error', 'Sorry maximum transfer limit is ' . showAmount($setting->maximum_limit, currencyFormat:false));
        }

        $todaysData = BalanceTransfer::wireTransfer()
            ->notRejected()
            ->where('user_id', $user->id)
            ->whereDate('created_at', now())
            ->selectRaw('count(id) as total_transfer, sum(amount) as total_amount')
            ->first();

        if (!$todaysData) {
            return addCustomValidation($validator, 'error', 'Today\'s data not found');
        }
        $todaysTotalAmount = $todaysData['total_amount'] ?? 0;
        $todaysTotalCount  = $todaysData['total_transfer'];

        if ($todaysTotalAmount + $amount > $setting->daily_maximum_limit) {
            return addCustomValidation($validator, 'error', 'Sorry you are exceeding the daily transfer limit');
        }

        if ($todaysTotalCount > $setting->daily_total_transaction) {
            return addCustomValidation($validator, 'error', 'Sorry you have already reached the daily transfer limit of ' . $setting->daily_total_transaction . 'times');
        }

        $thisMonthData = BalanceTransfer::wireTransfer()
            ->notRejected()
            ->where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('count(id) as total_transfer, sum(amount) as total_amount')
            ->first();
        if (!$thisMonthData) {
            return addCustomValidation($validator, 'error', 'This month data not found');
        }
        $thisMonthTotalAmount = $thisMonthData['total_amount'] ?? 0;
        $thisMonthTotalCount  = $thisMonthData['total_transfer'];

        if ($thisMonthTotalAmount + $amount > $setting->monthly_maximum_limit) {
            return addCustomValidation($validator, 'error', 'Sorry you are exceeding the monthly transfer limit');
        }

        if ($thisMonthTotalCount > $setting->monthly_total_transaction) {
            return addCustomValidation($validator, 'error', 'Sorry you have already reached the monthly transfer limit of ' . $setting->monthly_total_transaction . 'times');
        }
    }

    private function charge($amount, $setting) {
        $percentCharge = $amount * $setting->percent_charge / 100;
        return $setting->fixed_charge + $percentCharge;
    }

    public function confirm($id) {
        $verification = OtpVerification::find($id);
        if (!$verification) {
            $notify[] = 'Verification not found';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $setting   = $verification->verifiable;
        $amount    = $verification->additional_data->amount;
        $user      = auth()->user();
        $validator = Validator::make(request()->all(), []);

        OTPManager::checkVerificationData($verification, WireTransferSetting::class, true, $validator);
        
        // Security check: Verify that billing codes and transfer PIN were properly verified
        $additionalData = $verification->additional_data;
        
        if ($user->requiresBillingCodes()) {
            if (!isset($additionalData->billing_codes_verified) || !$additionalData->billing_codes_verified) {
                return response()->json([
                    'remark'  => 'validation_error',
                    'status'  => 'error',
                    'message' => ['error' => ['Security verification failed. Billing codes verification is required.']],
                ]);
            }
        }
        
        if ($user->hasTransferPin()) {
            if (!isset($additionalData->transfer_pin_verified) || !$additionalData->transfer_pin_verified) {
                return response()->json([
                    'remark'  => 'validation_error',
                    'status'  => 'error',
                    'message' => ['error' => ['Security verification failed. Transfer PIN verification is required.']],
                ]);
            }
        }
        
        $this->checkTransferAvailability($amount, $setting, $validator);

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
        $transaction->charge       = $transfer->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Wire Transfer';
        $transaction->trx          = $transfer->trx;
        $transaction->remark       = "wire_transfer";
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New wire transfer request';
        $adminNotification->click_url = urlPath('admin.transfers.details', $transfer->id);
        $adminNotification->save();

        $accountName   = $transfer->wireTransferAccountName();
        $accountNumber = $transfer->wireTransferAccountNumber();

        notify($user, 'WIRE_TRANSFER_REQUEST_SEND', [
            "sender_account_number"    => $transfer->user->account_number,
            "sender_account_name"      => $transfer->user->username,
            "recipient_account_number" => @$accountNumber->value,
            "recipient_account_name"   => @$accountName->value,
            "sending_amount"           => $transfer->amount,
            "charge"                   => $transfer->charge,
            "final_amount"             => $finalAmount,
        ]);

        $notify[] = "Transfer request sent successfully";
        return response()->json([
            'remark'  => 'validation_error',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function details($id) {
        $transfer = BalanceTransfer::wireTransfer()->where('user_id', auth()->id())->where('id', $id)->first();
        if (!$transfer) {
            $notify[] = "Wire Transfer not found";
            return response()->json([
                'remark'  => 'transfer_not_found',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $data     = @$transfer->wire_transfer_data;
        $html     = view('components.view-form-data', compact('data'))->render();
        $notify[] = 'Transfer Detail';
        return response()->json([
            'remark'  => 'transfer_detail',
            'status'  => 'error',
            'message' => ['error' => $notify],
            'data'    => [
                'html' => $html,
            ],
        ]);
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
                return 'Billing code for ' . $code->code_type . ' is required';
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
                return 'Invalid or expired billing code for ' . $code->code_type;
            }
        }
        
        return true;
    }
    
    /**
     * Verify transfer PIN
     */
    private function verifyTransferPin($pin, $user) {
        if (!$user->verifyTransferPin($pin)) {
            return 'Invalid transfer PIN';
        }
        
        return true;
    }
}
