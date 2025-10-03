@extends($activeTemplate . 'layouts.master')
@section('content')
<div x-data="dashboardData()" class="space-y-6 pb-20 lg:pb-6">
    <!-- KYC Status Alerts -->
    @if ($user->kv != Status::KYC_VERIFIED)
        @php $kyc = getContent('kyc.content', true); @endphp
        
        @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-6" role="alert">
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-red-800 dark:text-red-200 font-semibold text-lg">@lang('KYC Documents Rejected')</h4>
                            <p class="text-red-700 dark:text-red-300 text-sm mt-1">{{ __(@$kyc->data_values->reject) }}</p>
                        </div>
                    </div>
                    <button @click="showModal = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                        @lang('Show Reason')
                    </button>
                </div>
                <div class="mt-4 flex space-x-4">
                    <a href="{{ route('user.kyc.form') }}" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium text-sm underline">
                        @lang('Re-submit Documents')
                    </a>
                    <a href="{{ route('user.kyc.data') }}" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium text-sm underline">
                        @lang('See KYC Data')
                    </a>
                </div>
            </div>
        @elseif(auth()->user()->kv == Status::KYC_UNVERIFIED)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6" role="alert">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="las la-id-card text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-blue-800 dark:text-blue-200 font-semibold text-lg">@lang('KYC Verification Required')</h4>
                        <p class="text-blue-700 dark:text-blue-300 text-sm mt-1">{{ __(@$kyc->data_values->required) }}</p>
                        <a href="{{ route('user.kyc.form') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium text-sm underline mt-2 inline-block">
                            @lang('Submit Documents Now')
                        </a>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->kv == Status::KYC_PENDING)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-6" role="alert">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                        <i class="las la-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-yellow-800 dark:text-yellow-200 font-semibold text-lg">@lang('KYC Verification Pending')</h4>
                        <p class="text-yellow-700 dark:text-yellow-300 text-sm mt-1">{{ __(@$kyc->data_values->pending) }}</p>
                        <a href="{{ route('user.kyc.data') }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200 font-medium text-sm underline mt-2 inline-block">
                            @lang('View KYC Data')
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Account Balance Header -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 rounded-3xl p-6 lg:p-8 text-white shadow-xl" x-data="{ balanceVisible: true, accountVisible: false }">
        <!-- Welcome Message -->
        <div class="hidden md:flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-2xl lg:text-3xl font-bold mb-1">
                    {{ __('Good') }} 
                    <span x-text="getGreeting()"></span>, 
                    {{ $user->firstname }}!
                </h1>
                <p class="text-primary-100">@lang('Your account overview')</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-primary-100 text-sm">@lang('Live')</span>
                </div>
                <div class="text-right">
                    <p class="text-primary-100 text-xs">@lang('Last Updated')</p>
                    <p class="text-white text-sm font-medium" x-text="new Date().toLocaleTimeString()"></p>
                </div>
            </div>
        </div>

        <!-- Account Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Balance Card -->
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="las la-wallet text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">@lang('Available Balance')</h3>
                            <p class="text-primary-100 text-sm">@lang('Main Account')</p>
                        </div>
                    </div>
                    <button @click="balanceVisible = !balanceVisible" class="text-white/70 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
                        <i class="las text-lg" :class="balanceVisible ? 'la-eye-slash' : 'la-eye'"></i>
                    </button>
                </div>
                <div>
                    <p class="text-3xl lg:text-4xl font-bold text-white" x-show="balanceVisible" x-transition>
                        {{ showUserAmount($user->balance, $user) }}
                    </p>
                    <p class="text-3xl lg:text-4xl font-bold text-white" x-show="!balanceVisible" x-transition>
                        ••••••••
                    </p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-green-300 text-sm font-medium flex items-center">
                            <i class="las la-arrow-up text-xs mr-1"></i>
                            @lang('Active')
                        </span>
                        <div class="flex items-center text-white/70 text-sm">
                            <i class="las la-coins mr-1"></i>
                            <span>{{ strtoupper($user->preferred_currency ?? gs('cur_text')) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Details Card -->
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="las la-user-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">@lang('Account Details')</h3>
                            <p class="text-primary-100 text-sm">@lang('Account Information')</p>
                        </div>
                    </div>
                    <button @click="accountVisible = !accountVisible" class="text-white/70 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-lg">
                        <i class="las text-lg" :class="accountVisible ? 'la-eye-slash' : 'la-eye'"></i>
                    </button>
                </div>
                <div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-primary-100 text-xs uppercase tracking-wide">@lang('Account Number')</p>
                            <p class="text-white font-mono text-lg" x-show="accountVisible" x-transition>
                                {{ $user->account_number ?? 'Not Available' }}
                            </p>
                            <p class="text-white font-mono text-lg" x-show="!accountVisible" x-transition>
                                ••••••••••••
                            </p>
                        </div>
                        <div>
                            <p class="text-primary-100 text-xs uppercase tracking-wide">@lang('Account Type')</p>
                            <p class="text-white font-medium">@lang('Primary Savings')</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Summary Card -->
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="las la-chart-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold">@lang('This Month')</h3>
                            <p class="text-primary-100 text-sm">@lang('Transaction Summary')</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-green-400/20 rounded-lg flex items-center justify-center">
                                <i class="las la-arrow-down text-green-300 text-sm"></i>
                            </div>
                            <span class="text-primary-100 text-sm">@lang('Money In')</span>
                        </div>
                        <span class="text-white font-semibold" x-show="balanceVisible" x-transition>
                            {{ showUserAmount($user->deposits()->where('created_at', '>=', now()->startOfMonth())->where('status', 1)->sum('amount'), $user) }}
                        </span>
                        <span class="text-white font-semibold" x-show="!balanceVisible" x-transition>••••</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-red-400/20 rounded-lg flex items-center justify-center">
                                <i class="las la-arrow-up text-red-300 text-sm"></i>
                            </div>
                            <span class="text-primary-100 text-sm">@lang('Money Out')</span>
                        </div>
                        <span class="text-white font-semibold" x-show="balanceVisible" x-transition>
                            {{ showUserAmount($user->withdrawals()->where('created_at', '>=', now()->startOfMonth())->where('status', 1)->sum('amount'), $user) }}
                        </span>
                        <span class="text-white font-semibold" x-show="!balanceVisible" x-transition>••••</span>
                    </div>
                    <div class="pt-2 border-t border-white/20">
                        <div class="flex items-center justify-between">
                            <span class="text-primary-100 text-sm">@lang('Net Flow')</span>
                            @php
                                $moneyIn = $user->deposits()->where('created_at', '>=', now()->startOfMonth())->where('status', 1)->sum('amount') ?: 0;
                                $moneyOut = $user->withdrawals()->where('created_at', '>=', now()->startOfMonth())->where('status', 1)->sum('amount') ?: 0;
                                $netFlow = $moneyIn - $moneyOut;
                            @endphp
                            <span class="text-white font-bold text-sm {{ $netFlow >= 0 ? 'text-green-300' : 'text-red-300' }}" x-show="balanceVisible" x-transition>
                                {{ $netFlow >= 0 ? '+' : '' }}{{ showUserAmount(abs($netFlow), $user) }}
                            </span>
                            <span class="text-white font-bold text-sm" x-show="!balanceVisible" x-transition>••••</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Banking Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Referral System Module -->
        @if (gs()->modules->referral_system)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300 md:col-span-2 lg:col-span-3 xl:col-span-4">
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-xl border border-purple-200 dark:border-purple-700">
                    <div class="flex-1">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                            <code class="text-sm text-gray-600 dark:text-gray-300 break-all" id="referralLink">{{ route('home') . '?reference=' . $user->username }}</code>
                        </div>
                    </div>
                    <button onclick="copyReferralLink()" class="ml-4 w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400 hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
                        <i class="las la-copy text-xl"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- FDR Module -->
        @if (gs()->modules->fdr)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.fdr.list') }}?status={{ Status::FDR_RUNNING }}" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-money-bill text-orange-600 dark:text-orange-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Running FDR')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ @$widget['total_fdr'] ?? 0 }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-orange-600 text-sm font-medium">@lang('Fixed Deposits')</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <!-- DPS Module -->
        @if (gs()->modules->dps)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.dps.list') }}?status={{ Status::FDR_RUNNING }}" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-box-open text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Running DPS')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ @$widget['total_dps'] ?? 0 }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-blue-600 text-sm font-medium">@lang('Deposit Schemes')</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <!-- Loan Module -->
        @if (gs()->modules->loan)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.loan.list') }}?status={{ Status::LOAN_RUNNING }}" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-hand-holding-usd text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Running Loan')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ @$widget['total_loan'] ?? 0 }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-indigo-600 text-sm font-medium">@lang('Active Loans')</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <!-- Rebate System Module -->
        @if (gs()->rebate_system_enabled)
            <!-- Total Rebate Earned -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.rebate.dashboard') }}" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-gift text-purple-600 dark:text-purple-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Total Rebate Earned')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ showUserAmount(@$widget['total_rebate_earned'] ?? 0, $user) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <span class="text-purple-600 text-sm font-medium">@lang('All Time')</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Pending Rebate -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.rebate.history') }}?status=pending" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-clock text-amber-600 dark:text-amber-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Pending Rebate')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ showUserAmount(@$widget['pending_rebate'] ?? 0, auth()->user()) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <span class="text-amber-600 text-sm font-medium">@lang('Processing')</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Current Rebate Tier -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.rebate.programs') }}" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-star text-pink-600 dark:text-pink-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Current Tier')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ @$widget['current_rebate_tier'] ?? 'Bronze' }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-pink-600 text-sm font-medium">{{ @$widget['rebate_tier_rate'] ?? '1.0' }}% @lang('rate')</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Rebate Success Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.rebate.history') }}" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-chart-line text-teal-600 dark:text-teal-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Success Rate')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ @$widget['rebate_success_rate'] ?? '95' }}%</p>
                        <div class="flex items-center mt-2">
                            <span class="text-teal-600 text-sm font-medium">@lang('Performance')</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <!-- Today's Transactions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
            <a href="{{ route('user.transaction.history') }}?today=1" class="block">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center">
                        <i class="las la-exchange-alt text-cyan-600 dark:text-cyan-400 text-2xl"></i>
                    </div>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Today Transactions')</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ @$widget['total_trx'] ?? 0 }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-cyan-600 text-sm font-medium">@lang('Today\'s Activity')</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Pending Deposits -->
        @if (@gs()->modules->deposit)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.deposit.history') }}?status={{ Status::PAYMENT_PENDING }}" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-wallet text-emerald-600 dark:text-emerald-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Pending Deposits')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ showUserAmount(@$widget['total_deposit'] ?? 0, $user) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <span class="text-emerald-600 text-sm font-medium">@lang('Awaiting Approval')</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <!-- Pending Withdrawals -->
        @if (@gs()->modules->withdraw)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                <a href="{{ route('user.withdraw.history') }}?status={{ Status::PAYMENT_PENDING }}" class="block">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-money-check text-rose-600 dark:text-rose-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Pending Withdrawals')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ showUserAmount(@$widget['total_withdraw'] ?? 0, $user) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <span class="text-rose-600 text-sm font-medium">@lang('Processing')</span>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>

    <!-- Account Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Main Balance -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-wallet text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <button @click="balanceVisible = !balanceVisible" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="las" :class="balanceVisible ? 'la-eye-slash' : 'la-eye'"></i>
                </button>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Available Balance')</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1" x-show="balanceVisible">
                    {{ showUserAmount($user->balance, $user) }}
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1" x-show="!balanceVisible">••••••</p>
                <div class="flex items-center mt-2">
                    <span class="text-green-600 text-sm font-medium">+2.5%</span>
                    <span class="text-gray-500 dark:text-gray-400 text-sm ml-2">@lang('this month')</span>
                </div>
            </div>
        </div>

        <!-- Total Deposits -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-arrow-down text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Total Deposits')</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ showUserAmount($user->deposits()->where('status', 1)->sum('amount'), $user) }}
                </p>
                <div class="flex items-center mt-2">
                    <span class="text-green-600 text-sm font-medium">{{ $user->deposits()->where('status', 1)->count() }} @lang('completed')</span>
                </div>
            </div>
        </div>

        <!-- Total Withdrawals -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-arrow-up text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Total Withdrawals')</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ showUserAmount($user->withdrawals()->where('status', 1)->sum('amount'), $user) }}
                </p>
                <div class="flex items-center mt-2">
                    <span class="text-red-600 text-sm font-medium">{{ $user->withdrawals()->where('status', 1)->count() }} @lang('completed')</span>
                </div>
            </div>
        </div>

        <!-- Pending Withdrawals -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-clock text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                </div>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Pending Withdrawals')</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ showUserAmount($user->withdrawals()->pending()->sum('amount'), $user) }}
                </p>
                <div class="flex items-center mt-2">
                    <span class="text-yellow-600 text-sm font-medium">{{ $user->withdrawals()->pending()->count() }} @lang('pending')</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">@lang('Quick Actions')</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('user.deposit.index') }}" class="group flex flex-col items-center p-6 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-700 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-all duration-300">
                <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50 transition-colors mb-4">
                    <i class="las la-plus text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <span class="text-gray-700 dark:text-gray-300 font-medium group-hover:text-primary-700 dark:group-hover:text-primary-300 transition-colors">@lang('Deposit')</span>
            </a>

            <a href="{{ route('user.withdraw') }}" class="group flex flex-col items-center p-6 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-red-200 dark:hover:border-red-700 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-300">
                <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center group-hover:bg-red-200 dark:group-hover:bg-red-900/50 transition-colors mb-4">
                    <i class="las la-minus text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <span class="text-gray-700 dark:text-gray-300 font-medium group-hover:text-red-700 dark:group-hover:text-red-300 transition-colors">@lang('Withdraw')</span>
            </a>

            <a href="{{ route('user.transfer.history') }}" class="group flex flex-col items-center p-6 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all duration-300">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors mb-4">
                    <i class="las la-exchange-alt text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <span class="text-gray-700 dark:text-gray-300 font-medium group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">@lang('Transfer')</span>
            </a>

            <a href="{{ route('ticket.index') }}" class="group flex flex-col items-center p-6 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-green-200 dark:hover:border-green-700 hover:bg-green-50 dark:hover:bg-green-900/10 transition-all duration-300">
                <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors mb-4">
                    <i class="las la-headset text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
                <span class="text-gray-700 dark:text-gray-300 font-medium group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors">@lang('Support')</span>
            </a>
        </div>
    </div>

    <!-- Recent Transactions & Account Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Recent Transactions')</h2>
                <a href="{{ route('user.transaction.history') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium">
                    @lang('View All')
                </a>
            </div>
            <div class="space-y-4">
                @forelse(collect($credits)->merge($debits)->sortByDesc('created_at')->take(5) as $trx)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $trx->trx_type == '+' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                <i class="las {{ $trx->trx_type == '+' ? 'la-arrow-down text-green-600 dark:text-green-400' : 'la-arrow-up text-red-600 dark:text-red-400' }}"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ __($trx->details) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ showDateTime($trx->created_at, 'M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold {{ $trx->trx_type == '+' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $trx->trx_type }}{{ showUserAmount($trx->amount, $user) }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $trx->trx }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="las la-receipt text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">@lang('No transactions yet')</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Account Security -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">@lang('Account Security')</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-shield-alt text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">@lang('2FA Authentication')</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($user->ts)
                                    @lang('Enabled')
                                @else
                                    @lang('Disabled')
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-5 h-5 rounded-full {{ $user->ts ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-envelope text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">@lang('Email Verification')</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($user->ev)
                                    @lang('Verified')
                                @else
                                    @lang('Unverified')
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-5 h-5 rounded-full {{ $user->ev ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-mobile text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">@lang('SMS Verification')</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($user->sv)
                                    @lang('Verified')
                                @else
                                    @lang('Unverified')
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-5 h-5 rounded-full {{ $user->sv ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-user-check text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">@lang('KYC Verification')</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($user->kv == Status::KYC_VERIFIED)
                                    @lang('Verified')
                                @elseif($user->kv == Status::KYC_PENDING)
                                    @lang('Pending Review')
                                @else
                                    @lang('Unverified')
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if($user->kv == Status::KYC_VERIFIED)
                            <div class="w-5 h-5 rounded-full bg-green-500"></div>
                        @elseif($user->kv == Status::KYC_PENDING)
                            <div class="w-5 h-5 rounded-full bg-yellow-500"></div>
                        @else
                            <div class="w-5 h-5 rounded-full bg-red-500"></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('user.profile.setting') }}" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 px-4 rounded-xl font-medium transition-colors text-center block">
                    @lang('Manage Security Settings')
                </a>
            </div>
        </div>
    </div>

    <!-- Latest Credits & Debits Tables -->
    <div class="hidden lg:grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Latest Credits -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Latest Credits')</h2>
                <a href="{{ route('user.transaction.history') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium">
                    @lang('View All')
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-sm">@lang('TRX No.')</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-sm">@lang('Date')</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-sm">@lang('Amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($credits as $credit)
                            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white font-medium">{{ $credit->trx }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ showDateTime($credit->created_at, 'd M, Y h:i A') }}
                                </td>
                                <td class="py-3 px-4 text-sm font-bold text-green-600 dark:text-green-400 text-right">
                                    {{ showAmount((float)$credit->amount) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Latest Debits -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Latest Debits')</h2>
                <a href="{{ route('user.transaction.history') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium">
                    @lang('View All')
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-sm">@lang('TRX No.')</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-sm">@lang('Date')</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-500 dark:text-gray-400 text-sm">@lang('Amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debits as $debit)
                            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white font-medium">{{ $debit->trx }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-300">{{ showDateTime($debit->created_at, 'd M, Y h:i A') }}</td>
                                <td class="py-3 px-4 text-sm font-bold text-red-600 dark:text-red-400 text-right">
                                    {{ showAmount((float)$debit->amount) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-500 dark:text-gray-400">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardData() {
    return {
        balanceVisible: false,
        showModal: false,
        
        getGreeting() {
            const hour = new Date().getHours();
            if (hour < 12) return 'morning';
            if (hour < 17) return 'afternoon';
            return 'evening';
        }
    }
}

// Copy referral link function
function copyReferralLink() {
    const referralLink = document.getElementById('referralLink').textContent;
    navigator.clipboard.writeText(referralLink).then(function() {
        // Show success notification using system notify
        notify('success', '@lang("Referral link copied to clipboard!")');
    }).catch(function(err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = referralLink;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        notify('success', '@lang("Referral link copied to clipboard!")');
    });
}


</script>

@endsection

@if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
    <!-- KYC Rejection Reason Modal (from crystal_sky) -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="showModal = false" type="button" class="bg-white dark:bg-gray-800 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <span class="sr-only">Close</span>
                        <i class="las la-times text-xl"></i>
                    </button>
                </div>
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="las la-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            @lang('KYC Document Rejection Reason')
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ auth()->user()->kyc_rejection_reason }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button @click="showModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        @lang('Close')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif