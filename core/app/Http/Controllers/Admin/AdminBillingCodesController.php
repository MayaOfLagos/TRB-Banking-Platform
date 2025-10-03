<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBillingCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBillingCodesController extends Controller
{
    public function index()
    {
        // Check if admin has permission to view billing codes
        if (!can('admin.billing.codes.index')) {
            abort(403, 'Unauthorized action.');
        }

        $pageTitle = 'All Billing Codes';
        
        $billingCodes = UserBillingCode::with(['user:id,firstname,lastname,username'])
            ->searchable(['user:username,firstname,lastname', 'code_type', 'code'])
            ->filterable()
            ->orderable()
            ->dynamicPaginate();

        return view('admin.billing_codes.index', compact('pageTitle', 'billingCodes'));
    }

    public function userCodes($userId)
    {
        // Check if admin has permission to view user billing codes
        if (!can('admin.billing.codes.user')) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($userId);
        $pageTitle = 'Billing Codes for ' . $user->fullname;
        
        $billingCodes = UserBillingCode::where('user_id', $userId)
            ->searchable(['code_type', 'code'])
            ->filterable()
            ->orderable()
            ->dynamicPaginate();

        return view('admin.billing_codes.user_codes', compact('pageTitle', 'billingCodes', 'user'));
    }

    public function store(Request $request)
    {
        // Check if admin has permission to create billing codes
        if (!can('admin.billing.codes.store')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code_type' => 'required|in:IMF,TAX,COT',
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'is_required' => 'required|in:0,1',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Check if code already exists for this user and type
        $existingCode = UserBillingCode::where('user_id', $request->user_id)
            ->where('code_type', $request->code_type)
            ->where('code', $request->code)
            ->first();

        if ($existingCode) {
            $notify[] = ['error', 'This code already exists for the user'];
            return back()->withNotify($notify);
        }

        DB::beginTransaction();
        try {
            UserBillingCode::create([
                'user_id' => $request->user_id,
                'code_type' => $request->code_type,
                'code' => $request->code,
                'amount' => $request->amount,
                'description' => $request->description,
                'is_required' => $request->is_required,
                'expires_at' => $request->expires_at,
                'status' => UserBillingCode::STATUS_ACTIVE,
            ]);

            DB::commit();
            $notify[] = ['success', 'Billing code created successfully'];
        } catch (\Exception $e) {
            DB::rollback();
            $notify[] = ['error', 'Failed to create billing code'];
        }

        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        // Check if admin has permission to update billing codes
        if (!can('admin.billing.codes.update')) {
            abort(403, 'Unauthorized action.');
        }

        $billingCode = UserBillingCode::findOrFail($id);

        $request->validate([
            'code_type' => 'required|in:IMF,TAX,COT',
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
            'is_required' => 'required|in:0,1',
            'expires_at' => 'nullable|date',
        ]);

        // Check if code already exists for this user and type (excluding current)
        $existingCode = UserBillingCode::where('user_id', $billingCode->user_id)
            ->where('code_type', $request->code_type)
            ->where('code', $request->code)
            ->where('id', '!=', $id)
            ->first();

        if ($existingCode) {
            $notify[] = ['error', 'This code already exists for the user'];
            return back()->withNotify($notify);
        }

        DB::beginTransaction();
        try {
            $billingCode->update([
                'code_type' => $request->code_type,
                'code' => $request->code,
                'amount' => $request->amount,
                'description' => $request->description,
                'status' => $request->status,
                'is_required' => $request->is_required,
                'expires_at' => $request->expires_at,
            ]);

            DB::commit();
            $notify[] = ['success', 'Billing code updated successfully'];
        } catch (\Exception $e) {
            DB::rollback();
            $notify[] = ['error', 'Failed to update billing code'];
        }

        return back()->withNotify($notify);
    }

    public function destroy($id)
    {
        // Check if admin has permission to delete billing codes
        if (!can('admin.billing.codes.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $billingCode = UserBillingCode::findOrFail($id);

        // Don't allow deletion if code has been used
        // if ($billingCode->isUsed()) {
        //     $notify[] = ['error', 'Cannot delete a billing code that has been used'];
        //     return back()->withNotify($notify);
        // }

        DB::beginTransaction();
        try {
            $billingCode->delete();
            DB::commit();
            $notify[] = ['success', 'Billing code deleted successfully'];
        } catch (\Exception $e) {
            DB::rollback();
            $notify[] = ['error', 'Failed to delete billing code'];
        }

        return back()->withNotify($notify);
    }

    public function markAsUsed($id)
    {
        // Check if admin has permission to mark billing codes as used
        if (!can('admin.billing.codes.mark.used')) {
            abort(403, 'Unauthorized action.');
        }

        $billingCode = UserBillingCode::findOrFail($id);

        if ($billingCode->isUsed()) {
            $notify[] = ['error', 'Billing code is already marked as used'];
            return back()->withNotify($notify);
        }

        DB::beginTransaction();
        try {
            $billingCode->markAsUsed();
            DB::commit();
            $notify[] = ['success', 'Billing code marked as used successfully'];
        } catch (\Exception $e) {
            DB::rollback();
            $notify[] = ['error', 'Failed to mark billing code as used'];
        }

        return back()->withNotify($notify);
    }

    public function bulkSetup(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'setup_type' => 'required|in:standard,custom',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Clean up empty string values for standard setup
        if ($request->setup_type === 'standard') {
            $request->merge([
                'imf_amount' => $request->imf_amount === '' ? null : $request->imf_amount,
                'tax_amount' => $request->tax_amount === '' ? null : $request->tax_amount,
                'cot_amount' => $request->cot_amount === '' ? null : $request->cot_amount,
            ]);
        }

        // Validate based on setup type
        if ($request->setup_type === 'standard') {
            $request->validate([
                'imf_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'cot_amount' => 'nullable|numeric|min:0',
            ], [
                'imf_amount.numeric' => 'The IMF amount must be a valid number',
                'tax_amount.numeric' => 'The TAX amount must be a valid number',
                'cot_amount.numeric' => 'The COT amount must be a valid number',
            ]);
        } else {
            $request->validate([
                'codes' => 'required|array',
                'codes.*.code_type' => 'required|in:IMF,TAX,COT',
                'codes.*.code' => 'required|string|max:50',
                'codes.*.amount' => 'required|numeric|min:0',
                'codes.*.is_required' => 'required|in:0,1',
            ]);
        }

        DB::beginTransaction();
        try {
            // Remove existing unused billing codes for this user
            UserBillingCode::where('user_id', $userId)->unused()->delete();

            if ($request->setup_type === 'standard') {
                // Create standard IMF, TAX, COT codes
                $standardCodes = [
                    ['type' => 'IMF', 'amount' => floatval($request->imf_amount ?: 0)],
                    ['type' => 'TAX', 'amount' => floatval($request->tax_amount ?: 0)],
                    ['type' => 'COT', 'amount' => floatval($request->cot_amount ?: 0)],
                ];

                $codesCreated = 0;
                foreach ($standardCodes as $code) {
                    if ($code['amount'] > 0) {
                        UserBillingCode::create([
                            'user_id' => $userId,
                            'code_type' => $code['type'],
                            'code' => strtoupper(\Illuminate\Support\Str::random(10)),
                            'amount' => $code['amount'],
                            'description' => 'Standard ' . $code['type'] . ' code',
                            'is_required' => UserBillingCode::REQUIRED,
                            'expires_at' => $request->expires_at,
                            'status' => UserBillingCode::STATUS_ACTIVE,
                        ]);
                        $codesCreated++;
                    }
                }
                
                if ($codesCreated === 0) {
                    DB::rollback();
                    $notify[] = ['error', 'No billing codes were created. Please enter amounts greater than 0.'];
                    return back()->withNotify($notify);
                }
            } else {
                // Create custom codes
                $codesCreated = 0;
                foreach ($request->codes as $codeData) {
                    UserBillingCode::create([
                        'user_id' => $userId,
                        'code_type' => $codeData['code_type'],
                        'code' => $codeData['code'],
                        'amount' => $codeData['amount'],
                        'description' => $codeData['description'] ?? '',
                        'is_required' => $codeData['is_required'],
                        'expires_at' => $request->expires_at,
                        'status' => UserBillingCode::STATUS_ACTIVE,
                    ]);
                    $codesCreated++;
                }
            }

            DB::commit();
            $notify[] = ['success', 'Billing codes setup completed successfully. ' . $codesCreated . ' codes created.'];
        } catch (\Exception $e) {
            DB::rollback();
            $notify[] = ['error', 'Failed to setup billing codes: ' . $e->getMessage()];
        }

        return back()->withNotify($notify);
    }

    public function getUserModal($userId)
    {
        $user = User::findOrFail($userId);
        $existingCodes = UserBillingCode::where('user_id', $userId)->unused()->get();
        
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'existing_codes' => $existingCodes,
            'code_types' => UserBillingCode::getCodeTypes()
        ]);
    }
}
