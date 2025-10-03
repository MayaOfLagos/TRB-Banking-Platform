@extends($activeTemplate . 'layouts.app')
@section('app')
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Preparing SMS verification...',
        'showPattern' => false
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

        <div class="relative w-full max-w-md mx-auto">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ siteLogo() }}" alt="{{ __(gs('site_name')) }}" class="h-16 w-auto mx-auto">
                </a>
            </div>

            <!-- SMS Verification Content -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-8">
                @if(auth()->user()->sv)
                    <!-- SMS Verified -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="las la-check-circle text-3xl"></i>
                        </div>
                        
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">@lang('Mobile Verified!')</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('Your mobile number has been successfully verified. Your account security has been enhanced.')</p>
                        
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 rounded-2xl p-4 mb-6">
                            <div class="flex items-center justify-center">
                                <i class="las la-check text-emerald-600 dark:text-emerald-400 mr-2"></i>
                                <span class="text-sm text-emerald-800 dark:text-emerald-200 font-medium">@lang('Mobile Successfully Verified')</span>
                            </div>
                        </div>

                        <!-- Continue Button -->
                        <a href="{{ route('user.home') }}" 
                           class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-emerald-500/20 text-center block">
                            <i class="las la-arrow-right mr-2"></i>@lang('Continue to Dashboard')
                        </a>
                    </div>
                @else
                    <!-- SMS Verification Required -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="las la-mobile text-3xl"></i>
                        </div>
                        
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">@lang('SMS Verification Required')</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('Please verify your mobile number to enhance your account security.')</p>
                    </div>

                    <!-- Mobile Info -->
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 rounded-2xl p-4 mb-6">
                        <div class="flex items-center">
                            <i class="las la-mobile text-emerald-600 dark:text-emerald-400 mr-3"></i>
                            <div>
                                <p class="text-sm text-emerald-800 dark:text-emerald-200">
                                    <strong>@lang('Mobile Number:')</strong> {{ auth()->user()->mobile }}
                                </p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">@lang('A 6 digit verification code sent to your mobile number')</p>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Code Form -->
                    <form method="POST" action="{{ route('user.verify.mobile') }}" class="mb-6" id="verification-form">
                        @csrf
                        <div class="mb-6">
                            @include($activeTemplate . 'partials.verification_code')
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                    id="verify-submit-btn">
                                <span class="default-text inline-flex items-center justify-center">
                                    <i class="las la-check text-xl mr-2"></i>@lang('Verify Code')
                                </span>
                                <span class="loading-text hidden inline-flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    @lang('Verifying...')
                                </span>
                            </button>
                            
                            <a href="{{ route('user.logout') }}" 
                               class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-gray-300/20 text-center inline-flex items-center justify-center">
                                <i class="las la-sign-out-alt text-xl mr-2"></i>@lang('Log Out')
                            </a>
                        </div>
                    </form>

                    <!-- Resend Code -->
                    <div class="text-center">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            @lang('If you don\'t get any code'), 
                            <span class="countdown-wrapper">
                                @lang('try again after') 
                                <span id="countdown" class="font-bold text-emerald-600 dark:text-emerald-400">--</span> 
                                @lang('seconds.')
                            </span>
                            <a href="{{ route('user.send.verify.code', 'sms') }}" 
                               class="try-again-link text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium hidden">
                                @lang('Try again')
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            const sendForm = document.getElementById('sendSmsForm');
            const sendBtn = document.getElementById('sendSmsBtn');
            const codeSection = document.getElementById('codeVerificationSection');
            const verifyForm = document.getElementById('verifyCodeForm');
            const verifyBtn = document.getElementById('verifyBtn');
            
            // Handle Send SMS form submission
            if (sendForm && sendBtn) {
                const defaultText = sendBtn.querySelector('.default-text');
                const loadingText = sendBtn.querySelector('.loading-text');

                sendForm.addEventListener('submit', function(e) {
                    // Show loading state
                    sendBtn.disabled = true;
                    defaultText.classList.add('hidden');
                    loadingText.classList.remove('hidden');
                    
                    // Simulate SMS sent - show code verification section
                    setTimeout(() => {
                        sendBtn.disabled = false;
                        defaultText.classList.remove('hidden');
                        loadingText.classList.add('hidden');
                        
                        // Show code verification section
                        if (codeSection) {
                            codeSection.classList.remove('hidden');
                            codeSection.scrollIntoView({ behavior: 'smooth' });
                        }
                    }, 2000);
                });
            }

            // Handle Verify Code form submission
            if (verifyForm && verifyBtn) {
                const defaultText = verifyBtn.querySelector('.default-text');
                const loadingText = verifyBtn.querySelector('.loading-text');

                verifyForm.addEventListener('submit', function(e) {
                    // Show loading state
                    verifyBtn.disabled = true;
                    defaultText.classList.add('hidden');
                    loadingText.classList.remove('hidden');
                });

                // Auto-submit when all digits are entered
                const verificationInputs = document.querySelectorAll('.verification-input');
                if (verificationInputs.length > 0) {
                    verificationInputs.forEach((input, index) => {
                        input.addEventListener('input', function() {
                            if (this.value && index === verificationInputs.length - 1) {
                                // Check if all inputs are filled
                                const allFilled = Array.from(verificationInputs).every(inp => inp.value);
                                if (allFilled) {
                                    setTimeout(() => {
                                        verifyForm.submit();
                                    }, 300);
                                }
                            }
                        });
                    });
                }
            }
        });
    </script>
