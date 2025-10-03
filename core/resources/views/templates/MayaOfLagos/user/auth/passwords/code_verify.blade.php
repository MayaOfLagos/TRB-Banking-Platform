@extends($activeTemplate . 'layouts.app')
@section('app')
    @php
        use App\Constants\Status;
    @endphp

    <!-- Auth Preloader Component -->
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Verifying your email...',
        'showPattern' => false
    ])

    <!-- Dark/Light Mode Toggle - Fixed Top -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
            <i class="las la-sun text-xl hidden dark:block"></i>
            <i class="las la-moon text-xl block dark:hidden"></i>
        </button>
    </div>

    <!-- Main Container -->
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Mobile Logo -->
        <div class="lg:hidden fixed top-8 left-1/2 transform -translate-x-1/2 z-40">
            <!-- Dark colored logo → shown in light mode -->
            <img src="{{ siteLogo('dark') }}" alt="@lang('logo')" 
                 class="h-12 block dark:hidden" id="logo-for-white-bg">
            <!-- White colored logo -->
            <img src="{{ siteLogo() }}" alt="@lang('logo')" 
                 class="h-12 hidden dark:block" id="logo-for-dark-bg">
        </div>

        <!-- Main Content Container -->
        <div class="relative z-20 min-h-screen flex items-center justify-center p-4 pt-20 lg:pt-4">
            <div class="w-full max-w-md mx-auto">

                <!-- Desktop Logo -->
                <div class="hidden lg:block text-center mb-8">
                    <!-- Dark colored logo → shown in light mode -->
                    <img src="{{ siteLogo('dark') }}" alt="@lang('logo')" 
                         class="h-16 mx-auto mb-4 block dark:hidden" id="logo-for-white-bg">
                    <!-- White colored logo -->
                    <img src="{{ siteLogo() }}" alt="@lang('logo')" 
                         class="h-16 mx-auto mb-4 hidden dark:block" id="logo-for-dark-bg">
                </div>                <!-- Mobile Header -->
                <div class="lg:hidden text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Verify Email</h2>
                    <p class="text-gray-600 dark:text-gray-400">Enter the 6-digit code we sent</p>
                </div>

                <!-- Verification Form -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 transition-colors duration-300">
                
                <!-- Desktop Header -->
                <div class="hidden lg:block mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Verify Your Email</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">Enter the verification code to continue</p>
                </div>
                    <form action="{{ route('user.password.verify.code') }}" method="POST" id="verification-form" class="space-y-6">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        
                        <!-- Icon -->
                        <div class="flex justify-center">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center">
                                <i class="las la-envelope-open-text text-2xl"></i>
                            </div>
                        </div>

                        <!-- Email Info -->
                        <div class="text-center">
                            <p class="text-gray-700 dark:text-gray-300 mb-2">
                                @lang('A 6-digit verification code has been sent to:')
                            </p>
                            <p class="text-emerald-600 dark:text-emerald-400 font-semibold text-lg">
                                {{ showEmailAddress($email) }}
                            </p>
                        </div>

                        <!-- Verification Code Input -->
                        @include($activeTemplate . 'partials.verification_code')

                        <!-- Submit Button -->
                        <button type="submit" 
                                id="submit-btn"
                                class="w-full bg-emerald-600 dark:bg-emerald-500 text-white font-semibold py-3 px-4 rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 focus:ring-4 focus:ring-emerald-200 dark:focus:ring-emerald-400/30 transition-all duration-300 transform hover:scale-105 flex items-center justify-center disabled:bg-gray-400 dark:disabled:bg-gray-600 disabled:cursor-not-allowed disabled:transform-none">
                            <!-- Loading Spinner (hidden by default) -->
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" id="loading-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <!-- Normal Icon -->
                            <i class="las la-check mr-2" id="normal-icon"></i>
                            <!-- Button Text -->
                            <span id="button-text">@lang('Verify Code')</span>
                        </button>
                    </form>

                    <!-- Help Text -->
                    <div class="mt-6 text-center">
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            @lang('Please check your inbox, including spam/junk folder')
                        </p>
                        <a href="{{ route('user.password.request') }}" 
                           class="inline-flex items-center text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors">
                            <i class="las la-redo mr-1"></i>
                            @lang('Resend Code')
                        </a>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-4 transition-colors duration-300">
                    <div class="flex items-center">
                        <i class="las la-shield-alt text-amber-600 dark:text-amber-400 mr-2"></i>
                        <div class="text-sm text-amber-800 dark:text-amber-300">
                            <strong>@lang('Security Notice:')</strong> @lang('Never share verification codes with anyone. Our team will never ask for your code.')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Management Script -->
    <script>
        // Theme management
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        
        // Check for saved theme preference or default to 'light'
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.classList.toggle('dark', savedTheme === 'dark');
        
        themeToggle.addEventListener('click', () => {
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    </script>
@endsection

@push('script')
<script>
    (function($) {
        'use strict';

        // Form submission with loading state
        $('#verification-form').on('submit', function(e) {
            showLoadingState();
        });

        function showLoadingState() {
            const submitBtn = $('#submit-btn');
            const loadingSpinner = $('#loading-spinner');
            const normalIcon = $('#normal-icon');
            const buttonText = $('#button-text');
            
            // Disable button
            submitBtn.prop('disabled', true);
            
            // Hide normal icon and show spinner
            normalIcon.addClass('hidden');
            loadingSpinner.removeClass('hidden');
            
            // Update button text
            buttonText.text('@lang("Verifying...")');
            
            // Remove hover effects
            submitBtn.removeClass('hover:scale-105 hover:bg-emerald-700 dark:hover:bg-emerald-600');
        }

        // Reset loading state (in case of validation errors)
        function resetLoadingState() {
            const submitBtn = $('#submit-btn');
            const loadingSpinner = $('#loading-spinner');
            const normalIcon = $('#normal-icon');
            const buttonText = $('#button-text');
            
            // Enable button
            submitBtn.prop('disabled', false);
            
            // Show normal icon and hide spinner
            normalIcon.removeClass('hidden');
            loadingSpinner.addClass('hidden');
            
            // Reset button text
            buttonText.text('@lang("Verify Code")');
            
            // Restore hover effects
            submitBtn.addClass('hover:scale-105 hover:bg-emerald-700 dark:hover:bg-emerald-600');
        }

        // Reset loading state if there are validation errors on page load
        @if ($errors->any())
            resetLoadingState();
        @endif

    })(jQuery);
</script>
@endpush