@extends($activeTemplate . 'layouts.app')
@section('app')
    @php
        use App\Constants\Status;
    @endphp

    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Preparing password reset...',
        'showPattern' => false
    ])

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
            <img src="{{ siteLogo('dark') }}" alt="@lang('logo')" 
                 class="h-12 block dark:hidden" id="logo-for-white-bg">
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
                    <!-- White colored logo → shown in dark mode -->
                    <img src="{{ siteLogo() }}" alt="@lang('logo')" 
                         class="h-16 mx-auto mb-4 hidden dark:block" id="logo-for-dark-bg">
                </div>
                <!-- Mobile Header -->
                <div class="lg:hidden text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Forgot Password?</h2>
                    <p class="text-gray-600 dark:text-gray-400">No worries, we'll send you reset instructions</p>
                </div>

                <!-- Reset Form -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 mb-4 transition-colors duration-300">
                
                <!-- Desktop Header -->
                <div class="hidden lg:block mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Forgot Password?</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">Enter your email to receive reset instructions</p>
                </div>
                    <form method="POST" action="{{ route('user.password.email') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Icon -->
                        <div class="flex justify-center">
                            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center">
                                <i class="las la-key text-2xl"></i>
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Email or Username')
                            </label>
                            <input type="text" 
                                   name="value" 
                                   id="value" 
                                   value="{{ old('value') }}" 
                                   required
                                   autofocus
                                   placeholder="@lang('Enter your email or username')"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 @error('value') border-red-500 @enderror">
                            @error('value')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- reCAPTCHA -->
                        @if(gs()->reCaptcha)
                            <div class="flex justify-center">
                                @php echo reCaptcha() @endphp
                            </div>
                        @endif

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
                            <i class="las la-paper-plane mr-2" id="normal-icon"></i>
                            <!-- Button Text -->
                            <span id="button-text">@lang('Send Reset Link')</span>
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

                <!-- Security Notice -->
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-4 transition-colors duration-300">
                    <div class="flex items-center">
                        <i class="las la-shield-alt text-amber-600 dark:text-amber-400 mr-2"></i>
                        <div class="text-sm text-amber-800 dark:text-amber-300">
                            <strong>@lang('Security Notice:')</strong> @lang('Reset links expire after 1 hour for your security. Never share reset links with anyone.')
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

@push('script')
@if(gs()->reCaptcha)
    <script src="https://www.google.com/recaptcha/api.js"></script>
@endif

<script>
    (function($) {
        'use strict';

        $('#value').focus();

        $('form').on('submit', function(e) {
            const value = $('#value').val().trim();
            
            if (value === '') {
                e.preventDefault();
                alert('@lang("Please enter your email or username")');
                return false;
            }
            
            showLoadingState();
        });

        function showLoadingState() {
            const submitBtn = $('#submit-btn');
            const loadingSpinner = $('#loading-spinner');
            const normalIcon = $('#normal-icon');
            const buttonText = $('#button-text');
            
            submitBtn.prop('disabled', true);
            
            normalIcon.addClass('hidden');
            loadingSpinner.removeClass('hidden');
            
            buttonText.text('@lang("Sending...")');
            
            submitBtn.removeClass('hover:scale-105 hover:bg-emerald-700 dark:hover:bg-emerald-600');
        }

        function resetLoadingState() {
            const submitBtn = $('#submit-btn');
            const loadingSpinner = $('#loading-spinner');
            const normalIcon = $('#normal-icon');
            const buttonText = $('#button-text');
            
            submitBtn.prop('disabled', false);
            
            normalIcon.removeClass('hidden');
            loadingSpinner.addClass('hidden');
            
            buttonText.text('@lang("Send Reset Link")');
            
            submitBtn.addClass('hover:scale-105 hover:bg-emerald-700 dark:hover:bg-emerald-600');
        }

        @if ($errors->any())
            resetLoadingState();
        @endif

    })(jQuery);
</script>
@endpush