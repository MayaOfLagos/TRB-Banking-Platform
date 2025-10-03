<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProductUpload;
use App\Models\RebateProgram;
use App\Models\UserRebate;
use App\Services\UserTierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Rules\FileTypeValidate;

class ProductUploadController extends Controller
{
    protected $tierService;

    public function __construct(UserTierService $tierService)
    {
        $this->tierService = $tierService;
    }
    
    /**
     * Check if rebate system is enabled, redirect if not
     */
    private function ensureRebateSystemEnabled()
    {
        if (!isRebateSystemEnabled()) {
            $notify[] = ['error', 'Rebate system is currently disabled. Please contact administrator.'];
            return redirect()->route('user.home')->withNotify($notify);
        }
        return null;
    }

    /**
     * Show product upload form
     */
    public function create($programId = null)
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $pageTitle = "Upload Product Receipt";
        $general = gs(); // Get general settings
        $user = Auth::user();
        
        // Get available programs
        $programs = RebateProgram::with('rebateCategory')
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->get();

        // Selected program if provided
        $selectedProgram = null;
        if ($programId) {
            $selectedProgram = RebateProgram::with('rebateCategory')
                ->where('is_active', true)
                ->findOrFail($programId);
        }

        // Get user tier information for rebate calculation
        $tierInfo = [
            'multiplier' => $this->getUserTierMultiplier($user),
            'name' => $this->getUserTierName($user)
        ];

