<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankingProfileRequest;
use App\Lib\FormProcessor;
use App\Services\UserTierService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Lib\GoogleAuthenticator;
use App\Models\BalanceTransfer;
use App\Models\Deposit;
use App\Models\DeviceToken;
use App\Models\Dps;
use App\Models\Fdr;
use App\Models\Form;
use App\Models\Loan;
use App\Models\ReferralSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller {
    protected $tierService;

    public function __construct(UserTierService $tierService)
    {
        $this->tierService = $tierService;
    }

    public function home() {
        $pageTitle = 'Dashboard';
        $user                     = Auth::user();
        $widget['total_deposit']  = Deposit::pending()->where('user_id', $user->id)->sum('amount');
        $widget['total_withdraw'] = Withdrawal::pending()->where('user_id', $user->id)->sum('amount');
        $widget['total_trx']      = Transaction::where('user_id', $user->id)->whereDate('created_at', now()->today())->count();
        $widget['total_fdr']      = Fdr::running()->where('user_id', $user->id)->count();
        $widget['total_loan']     = Loan::running()->where('user_id', $user->id)->count();
        $widget['total_dps']      = Dps::running()->where('user_id', $user->id)->count();

        // Rebate System Widgets
        if (gs('rebate_system_enabled')) {
            $widget['total_rebate_earned'] = 0;
            $widget['pending_rebate'] = 0;
            $widget['current_rebate_tier'] = 'Bronze';
            $widget['rebate_tier_rate'] = '1.0';
            $widget['rebate_success_rate'] = '95';
            
            // Check if rebate tables exist
            if (Schema::hasTable('rebate_transactions')) {
                $widget['total_rebate_earned'] = DB::table('rebate_transactions')
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->sum('rebate_amount');
                    
                $widget['pending_rebate'] = DB::table('rebate_transactions')
                    ->where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->sum('rebate_amount');
                    
                // Calculate current tier and rate using UserTierService
                $widget['current_rebate_tier'] = ucfirst($this->tierService->getUserTier($user));
                $widget['rebate_tier_rate'] = number_format($this->tierService->getTierMultiplier($user), 1);
                
                // Calculate success rate
                $totalTransactions = DB::table('rebate_transactions')
                    ->where('user_id', $user->id)
                    ->count();
                    
                $approvedTransactions = DB::table('rebate_transactions')
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->count();
                    
                if ($totalTransactions > 0) {
                    $widget['rebate_success_rate'] = number_format(($approvedTransactions / $totalTransactions) * 100, 0);
                }
            }
        }

        $credits = Transaction::where('user_id', $user->id)->where('trx_type', '+')->latest()->limit(5)->get();
        $debits  = Transaction::where('user_id', $user->id)->where('trx_type', '-')->latest()->limit(5)->get();
        return view('Template::user.dashboard', compact('pageTitle', 'user', 'credits', 'debits', 'widget'));
    }

    public function depositHistory(Request $request) {
        $pageTitle = 'Deposit History';
        $deposits = Auth::user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit.index', compact('pageTitle', 'deposits'));
    }

    public function details($trxNumber) {
        $pageTitle = 'Deposit Details';
        $deposit = Auth::user()->deposits()->where('trx', $trxNumber)->with(['gateway'])->orderBy('id', 'desc')->firstOrFail();
        return view('Template::user.deposit.details', compact('pageTitle', 'deposit'));
    }

    public function show2faForm() {
        $ga = new GoogleAuthenticator();
        $user = Auth::user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request) {
        $user = Auth::user();
        $request->validate([
            'key' => 'required',
            'code' => 'required|numeric|digits:6',
        ], [
            'code.numeric' => 'The verification code must be numeric',
            'code.digits' => 'The verification code must be exactly 6 digits',
        ]);
        
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code. Please make sure your device time is synced and try again.'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request) {
        $request->validate([
            'code' => 'required',
        ]);

        $user = Auth::user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions() {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        if (request()->today) {
            request()->merge(['date' => now()->today()->format('F d, Y')]);
        }

        $transactions = Transaction::where('user_id', Auth::id())->searchable(['trx'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm() {
        if (Auth::user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (Auth::user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'kyc')->first();
        return view('Template::user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData() {
        $user = Auth::user();
        $pageTitle = 'KYC Data';
        return view('Template::user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request) {
        $form = Form::where('act', 'kyc')->firstOrFail();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $user = Auth::user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);
        $user->kyc_data = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function userData() {
        $user = Auth::user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'Onboarding Process';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        // Check if the active template is MayaOfLagos and use banking profile
        $activeTemplate = activeTemplateName();
        if ($activeTemplate === 'MayaOfLagos') {
            $pageTitle = 'Complete Your Banking Profile';
            $currencies = getAvailableCurrencies();
            return view('Template::user.onboarding', compact('pageTitle', 'user', 'countries', 'mobileCode', 'currencies'));
        }

        // For other templates, use the standard user_data view
        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request) {
        $user = Auth::user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $activeTemplate = activeTemplateName();

        // Handle MayaOfLagos template with banking profile
        if ($activeTemplate === 'MayaOfLagos') {
            return $this->handleBankingProfileSubmit($request, $user);
        }

        // Handle other templates with standard user data
        return $this->handleStandardUserDataSubmit($request, $user);
    }

    private function handleBankingProfileSubmit(Request $request, $user) {
        // Debug: Log that the method is being called
        Log::info('Banking profile submit method called', ['user_id' => Auth::id()]);

        // Simplified validation for debugging
        $request->validate([
            'username' => 'required|string|min:6|unique:users,username,' . $user->id,
            'title' => 'required|string',
            'full_legal_name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        Log::info('Validation passed, attempting to save banking profile data');

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                Log::info('Image file detected, uploading...');
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $user->image);
                Log::info('Image uploaded successfully: ' . $user->image);
            } else {
                Log::warning('No image file found in request');
            }

            // Update user with banking profile fields
            $user->username = $request->username;
            $user->title = $request->title; 
            $user->full_legal_name = $request->full_legal_name;
            $user->date_of_birth = $request->date_of_birth;
            $user->gender = $request->gender;
            $user->nationality = $request->nationality;
            $user->account_type_preference = $request->account_type_preference;
            $user->preferred_currency = $request->preferred_currency;
            $user->purpose_of_account = $request->purpose_of_account;
            $user->source_of_funds = $request->source_of_funds;
            $user->employment_status = $request->employment_status;
            $user->occupation = $request->occupation;
            $user->country_name = $request->country;
            $user->dial_code = $request->mobile_code;
            $user->mobile = $request->mobile;
            $user->country_code = $request->country_code;
            $user->address = $request->address;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->zip = $request->zip;
            $user->profile_complete = Status::YES;
            $user->banking_profile_complete = true;
            $user->banking_profile_completed_at = now();

            $user->save();

            Log::info('Banking profile data saved successfully');

            $notify[] = ['success', 'Banking profile completed successfully!'];
            return to_route('user.home')->withNotify($notify);

        } catch (\Exception $e) {
            Log::error('Error saving banking profile data: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while saving your profile. Please try again.'];
            return back()->withNotify($notify)->withInput();
        }
    }

    private function handleStandardUserDataSubmit(Request $request, $user) {
        // Standard validation for other templates
        $request->validate([
            'username' => 'required|string|min:6|unique:users,username,' . $user->id,
            'country' => 'required|string',
            'mobile' => 'required|numeric|unique:users,mobile,' . $user->id,
            'mobile_code' => 'required|string',
            'country_code' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $user->image);
            }

            // Update user with standard fields
            $user->username = $request->username;
            $user->country_name = $request->country;
            $user->dial_code = $request->mobile_code;
            $user->mobile = $request->mobile;
            $user->country_code = $request->country_code;
            $user->profile_complete = Status::YES;

            $user->save();

            $notify[] = ['success', 'Profile completed successfully!'];
            return to_route('user.home')->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', 'An error occurred while saving your profile. Please try again.'];
            return back()->withNotify($notify)->withInput();
        }
    }

    public function bankingProfile() {
        $pageTitle  = 'Banking Profile';
        $user = Auth::user();
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.onboarding', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function bankingProfileUpdate(BankingProfileRequest $request) {
        $user = Auth::user();

        // Update banking data
        $user->update([
            'title' => $request->title,
            'full_legal_name' => $request->full_legal_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'account_type_preference' => $request->account_type_preference,
            'preferred_currency' => $request->preferred_currency,
            'purpose_of_account' => $request->purpose_of_account,
            'source_of_funds' => $request->source_of_funds,
            'employment_status' => $request->employment_status,
            'occupation' => $request->occupation,
            'banking_profile_complete' => true,
            'banking_profile_completed_at' => now()
        ]);

        $notify[] = ['success', 'Banking profile updated successfully!'];
        return back()->withNotify($notify);
    }


    public function addDeviceToken(Request $request) {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = Auth::user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash) {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function referredUsers() {
        $pageTitle = "My referred Users";
        $user      = Auth::user();
        $referees  = User::where('ref_by', $user->id)->with('allReferees')->paginate(getPaginate());
        $maxLevel  = ReferralSetting::max('level');
        return view('Template::user.referral.index', compact('pageTitle', 'referees', 'user', 'maxLevel'));
    }

    public function transferHistory() {
        $pageTitle = 'Transfer History';
        $transfers = BalanceTransfer::where('user_id', Auth::id())->searchable(['trx', 'beneficiary:account_number'])->dateFilter()->with('beneficiary', 'beneficiary.beneficiaryOf');
        if (request()->download == 'pdf') {
            $transfers = $transfers->get();
            return downloadPDF('Template::pdf.transfer_list', compact('pageTitle', 'transfers'));
        }
        $transfers = $transfers->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::user.transfer.history', compact('pageTitle', 'transfers'));
    }

    public function transferDetails($trxNumber) {
        $transfer = Auth::user()->transfer()->where('trx', $trxNumber)->with(['user', 'beneficiary'])->orderBy('id', 'DESC')->firstOrFail();
        $pageTitle    = "Transfer Information";
        if (request()->has('download')) {
            return downloadPDF('pdf.transfer_details', compact('pageTitle', 'transfer'));
        }
        return view('Template::user.transfer.details', compact('pageTitle', 'transfer'));
    }
}
