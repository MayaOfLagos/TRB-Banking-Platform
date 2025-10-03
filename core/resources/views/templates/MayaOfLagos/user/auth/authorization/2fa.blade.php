@extends($activeTemplate . 'layouts.app')
@section('app')
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Verifying your security code...',
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

            <!-- 2FA Form -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-8">
                <form method="POST" action="{{ route('user.2fa.verify') }}" id="twoFaForm">
                    @csrf
                    
                    <!-- Info Section -->
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 rounded-2xl p-4 mb-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <i class="las la-info-circle text-emerald-600 dark:text-emerald-400 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-emerald-800 dark:text-emerald-200 mb-1">@lang('Security Verification Required')</h4>
                                <p class="text-sm text-emerald-700 dark:text-emerald-300">@lang('Please enter the 6-digit verification code from your Google Authenticator app to continue.')</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2FA Code Input -->
                    <div class="mb-6">
                        @include($activeTemplate . 'partials.verification_code', ['inputName' => 'code', 'required' => true])
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            id="submitBtn">
                        <span class="flex items-center justify-center">
                            <span class="default-text flex items-center">
                                <i class="las la-check mr-2"></i>
                                @lang('Verify & Continue')
                            </span>
                            <span class="loading-text hidden flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @lang('Verifying...')
                            </span>
                        </span>
                    </button>

                    <!-- Alternative Actions -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Logout Button -->
                            <a href="{{ route('user.logout') }}" 
                               class="flex-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 font-medium py-3 px-4 rounded-xl transition-colors duration-200 text-center">
                                <i class="las la-sign-out-alt mr-2"></i>@lang('Logout')
                            </a>
                            
                            <!-- Back to Dashboard (if applicable) -->
                            <a href="{{ route('user.home') }}" 
                               class="flex-1 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-medium py-3 px-4 rounded-xl transition-colors duration-200 text-center">
                                <i class="las la-home mr-2"></i>@lang('Dashboard')
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">@lang('Having trouble with your authenticator?')</p>
                <a href="#" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 text-sm font-medium transition-colors duration-200">
                    @lang('Contact Support')
                </a>
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

            const form = document.getElementById('twoFaForm');
            const submitBtn = document.getElementById('submitBtn');
            const defaultText = submitBtn.querySelector('.default-text');
            const loadingText = submitBtn.querySelector('.loading-text');

            // Handle form submission
            form.addEventListener('submit', function(e) {
                // Show loading state
                submitBtn.disabled = true;
                defaultText.classList.add('hidden');
                loadingText.classList.remove('hidden');
            });

            // Auto-submit when all 6 digits are entered
            const verificationInputs = document.querySelectorAll('.verification-input');
            if (verificationInputs.length > 0) {
                // Function to check if all inputs are filled
                const checkAllFilled = () => {
                    const allFilled = Array.from(verificationInputs).every(inp => inp.value.length === 1);
                    if (allFilled) {
                        // Auto-submit after a short delay
                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    }
                };

                // Add input event listener to each input
                verificationInputs.forEach((input, index) => {
                    input.addEventListener('input', function() {
                        // Only allow single digit
                        if (this.value.length > 1) {
                            this.value = this.value.slice(0, 1);
                        }
                        // Move to next input if current is filled
                        if (this.value.length === 1) {
                            const nextInput = this.nextElementSibling;
                            if (nextInput && nextInput.classList.contains('verification-input')) {
                                nextInput.focus();
                            }
                        }
                        // Check if all inputs are filled
                        checkAllFilled();
                    });

                    // Handle backspace to move to previous input
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Backspace' && this.value.length === 0) {
                            const prevInput = this.previousElementSibling;
                            if (prevInput && prevInput.classList.contains('verification-input')) {
                                prevInput.focus();
                            }
                        }
                    });

                    // Handle paste event for better UX
                    input.addEventListener('paste', function(e) {
                        e.preventDefault();
                        const pasteData = e.clipboardData.getData('text').replace(/\D/g, ''); // Only digits
                        const maxLength = verificationInputs.length;

                        // Fill inputs with pasted data
                        for (let i = 0; i < Math.min(pasteData.length, maxLength - index); i++) {
                            verificationInputs[index + i].value = pasteData[i];
                        }

                        // Focus on the next empty input or last input
                        const nextEmptyIndex = Array.from(verificationInputs).findIndex(inp => !inp.value);
                        if (nextEmptyIndex !== -1) {
                            verificationInputs[Math.min(nextEmptyIndex, maxLength - 1)].focus();
                        } else {
                            verificationInputs[maxLength - 1].focus();
                        }

                        // Check if all inputs are filled after paste
                        setTimeout(checkAllFilled, 100);
                    });
                });
            }

            // Reset loading state on page focus (in case of back navigation)
            window.addEventListener('focus', function() {
                submitBtn.disabled = false;
                defaultText.classList.remove('hidden');
                loadingText.classList.add('hidden');
            });
        });
    </script>
@endsection

@push('style')
<style>
    /* Custom 2FA specific styles */
    .verification-input {
        transition: all 0.3s ease;
    }

    .verification-input:focus {
        transform: scale(1.05);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    /* Dark mode enhancements for verification inputs */
    .dark .verification-input {
        background-color: rgba(31, 41, 55, 0.8);
        border-color: rgba(75, 85, 99, 0.5);
        color: white;
    }

    .dark .verification-input:focus {
        border-color: rgba(16, 185, 129, 0.5);
        background-color: rgba(31, 41, 55, 0.9);
    }

    /* Button text transition for smooth loading state */
    .default-text, .loading-text {
        transition: opacity 0.3s ease;
    }

    /* Prevent button text jumping during loading */
    #submitBtn {
        min-height: 56px; /* Ensure consistent button height */
    }
</style>
@endpush