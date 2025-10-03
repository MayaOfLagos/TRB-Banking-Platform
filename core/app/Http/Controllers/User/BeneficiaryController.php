<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\FileManager;
use App\Lib\FormProcessor;
use App\Models\Beneficiary;
use App\Models\OtherBank;
use App\Models\User;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{

    public function ownBankBeneficiaries()
    {
        $pageTitle     = 'Beneficiaries of ' . gs('site_name');
        $beneficiaries = Beneficiary::ownBank()->where('user_id', auth()->id())->paginate(getPaginate());
        return view('Template::user.transfer.beneficiary.own', compact('pageTitle', 'beneficiaries'));
    }

    public function otherBankBeneficiaries()
    {
        $pageTitle = 'Other Bank Beneficiaries';
        $otherBanks = OtherBank::active()->get();
        $beneficiaries = Beneficiary::otherBank()->where('user_id', auth()->id())->with('beneficiaryOf')->paginate(getPaginate());
        return view('Template::user.transfer.beneficiary.other', compact('pageTitle', 'beneficiaries', 'otherBanks'));
    }

    public function addOwnBeneficiary(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'short_name' => 'required|string',
        ]);

        if ($request->id) {
            $beneficiary = Beneficiary::where('id', $request->id)->firstOrFail();
            $notification = "Beneficiary updated successfully";
        } else {
            $beneficiary = new Beneficiary();
            $notification = "Beneficiary added successfully";
        }


        $beneficiaryUser = User::where('account_number', $request->account_number)->where('username', $request->account_name)->first();

        if (!$beneficiaryUser) {
            $notify[] = ['error', 'Beneficiary account doesn\'t exists'];
            return back()->withNotify($notify)->withInput();
        }

        if (!$request->id) {
            $beneficiaryExist = Beneficiary::where('user_id', auth()->id())->where('beneficiary_type', User::class)->where('beneficiary_id', $beneficiaryUser->id)->exists();
            if ($beneficiaryExist) {
                $notify[] = ['error', 'This beneficiary already added'];
                return back()->withNotify($notify);
            }
        }

        $beneficiary->user_id = auth()->id();
        $beneficiary->account_number = $request->account_number;
        $beneficiary->account_name   = $request->account_name;
        $beneficiary->short_name     = $request->short_name;

        $beneficiaryUser->beneficiaryTypes()->save($beneficiary);

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function addOtherBeneficiary(Request $request)
    {
        $request->validate([
            'bank'           => 'required|integer',
            'account_number' => 'required|string',
            'short_name'     => 'required',
        ]);

        $bank = OtherBank::active()->findOrFail($request->bank);
        $formData = $bank->form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        $request->validate($validationRule);

        $checkDuplicate = Beneficiary::otherBank()
            ->where('id', '!=', $request->id)
            ->where('user_id', auth()->id())
            ->where('beneficiary_id', $bank->id)
            ->where('account_number', $request->account_number)
            ->exists();


        if ($checkDuplicate) {
            $notify[] = ['error', 'Beneficiary already added with this account number'];
            return back()->withNotify($notify);
        }

        $userData = $formProcessor->processFormData($request, $formData);

        if ($request->id) {
            $beneficiary = Beneficiary::findOrFail($request->id);
            $path        = getFilePath('verify');
            $fileManager = new FileManager();

            foreach ($beneficiary->details as $file) {
                if ($request->file() && $file->type == 'file') {
                    $fileManager->removeFile($path . '/' . $file->value);
                }
            }

            $notification = 'Beneficiary updated successfully';
        } else {
            $beneficiary = new Beneficiary();
            $notification = 'Beneficiary added successfully';
        }

        $beneficiary->user_id = auth()->id();
        $beneficiary->account_number = $request->account_number;
        $beneficiary->account_name = $request->account_name;
        $beneficiary->short_name = $request->short_name;
        $beneficiary->details = $userData;

        $bank->beneficiaryTypes()->save($beneficiary);

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }



    public function details($id)
    {
        $beneficiary = Beneficiary::where('user_id', auth()->id())->where('id', $id)->first();

        if (!$beneficiary) {
            return response()->json([
                'success' => false,
                'message' => "Beneficiary not found",
            ]);
        }
        $data = @$beneficiary->details;

        // Use template-specific component if available, fallback to default
        $activeTemplate = activeTemplate(); // Remove (true) parameter to get view path
        $templateComponent = $activeTemplate . 'components.view-form-data';
        
        // Add debug to see which component is being used
        try {
            if (view()->exists($templateComponent)) {
                $html = view($templateComponent, compact('data', 'beneficiary'))->render();
                $html = "<!-- Using template-specific component: {$templateComponent} -->\n" . $html;
            } else {
                $html = view('components.view-form-data', compact('data', 'beneficiary'))->render();
                $html = "<!-- Using generic component - template not found: {$templateComponent} -->\n" . $html;
            }
        } catch (\Exception $e) {
            $html = view('components.view-form-data', compact('data', 'beneficiary'))->render();
            $html = "<!-- Using generic component - error: " . $e->getMessage() . " -->\n" . $html;
        }

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    public function otherBankForm($id)
    {
        $bank = OtherBank::active()->where('id', $id)->first();

        if (!$bank) {
            return response()->json([
                'success' => false,
                'message' => "Bank not found",
            ]);
        }


        $formData = $bank->form->form_data;
        $activeTemplate = activeTemplate();
        
        // Ensure we use template-specific component, fallback to default if not found
        $templateComponent = $activeTemplate . 'components.viser-form';
        
        try {
            // Try template-specific component first
            if (view()->exists($templateComponent)) {
                $html = view($templateComponent, compact('formData'))->render();
                // Debug: Add a comment to identify which component was used
                $html = "<!-- Template-specific component: {$templateComponent} -->\n" . $html;
            } else {
                // Fallback to generic component
                $html = view('components.viser-form', compact('formData'))->render();
                $html = "<!-- Generic component used - template component not found -->\n" . $html;
            }
        } catch (\Exception $e) {
            // If template-specific component fails, use generic one
            $html = view('components.viser-form', compact('formData'))->render();
            $html = "<!-- Generic component used - template component failed: " . $e->getMessage() . " -->\n" . $html;
        }

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    public function deleteBeneficiary($id)
    {
        $beneficiary = Beneficiary::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$beneficiary) {
            $notify[] = ['error', 'Beneficiary not found'];
            return back()->withNotify($notify);
        }

        $beneficiary->delete();
        
        $notify[] = ['success', 'Beneficiary deleted successfully'];
        return back()->withNotify($notify);
    }

    public function updateOwnBeneficiary(Request $request, $id)
    {
        $request->validate([
            'short_name' => 'required|string|max:50',
        ]);

        $beneficiary = Beneficiary::where('id', $id)->where('user_id', auth()->id())->first();
        
        if (!$beneficiary) {
            $notify[] = ['error', 'Beneficiary not found'];
            return back()->withNotify($notify);
        }

        // Check if the short name is already used for another beneficiary
        $existingBeneficiary = Beneficiary::where('user_id', auth()->id())
            ->where('short_name', $request->short_name)
            ->where('id', '!=', $id)
            ->first();

        if ($existingBeneficiary) {
            $notify[] = ['error', 'Short name already exists for another beneficiary'];
            return back()->withNotify($notify);
        }

        $beneficiary->short_name = $request->short_name;
        $beneficiary->save();
        
        $notify[] = ['success', 'Beneficiary updated successfully'];
        return back()->withNotify($notify);
    }

    public function checkAccountNumber(Request $request)
    {
        if ($request->edit) {
            $user = User::where('account_number', $request->account_number)->first();
        } else {
            $user = User::where('account_number', $request->account_number)->orWhere('username', $request->account_name)->first();
        }
        if (!$user || @$user->id == auth()->id()) {
            return response()->json(['error' => true, 'message' => 'No such account found']);
        }

        $data = [
            'account_number' => $user->account_number,
            'account_name' => $user->username,
        ];

        return response()->json(['error' => false, 'data' => $data]);
    }

    public function checkDuplicate(Request $request)
    {
        if ($request->id) {
            $beneficiary = Beneficiary::where('user_id', auth()->id())->where('beneficiary_type', $request->beneficiary_type)->where('account_number', $request->account_number)->where('id', '!=', $request->id)->first();
        } else {
            $beneficiary = Beneficiary::where('user_id', auth()->id())->where('beneficiary_type', $request->beneficiary_type)->where('account_number', $request->account_number)->first();
        }

        return response()->json([
            'status' => $beneficiary ? true : false,
        ]);
    }
}