        return view(activeTemplate() . 'user.product.upload', compact(
            'pageTitle',
            'general',
            'programs', 
            'selectedProgram',
            'tierInfo'
        ));
    }

    /**
     * Store product upload
     */
    public function store(Request $request)
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $request->validate([
            'rebate_program_id' => 'required|exists:rebate_programs,id',
            'receipt_image' => ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'purchase_amount' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date|before_or_equal:today',
            'store_name' => 'required|string|max:255',
            'product_names' => 'nullable|array',
            'product_names.*' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Additional KYC verification check (backup to middleware)
        if ($user->kv != \App\Constants\Status::KYC_VERIFIED) {
            $notify[] = ['error', 'KYC verification is required to submit rebate claims. Please complete your KYC verification first.'];
            return redirect()->route('user.kyc.form')->withNotify($notify);
        }
        
        // Check if program is active
        $program = RebateProgram::where('is_active', true)
            ->where('id', $request->rebate_program_id)
            ->first();
            
        if (!$program) {
            $notify[] = ['error', 'Selected program is not available'];
            return back()->withNotify($notify);
        }

        // Check program date restrictions
        if ($program->starts_at && $program->starts_at > now()) {
            $notify[] = ['error', 'This program has not started yet'];
            return back()->withNotify($notify);
        }

        if ($program->ends_at && $program->ends_at < now()) {
            $notify[] = ['error', 'This program has ended'];
            return back()->withNotify($notify);
        }

        // Check user limits
        if ($program->user_limit) {
            $userRebateCount = UserRebate::where('user_id', $user->id)
                ->where('rebate_program_id', $program->id)
                ->count();
                
            if ($userRebateCount >= $program->user_limit) {
                $notify[] = ['error', 'You have reached the limit for this program'];
                return back()->withNotify($notify);
            }
        }

        // Upload receipt image
        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = fileUploader($request->receipt_image, getFilePath('productUploads'), getFileSize('productUploads'));
        }

        // Process multiple product names
        $productNames = '';
        if ($request->has('product_names') && is_array($request->product_names)) {
            // Filter out empty values and trim whitespace
            $filteredNames = array_filter(array_map('trim', $request->product_names), function($name) {
                return !empty($name);
            });
            // Join with comma and space
            $productNames = implode(', ', $filteredNames);
        }

        // Create product upload record
        $productUpload = ProductUpload::create([
            'user_id' => $user->id,
            'rebate_program_id' => $program->id,
            'receipt_image' => $receiptPath,
            'purchase_amount' => $request->purchase_amount,
            'purchase_date' => $request->purchase_date,
            'store_name' => $request->store_name,
            'product_name' => $productNames,
            'description' => $request->description,
            'submission_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'pending'
        ]);

        // Calculate rebate amounts
        $baseRebateAmount = $this->getBaseRebateAmount($program, $request->purchase_amount);
        $tierMultiplier = $this->tierService->getTierMultiplier($user);
        $finalRebateAmount = $this->calculateRebateAmount($program, $request->purchase_amount, $user);

        // Update ProductUpload with calculated rebate
        $productUpload->update(['calculated_rebate' => $baseRebateAmount]);

        // Ensure calculations are not null
        if ($baseRebateAmount === null || $finalRebateAmount === null) {
            Log::error('Rebate calculation returned null', [
                'program_id' => $program->id,
                'purchase_amount' => $request->purchase_amount,
                'base_rebate' => $baseRebateAmount,
                'final_rebate' => $finalRebateAmount
            ]);
            
            $notify[] = ['error', 'Error calculating rebate amount. Please try again.'];
            return back()->withNotify($notify);
        }

        // Get the rebate category for this program
        $rebateCategory = \App\Models\RebateCategory::where('rebate_program_id', $program->id)
            ->where('is_active', true)
            ->first();
            
        if (!$rebateCategory) {
            $notify[] = ['error', 'No active category found for this program'];
            return back()->withNotify($notify);
        }

        // Create rebate transaction record
        $rebateTransaction = \App\Models\RebateTransaction::create([
            'user_id' => $user->id,
            'rebate_category_id' => $rebateCategory->id, // Get from rebate categories table
            'rebate_program_id' => $program->id, // Link to rebate program
            'product_upload_id' => $productUpload->id, // Link to product upload
            'transaction_type' => 'product_upload',
            'reference_id' => $productUpload->id,
            'reference_type' => ProductUpload::class,
            'original_amount' => (float) $request->purchase_amount, // Use original_amount as per migration
            'rebate_rate' => (float) $program->default_rate,
            'rebate_amount' => (float) $baseRebateAmount, // Ensure it's cast to float
            'tier_multiplier' => (float) $tierMultiplier,
            'final_amount' => (float) $finalRebateAmount, // Ensure it's cast to float
            'status' => 'pending',
            'description' => 'Product upload rebate',
            'metadata' => [
                'product_upload_id' => $productUpload->id,
                'purchase_amount' => $request->purchase_amount,
                'store_name' => $request->store_name,
                'rebate_program_id' => $program->id // Store program ID in metadata
            ],
            'ip_address' => request()->ip()
        ]);

        // Link product upload to rebate transaction
        $productUpload->update(['rebate_transaction_id' => $rebateTransaction->id]);

        // Run proper fraud detection using service
        $this->runFraudDetection($rebateTransaction, $productUpload);

        $notify[] = ['success', 'Receipt uploaded successfully! Your rebate is being processed.'];
        return redirect()->route('user.product.history')->withNotify($notify);
    }

    /**
     * Show upload history
     */
    public function history(Request $request)
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $pageTitle = "Upload History";
        $general = gs(); // Get general settings
        $user = Auth::user();

        $uploads = ProductUpload::with(['rebateProgram.rebateCategory', 'rebateTransaction'])
            ->where('user_id', $user->id)
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->program_id, function($query, $programId) {
                $query->where('rebate_program_id', $programId);
            })
            ->latest()
            ->paginate(getPaginate());

        // Get programs for filter
        $programs = RebateProgram::where('is_active', true)->get();

        return view(activeTemplate() . 'user.product.history', compact(
            'pageTitle',
            'general',
            'uploads',
            'programs'
        ));
    }

    /**
     * Show specific upload details
     */
    public function show($id)
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $general = gs(); // Get general settings
        $user = Auth::user();
        $upload = ProductUpload::with(['rebateProgram.rebateCategory', 'rebateTransaction'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $pageTitle = "Upload Details";

        return view(activeTemplate() . 'user.product.show', compact(
            'pageTitle',
            'general',
            'upload'
        ));
    }

    /**
     * Calculate rebate amount including tier bonus
     */
    private function calculateRebateAmount($program, $purchaseAmount, $user)
    {
        $baseAmount = $this->getBaseRebateAmount($program, $purchaseAmount);
        $tierMultiplier = $this->getUserTierMultiplier($user);
        
        // Ensure we have valid numbers and handle nulls
        if ($baseAmount === null || !is_numeric($baseAmount)) {
            Log::warning('Base rebate amount is null or invalid', [
                'program_id' => $program->id,
                'purchase_amount' => $purchaseAmount,
                'base_amount' => $baseAmount
            ]);
            $baseAmount = 0.0;
        }
        
        if ($tierMultiplier === null || !is_numeric($tierMultiplier)) {
            Log::warning('Tier multiplier is null or invalid', [
                'user_id' => $user->id,
                'tier_multiplier' => $tierMultiplier
            ]);
            $tierMultiplier = 1.0;
        }
        
        $baseAmount = (float) $baseAmount;
        $tierMultiplier = (float) $tierMultiplier;
        
        $finalAmount = $baseAmount * $tierMultiplier;
        
        return round($finalAmount, 2);
    }

    /**
     * Get base rebate amount (before tier bonus)
     */
    private function getBaseRebateAmount($program, $purchaseAmount)
    {
        // Validate inputs
        if (!$program) {
            Log::warning('Program is null in getBaseRebateAmount');
            return 0.00;
        }
        
        if (!isset($program->default_rate) || $program->default_rate === null) {
            Log::warning('Program default_rate is null', ['program_id' => $program->id]);
            return 0.00;
        }
        
        if (!$purchaseAmount || $purchaseAmount <= 0) {
            Log::warning('Invalid purchase amount', ['purchase_amount' => $purchaseAmount]);
            return 0.00;
        }
        
        // Convert to numeric values to ensure proper calculation
        $rate = (float) $program->default_rate;
        $amount = (float) $purchaseAmount;
        
        // Validate conversion worked
        if ($rate <= 0 || $amount <= 0) {
            Log::warning('Invalid rate or amount after conversion', [
                'rate' => $rate,
                'amount' => $amount,
                'original_rate' => $program->default_rate,
                'original_amount' => $purchaseAmount
            ]);
            return 0.00;
        }
        
        // Calculate percentage-based rebate
        $rebateAmount = ($amount * $rate) / 100;
        
        // Apply maximum rebate limit if set
        if ($program->maximum_rebate && $rebateAmount > $program->maximum_rebate) {
            $rebateAmount = (float) $program->maximum_rebate;
        }
        
        // Ensure we return a valid positive number
        $finalAmount = round($rebateAmount, 2);
        
        if (!is_numeric($finalAmount) || $finalAmount < 0) {
            Log::error('Invalid final rebate amount', [
                'final_amount' => $finalAmount,
                'rebate_amount' => $rebateAmount,
                'rate' => $rate,
                'amount' => $amount
            ]);
            return 0.00;
        }
        
        return $finalAmount;
    }

    /**
     * Get user tier multiplier (DEPRECATED - now using UserTierService)
     */
    private function getUserTierMultiplier($user)
    {
        return $this->tierService->getTierMultiplier($user);
    }

    /**
     * Get user tier name (DEPRECATED - now using UserTierService)
     */
    private function getUserTierName($user)
    {
        return $this->tierService->getUserTier($user);
    }

    /**
     * Run fraud detection using the proper service that respects settings
     */
    private function runFraudDetection($rebateTransaction, $productUpload)
    {
        try {
            // Get fraud detection service
            $fraudService = app(\App\Services\FraudDetectionService::class);
            
            // Run fraud detection - this now respects the fraud settings and KYC verification
            $fraudResult = $fraudService->validateProductUpload($productUpload);
            
            // Handle the fraud detection result
            if (!$fraudResult['valid']) {
                // Upload is flagged as fraudulent
                $rebateTransaction->update([
                    'status' => 'flagged',
                    'review_notes' => 'Fraud detected: ' . $fraudResult['reason'] . ' (Score: ' . $fraudResult['score'] . ')'
                ]);

                $productUpload->update([
                    'status' => 'flagged'
                ]);
                
                Log::info('Upload flagged by fraud detection', [
                    'product_upload_id' => $productUpload->id,
                    'user_id' => $productUpload->user_id,
                    'fraud_score' => $fraudResult['score'],
                    'flags' => $fraudResult['flags'] ?? [],
                    'reason' => $fraudResult['reason'],
                    'kyc_verified' => $fraudResult['kyc_verified'] ?? false
                ]);
                
            } elseif ($fraudResult['requires_review'] ?? false) {
                // Upload needs manual review, but check for KYC instant approval eligibility
                if ($fraudResult['instant_approval_eligible'] ?? false) {
                    // KYC-verified user with instant approval - auto-approve with high confidence
                    $rebateTransaction->update([
                        'status' => 'approved',
                        'review_notes' => 'Auto-approved: KYC verified user with instant approval enabled (Score: ' . $fraudResult['score'] . ')',
                        'approved_at' => now(),
                        'approved_by' => 'system_kyc_instant'
                    ]);

                    $productUpload->update([
                        'status' => 'approved'
                    ]);
                    
                    Log::info('Upload auto-approved via KYC instant approval', [
                        'product_upload_id' => $productUpload->id,
                        'user_id' => $productUpload->user_id,
                        'fraud_score' => $fraudResult['score'],
                        'kyc_verified' => true,
                        'instant_approval' => true
                    ]);
                } else {
                    // Standard manual review
                    $rebateTransaction->update([
                        'status' => 'pending',
                        'review_notes' => 'Requires review: ' . $fraudResult['reason'] . ' (Score: ' . $fraudResult['score'] . ')'
                    ]);
                    
                    Log::info('Upload requires manual review', [
                        'product_upload_id' => $productUpload->id,
                        'user_id' => $productUpload->user_id,
                        'fraud_score' => $fraudResult['score'],
                        'reason' => $fraudResult['reason'],
                        'kyc_verified' => $fraudResult['kyc_verified'] ?? false
                    ]);
                }
                
            } else {
                // Upload is clean - check for KYC instant approval eligibility
                if ($fraudResult['instant_approval_eligible'] ?? false) {
                    // KYC-verified user with instant approval and clean fraud check - auto-approve
                    $rebateTransaction->update([
                        'status' => 'approved',
                        'review_notes' => 'Auto-approved: KYC verified user, clean fraud check (Score: ' . $fraudResult['score'] . ')',
                        'approved_at' => now(),
                        'approved_by' => 'system_kyc_clean'
                    ]);

                    $productUpload->update([
                        'status' => 'approved'
                    ]);
                    
                    Log::info('Upload auto-approved via KYC with clean fraud check', [
                        'product_upload_id' => $productUpload->id,
                        'user_id' => $productUpload->user_id,
                        'fraud_score' => $fraudResult['score'],
                        'kyc_verified' => true,
                        'instant_approval' => true
                    ]);
                } else {
                    // Upload is clean but no instant approval - stays pending for normal processing
                    Log::info('Upload passed fraud detection', [
                        'product_upload_id' => $productUpload->id,
                        'user_id' => $productUpload->user_id,
                        'fraud_score' => $fraudResult['score'],
                        'kyc_verified' => $fraudResult['kyc_verified'] ?? false
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            // If fraud detection fails, log error but don't block the upload
            Log::error('Fraud detection service error', [
                'product_upload_id' => $productUpload->id,
                'user_id' => $productUpload->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Continue with normal processing when fraud detection service fails
        }
    }
}