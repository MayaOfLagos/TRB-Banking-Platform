<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankingProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        return [
            // Basic Information
            'username'     => [
                'required',
                'min:6',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('users')->ignore(auth()->id())
            ],
            'title'        => 'required|in:Mr,Mrs,Ms,Dr,Prof',
            'full_legal_name' => 'required|string|min:2|max:255',
            'image'        => $this->isMethod('post') && $this->route()->getName() === 'user.data.submit' 
                             ? 'required|image|mimes:jpg,jpeg,png|max:2048' 
                             : 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // Personal Details
            'date_of_birth' => 'required|date|before:-18 years|after:1900-01-01',
            'gender'       => 'required|in:male,female,other,prefer_not_to_say',
            'nationality'  => 'required|in:' . $countries,
            
            // Contact Information (for new registrations)
            'country_code' => $this->route()->getName() === 'user.data.submit' ? 'required|in:' . $countryCodes : 'nullable',
            'country'      => $this->route()->getName() === 'user.data.submit' ? 'required|in:' . $countries : 'nullable',
            'mobile_code'  => $this->route()->getName() === 'user.data.submit' ? 'required|in:' . $mobileCodes : 'nullable',
            'mobile'       => $this->route()->getName() === 'user.data.submit' 
                             ? ['required', 'regex:/^([0-9]+)$/', 'min:7', 'max:15', Rule::unique('users')->where('dial_code', $this->mobile_code)->ignore(auth()->id())]
                             : 'nullable',
            
            // Banking Preferences
            'account_type_preference' => 'required|in:savings,checking,business,premium,student,joint',
            'preferred_currency' => 'required|string|size:3|in:USD,EUR,GBP,CAD,AUD,JPY,NGN,CNY,INR,BRL,ZAR,KES,GHS,EGP,MAD',
            'purpose_of_account' => 'required|in:personal_banking,business_operations,savings_investment,salary_deposit,international_transfers,bill_payments,online_shopping,other',
            
            // Financial Information
            'source_of_funds' => 'required|in:employment,business_income,investment,inheritance,gift,savings,pension,government_benefits,other',
            'employment_status' => 'required|in:employed_full_time,employed_part_time,self_employed,business_owner,unemployed,student,retired,homemaker,other',
            'occupation'   => 'required|string|min:2|max:255',
            
            // Address Information (Optional)
            'address'      => 'nullable|string|max:500',
            'state'        => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:255',
            'zip'          => 'nullable|string|max:20',
            
            // Confirmation
            'confirm_details' => 'required|accepted'
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 6 characters.',
            'username.regex' => 'Username can only contain lowercase letters, numbers, and underscores.',
            'username.unique' => 'This username is already taken.',
            
            'title.required' => 'Please select a title.',
            'title.in' => 'Please select a valid title.',
            
            'full_legal_name.required' => 'Full legal name is required.',
            'full_legal_name.min' => 'Full legal name must be at least 2 characters.',
            'full_legal_name.max' => 'Full legal name cannot exceed 255 characters.',
            
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'You must be at least 18 years old.',
            'date_of_birth.after' => 'Please enter a valid date of birth.',
            
            'gender.required' => 'Please select your gender.',
            'gender.in' => 'Please select a valid gender option.',
            
            'nationality.required' => 'Nationality is required.',
            'nationality.in' => 'Please select a valid nationality.',
            
            'mobile.required' => 'Mobile number is required.',
            'mobile.regex' => 'Mobile number can only contain numbers.',
            'mobile.min' => 'Mobile number must be at least 7 digits.',
            'mobile.max' => 'Mobile number cannot exceed 15 digits.',
            'mobile.unique' => 'This mobile number is already registered.',
            
            'account_type_preference.required' => 'Please select an account type.',
            'account_type_preference.in' => 'Please select a valid account type.',
            
            'preferred_currency.required' => 'Please select your preferred currency.',
            'preferred_currency.in' => 'Please select a valid currency.',
            
            'purpose_of_account.required' => 'Please specify the purpose of your account.',
            'purpose_of_account.in' => 'Please select a valid account purpose.',
            
            'source_of_funds.required' => 'Please specify your source of funds.',
            'source_of_funds.in' => 'Please select a valid source of funds.',
            
            'employment_status.required' => 'Please specify your employment status.',
            'employment_status.in' => 'Please select a valid employment status.',
            
            'occupation.required' => 'Occupation is required.',
            'occupation.min' => 'Occupation must be at least 2 characters.',
            'occupation.max' => 'Occupation cannot exceed 255 characters.',
            
            'image.required' => 'Profile image is required.',
            'image.image' => 'Please upload a valid image file.',
            'image.mimes' => 'Profile image must be a JPG, JPEG, or PNG file.',
            'image.max' => 'Profile image size cannot exceed 2MB.',
            
            'confirm_details.required' => 'Please confirm that your details are accurate.',
            'confirm_details.accepted' => 'You must confirm that your details are accurate.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'full_legal_name' => 'full legal name',
            'date_of_birth' => 'date of birth',
            'account_type_preference' => 'account type preference',
            'preferred_currency' => 'preferred currency',
            'purpose_of_account' => 'purpose of account',
            'source_of_funds' => 'source of funds',
            'employment_status' => 'employment status',
            'confirm_details' => 'confirmation',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors()->all();
        $errorMessage = implode(' ', $errors);
        
        // For AJAX requests, return JSON response
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        // For regular requests, redirect back with errors
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}