@endsection

@push('style')
<style>
    /* SMS verification page specific styles */
    .verification-status {
        transition: all 0.3s ease;
    }
    
    .verification-status:hover {
        transform: translateY(-2px);
    }
    
    /* Code verification section animation */
    #codeVerificationSection {
        transition: all 0.5s ease;
    }
    
    #codeVerificationSection.hidden {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
    }
    
    #codeVerificationSection:not(.hidden) {
        max-height: 500px;
        opacity: 1;
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        'use strict';

        $(document).ready(function() {
            const verifyForm = $('#verification-form');
            const verifyBtn = $('#verify-submit-btn');
            const defaultText = verifyBtn.find('.default-text');
            const loadingText = verifyBtn.find('.loading-text');

            // Countdown functionality
            var distance = Number("{{ @$user->ver_code_send_at->addMinutes(2)->timestamp - time() }}");
            var countdownElement = document.getElementById("countdown");
            var countdownWrapper = document.querySelector('.countdown-wrapper');
            var tryAgainLink = document.querySelector('.try-again-link');
            
            if (distance > 0 && countdownElement) {
                var x = setInterval(function() {
                    distance--;
                    countdownElement.innerHTML = distance;
                    if (distance <= 0) {
                        clearInterval(x);
                        if (countdownWrapper) countdownWrapper.classList.add('hidden');
                        if (tryAgainLink) tryAgainLink.classList.remove('hidden');
                    }
                }, 1000);
            } else {
                // If countdown is already expired, show try again link immediately
                if (countdownWrapper) countdownWrapper.classList.add('hidden');
                if (tryAgainLink) tryAgainLink.classList.remove('hidden');
            }

            // Handle form submission
            verifyForm.on('submit', function(e) {
                // Show loading state
                verifyBtn.prop('disabled', true);
                verifyBtn.removeClass('hover:scale-[1.02]');
                defaultText.addClass('hidden');
                loadingText.removeClass('hidden');
            });

            // Function to show loading state on button
            function showLoadingState() {
                verifyBtn.prop('disabled', true);
                verifyBtn.removeClass('hover:scale-[1.02]');
                defaultText.addClass('hidden');
                loadingText.removeClass('hidden');
            }

            // Function to hide loading state on button
            function hideLoadingState() {
                verifyBtn.prop('disabled', false);
                verifyBtn.addClass('hover:scale-[1.02]');
                defaultText.removeClass('hidden');
                loadingText.addClass('hidden');
            }

            // Override the verification digit input handler to add button state management
            $(document).on('input', '.verification-digit', function() {
                // Small delay to allow the verification code script to process
                setTimeout(() => {
                    const code = $('#verification-code-hidden').val();
                    if (code.length === 6) {
                        showLoadingState();
                    } else {
                        hideLoadingState();
                    }
                }, 100);
            });

            // Initial state - disable button if no code
            hideLoadingState();
        });

    })(jQuery);
</script>
@endpush