<!-- Sidebar -->
<aside x-cloak :class="{ '-translate-x-full': !sidebarOpen }"
    class="sidebar-loading-fix fixed top-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out"
    style="height: calc(100vh - 5rem); height: calc(100dvh - 5rem);">
    <!-- Mobile: Reduce height to account for bottom nav, Desktop: Full height -->
    <style>
        @media (min-width: 1024px) {
            aside {
                height: 100vh !important;
                height: 100dvh !important;
            }
        }
    </style>
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-6">
            <a href="{{ route('user.home') }}" class="flex items-center space-x-3">
                @php
                    $whiteColoredLogo = null; // White logo for dark backgrounds
                    $darkColoredLogo = null;  // Dark logo for light backgrounds
                    try {
                        $whiteColoredLogo = siteLogo(); // logo.png - White colored logo
                        $darkColoredLogo = siteLogo('dark'); // logo_dark.png - Dark colored logo
                    } catch (Exception $e) {
                        $whiteColoredLogo = null;
                        $darkColoredLogo = null;
                    }
                @endphp

                @if ($whiteColoredLogo && $darkColoredLogo)
                    <!-- Dynamic Site Logo -->
                    <div class="flex-shrink-0">
                        <!-- Dark colored logo → shown in light mode (on white background) -->
                        <img src="{{ $darkColoredLogo }}" alt="{{ gs()->siteName ?? 'Site Logo' }}"
                            class="h-10 w-auto max-w-32 object-contain block dark:hidden"
                            id="logo-for-white-bg">
                        <!-- White colored logo → shown in dark mode (on dark background) -->
                        <img src="{{ $whiteColoredLogo }}" alt="{{ gs()->siteName ?? 'Site Logo' }}"
                            class="h-10 w-auto max-w-32 object-contain hidden dark:block"
                            id="logo-for-dark-bg">
                    </div>
                @elseif ($whiteColoredLogo)
                    <!-- Fallback to white colored logo only -->
                    <div class="flex-shrink-0">
                        <img src="{{ $whiteColoredLogo }}" alt="{{ gs()->siteName ?? 'Site Logo' }}"
                            class="h-10 w-auto max-w-32 object-contain">
                    </div>
                @else
                    <!-- Fallback Icon -->
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center">
                            <i class="las la-university text-white text-xl"></i>
                        </div>
                    </div>

                    <div class="hidden sm:block">
                        <span
                            class="text-lg font-bold text-gray-900 dark:text-white">{{ gs()->siteName ?? 'Site Title' }}</span>
                        <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Web Portal')</div>
                    </div>
                @endif
            </a>
            <button @click="sidebarOpen = false"
                class="lg:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- User Quick Info -->
        @auth
        <div
            class="p-4 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div
                    class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                    <span
                        class="text-primary-600 dark:text-primary-400 font-bold text-sm">{{ substr(auth()->user()->firstname, 0, 1) }}{{ substr(auth()->user()->lastname, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ gs()->cur_sym . showAmount(auth()->user()->balance, currencyFormat: false) }}</p>
                </div>
            </div>
        </div>
        @endauth

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent pb-20 lg:pb-6">
            <!-- Mobile spacing adjustment: pb-20 for mobile bottom nav clearance, pb-6 for desktop -->
            <!-- Dashboard -->
            <a href="{{ route('user.home') }}"
                class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-700 dark:hover:text-primary-300 transition-all duration-200 {{ menuActive('user.home') ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium shadow-sm' : '' }} group">
                <i
                    class="las la-home text-xl mr-3 {{ menuActive('user.home') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-600 dark:group-hover:text-primary-400' }}"></i>
                <span>@lang('Dashboard')</span>
            </a>

            @if (gs()->modules->deposit ?? true)
                <!-- Deposits -->
                <div class="relative" x-data="{ depositOpen: false }">
                    <button @click="depositOpen = !depositOpen"
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-300 transition-all duration-200 {{ menuActive('user.deposit*') ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 font-medium shadow-sm' : '' }} group">
                        <div class="flex items-center">
                            <i
                                class="las la-arrow-down text-xl mr-3 {{ menuActive('user.deposit*') ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-green-600 dark:group-hover:text-green-400' }}"></i>
                            <span>@lang('Deposits')</span>
                        </div>
                        <i class="las la-chevron-down text-sm transition-transform duration-200"
                            :class="{ 'rotate-180': depositOpen }"></i>
                    </button>
                    <div x-show="depositOpen" x-transition class="mt-1 ml-8 space-y-1">
                        <a href="{{ route('user.deposit.index') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-300 transition-colors text-sm {{ menuActive('user.deposit.index') ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300' : '' }}">
                            <i class="las la-plus text-sm mr-2"></i>
                            @lang('New Deposit')
                        </a>
                        <a href="{{ route('user.deposit.history') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-300 transition-colors text-sm {{ menuActive('user.deposit.history') ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300' : '' }}">
                            <i class="las la-history text-sm mr-2"></i>
                            @lang('Deposit History')
                        </a>
                    </div>
                </div>
            @endif

            @if (gs()->modules->withdraw ?? true)
                <!-- Withdrawals -->
                <div class="relative" x-data="{ withdrawOpen: false }">
                    <button @click="withdrawOpen = !withdrawOpen"
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300 transition-all duration-200 {{ menuActive('user.withdraw*') ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 font-medium shadow-sm' : '' }} group">
                        <div class="flex items-center">
                            <i
                                class="las la-arrow-up text-xl mr-3 {{ menuActive('user.withdraw*') ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-red-600 dark:group-hover:text-red-400' }}"></i>
                            <span>@lang('Withdrawals')</span>
                        </div>
                        <i class="las la-chevron-down text-sm transition-transform duration-200"
                            :class="{ 'rotate-180': withdrawOpen }"></i>
                    </button>
                    <div x-show="withdrawOpen" x-transition class="mt-1 ml-8 space-y-1">
                        <a href="{{ route('user.withdraw') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300 transition-colors text-sm {{ menuActive('user.withdraw') && !request()->routeIs('user.withdraw.history') ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300' : '' }}">
                            <i class="las la-minus text-sm mr-2"></i>
                            @lang('New Withdrawal')
                        </a>
                        <a href="{{ route('user.withdraw.history') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300 transition-colors text-sm {{ menuActive('user.withdraw.history') ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300' : '' }}">
                            <i class="las la-history text-sm mr-2"></i>
                            @lang('Withdrawal History')
                        </a>
                    </div>
                </div>
            @endif

            @if (gs()->modules->own_bank || gs()->modules->other_bank || gs()->modules->wire_transfer)
                <!-- Transfer -->
                <div class="relative" x-data="{ transferOpen: false }">
                    <button @click="transferOpen = !transferOpen"
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-700 dark:hover:text-blue-300 transition-all duration-200 {{ menuActive('user.transfer*') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium shadow-sm' : '' }} group">
                        <div class="flex items-center">
                            <i
                                class="las la-exchange-alt text-xl mr-3 {{ menuActive('user.transfer*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400' }}"></i>
                            <span>@lang('Transfer Money')</span>
                        </div>
                        <i class="las la-chevron-down text-sm transition-transform duration-200"
                            :class="{ 'rotate-180': transferOpen }"></i>
                    </button>
                    <div x-show="transferOpen" x-transition class="mt-1 ml-8 space-y-1">
                        <button @click="$dispatch('open-transfer-modal')"
                            class="w-full flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-700 dark:hover:text-blue-300 transition-colors text-sm">
                            <i class="las la-paper-plane text-sm mr-2"></i>
                            @lang('Send Money')
                        </button>
                        <a href="{{ route('user.transfer.history') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-700 dark:hover:text-blue-300 transition-colors text-sm {{ menuActive('user.transfer.history') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : '' }}">
                            <i class="las la-history text-sm mr-2"></i>
                            @lang('Transfer History')
                        </a>
                    </div>
                </div>
            @endif

            @if (gs()->modules->airtime ?? false)
                <!-- Airtime Top Up -->
                <a href="{{ route('user.airtime.form') }}"
                    class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-teal-50 dark:hover:bg-teal-900/20 hover:text-teal-700 dark:hover:text-teal-300 transition-all duration-200 {{ menuActive('user.airtime.*') ? 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 font-medium shadow-sm' : '' }} group">
                    <i
                        class="las la-mobile-alt text-xl mr-3 {{ menuActive('user.airtime.*') ? 'text-teal-600 dark:text-teal-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-teal-600 dark:group-hover:text-teal-400' }}"></i>
                    <span>@lang('Airtime Top Up')</span>
                </a>
            @endif

            <!-- Transactions -->
            <a href="{{ route('user.transaction.history') }}"
                class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-300 transition-all duration-200 {{ menuActive('user.transaction*') ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium shadow-sm' : '' }} group">
                <i
                    class="las la-list text-xl mr-3 {{ menuActive('user.transaction*') ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-purple-600 dark:group-hover:text-purple-400' }}"></i>
                <span>@lang('Transactions')</span>
            </a>

            @if (gs()->modules->fdr ?? false)
                <!-- FDR -->
                <a href="{{ route('user.fdr.list') }}"
                    class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-yellow-50 dark:hover:bg-yellow-900/20 hover:text-yellow-700 dark:hover:text-yellow-300 transition-all duration-200 {{ menuActive('user.fdr.*') ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 font-medium shadow-sm' : '' }} group">
                    <i
                        class="las la-certificate text-xl mr-3 {{ menuActive('user.fdr.*') ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-yellow-600 dark:group-hover:text-yellow-400' }}"></i>
                    <span>@lang('Fixed Deposits')</span>
                </a>
            @endif

            @if (gs()->modules->dps ?? false)
                <!-- DPS -->
                <a href="{{ route('user.dps.list') }}"
                    class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-700 dark:hover:text-indigo-300 transition-all duration-200 {{ menuActive('user.dps.*') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium shadow-sm' : '' }} group">
                    <i
                        class="las la-piggy-bank text-xl mr-3 {{ menuActive('user.dps.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}"></i>
                    <span>@lang('DPS')</span>
                </a>
            @endif

            @if (gs()->modules->loan ?? false)
                <!-- Loans -->
                <a href="{{ route('user.loan.list') }}"
                    class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-700 dark:hover:text-orange-300 transition-all duration-200 {{ menuActive('user.loan.*') ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 font-medium shadow-sm' : '' }} group">
                    <i
                        class="las la-hand-holding-usd text-xl mr-3 {{ menuActive('user.loan.*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-orange-600 dark:group-hover:text-orange-400' }}"></i>
                    <span>@lang('Loans')</span>
                </a>
            @endif

            @if (gs()->modules->referral_system)
                <!-- Referral -->
                <a href="{{ route('user.referral.users') }}"
                    class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-pink-50 dark:hover:bg-pink-900/20 hover:text-pink-700 dark:hover:text-pink-300 transition-all duration-200 {{ menuActive('user.referral.*') ? 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 font-medium shadow-sm' : '' }} group">
                    <i
                        class="las la-users text-xl mr-3 {{ menuActive('user.referral.*') ? 'text-pink-600 dark:text-pink-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-pink-600 dark:group-hover:text-pink-400' }}"></i>
                    <span>@lang('Referral')</span>
                    @auth
                        @if (auth()->user()->allReferees->count() > 0)
                            <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300">
                                {{ auth()->user()->allReferees->count() }}
                            </span>
                        @endif
                    @endauth
                </a>
            @endif

            @if (gs('rebate_system_enabled'))
                <!-- Rebate System -->
                <div class="relative" x-data="{ rebateOpen: false }">
                    <button @click="rebateOpen = !rebateOpen"
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-300 transition-all duration-200 {{ menuActive('user.rebate*') ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium shadow-sm' : '' }} group">
                        <div class="flex items-center">
                            <i
                                class="las la-gift text-xl mr-3 {{ menuActive('user.rebate*') ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-purple-600 dark:group-hover:text-purple-400' }}"></i>
                            <span>@lang('Rebate System')</span>
                        </div>
                        <i class="las la-chevron-down text-sm transition-transform duration-200"
                            :class="{ 'rotate-180': rebateOpen }"></i>
                    </button>
                    <div x-show="rebateOpen" x-transition class="mt-1 ml-8 space-y-1">
                        <a href="{{ route('user.rebate.dashboard') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-300 transition-colors text-sm {{ menuActive('user.rebate.dashboard') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : '' }}">
                            <i class="las la-chart-pie text-sm mr-2"></i>
                            @lang('Dashboard')
                        </a>
                        <a href="{{ route('user.rebate.programs') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-300 transition-colors text-sm {{ menuActive('user.rebate.programs') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : '' }}">
                            <i class="las la-list-alt text-sm mr-2"></i>
                            @lang('Programs')
                        </a>
                        <a href="{{ route('user.product.upload') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-300 transition-colors text-sm {{ menuActive('user.product.upload') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : '' }}">
                            <i class="las la-upload text-sm mr-2"></i>
                            @lang('Submit Rebate')
                        </a>
                        <a href="{{ route('user.rebate.history') }}"
                            class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-300 transition-colors text-sm {{ menuActive('user.rebate.history') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : '' }}">
                            <i class="las la-history text-sm mr-2"></i>
                            @lang('History')
                        </a>
                    </div>
                </div>
            @endif

            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

            <!-- Support -->
            <a href="{{ route('ticket.index') }}"
                class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-300 transition-all duration-200 {{ menuActive('ticket*') ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 font-medium shadow-sm' : '' }} group">
                <i
                    class="las la-headset text-xl mr-3 {{ menuActive('ticket*') ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-green-600 dark:group-hover:text-green-400' }}"></i>
                <span>@lang('Support Tickets')</span>
            </a>

            <!-- Profile Settings -->
            <a href="{{ route('user.profile.setting') }}"
                class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 transition-all duration-200 {{ menuActive(['user.profile.setting', 'user.change.password', 'user.transfer.pin', 'user.twofactor']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-medium shadow-sm' : '' }} group">
                <i
                    class="las la-user-cog text-xl mr-3 {{ menuActive(['user.profile.setting', 'user.change.password', 'user.transfer.pin', 'user.twofactor']) ? 'text-gray-600 dark:text-gray-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400' }}"></i>
                <span>@lang('Profile Settings')</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

            <!-- Logout -->
            <a href="{{ route('user.logout') }}"
                class="flex items-center px-4 py-3 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300 transition-all duration-200 group">
                <i class="las la-sign-out-alt text-xl mr-3 text-red-500 dark:text-red-400 group-hover:text-red-600 dark:group-hover:text-red-300"></i>
                <span class="font-medium">@lang('Logout')</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    © {{ date('Y') }} {{ gs()->site_name ?? 'Site Name' }}
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">@lang('Online')</span>
                </div>
            </div>
        </div>
    </div>
</aside>
