@extends($activeTemplate . 'layouts.app')

@push('style')
<link href="{{ asset('assets/global/css/wizard-components.css') }}" rel="stylesheet">
<style>
    .select-box {
        position: relative;
    }
    .select-box select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
    .auth-container {
        background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 50%, #f0fdfa 100%);
        min-height: 100vh;
    }
    .dark .auth-container {
        background: linear-gradient(135deg, #1f2937 0%, #111827 50%, #1f2937 100%);
    }
</style>
@endpush

@section('app')
    {{-- Auth Preloader Component --}}
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Setting up your banking profile...',
        'showPattern' => true
    ])

    {{-- Dark/Light Mode Toggle - Fixed Top --}}
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
            <i class="las la-sun text-xl hidden dark:block"></i>
            <i class="las la-moon text-xl block dark:hidden"></i>
        </button>
    </div>

    {{-- Include wizard container component --}}
    @component($activeTemplate . 'partials.wizard_container', [
        'title' => 'Complete Your Banking Profile',
        'subtitle' => 'Help us provide you with better banking services',
        'maxWidth' => '4xl',
        'theme' => 'emerald'
    ])
        
        {{-- Include wizard progress component --}}
        @include($activeTemplate . 'partials.wizard_progress', [
            'steps' => [
                ['id' => 1, 'label' => 'Basic'],
                ['id' => 2, 'label' => 'Personal'],
                ['id' => 3, 'label' => 'Account'],
                ['id' => 4, 'label' => 'Financial'],
                ['id' => 5, 'label' => 'Complete']
            ],
            'currentStep' => 1,
            'theme' => 'emerald'
        ])

        {{-- Form --}}
        <form method="POST" action="{{ route('user.data.submit') }}" id="bankingProfileWizard" enctype="multipart/form-data">
            @csrf
            
            {{-- Step 1: Basic Information --}}
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 1,
                'title' => 'Basic Information',
                'subtitle' => 'Let\'s start with your basic details',
                'icon' => 'user',
                'iconBg' => 'emerald',
                'isActive' => true
            ])
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="title" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Title</option>
                                <option value="Mr" {{ old('title', $user->title) == 'Mr' ? 'selected' : '' }}>Mr</option>
                                <option value="Mrs" {{ old('title', $user->title) == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                <option value="Ms" {{ old('title', $user->title) == 'Ms' ? 'selected' : '' }}>Ms</option>
                                <option value="Dr" {{ old('title', $user->title) == 'Dr' ? 'selected' : '' }}>Dr</option>
                                <option value="Prof" {{ old('title', $user->title) == 'Prof' ? 'selected' : '' }}>Prof</option>
                            </select>
                        </div>
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                               placeholder="Enter username">
                    </div>

                    {{-- Full Legal Name --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Full Legal Name (as on ID) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="full_legal_name" value="{{ old('full_legal_name', $user->full_legal_name) }}" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                               placeholder="Enter your full legal name">
                    </div>

                    {{-- Profile Image --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Profile Image <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="image" accept="image/*" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">JPG, JPEG, PNG files only. Max size: 2MB</p>
                    </div>
                </div>

                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 1,
                    'totalSteps' => 5,
                    'nextText' => 'Continue',
                    'alignment' => 'end'
                ])
            @endcomponent

            {{-- Step 2: Personal Details --}}
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 2,
                'title' => 'Personal Details',
                'subtitle' => 'Tell us more about yourself',
                'icon' => 'id-card',
                'iconBg' => 'blue'
            ])
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Date of Birth --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                               max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="gender" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                <option value="prefer_not_to_say" {{ old('gender', $user->gender) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                        </div>
                    </div>

                    {{-- Nationality --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nationality <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="nationality" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Nationality</option>
                                @foreach($countries as $key => $country)
                                    <option value="{{ $country->country }}" {{ old('nationality', $user->nationality) == $country->country ? 'selected' : '' }}>
                                        {{ $country->country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Country and Mobile --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="country" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Country</option>
                                @foreach($countries as $key => $country)
                                    <option value="{{ $country->country }}" 
                                            data-mobile_code="{{ $country->dial_code }}" 
                                            data-country_code="{{ $key }}"
                                            {{ old('country', $user->country_name) == $country->country ? 'selected' : '' }}>
                                        {{ $country->country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Mobile Number --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            <input type="text" name="mobile_code" value="{{ old('mobile_code', $user->dial_code) }}" readonly 
                                   class="w-24 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white">
                            <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" required 
                                   class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                                   placeholder="Enter mobile number">
                        </div>
                    </div>
                </div>

                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 2,
                    'totalSteps' => 5,
                    'prevText' => 'Back',
                    'nextText' => 'Continue'
                ])
            @endcomponent

            {{-- Step 3: Account Preferences --}}
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 3,
                'title' => 'Account Preferences',
                'subtitle' => 'Choose your banking preferences',
                'icon' => 'cog',
                'iconBg' => 'purple'
            ])
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Account Type Preference --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Account Type Preference <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="account_type_preference" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Account Type</option>
                                <option value="savings" {{ old('account_type_preference', $user->account_type_preference) == 'savings' ? 'selected' : '' }}>Savings Account</option>
                                <option value="checking" {{ old('account_type_preference', $user->account_type_preference) == 'checking' ? 'selected' : '' }}>Checking Account</option>
                                <option value="business" {{ old('account_type_preference', $user->account_type_preference) == 'business' ? 'selected' : '' }}>Business Account</option>
                                <option value="premium" {{ old('account_type_preference', $user->account_type_preference) == 'premium' ? 'selected' : '' }}>Premium Account</option>
                                <option value="student" {{ old('account_type_preference', $user->account_type_preference) == 'student' ? 'selected' : '' }}>Student Account</option>
                                <option value="joint" {{ old('account_type_preference', $user->account_type_preference) == 'joint' ? 'selected' : '' }}>Joint Account</option>
                            </select>
                        </div>
                    </div>

                    {{-- Preferred Currency --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="las la-coins text-emerald-600 dark:text-emerald-400 mr-2"></i>
                            Preferred Currency <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="preferred_currency" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Your Preferred Currency</option>
                                @foreach($currencies as $code => $name)
                                    <option value="{{ $code }}" {{ old('preferred_currency', $user->preferred_currency) == $code ? 'selected' : '' }}>
                                        {{ $code }} - {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Purpose of Account --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Purpose of Account <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="purpose_of_account" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Purpose</option>
                                <option value="personal_banking" {{ old('purpose_of_account', $user->purpose_of_account) == 'personal_banking' ? 'selected' : '' }}>Personal Banking</option>
                                <option value="business_operations" {{ old('purpose_of_account', $user->purpose_of_account) == 'business_operations' ? 'selected' : '' }}>Business Operations</option>
                                <option value="savings_investment" {{ old('purpose_of_account', $user->purpose_of_account) == 'savings_investment' ? 'selected' : '' }}>Savings & Investment</option>
                                <option value="salary_deposit" {{ old('purpose_of_account', $user->purpose_of_account) == 'salary_deposit' ? 'selected' : '' }}>Salary Deposit</option>
                                <option value="international_transfers" {{ old('purpose_of_account', $user->purpose_of_account) == 'international_transfers' ? 'selected' : '' }}>International Transfers</option>
                                <option value="bill_payments" {{ old('purpose_of_account', $user->purpose_of_account) == 'bill_payments' ? 'selected' : '' }}>Bill Payments</option>
                                <option value="online_shopping" {{ old('purpose_of_account', $user->purpose_of_account) == 'online_shopping' ? 'selected' : '' }}>Online Shopping</option>
                                <option value="other" {{ old('purpose_of_account', $user->purpose_of_account) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 3,
                    'totalSteps' => 5,
                    'prevText' => 'Back',
                    'nextText' => 'Continue'
                ])
            @endcomponent

            {{-- Step 4: Financial Information --}}
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 4,
                'title' => 'Financial Information',
                'subtitle' => 'Help us understand your financial profile',
                'icon' => 'money-bill',
                'iconBg' => 'green'
            ])
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Source of Funds --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Source of Funds <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="source_of_funds" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Source</option>
                                <option value="employment" {{ old('source_of_funds', $user->source_of_funds) == 'employment' ? 'selected' : '' }}>Employment</option>
                                <option value="business_income" {{ old('source_of_funds', $user->source_of_funds) == 'business_income' ? 'selected' : '' }}>Business Income</option>
                                <option value="investment" {{ old('source_of_funds', $user->source_of_funds) == 'investment' ? 'selected' : '' }}>Investment</option>
                                <option value="inheritance" {{ old('source_of_funds', $user->source_of_funds) == 'inheritance' ? 'selected' : '' }}>Inheritance</option>
                                <option value="gift" {{ old('source_of_funds', $user->source_of_funds) == 'gift' ? 'selected' : '' }}>Gift</option>
                                <option value="savings" {{ old('source_of_funds', $user->source_of_funds) == 'savings' ? 'selected' : '' }}>Savings</option>
                                <option value="pension" {{ old('source_of_funds', $user->source_of_funds) == 'pension' ? 'selected' : '' }}>Pension</option>
                                <option value="government_benefits" {{ old('source_of_funds', $user->source_of_funds) == 'government_benefits' ? 'selected' : '' }}>Government Benefits</option>
                                <option value="other" {{ old('source_of_funds', $user->source_of_funds) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    {{-- Employment Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Employment Status <span class="text-red-500">*</span>
                        </label>
                        <div class="select-box">
                            <select name="employment_status" required 
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                <option value="">Select Status</option>
                                <option value="employed_full_time" {{ old('employment_status', $user->employment_status) == 'employed_full_time' ? 'selected' : '' }}>Employed (Full-time)</option>
                                <option value="employed_part_time" {{ old('employment_status', $user->employment_status) == 'employed_part_time' ? 'selected' : '' }}>Employed (Part-time)</option>
                                <option value="self_employed" {{ old('employment_status', $user->employment_status) == 'self_employed' ? 'selected' : '' }}>Self-employed</option>
                                <option value="business_owner" {{ old('employment_status', $user->employment_status) == 'business_owner' ? 'selected' : '' }}>Business Owner</option>
                                <option value="unemployed" {{ old('employment_status', $user->employment_status) == 'unemployed' ? 'selected' : '' }}>Unemployed</option>
                                <option value="student" {{ old('employment_status', $user->employment_status) == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="retired" {{ old('employment_status', $user->employment_status) == 'retired' ? 'selected' : '' }}>Retired</option>
                                <option value="homemaker" {{ old('employment_status', $user->employment_status) == 'homemaker' ? 'selected' : '' }}>Homemaker</option>
                                <option value="other" {{ old('employment_status', $user->employment_status) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    {{-- Occupation --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Occupation <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="occupation" value="{{ old('occupation', $user->occupation) }}" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                               placeholder="Enter your occupation/job title">
                    </div>

                    {{-- Address --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Address
                        </label>
                        <textarea name="address" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                                  placeholder="Enter your address">{{ old('address', $user->address) }}</textarea>
                    </div>

                    {{-- City, State, ZIP --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            City
                        </label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                               placeholder="Enter city">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            State/Province
                        </label>
                        <input type="text" name="state" value="{{ old('state', $user->state) }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                               placeholder="Enter state">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            ZIP/Postal Code
                        </label>
                        <input type="text" name="zip" value="{{ old('zip', $user->zip) }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300"
                               placeholder="Enter ZIP code">
                    </div>

                </div>

                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 4,
                    'totalSteps' => 5,
                    'prevText' => 'Back',
                    'nextText' => 'Review & Continue'
                ])
            @endcomponent

            {{-- Step 5: Complete Profile --}}
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 5,
                'title' => 'Complete Your Profile',
                'subtitle' => 'Review your information and finalize',
                'icon' => 'check-circle',
                'iconBg' => 'emerald'
            ])
                <div class="text-center">
                    <div class="mb-6">
                        <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="las la-check text-3xl"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Please review your information below and confirm to complete your banking profile.
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-6 mb-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Profile Summary</h3>
                        <div id="profile-summary" class="text-left space-y-3">
                            {{-- Summary will be populated by JavaScript --}}
                        </div>
                    </div>

                    <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl p-6 mb-6">
                        <label class="flex items-center justify-center">
                            <input type="checkbox" name="confirm_details" required 
                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                I confirm that all the information provided is accurate and complete
                            </span>
                        </label>
                    </div>
                </div>

                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 5,
                    'totalSteps' => 5,
                    'prevText' => 'Back',
                    'submitText' => 'Complete Banking Profile',
                    'formId' => 'bankingProfileWizard',
                    'loadingText' => 'Creating your banking profile...'
                ])
            @endcomponent

            {{-- Hidden fields for country data --}}
            <input type="hidden" name="country_code" value="{{ old('country_code', $user->country_code) }}">
        </form>

    @endcomponent
@endsection

@push('script')
<script src="{{ asset('assets/global/js/wizard-manager.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wizard = new WizardManager({
            formId: 'bankingProfileWizard',
            totalSteps: 5,
            validateOnNext: true,
            autoFocus: true,
            theme: 'emerald'
        });

        const countrySelect = document.querySelector('[name="country"]');
        const mobileCodeInput = document.querySelector('[name="mobile_code"]');
        const countryCodeInput = document.querySelector('[name="country_code"]');

        if (countrySelect) {
            countrySelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const mobileCode = selectedOption.getAttribute('data-mobile_code');
                const countryCode = selectedOption.getAttribute('data-country_code');
                
                if (mobileCodeInput && mobileCode) {
                    mobileCodeInput.value = mobileCode;
                }
                if (countryCodeInput && countryCode) {
                    countryCodeInput.value = countryCode;
                }
            });
        }

        document.addEventListener('wizard:stepChanged', function(e) {
            if (e.detail.step === 5) {
                updateProfileSummary();
            }
        });

        function updateProfileSummary() {
            const form = document.getElementById('bankingProfileWizard');
            const summaryContent = document.getElementById('profile-summary');
            
            const data = {
                title: form.querySelector('[name="title"]').value,
                fullLegalName: form.querySelector('[name="full_legal_name"]').value,
                dob: form.querySelector('[name="date_of_birth"]').value,
                gender: form.querySelector('[name="gender"]').value,
                nationality: form.querySelector('[name="nationality"]').value,
                accountType: form.querySelector('[name="account_type_preference"]').value,
                currency: form.querySelector('[name="preferred_currency"]').value,
                purpose: form.querySelector('[name="purpose_of_account"]').value,
                sourceOfFunds: form.querySelector('[name="source_of_funds"]').value,
                employmentStatus: form.querySelector('[name="employment_status"]').value,
                occupation: form.querySelector('[name="occupation"]').value
            };

            let summaryHTML = '';
            
            if (data.title && data.fullLegalName) {
                summaryHTML += `
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400">Full Name:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">${data.title} ${data.fullLegalName}</span>
                    </div>
                `;
            }
            
            if (data.dob) {
                summaryHTML += `
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400">Date of Birth:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">${new Date(data.dob).toLocaleDateString()}</span>
                    </div>
                `;
            }
            
            if (data.gender) {
                summaryHTML += `
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400">Gender:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">${data.gender.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                    </div>
                `;
            }
            
            if (data.nationality) {
                summaryHTML += `
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400">Nationality:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">${data.nationality}</span>
                    </div>
                `;
            }
            
            if (data.accountType) {
                summaryHTML += `
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400">Account Type:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">${data.accountType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                    </div>
                `;
            }
            
            if (data.currency) {
                summaryHTML += `
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400">Preferred Currency:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">${data.currency}</span>
                    </div>
                `;
            }
            
            if (data.employmentStatus && data.occupation) {
                summaryHTML += `
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 dark:text-gray-400">Employment:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">${data.occupation} (${data.employmentStatus.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())})</span>
                    </div>
                `;
            }

            summaryContent.innerHTML = summaryHTML || '<p class="text-gray-500 dark:text-gray-400 text-center">Please complete the previous steps to see your profile summary.</p>';
        }

        wizard.addStepValidator(1, function() {
            const title = document.querySelector('[name="title"]').value;
            const fullLegalName = document.querySelector('[name="full_legal_name"]').value;
            const username = document.querySelector('[name="username"]').value;
            const image = document.querySelector('[name="image"]').files.length;
            
            if (!title || !fullLegalName || !username || !image) {
                alert('Please fill in all required fields in this step.');
                return false;
            }
            return true;
        });

        wizard.addStepValidator(2, function() {
            const dob = document.querySelector('[name="date_of_birth"]').value;
            const gender = document.querySelector('[name="gender"]').value;
            const nationality = document.querySelector('[name="nationality"]').value;
            const country = document.querySelector('[name="country"]').value;
            const mobile = document.querySelector('[name="mobile"]').value;
            
            if (!dob || !gender || !nationality || !country || !mobile) {
                alert('Please fill in all required fields in this step.');
                return false;
            }
            return true;
        });

        wizard.addStepValidator(3, function() {
            const accountType = document.querySelector('[name="account_type_preference"]').value;
            const currency = document.querySelector('[name="preferred_currency"]').value;
            const purpose = document.querySelector('[name="purpose_of_account"]').value;
            
            if (!accountType || !currency || !purpose) {
                alert('Please fill in all required fields in this step.');
                return false;
            }
            return true;
        });

        wizard.addStepValidator(4, function() {
            const sourceOfFunds = document.querySelector('[name="source_of_funds"]').value;
            const employmentStatus = document.querySelector('[name="employment_status"]').value;
            const occupation = document.querySelector('[name="occupation"]').value;
            
            if (!sourceOfFunds || !employmentStatus || !occupation) {
                alert('Please fill in all required fields in this step.');
                return false;
            }
            return true;
        });

        wizard.addStepValidator(5, function() {
            const confirmDetails = document.querySelector('[name="confirm_details"]').checked;
            
            if (!confirmDetails) {
                alert('Please confirm that your details are accurate.');
                return false;
            }
            return true;
        });
    });

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.classList.toggle('dark', currentTheme === 'dark');
        
        themeToggle.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            themeToggle.style.transform = 'scale(0.9)';
            setTimeout(() => {
                themeToggle.style.transform = 'scale(1)';
            }, 150);
        });
    }
</script>
@endpush