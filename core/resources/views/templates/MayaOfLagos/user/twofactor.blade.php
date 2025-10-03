@extends($activeTemplate . 'layouts.master')
@section('content')
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang('Two Factor Authentication')</h1>
                <p class="text-gray-600 dark:text-gray-400">@lang('Add an extra layer of security to your account')</p>
            </div>
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                <i class="las la-shield-alt text-2xl text-green-600 dark:text-green-400"></i>
            </div>
        </div>
    </div>

    <!-- Premium Navigation Pills -->
    @include($activeTemplate . 'partials.user_nav_pills', [
        'currentPageTitle' => __('2FA Security'),
        'currentPageIcon' => 'las la-shield-alt'
    ])

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-8">
            @if(auth()->user()->ts)
                <!-- 2FA Enabled -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center mr-4">
                                <i class="las la-check-circle text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('2FA Enabled')</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Your account is protected with two-factor authentication')</p>
                            </div>
                        </div>
                        <span class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-sm font-medium">
                            @lang('Active')
                        </span>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="las la-info-circle text-green-600 dark:text-green-400 mr-2"></i>
                            <p class="text-sm text-green-800 dark:text-green-300">
                                @lang('Two-factor authentication is currently enabled. Your account has additional security protection.')
                            </p>
                        </div>
                    </div>

                    <!-- Disable 2FA Form -->
                    <form action="{{ route('user.twofactor.disable') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Google Authenticator Code')
                                </label>
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       required
                                       maxlength="6"
                                       placeholder="@lang('Enter 6-digit code')"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
                            </div>
                            
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white font-semibold px-6 py-3 rounded-lg focus:ring-4 focus:ring-red-200 dark:focus:ring-red-900 transition-all duration-300">
                                <i class="las la-times mr-2"></i>@lang('Disable 2FA')
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- 2FA Setup -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg flex items-center justify-center mr-4">
                                <i class="las la-exclamation-triangle text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('2FA Disabled')</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Enable two-factor authentication for better security')</p>
                            </div>
                        </div>
                        <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 px-3 py-1 rounded-full text-sm font-medium">
                            @lang('Inactive')
                        </span>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="las la-lightbulb text-blue-600 dark:text-blue-400 mr-2"></i>
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                @lang('Add an extra layer of security to your account. You\'ll need your phone or another device.')
                            </p>
                        </div>
                    </div>

                    <!-- Setup Steps -->
                    <div class="space-y-6">
                        <!-- Step 1: Download App -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                    <span class="text-sm font-bold">1</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('Download Authenticator App')</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        @lang('Download Google Authenticator or any compatible TOTP authenticator app on your mobile device.')
                                    </p>
                                    <div class="flex flex-wrap gap-3">
                                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" 
                                           target="_blank"
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                            <i class="lab la-google-play mr-2 text-green-600"></i>
                                            @lang('Google Play')
                                        </a>
                                        <a href="https://apps.apple.com/app/google-authenticator/id388497605" 
                                           target="_blank"
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                            <i class="lab la-app-store mr-2 text-blue-600"></i>
                                            @lang('App Store')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Scan QR Code -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                    <span class="text-sm font-bold">2</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('Scan QR Code')</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        @lang('Use your authenticator app to scan this QR code. If you can\'t scan it, enter the key manually.')
                                    </p>
                                    
                                    <div class="flex flex-col lg:flex-row gap-6">
                                        <!-- QR Code -->
                                        <div class="flex-shrink-0">
                                            <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 text-center">
                                                <img class="mx-auto max-w-full h-auto" src="{{ $qrCodeUrl }}" alt="@lang('QR Code for 2FA Setup')">
                                            </div>
                                        </div>
                                        
                                        <!-- Manual Entry -->
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">@lang('Manual Entry Key')</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                                @lang('If you can\'t scan the QR code, enter this key manually in your authenticator app:')
                                            </p>
                                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3 mb-3">
                                                <code class="text-sm font-mono text-gray-900 dark:text-gray-100 break-all">{{ $secret }}</code>
                                            </div>
                                            <button type="button" 
                                                    onclick="copyToClipboard('{{ $secret }}')"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                <i class="las la-copy mr-1"></i>@lang('Copy Key')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Verify -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                    <span class="text-sm font-bold">3</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('Verify Setup')</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        @lang('Enter the 6-digit code from your authenticator app to complete the setup.')
                                    </p>
                                    
                                    <form action="{{ route('user.twofactor.enable') }}" method="POST" class="max-w-md">
                                        @csrf
                                        <input type="hidden" name="key" value="{{ $secret }}">
                                        <div class="space-y-4">
                                            <div>
                                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    @lang('Verification Code')
                                                </label>
                                                <input type="text" 
                                                       name="code" 
                                                       id="code" 
                                                       required
                                                       maxlength="6"
                                                       placeholder="@lang('Enter 6-digit code')"
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
                                            </div>
                                            
                                            <button type="submit" 
                                                    class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-semibold px-6 py-3 rounded-lg focus:ring-4 focus:ring-green-200 dark:focus:ring-green-900 transition-all duration-300 transform hover:scale-105">
                                                <i class="las la-check mr-2"></i>@lang('Enable 2FA')
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4">
            <!-- Security Benefits -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Security Benefits')</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                            <i class="las la-shield-alt text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Enhanced Security')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Protects against unauthorized access even if your password is compromised')</div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                            <i class="las la-mobile text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Mobile Verification')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Use your mobile device as a second factor for authentication')</div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                            <i class="las la-clock text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Time-Based Codes')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Codes change every 30 seconds for maximum security')</div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                            <i class="las la-wifi text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Works Offline')</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">@lang('Authenticator apps work without internet connection')</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!auth()->user()->ts)
                <!-- Compatible Apps -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Compatible Apps')</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mr-3">
                                <i class="lab la-google text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">@lang('Google Authenticator')</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Official Google app')</div>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center mr-3">
                                <i class="las la-key text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">@lang('Authy')</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Cross-platform authenticator')</div>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center mr-3">
                                <i class="las la-shield-alt text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">@lang('Microsoft Authenticator')</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Microsoft\'s authenticator app')</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recovery Information -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-6">
                    <div class="flex items-center mb-3">
                        <i class="las la-exclamation-triangle text-yellow-600 dark:text-yellow-400 mr-2"></i>
                        <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-300">@lang('Important')</h3>
                    </div>
                    
                    <div class="text-sm text-yellow-800 dark:text-yellow-300 space-y-2">
                        <p>@lang('Keep your authenticator app secure and backed up. If you lose access to your device, you may be locked out of your account.')</p>
                        <p>@lang('Consider saving backup codes or setting up multiple devices for recovery purposes.')</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.textContent = '@lang("Copied to clipboard!")';
            document.body.appendChild(toast);
            
            setTimeout(function() {
                document.body.removeChild(toast);
            }, 2000);
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
        });
    }

    // Auto-format verification code input
    document.addEventListener('DOMContentLoaded', function() {
        const codeInputs = document.querySelectorAll('input[name="code"]');
        codeInputs.forEach(function(input) {
            input.addEventListener('input', function(e) {
                // Remove any non-digit characters
                this.value = this.value.replace(/\D/g, '');
                
                // Limit to 6 digits
                if (this.value.length > 6) {
                    this.value = this.value.slice(0, 6);
                }
            });
        });
    });
</script>
@endpush