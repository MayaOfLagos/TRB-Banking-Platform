@extends($activeTemplate . 'layouts.app')

@push('style')
<link href="{{ asset('assets/global/css/registration-wizard.css') }}" rel="stylesheet">
<style>
    /* Additional registration-specific styles */
    .auth-container {
        background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 50%, #f0fdfa 100%);
        min-height: 100vh;
    }
    .dark .auth-container {
        background: linear-gradient(135deg, #1f2937 0%, #111827 50%, #1f2937 100%);
    }
    
    /* Password toggle button styles */
    .toggle-password {
        cursor: pointer;
        user-select: none;
        z-index: 10;
        transition: all 0.2s ease;
    }
    
    .toggle-password:hover {
        transform: scale(1.1);
    }
    
    .toggle-password:active {
        transform: scale(0.95);
    }
    
    /* Ensure password input padding accommodates the toggle button */
    .toggle-password + input[type="password"],
    .toggle-password + input[type="text"] {
        padding-right: 3rem;
    }
    
    /* Submit button loading state styles */
    #submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    /* Smooth transitions for form elements */
    input[type="password"], input[type="text"], input[type="email"] {
        transition: all 0.3s ease;
    }
    
    input:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
    }
</style>
@endpush

@section('app')
    @php
        $policyPages = getContent('policy_pages.element', orderById: true);
    @endphp

    <!-- Auth Preloader Component -->
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Creating your account...',
        'showPattern' => true
    ])

    <!-- Dark/Light Mode Toggle - Fixed Top -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
            <i class="las la-sun text-xl hidden dark:block"></i>
            <i class="las la-moon text-xl block dark:hidden"></i>
        </button>
    </div>

    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-emerald-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center p-4 transition-all duration-300">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-300/20 dark:bg-emerald-600/10 rounded-full blur-3xl"></div>
            <div class="absolute top-20 left-20 w-60 h-60 bg-emerald-200/20 dark:bg-emerald-400/5 rounded-full blur-2xl"></div>
        </div>

        <div class="relative w-full max-w-2xl mx-auto">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <!-- Dark logo -->
                    <img src="{{ siteLogo('dark') }}" alt="{{ __(gs('site_name')) }}" 
                         class="h-16 w-auto mx-auto block dark:hidden" id="logo-for-white-bg">
                    <!-- White logo -->
                    <img src="{{ siteLogo() }}" alt="{{ __(gs('site_name')) }}" 
                         class="h-16 w-auto mx-auto hidden dark:block" id="logo-for-dark-bg">
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mt-4 mb-2">@lang('Create Account')</h1>
                <p class="text-gray-600 dark:text-gray-400">@lang('Join thousands of satisfied customers')</p>
            </div>

            <!-- Registration disabled check -->
            @if (!gs('registration'))
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-8 text-center">
                    <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="las la-lock text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">@lang('Registration Disabled')</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('Registration is currently disabled. Please contact support for assistance.')</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-2xl transition-all duration-300 transform hover:scale-105">
                        <i class="las la-home mr-2"></i>@lang('Back to Home')
                    </a>
                </div>
            @else
                <!-- Multi-Step Registration Form -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                    
                    <!-- Progress Steps -->
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 px-8 py-6 border-b border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Step 1 -->
                                <div class="flex items-center step-indicator active" data-step="1">
                                    <div class="w-10 h-10 bg-emerald-600 text-white rounded-full flex items-center justify-center font-semibold step-circle">
                                        <span class="step-number">1</span>
                                        <i class="las la-check text-lg step-check hidden"></i>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-emerald-700 dark:text-emerald-300 hidden sm:block">@lang('Personal Info')</span>
                                </div>
                                
                                <!-- Progress Line 1 -->
                                <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full mx-4 progress-line" data-line="1">
                                    <div class="h-full bg-emerald-600 rounded-full transition-all duration-500 progress-fill" style="width: 0%"></div>
                                </div>
                                
                                <!-- Step 2 -->
                                <div class="flex items-center step-indicator" data-step="2">
                                    <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center font-semibold step-circle">
                                        <span class="step-number">2</span>
                                        <i class="las la-check text-lg step-check hidden"></i>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-500 dark:text-gray-400 hidden sm:block">@lang('Security')</span>
                                </div>
                                
                                <!-- Progress Line 2 -->
                                <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full mx-4 progress-line" data-line="2">
                                    <div class="h-full bg-emerald-600 rounded-full transition-all duration-500 progress-fill" style="width: 0%"></div>
                                </div>
                                
                                <!-- Step 3 -->
                                <div class="flex items-center step-indicator" data-step="3">
                                    <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center font-semibold step-circle">
                                        <span class="step-number">3</span>
                                        <i class="las la-check text-lg step-check hidden"></i>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-500 dark:text-gray-400 hidden sm:block">@lang('Complete')</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <form method="POST" action="{{ route('user.register') }}" class="verify-gcaptcha" id="registrationForm">
                        @csrf
                        
                        <!-- Step 1: Personal Information -->
                        <div class="form-step sm:p-0 active" id="step-1">
                            <div class="p-4 lg:p-8">
                                <div class="text-center mb-8">
                                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="las la-user text-2xl"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang('Personal Information')</h2>
                                    <p class="text-gray-600 dark:text-gray-400">@lang('Let\'s start with your basic information')</p>
                                </div>

                                <div class="space-y-6">
                                    @if (session()->get('reference') != null && gs()->modules->referral_system)
                                        <!-- Referral -->
                                        <div>
                                            <label for="referBy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                @lang('Referred by')
                                            </label>
                                            <input type="text" 
                                                   name="referBy" 
                                                   id="referBy" 
                                                   value="{{ session()->get('reference') }}" 
                                                   readonly
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white">
                                        </div>
                                    @endif

                                    <!-- Name Fields -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="firstname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                @lang('First Name') <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="firstname" 
                                                   id="firstname" 
                                                   value="{{ old('firstname') }}" 
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('firstname') border-red-500 @enderror">
                                            @error('firstname')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="lastname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                @lang('Last Name') <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="lastname" 
                                                   id="lastname" 
                                                   value="{{ old('lastname') }}" 
                                                   required
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('lastname') border-red-500 @enderror">
                                            @error('lastname')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            @lang('Email Address') <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               value="{{ old('email') }}" 
                                               required
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('email') border-red-500 @enderror checkUser">
                                        @error('email')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Step Navigation -->
                                <div class="flex justify-end mt-8">
                                    <button type="button" class="btn-next bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-8 sm:py-3 sm:px-8 py-2 px-4 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center text-sm sm:text-base">
                                        @lang('Continue')
                                        <i class="las la-arrow-right ml-2 text-base sm:text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Security Setup -->
                        <div class="form-step sm:p-0" id="step-2">
                            <div class="p-4 lg:p-8">
                                <div class="text-center mb-8">
                                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="las la-shield-alt text-2xl"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang('Security Setup')</h2>
                                    <p class="text-gray-600 dark:text-gray-400">@lang('Create a secure password for your account')</p>
                                </div>

                                <div class="space-y-6">
                                    <!-- Password Fields -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                @lang('Password') <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="password" 
                                                       name="password" 
                                                       id="password" 
                                                       required
                                                       class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('password') border-red-500 @enderror @if (gs('secure_password')) secure-password @endif">
                                                <button type="button" 
                                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 toggle-password"
                                                        data-target="password">
                                                    <i class="las la-eye text-xl"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                @lang('Confirm Password') <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="password" 
                                                       name="password_confirmation" 
                                                       id="password_confirmation" 
                                                       required
                                                       class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                                                <button type="button" 
                                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 toggle-password"
                                                        data-target="password_confirmation">
                                                    <i class="las la-eye text-xl"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Strength Indicator -->
                                    <div id="password-strength-container" class="hidden">
                                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2">@lang('Password strength:')</div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div id="password-strength" class="bg-red-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                        <div id="password-strength-text" class="text-sm text-gray-500 dark:text-gray-400 mt-1">@lang('Enter a password')</div>
                                    </div>
                                </div>

                                <!-- Step Navigation -->
                                <div class="flex justify-between mt-8 gap-3 sm:gap-4">
                                    <button type="button" class="btn-prev bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-3 px-4 sm:py-3 sm:px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center text-sm sm:text-base">
                                        <i class="las la-arrow-left mr-2 text-base sm:text-xl"></i>
                                        @lang('Back')
                                    </button>
                                    <button type="button" class="btn-next bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 sm:py-3 sm:px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center text-sm sm:text-base">
                                        @lang('Next')
                                        <i class="las la-arrow-right ml-2 text-base sm:text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Terms & Completion -->
                        <div class="form-step sm:p-0" id="step-3">
                            <div class="p-4 lg:p-8">
                                <div class="text-center mb-8">
                                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="las la-check-circle text-2xl"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang('Complete Registration')</h2>
                                    <p class="text-gray-600 dark:text-gray-400">@lang('Review and accept our terms to complete your registration')</p>
                                </div>

                                <div class="space-y-6">
                                    <!-- CAPTCHA -->
                                    <x-captcha />

                                    @if(gs()->agree)
                                        <!-- Terms & Conditions -->
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-6">
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5 mt-1">
                                                    <input type="checkbox" 
                                                           name="agree" 
                                                           id="agree" 
                                                           required
                                                           @checked(old('agree'))
                                                           class="w-4 h-4 text-emerald-600 bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded focus:ring-emerald-500 focus:ring-2">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="agree" class="text-gray-700 dark:text-gray-300">
                                                        @lang('I agree to the') 
                                                        @foreach($policyPages as $policy)
                                                            <a href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}" 
                                                               target="_blank" 
                                                               class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium underline">{{ __($policy->data_values->title) }}</a>@if(!$loop->last), @endif
                                                        @endforeach
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Step Navigation -->
                                <div class="flex justify-between mt-8 gap-3 sm:gap-4">
                                    <button type="button" class="btn-prev bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-3 px-4 sm:py-3 sm:px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center text-sm sm:text-base">
                                        <i class="las la-arrow-left mr-2 text-base sm:text-xl"></i>
                                        @lang('Back')
                                    </button>
                                    <button type="submit" 
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 sm:py-3 sm:px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center text-sm sm:text-base"
                                            id="submit-btn">
                                        <span class="default-text inline-flex items-center">
                                            <i class="las la-user-plus text-base sm:text-xl mr-2"></i>@lang('Register')
                                        </span>
                                        <span class="loading-text hidden inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            @lang('Registering...')
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Sign In Link -->
                    <div class="px-8 pb-8 text-center border-t border-gray-200/50 dark:border-gray-700/50">
                        <p class="text-gray-600 dark:text-gray-400 mt-6">
                            @lang('Already have an account?')
                            <a href="{{ route('user.login') }}" 
                               class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors">
                                @lang('Sign In')
                            </a>
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- User Exists Modal -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden" id="existModal">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full p-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="las la-user-check text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">@lang('You are with us')</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('You already have an account, please Login.')</p>
                <div class="flex space-x-3">
                    <button type="button" 
                            class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-3 px-4 rounded-2xl transition-all duration-300"
                            onclick="closeExistModal()">
                        @lang('Close')
                    </button>
                    <a href="{{ route('user.login') }}" 
                       class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-2xl transition-all duration-300 text-center">
                        @lang('Login')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="{{ asset('assets/global/js/registration-wizard.js') }}"></script>
<script>
    'use strict';
    
    // Ensure submit button is never stuck in disabled state
    $(document).ready(function() {
        // Reset submit button state on page load
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.disabled = false;
            const defaultText = submitBtn.querySelector('.default-text');
            const loadingText = submitBtn.querySelector('.loading-text');
            
            if (defaultText) defaultText.classList.remove('hidden');
            if (loadingText) loadingText.classList.add('hidden');
        }
    });

    // User existence check
    $('.checkUser').on('focusout', function(e) {
        var url = '{{ route('user.checkUser') }}';
        var value = e.target.value;
        var token = '{{ csrf_token() }}';
        
        if ($(this).attr('name') == 'email') {
            var data = {email: value, _token: token}
        }
        
        $.post(url, data, function(response) {
            if (response.data != false && response.data != null) {
                $('#existModal').removeClass('hidden');
            }
        });
    });

    // Close modal function
    function closeExistModal() {
        $('#existModal').addClass('hidden');
    }

    // reCAPTCHA callback
    function submitUserForm() {
        $("#registrationForm").find('button[type="submit"]').click();
    }

    // Prevent form double submission and handle loading state properly
    $('#registrationForm').on('submit', function(e) {
        const submitBtn = $('#submit-btn');
        const defaultText = submitBtn.find('.default-text');
        const loadingText = submitBtn.find('.loading-text');
        
        // Show loading state
        defaultText.addClass('hidden');
        loadingText.removeClass('hidden');
        submitBtn.prop('disabled', true);
        
        // Re-enable after a timeout as fallback (in case of network issues)
        setTimeout(function() {
            if (submitBtn.prop('disabled')) {
                defaultText.removeClass('hidden');
                loadingText.addClass('hidden');
                submitBtn.prop('disabled', false);
            }
        }, 10000); // 10 seconds timeout
    });

    // Theme toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.classList.toggle('dark', currentTheme === 'dark');
        
        themeToggle.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            // Add animation effect
            themeToggle.style.transform = 'scale(0.9)';
            setTimeout(() => {
                themeToggle.style.transform = 'scale(1)';
            }, 150);
        });
    }
</script>
@endpush

@push('style')
<style>
    .toggle-password {
        cursor: pointer;
    }
</style>
@endpush

@push('script')
@if(gs()->reCaptcha)
    <script src="https://www.google.com/recaptcha/api.js"></script>
@endif
@endpush