@extends($activeTemplate . 'layouts.app')
@section('app')
    @php
        use App\Constants\Status;
    @endphp

    <!-- Auth Preloader Component -->
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Resetting your password...',
        'showPattern' => false,
    ])

    <!-- Dark/Light Mode Toggle - Fixed Top -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle"
            class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
            <i class="las la-sun text-xl hidden dark:block"></i>
            <i class="las la-moon text-xl block dark:hidden"></i>
        </button>
    </div>

    <!-- Main Container -->
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Mobile Logo - Centered Top -->
        <div class="lg:hidden fixed top-8 left-1/2 transform -translate-x-1/2 z-40">
            <!-- Dark colored logo → shown in light mode -->
            <img src="{{ siteLogo('dark') }}" alt="@lang('logo')" 
                 class="h-12 block dark:hidden" id="logo-for-white-bg">
            <!-- White colored logo → shown in dark mode -->
            <img src="{{ siteLogo() }}" alt="@lang('logo')" 
                 class="h-12 hidden dark:block" id="logo-for-dark-bg">
        </div>

        <!-- Main Content Container -->
        <div class="relative z-20 min-h-screen flex items-center justify-center p-4 pt-20 lg:pt-4">
            <div class="w-full max-w-md mx-auto">

                <!-- Desktop Logo - Above Container -->
                <div class="hidden lg:block text-center mb-8">
                    <!-- Dark colored logo → shown in light mode -->
                    <img src="{{ siteLogo('dark') }}" alt="@lang('logo')" 
                         class="h-16 mx-auto mb-4 block dark:hidden" id="logo-for-white-bg">
                    <!-- White colored logo → shown in dark mode -->
                    <img src="{{ siteLogo() }}" alt="@lang('logo')" 
                         class="h-16 mx-auto mb-4 hidden dark:block" id="logo-for-dark-bg">
                </div>

                <!-- Mobile Header -->
                <div class="lg:hidden text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Reset Password</h2>
                    <p class="text-gray-600 dark:text-gray-400">Create a new secure password</p>
                </div>

                <!-- Reset Form -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 transition-colors duration-300">

                <!-- Desktop Header -->
                <div class="hidden lg:block mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Reset Password</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">Enter your new password below</p>
                </div>
                    <form method="POST" action="{{ route('user.password.update') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <!-- Icon -->
                        <div class="flex justify-center">
                            <div
                                class="w-16 h-16 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center">
                                <i class="las la-lock text-2xl"></i>
                            </div>
                        </div>

                        <!-- Email Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Email Address')
                            </label>
                            <div
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors duration-300">
                                {{ $email }}
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('New Password')
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required autocomplete="new-password"
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('password') border-red-500 @enderror">
                                <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 toggle-password"
                                    data-target="password">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            <!-- Password Strength Indicator -->
                            <div id="password-strength-container" class="mt-3 hidden">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">@lang('Password strength:')</div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div id="password-strength"
                                        class="bg-red-500 h-2 rounded-full transition-all duration-300" style="width: 0%">
                                    </div>
                                </div>
                                <div id="password-strength-text" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    @lang('Enter a password')</div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Confirm New Password')
                            </label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300">
                                <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 toggle-password"
                                    data-target="password_confirmation">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                            <div id="password-match" class="text-xs mt-1 hidden">
                                <span class="text-red-500">
                                    <i class="las la-times"></i> @lang('Passwords do not match')
                                </span>
                            </div>
                        </div>

                        <!-- Password Requirements -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 transition-colors duration-300">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">@lang('Password Requirements')</h3>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center">
                                    <i id="req-length" class="las la-times text-red-500 mr-2"></i>
                                    @lang('At least 8 characters long')
                                </li>
                                <li class="flex items-center">
                                    <i id="req-uppercase" class="las la-times text-red-500 mr-2"></i>
                                    @lang('Contains uppercase letter')
                                </li>
                                <li class="flex items-center">
                                    <i id="req-lowercase" class="las la-times text-red-500 mr-2"></i>
                                    @lang('Contains lowercase letter')
                                </li>
                                <li class="flex items-center">
                                    <i id="req-number" class="las la-times text-red-500 mr-2"></i>
                                    @lang('Contains number')
                                </li>
                                <li class="flex items-center">
                                    <i id="req-special" class="las la-times text-red-500 mr-2"></i>
                                    @lang('Contains special character')
                                </li>
                            </ul>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submit-btn"
                            class="w-full bg-emerald-600 dark:bg-emerald-500 text-white font-semibold py-3 px-4 rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 focus:ring-4 focus:ring-emerald-200 dark:focus:ring-emerald-400/30 transition-all duration-300 transform hover:scale-105 flex items-center justify-center disabled:bg-gray-400 dark:disabled:bg-gray-600 disabled:cursor-not-allowed disabled:transform-none">
                            <i class="las la-check mr-2"></i>
                            @lang('Reset Password')
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('user.login') }}"
                            class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium transition-colors">
                            <i class="las la-arrow-left mr-1"></i>
                            @lang('Back to Sign In')
                        </a>
                    </div>
                </div>

                <!-- Success Notice -->
                <div
                    class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700/50 rounded-xl p-4 mt-6 transition-colors duration-300">
                    <div class="flex items-center">
                        <i class="las la-info-circle text-green-600 dark:text-green-400 mr-2"></i>
                        <div class="text-sm text-green-800 dark:text-green-300">
                            <strong>@lang('Almost Done!')</strong> @lang('After resetting your password, you\'ll be redirected to sign in with your new credentials.')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        const savedTheme = localStorage.getItem('theme') || 'light';
        html.classList.toggle('dark', savedTheme === 'dark');

        themeToggle.addEventListener('click', () => {
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    </script>
@endsection

@push('style')
    <style>
        .toggle-password {
            cursor: pointer;
        }
    </style>
@endpush

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            document.querySelectorAll('.toggle-password').forEach(function(button) {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    const input = document.getElementById(target);
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('la-eye');
                        icon.classList.add('la-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('la-eye-slash');
                        icon.classList.add('la-eye');
                    }
                });
            });

            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('submit-btn');
                        
            passwordInput.addEventListener('input', function() {
                const password = this.value;

                if (password.length > 0) {
                    document.getElementById('password-strength-container').classList.remove('hidden');
                    const strength = checkPasswordStrength(password);
                    updatePasswordStrength(strength);
                    updatePasswordRequirements(password);
                } else {
                    document.getElementById('password-strength-container').classList.add('hidden');
                }

                checkPasswordMatch();
            });

            passwordConfirmationInput.addEventListener('input', function() {
                checkPasswordMatch();
            });

            function checkPasswordStrength(password) {
                let score = 0;

                if (password.length >= 8) score++;
                if (/[a-z]/.test(password)) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[^A-Za-z0-9]/.test(password)) score++;

                return score;
            }

            function updatePasswordStrength(score) {
                const strengthBar = document.getElementById('password-strength');
                const strengthText = document.getElementById('password-strength-text');

                const colors = ['bg-red-500', 'bg-red-400', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
                const texts = ['@lang("Very Weak")', '@lang("Weak")', '@lang("Fair")', '@lang("Good")', '@lang("Strong")'];
                const widths = [20, 40, 60, 80, 100];

                strengthBar.className = strengthBar.className.replace(/bg-\w+-\d+/g, '');
                strengthBar.classList.add('h-2', 'rounded-full', 'transition-all', 'duration-300');
                strengthBar.classList.add(colors[score - 1] || 'bg-gray-300');
                strengthBar.style.width = (widths[score - 1] || 0) + '%';
                strengthText.textContent = texts[score - 1] || '@lang("Enter a password")';
            }

            function updatePasswordRequirements(password) {
                const requirements = [{
                        id: 'req-length',
                        test: password.length >= 8
                    },
                    {
                        id: 'req-uppercase',
                        test: /[A-Z]/.test(password)
                    },
                    {
                        id: 'req-lowercase',
                        test: /[a-z]/.test(password)
                    },
                    {
                        id: 'req-number',
                        test: /[0-9]/.test(password)
                    },
                    {
                        id: 'req-special',
                        test: /[^A-Za-z0-9]/.test(password)
                    }
                ];

                requirements.forEach(req => {
                    const icon = document.getElementById(req.id);
                    if (req.test) {
                        icon.classList.remove('la-times', 'text-red-500');
                        icon.classList.add('la-check', 'text-green-500');
                    } else {
                        icon.classList.remove('la-check', 'text-green-500');
                        icon.classList.add('la-times', 'text-red-500');
                    }
                });

                const allMet = requirements.every(req => req.test);
                const passwordValue = document.getElementById('password').value;
                const confirmation = document.getElementById('password_confirmation').value;
                const passwordsMatch = passwordValue === confirmation && confirmation.length > 0;

                document.getElementById('submit-btn').disabled = (confirmation.length > 0 && !passwordsMatch);
            }

            function checkPasswordMatch() {
                const passwordValue = document.getElementById('password').value;
                const confirmation = document.getElementById('password_confirmation').value;
                const matchDiv = document.getElementById('password-match');

                if (confirmation.length > 0) {
                    if (passwordValue === confirmation) {
                        matchDiv.classList.remove('text-red-500');
                        matchDiv.classList.add('text-green-500');
                        matchDiv.innerHTML = '<i class="las la-check"></i> @lang("Passwords match")';
                        matchDiv.classList.remove('hidden');
                    } else {
                        matchDiv.classList.remove('text-green-500');
                        matchDiv.classList.add('text-red-500');
                        matchDiv.innerHTML = '<i class="las la-times"></i> @lang("Passwords do not match")';
                        matchDiv.classList.remove('hidden');
                    }
                } else {
                    matchDiv.classList.add('hidden');
                }

                const password_val = document.getElementById('password').value;
                if (password_val.length > 0) {
                    updatePasswordRequirements(password_val);
                }
            }

        });
    </script>
@endpush
