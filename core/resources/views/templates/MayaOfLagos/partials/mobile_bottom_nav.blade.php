<!-- Premium Banking Mobile Bottom Navigation -->
<div class="fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg border-t border-gray-200 dark:border-gray-700 lg:hidden z-50 shadow-2xl">
    <div class="relative">
        <!-- Main Navigation Grid -->
        <div class="grid grid-cols-5 py-3 px-2">
            <!-- Dashboard -->
            <a href="{{ route('user.home') }}" class="flex flex-col items-center py-2 px-1 {{ request()->routeIs('user.home') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }} transition-all duration-300 hover:scale-105 group">
                <div class="relative">
                    <i class="las la-home text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                    @if(request()->routeIs('user.home'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-primary-500 rounded-full animate-pulse"></div>
                    @endif
                </div>
                <span class="text-xs font-medium mt-1 {{ request()->routeIs('user.home') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">@lang('Dashboard')</span>
            </a>

            <!-- History -->
            <a href="{{ route('user.transaction.history') }}" class="flex flex-col items-center py-2 px-1 {{ request()->routeIs('user.transaction.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }} transition-all duration-300 hover:scale-105 group">
                <div class="relative">
                    <i class="las la-history text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                    @if(request()->routeIs('user.transaction.*'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    @endif
                </div>
                <span class="text-xs font-medium mt-1 {{ request()->routeIs('user.transaction.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">@lang('History')</span>
            </a>

            <!-- Transfer (Main Center Button) -->
            <button @click="$dispatch('open-transfer-modal')" class="flex flex-col items-center relative -top-4 group">
                <div class="relative">
                    <!-- Main Button with Gradient and Breathing Animation -->
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 dark:from-primary-600 dark:to-primary-800 rounded-full shadow-xl flex items-center justify-center group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110">
                        <!-- Breathing Ring Animation -->
                        <div class="absolute inset-0 rounded-full bg-primary-400 opacity-20 animate-ping"></div>
                        <div class="absolute inset-0 rounded-full bg-primary-400 opacity-10 animate-ping animation-delay-150"></div>
                        
                        <!-- Transfer Icon with Animation -->
                        <i class="las la-exchange-alt text-white text-3xl transform group-hover:rotate-180 transition-all duration-500"></i>
                    </div>
                </div>
                <span class="text-xs font-bold mt-2 text-gray-600 dark:text-gray-300 transition-colors duration-300">@lang('Transfer')</span>
            </button>

            <!-- Deposit -->
            <a href="{{ route('user.deposit.index') }}" class="flex flex-col items-center py-2 px-1 {{ request()->routeIs('user.deposit.*') ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }} transition-all duration-300 hover:scale-105 group">
                <div class="relative">
                    <i class="las la-plus-circle text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                    @if(request()->routeIs('user.deposit.*'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    @endif
                </div>
                <span class="text-xs font-medium mt-1 {{ request()->routeIs('user.deposit.*') ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">@lang('Deposit')</span>
            </a>

            <!-- Withdrawal -->
            <a href="{{ route('user.withdraw') }}" class="flex flex-col items-center py-2 px-1 {{ request()->routeIs('user.withdraw*') ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }} transition-all duration-300 hover:scale-105 group">
                <div class="relative">
                    <i class="las la-minus-circle text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                    @if(request()->routeIs('user.withdraw*'))
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                    @endif
                </div>
                <span class="text-xs font-medium mt-1 {{ request()->routeIs('user.withdraw*') ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">@lang('Withdraw')</span>
            </a>
        </div>

        <!-- Premium Banking Accent Line -->
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-16 h-1 bg-gradient-to-r from-primary-400 via-primary-500 to-primary-600 rounded-full opacity-60"></div>
    </div>
</div>

<style>
/* Custom Animation Delays */
.animation-delay-150 {
    animation-delay: 150ms;
}

/* Enhanced Breathing Animation for Transfer Button */
@keyframes breathe {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.animate-breathe {
    animation: breathe 2s ease-in-out infinite;
}

/* Custom Hover Effects */
.group:hover .las {
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

/* Active State Glow Effect */
.nav-active {
    filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.4));
}
</style>