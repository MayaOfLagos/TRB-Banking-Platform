@extends($activeTemplate . 'layouts.master')
@section('content')

<div class="grid grid-cols-1 gap-6">
    <!-- Transfer Navigation Header -->
    <div class="w-full mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header with Gradient - Mobile Responsive -->
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 dark:from-blue-700 dark:via-blue-800 dark:to-blue-900 px-4 py-4 lg:px-8 lg:py-6 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-transparent via-white/20 to-transparent transform rotate-12"></div>
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/10 rounded-full"></div>
                </div>
                
                <!-- Desktop Header Layout -->
                <div class="relative hidden lg:flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <i class="las la-exchange-alt text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">@lang('Transfer Center')</h1>
                            <p class="text-blue-100 text-sm">@lang('Manage your money transfers and beneficiaries')</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="text-right">
                            <p class="text-blue-100 text-sm">@lang('Available Balance')</p>
                            <p class="text-white text-xl font-bold">{{ showUserAmount(auth()->user()->balance, auth()->user()) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Mobile Header Layout -->
                <div class="relative lg:hidden">
                    <!-- Mobile Header Top Row -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <i class="las la-exchange-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-white">@lang('Transfer Center')</h1>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Balance Card -->
                    <div class="mobile-balance-card backdrop-blur-sm rounded-xl p-3 border border-white/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-xs">@lang('Available Balance')</p>
                                <p class="text-white text-lg font-bold">{{ showUserAmount(auth()->user()->balance, auth()->user()) }}</p>
                            </div>
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="las la-wallet text-white text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Tabs - Mobile Responsive -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <!-- Desktop Navigation -->
                <div class="hidden lg:block px-8 py-6">
                    <div class="flex flex-wrap gap-2">
                        @if (gs()->modules->own_bank ?? false)
                            <a href="{{ route('user.transfer.own.bank.beneficiaries') }}" 
                               class="flex items-center px-4 py-2 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('user.transfer.own.bank.*') ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200' }}">
                                <i class="las la-university text-lg mr-2"></i>
                                @lang('Own Bank')
                            </a>
                        @endif
                        
                        @if (gs()->modules->other_bank ?? false)
                            <a href="{{ route('user.transfer.other.bank.beneficiaries') }}" 
                               class="flex items-center px-4 py-2 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('user.transfer.other.bank.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200' }}">
                                <i class="las la-building text-lg mr-2"></i>
                                @lang('Other Banks')
                            </a>
                        @endif
                        
                        @if (gs()->modules->wire_transfer ?? false)
                            <a href="{{ route('user.transfer.wire.index') }}" 
                               class="flex items-center px-4 py-2 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('user.transfer.wire.*') ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200' }}">
                                <i class="las la-globe text-lg mr-2"></i>
                                @lang('Wire Transfer')
                            </a>
                        @endif
                        
                        <a href="{{ route('user.transfer.history') }}" 
                           class="flex items-center px-4 py-2 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('user.transfer.history') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border border-gray-200 dark:border-gray-600' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200' }}">
                            <i class="las la-history text-lg mr-2"></i>
                            @lang('Transfer History')
                        </a>
                    </div>
                </div>

                <!-- Mobile Navigation with Horizontal Scroll -->
                <div class="lg:hidden px-4 py-4">
                    <div class="overflow-x-auto">
                        <div class="flex gap-3 min-w-max pb-2">
                            @if (gs()->modules->own_bank ?? false)
                                <a href="{{ route('user.transfer.own.bank.beneficiaries') }}" 
                                   class="flex flex-col items-center px-3 py-3 rounded-xl font-medium transition-all duration-200 flex-shrink-0 w-20 {{ request()->routeIs('user.transfer.own.bank.*') ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200 border border-gray-200 dark:border-gray-600' }}">
                                    <i class="las la-university text-2xl mb-1"></i>
                                    <span class="text-xs text-center leading-tight">@lang('Own Bank')</span>
                                </a>
                            @endif
                            
                            @if (gs()->modules->other_bank ?? false)
                                <a href="{{ route('user.transfer.other.bank.beneficiaries') }}" 
                                   class="flex flex-col items-center px-3 py-3 rounded-xl font-medium transition-all duration-200 flex-shrink-0 w-20 {{ request()->routeIs('user.transfer.other.bank.*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200 border border-gray-200 dark:border-gray-600' }}">
                                    <i class="las la-building text-2xl mb-1"></i>
                                    <span class="text-xs text-center leading-tight">@lang('Other Banks')</span>
                                </a>
                            @endif
                            
                            @if (gs()->modules->wire_transfer ?? false)
                                <a href="{{ route('user.transfer.wire.index') }}" 
                                   class="flex flex-col items-center px-3 py-3 rounded-xl font-medium transition-all duration-200 flex-shrink-0 w-20 {{ request()->routeIs('user.transfer.wire.*') ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200 border border-gray-200 dark:border-gray-600' }}">
                                    <i class="las la-globe text-2xl mb-1"></i>
                                    <span class="text-xs text-center leading-tight">@lang('Wire Transfer')</span>
                                </a>
                            @endif
                            
                            <a href="{{ route('user.transfer.history') }}" 
                               class="flex flex-col items-center px-3 py-3 rounded-xl font-medium transition-all duration-200 flex-shrink-0 w-20 {{ request()->routeIs('user.transfer.history') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border border-gray-200 dark:border-gray-600' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200 border border-gray-200 dark:border-gray-600' }}">
                                <i class="las la-history text-2xl mb-1"></i>
                                <span class="text-xs text-center leading-tight">@lang('History')</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="w-full">
        @yield('transfer-content')
    </div>
</div>

@endsection

@push('style')
<style>
/* Mobile navigation horizontal scroll */
.overflow-x-auto {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

/* Ensure min-width for scroll container */
.min-w-max {
    min-width: max-content;
}

/* Mobile navigation item styles */
@media (max-width: 1023px) {
    .mobile-nav-item {
        min-width: 80px;
        max-width: 80px;
    }
    
    .mobile-nav-item:active {
        transform: scale(0.95);
        transition: transform 0.1s ease;
    }
    
    /* Fix for text wrapping */
    .mobile-nav-item span {
        word-wrap: break-word;
        hyphens: auto;
    }
}

/* Gradient background animation for mobile balance card */
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.mobile-balance-card {
    background: linear-gradient(-45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.15), rgba(255,255,255,0.1), rgba(255,255,255,0.05));
    background-size: 400% 400%;
    animation: gradientShift 3s ease infinite;
}

/* Ensure responsive container doesn't break */
@media (max-width: 1023px) {
    .overflow-x-auto {
        margin-left: -1rem;
        margin-right: -1rem;
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
@endpush