<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserLogin;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = auth()->user();
        return view('Template::user.profile_setting', compact('pageTitle', 'user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip' => 'nullable|string',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required'
        ]);

        $user = auth()->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;

        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        $user = auth()->user();
        $loginLogs = UserLogin::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('Template::user.password', compact('pageTitle', 'user', 'loginLogs'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordValidation]
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }

    public function transferPin()
    {
        $pageTitle = 'Transfer PIN Management';
        $user = auth()->user();
        return view('Template::user.transfer_pin', compact('pageTitle', 'user'));
    }

    public function setTransferPin(Request $request)
    {
        $request->validate([
            'transfer_pin' => 'required|digits:4|confirmed',
            'current_password' => 'required'
        ], [
            'transfer_pin.required' => 'Transfer PIN is required',
            'transfer_pin.digits' => 'Transfer PIN must be exactly 4 digits',
            'transfer_pin.confirmed' => 'Transfer PIN confirmation does not match',
            'current_password.required' => 'Current password is required for security'
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            $notify[] = ['error', 'Current password is incorrect'];
            return back()->withNotify($notify);
        }

        // Set the transfer PIN
        $user->setTransferPin($request->transfer_pin);

        $notify[] = ['success', 'Transfer PIN set successfully'];
        return back()->withNotify($notify);
    }

    public function updateTransferPin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|digits:4',
            'transfer_pin' => 'required|digits:4|confirmed',
        ], [
            'current_pin.required' => 'Current transfer PIN is required',
            'current_pin.digits' => 'Current transfer PIN must be exactly 4 digits',
            'transfer_pin.required' => 'New transfer PIN is required',
            'transfer_pin.digits' => 'Transfer PIN must be exactly 4 digits',
            'transfer_pin.confirmed' => 'Transfer PIN confirmation does not match'
        ]);

        $user = auth()->user();

        // Check if user has a transfer PIN
        if (!$user->hasTransferPin()) {
            $notify[] = ['error', 'You don\'t have a transfer PIN set'];
            return back()->withNotify($notify);
        }

        // Verify current PIN
        if (!$user->verifyTransferPin($request->current_pin)) {
            $notify[] = ['error', 'Current transfer PIN is incorrect'];
            return back()->withNotify($notify);
        }

        // Update the transfer PIN
        $user->setTransferPin($request->transfer_pin);

        $notify[] = ['success', 'Transfer PIN updated successfully'];
        return back()->withNotify($notify);
    }
}
