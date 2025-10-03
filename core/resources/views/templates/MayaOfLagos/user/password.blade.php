@extends($activeTemplate . 'layouts.master')
@section('content')
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang('Change Password')</h1>
                <p class="text-gray-600 dark:text-gray-400">@lang('Update your account password for better security')</p>
            </div>
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                <i class="las la-key text-2xl text-blue-600 dark:text-blue-400"></i>
            </div>
        </div>
    </div>

    <!-- Premium Navigation Pills -->
    @include($activeTemplate . 'partials.user_nav_pills', [
        'currentPageTitle' => __('Change Password'),
        'currentPageIcon' => 'las la-key',
    ])

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Password Form -->
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Update Password')</h2>
                    <span class="hidden md:block text-sm text-gray-500 dark:text-gray-400">@lang('Choose a strong password')</span>
                </div>

                <form method="post" action="{{ route('user.change.password') }}">
                    @csrf
                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Current Password')
                            </label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
                                <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 toggle-password"
                                    data-target="current_password">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('New Password')
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
                                <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 toggle-password"
                                    data-target="password">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                            <div class="mt-2">
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
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
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
                    </div>

                    <!-- Password Requirements -->
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 mt-6">
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

                    <!-- Update Button -->
                    <div class="flex justify-end mt-6">
                        <button type="submit" id="submit-btn" disabled
                            class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-semibold px-6 py-3 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all duration-300 transform hover:scale-105 disabled:bg-gray-400 dark:disabled:bg-gray-600 disabled:cursor-not-allowed disabled:transform-none">
                            <i class="las la-save mr-2"></i>@lang('Update Password')
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Tips Sidebar -->
        <div class="lg:col-span-4">
            <!-- Security Tips -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Security Tips')</h3>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                            <i class="las la-shield-alt text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Use Strong Passwords')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Combine uppercase, lowercase, numbers, and special characters')</div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                            <i class="las la-random text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Unique Passwords')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Don\'t reuse passwords from other accounts')</div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                            <i class="las la-clock text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Regular Updates')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Change your password regularly for better security')</div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                            <i class="las la-user-secret text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Keep it Private')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Never share your password with anyone')</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer PIN -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Transfer Security')</h3>

                <div
                    class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div
                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mr-3">
                            <i class="las la-lock"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">@lang('Transfer PIN')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Secure your wire transfers and billing codes')</div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        @lang('Set up a 4-digit PIN for secure wire transfers and billing code verification.')
                    </p>

                    <a href="{{ route('user.transfer.pin') }}"
                        class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors block">
                        @if(auth()->user()->hasTransferPin())
                            @lang('Manage Transfer PIN')
                        @else
                            @lang('Setup Transfer PIN')
                        @endif
                    </a>
                </div>
            </div>

            <!-- Two-Factor Authentication -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Additional Security')</h3>

                <div
                    class="bg-gradient-to-r from-teal-50 to-blue-50 dark:from-teal-900/20 dark:to-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div
                            class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 rounded-lg flex items-center justify-center mr-3">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">@lang('Two-Factor Authentication')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Add an extra layer of security')</div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        @lang('Protect your account with two-factor authentication for enhanced security.')
                    </p>

                    <a href="{{ route('user.twofactor') }}"
                        class="w-full bg-teal-600 hover:bg-teal-700 dark:bg-teal-700 dark:hover:bg-teal-800 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors block">
                        @lang('Setup 2FA')
                    </a>
                </div>
            </div>

            <!-- Recent Login Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Recent Login Activity')</h3>

                <div class="space-y-3">
                    @if ($loginLogs && $loginLogs->count() > 0)
                        @foreach ($loginLogs as $log)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $log->user_ip }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $log->browser ?: 'Unknown Browser' }} • {{ $log->os ?: 'Unknown OS' }}
                                    </div>
                                    @if($log->city && $log->country)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $log->city }}, {{ $log->country }}
                                        </div>
                                    @elseif($log->country)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $log->country }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-6">
                            <i class="las la-history text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('No recent login activity')</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

            // Toggle password visibility
            $('.toggle-password').on('click', function() {
                const target = $(this).data('target');
                const input = $('#' + target);
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('la-eye').addClass('la-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('la-eye-slash').addClass('la-eye');
                }
            });

            // Password strength checker
            $('#password').on('input', function() {
                const password = $(this).val();
                const strength = checkPasswordStrength(password);

                updatePasswordStrength(strength);
                updatePasswordRequirements(password);
                checkPasswordMatch();
            });

            // Password confirmation checker
            $('#password_confirmation').on('input', function() {
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
                const strengthBar = $('#password-strength');
                const strengthText = $('#password-strength-text');

                const colors = ['bg-red-500', 'bg-red-400', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
                const texts = ['@lang('Very Weak')', '@lang('Weak')', '@lang('Fair')', '@lang('Good')',
                    '@lang('Strong')'
                ];
                const widths = [20, 40, 60, 80, 100];

                strengthBar.removeClass('bg-red-500 bg-red-400 bg-yellow-500 bg-blue-500 bg-green-500');
                strengthBar.addClass(colors[score - 1] || 'bg-gray-300');
                strengthBar.css('width', (widths[score - 1] || 0) + '%');
                strengthText.text(texts[score - 1] || '@lang('Enter a password')');
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
                    const icon = $('#' + req.id);
                    if (req.test) {
                        icon.removeClass('la-times text-red-500').addClass('la-check text-green-500');
                    } else {
                        icon.removeClass('la-check text-green-500').addClass('la-times text-red-500');
                    }
                });

                // Enable/disable submit button
                const allMet = requirements.every(req => req.test);
                $('#submit-btn').prop('disabled', !allMet);
            }

            function checkPasswordMatch() {
                const password = $('#password').val();
                const confirmation = $('#password_confirmation').val();
                const matchDiv = $('#password-match');

                if (confirmation.length > 0) {
                    if (password === confirmation) {
                        matchDiv.removeClass('text-red-500').addClass('text-green-500');
                        matchDiv.html('<i class="las la-check"></i> @lang('Passwords match')');
                        matchDiv.removeClass('hidden');
                    } else {
                        matchDiv.removeClass('text-green-500').addClass('text-red-500');
                        matchDiv.html('<i class="las la-times"></i> @lang('Passwords do not match')');
                        matchDiv.removeClass('hidden');
                    }
                } else {
                    matchDiv.addClass('hidden');
                }
            }

        })(jQuery);
    </script>
@endpush

@if (gs()->secure_password)
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
