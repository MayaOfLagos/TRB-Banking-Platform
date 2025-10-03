@extends($activeTemplate . 'layouts.app')
@section('app')
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Preparing email verification...',
        'showPattern' => false,
    ])

    {{-- Dark/Light Mode Toggle - Fixed Top --}}
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle"
            class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
            <i class="las la-sun text-xl hidden dark:block"></i>
            <i class="las la-moon text-xl block dark:hidden"></i>
        </button>
    </div>

    <div
        class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-emerald-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center p-4 transition-all duration-300">
        {{-- Background Elements --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-3xl">
            </div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-300/20 dark:bg-emerald-600/10 rounded-full blur-3xl">
            </div>
            <div class="absolute top-20 left-20 w-60 h-60 bg-emerald-200/20 dark:bg-emerald-400/5 rounded-full blur-2xl">
            </div>
        </div>

        <div class="relative w-full max-w-md mx-auto">
            {{-- Logo Section --}}
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ siteLogo() }}" alt="{{ __(gs('site_name')) }}" class="h-16 w-auto mx-auto">
                </a>
            </div>

            {{-- Email Verification Content --}}
            <div
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-8">
                @if (auth()->user()->ev)
                    {{-- Email Verified --}}
                    <div class="text-center">
                        <div
                            class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="las la-check-circle text-3xl"></i>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">@lang('Email Verified!')</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('Your email address has been successfully verified. You now have full access to your account.')</p>

                        <div
                            class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 rounded-2xl p-4 mb-6">
                            <div class="flex items-center justify-center">
                                <i class="las la-check text-emerald-600 dark:text-emerald-400 mr-2"></i>
                                <span
                                    class="text-sm text-emerald-800 dark:text-emerald-200 font-medium">@lang('Email Successfully Verified')</span>
                            </div>
                        </div>

                        {{-- Continue Button --}}
                        <a href="{{ route('user.home') }}"
                            class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-emerald-500/20 text-center block">
                            <i class="las la-arrow-right mr-2"></i>@lang('Continue to Dashboard')
                        </a>
                    </div>
                @else
                    {{-- Email Verification Required --}}
                    <div class="text-center">
                        <div
                            class="w-20 h-20 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="las la-envelope text-3xl"></i>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">@lang('Email Verification Required')</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('Please verify your email address to secure your account and access all features.')</p>
                    </div>

                    {{-- Email Info --}}
                    <div
                        class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 rounded-2xl p-4 mb-6">
                        <div class="flex items-center">
                            <i class="las la-envelope text-emerald-600 dark:text-emerald-400 mr-3"></i>
                            <div>
                                <p class="text-sm text-emerald-800 dark:text-emerald-200">
                                    <strong>@lang('Email Address:')</strong> {{ auth()->user()->email }}
                                </p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">@lang('A verification email has been sent to this address')</p>
                            </div>
                        </div>
                    </div>

                    {{-- Verification Code Form --}}
                    <form action="{{ route('user.verify.email') }}" method="POST" class="mb-6" id="verificationForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Verification Code')
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           name="code" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-center text-lg font-mono tracking-widest"
                                           placeholder="000000"
                                           maxlength="6"
                                           pattern="[0-9]{6}"
                                           required
                                           autocomplete="one-time-code">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="las la-key text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    @lang('Enter the 6-digit verification code sent to your email')
                                </p>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-500/20 disabled:opacity-50"
                                    id="verifyButton">
                                <span class="verify-text">
                                    <i class="las la-check-circle mr-2"></i>@lang('Verify Email')
                                </span>
                                <span class="verify-loading hidden">
                                    <svg class="animate-spin mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    @lang('Verifying...')
                                </span>
                            </button>
                        </div>
                    </form>

                    {{-- Action Buttons --}}
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        {{-- Resend Button --}}
                        <a href="{{ route('user.send.verify.code', 'email') }}" 
                           class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 text-center block"
                           id="resendBtn">
                            <span class="flex items-center justify-center">
                                <span class="default-text flex items-center">
                                    <i class="las la-paper-plane mr-2"></i>@lang('Resend')
                                </span>
                                <span class="loading-text hidden flex items-center">
                                    <svg class="animate-spin mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    @lang('Sending')
                                </span>
                            </span>
                        </a>

                        {{-- Logout Button --}}
                        <a href="{{ route('user.logout') }}" 
                           class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-red-500/20 text-center block">
                            <i class="las la-sign-out-alt mr-2"></i>@lang('Logout')
                        </a>
                    </div>

                    {{-- Countdown and Help Text --}}
                    <div class="text-center">
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                            @lang('Didn\'t receive the email? Check your spam folder.')
                        </p>
                        
                        {{-- Countdown Wrapper --}}
                        <div class="countdown-wrapper">
                            <p class="text-amber-600 dark:text-amber-400 text-sm font-medium">
                                @lang('Try again after') 
                                <span id="countdown" class="font-bold text-amber-700 dark:text-amber-300">--</span> 
                                @lang('seconds.')
                            </p>
                        </div>
                        
                        {{-- Try Again Link (Initially Hidden) --}}
                        <p class="try-again-link text-emerald-600 dark:text-emerald-400 text-sm font-medium hidden">
                            @lang('You can now resend the verification email.')
                        </p>
                    </div>

                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const html = document.documentElement;

            const savedTheme = localStorage.getItem('theme') || 'light';
            html.classList.toggle('dark', savedTheme === 'dark');

            themeToggle.addEventListener('click', () => {
                const isDark = html.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });

            var distance = Number("{{ @$user->ver_code_send_at->addMinutes(2)->timestamp - time() }}");
            const resendBtn = document.getElementById('resendBtn');
            const countdownWrapper = document.querySelector('.countdown-wrapper');
            const tryAgainLink = document.querySelector('.try-again-link');
            const countdownElement = document.getElementById('countdown');
            
            function disableResendButton() {
                if (resendBtn) {
                    resendBtn.style.pointerEvents = 'none';
                    resendBtn.style.opacity = '0.5';
                    resendBtn.classList.add('cursor-not-allowed');
                }
            }
            
            function enableResendButton() {
                if (resendBtn) {
                    resendBtn.style.pointerEvents = 'auto';
                    resendBtn.style.opacity = '1';
                    resendBtn.classList.remove('cursor-not-allowed');
                }
            }

            if (distance > 0) {
                disableResendButton();
                
                var countdownInterval = setInterval(function() {
                    distance--;
                    if (countdownElement) {
                        countdownElement.innerHTML = distance;
                    }
                    
                    if (distance <= 0) {
                        clearInterval(countdownInterval);
                        if (countdownWrapper) {
                            countdownWrapper.classList.add('hidden');
                        }
                        if (tryAgainLink) {
                            tryAgainLink.classList.remove('hidden');
                        }
                        enableResendButton();
                    }
                }, 1000);
            } else {
                if (countdownWrapper) {
                    countdownWrapper.classList.add('hidden');
                }
                if (tryAgainLink) {
                    tryAgainLink.classList.remove('hidden');
                }
                enableResendButton();
            }

            if (resendBtn) {
                const defaultText = resendBtn.querySelector('.default-text');
                const loadingText = resendBtn.querySelector('.loading-text');

                resendBtn.addEventListener('click', function(e) {
                    if (resendBtn.style.pointerEvents !== 'none') {
                        defaultText.classList.add('hidden');
                        loadingText.classList.remove('hidden');
                        
                        setTimeout(() => {
                            defaultText.classList.remove('hidden');
                            loadingText.classList.add('hidden');
                        }, 3000);
                    }
                });
            }

            const codeInput = document.querySelector('input[name="code"]');
            const verificationForm = document.getElementById('verificationForm');
            const verifyButton = document.getElementById('verifyButton');
            
            if (codeInput && verificationForm) {
                codeInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    
                    if (value.length > 6) {
                        value = value.slice(0, 6);
                    }
                    
                    e.target.value = value;
                    
                    if (value.length === 6) {
                        setTimeout(() => {
                            submitVerificationForm();
                        }, 500);
                    }
                });

                codeInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let paste = (e.clipboardData || window.clipboardData).getData('text');
                    let digits = paste.replace(/\D/g, '').slice(0, 6);
                    e.target.value = digits;
                    
                    if (digits.length === 6) {
                        setTimeout(() => {
                            submitVerificationForm();
                        }, 500);
                    }
                });

                function submitVerificationForm() {
                    if (verifyButton) {
                        verifyButton.disabled = true;
                        verifyButton.querySelector('.verify-text').classList.add('hidden');
                        verifyButton.querySelector('.verify-loading').classList.remove('hidden');
                        verificationForm.classList.add('submitting');
                    }
                    verificationForm.submit();
                }

                verificationForm.addEventListener('submit', function(e) {
                    if (!verificationForm.classList.contains('submitting')) {
                        e.preventDefault();
                        submitVerificationForm();
                    }
                });

                codeInput.focus();
            }
        });
    </script>
@endsection

@push('style')
    <style>
        /* Email verification page specific styles */
        .verification-status {
            transition: all 0.3s ease;
        }
        
        .verification-status:hover {
            transform: translateY(-2px);
        }

        /* Enhanced verification code input styling */
        input[name="code"] {
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5em;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 600;
        }

        input[name="code"]:focus {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
        }

        /* Pulse animation for the input when focused */
        input[name="code"]:focus {
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Loading animation for form submission */
        .submitting input[name="code"] {
            background: linear-gradient(90deg, #10b981, #059669, #10b981);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            color: white;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>
@endpush