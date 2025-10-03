@extends($activeTemplate . 'layouts.app')
@section('app')
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Loading account status...',
        'showPattern' => false
    ])

    <!-- Dark/Light Mode Toggle - Fixed Top -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
            <i class="las la-sun text-xl hidden dark:block"></i>
            <i class="las la-moon text-xl block dark:hidden"></i>
        </button>
    </div>

    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center p-4 transition-all duration-300">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-red-400/20 dark:bg-red-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-red-300/20 dark:bg-red-600/10 rounded-full blur-3xl"></div>
            <div class="absolute top-20 left-20 w-60 h-60 bg-red-200/20 dark:bg-red-400/5 rounded-full blur-2xl"></div>
        </div>

        <div class="relative w-full max-w-lg mx-auto">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ siteLogo() }}" alt="{{ __(gs('site_name')) }}" class="h-16 w-auto mx-auto">
                </a>
            </div>

            <!-- Ban Notice -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-8">
                
                <!-- Alert Section -->
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/50 rounded-2xl p-6 mb-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">@lang('Access Restricted')</h3>
                        <p class="text-red-700 dark:text-red-300 text-sm">@lang('Your account access has been temporarily restricted due to policy violations.')</p>
                    </div>
                </div>

                <!-- Ban Details -->
                @if(auth()->user()->ban_reason)
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">@lang('Reason for Suspension')</h4>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                        <p class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed">{{ __(auth()->user()->ban_reason) }}</p>
                    </div>
                </div>
                @endif

                <!-- Account Status -->
                <div class="mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <div class="text-center">
                                <i class="las la-user-circle text-2xl text-gray-600 dark:text-gray-400 mb-2"></i>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">@lang('Account Status')</p>
                                <p class="text-sm font-semibold text-red-600 dark:text-red-400">@lang('Suspended')</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <div class="text-center">
                                <i class="las la-clock text-2xl text-gray-600 dark:text-gray-400 mb-2"></i>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">@lang('Suspended Since')</p>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ showDateTime(auth()->user()->updated_at, 'd M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appeal Process -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/50 rounded-2xl p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="las la-lightbulb text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-1">@lang('Appeal Process')</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">@lang('If you believe this suspension was made in error, you can submit an appeal to our support team.')</p>
                            <ul class="text-xs text-blue-600 dark:text-blue-400 space-y-1">
                                <li>• @lang('Review our Terms of Service')</li>
                                <li>• @lang('Contact our support team')</li>
                                <li>• @lang('Provide relevant documentation')</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <!-- Contact Support -->
                    <a href="{{ route('contact') }}" 
                       class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-4 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-500/20 text-center block">
                        <i class="las la-envelope mr-2"></i>@lang('Contact Support')
                    </a>
                    
                    <!-- Secondary Actions -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Home Page -->
                        <a href="{{ route('home') }}" 
                           class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-3 px-4 rounded-xl transition-colors duration-200 text-center">
                            <i class="las la-home mr-2"></i>@lang('Home')
                        </a>
                        
                        <!-- Logout -->
                        <a href="{{ route('user.logout') }}" 
                           class="bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 font-medium py-3 px-4 rounded-xl transition-colors duration-200 text-center">
                            <i class="las la-sign-out-alt mr-2"></i>@lang('Logout')
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">@lang('Need immediate assistance?')</p>
                <div class="flex items-center justify-center space-x-4 text-sm">
                    @php
                        $contactContent = getContent('contact_us.content', true);
                        $emailAddress = $contactContent && isset($contactContent->data_values->email_address) ? $contactContent->data_values->email_address : 'support@company.com';
                        $contactNumber = $contactContent && isset($contactContent->data_values->contact_number) ? $contactContent->data_values->contact_number : null;
                    @endphp
                    
                    <a href="mailto:{{ $emailAddress }}" 
                       class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200">
                        <i class="las la-envelope mr-1"></i>@lang('Email Support')
                    </a>
                    
                    @if($contactNumber)
                    <span class="text-gray-400 dark:text-gray-600">|</span>
                    <a href="tel:{{ $contactNumber }}" 
                       class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 transition-colors duration-200">
                        <i class="las la-phone mr-1"></i>@lang('Call Support')
                    </a>
                    @endif
                </div>
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

            // Add subtle animations on load
            const mainContainer = document.querySelector('.relative.w-full.max-w-lg');
            if (mainContainer) {
                mainContainer.style.opacity = '0';
                mainContainer.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    mainContainer.style.transition = 'all 0.6s ease-out';
                    mainContainer.style.opacity = '1';
                    mainContainer.style.transform = 'translateY(0)';
                }, 100);
            }
        });
    </script>
@endsection

@push('style')
<style>
    /* Ban page specific animations */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    
    /* Custom hover effects for ban page */
    .hover-lift:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }
</style>
@endpush