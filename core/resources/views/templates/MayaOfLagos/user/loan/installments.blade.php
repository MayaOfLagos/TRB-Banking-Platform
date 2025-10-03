@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8">
        <!-- Navigation Pills -->
        <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
            <div class="flex gap-3">
                <a href="{{ route('user.loan.list') }}" 
                   class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ menuActive('user.loan.list') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                    <i class="las la-list mr-2"></i>@lang('My Loan List')
                </a>
                <a href="{{ route('user.loan.plans') }}" 
                   class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ menuActive('user.loan.plans') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                    <i class="las la-clipboard-list mr-2"></i>@lang('Loan Plans')
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Loan Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-8">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="las la-file-invoice-dollar text-3xl text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-1">@lang('Loan Details')</h3>
                            <p class="text-blue-100 text-sm">{{ $loan->loan_number }}</p>
                        </div>
                    </div>

                    <!-- Loan Information -->
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Plan')</span>
                                <span class="text-gray-900 dark:text-white font-semibold text-sm">{{ $loan->plan->name }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Loan Amount')</span>
                                <span class="text-gray-900 dark:text-white font-semibold text-sm">{{ showUserAmount($loan->amount, auth()->user()) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Per Installment')</span>
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">{{ showUserAmount($loan->per_installment, auth()->user()) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Total Installments')</span>
                                <span class="text-gray-900 dark:text-white font-semibold text-sm">{{ $loan->total_installment }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Paid Installments')</span>
                                <span class="text-green-600 dark:text-green-400 font-semibold text-sm">{{ $loan->given_installment }}</span>
                            </div>

                            <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg border border-amber-200 dark:border-amber-800">
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-800 dark:text-amber-300 font-medium text-sm">@lang('Remaining to Pay')</span>
                                    <span class="text-amber-800 dark:text-amber-300 font-bold">{{ showUserAmount($loan->payable_amount, auth()->user()) }}</span>
                                </div>
                            </div>

                            @if(getAmount($loan->charge_per_installment))
                            <div class="bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-200 dark:border-red-800">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-red-800 dark:text-red-300 font-medium text-sm">@lang('Delay Charge')</span>
                                    <span class="text-red-800 dark:text-red-300 font-bold text-sm">
                                        {{ showUserAmount($loan->charge_per_installment, auth()->user()) }} / 
                                        @if($loan->delay_value == 1)
                                            @lang('Day')
                                        @else
                                            {{ $loan->delay_value }} @lang('Days')
                                        @endif
                                    </span>
                                </div>
                                <p class="text-xs text-red-700 dark:text-red-400">
                                    @lang('Charge will be applied if an installment is delayed for') {{ $loan->delay_value }} @lang('or more days')
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Installments Table -->
            <div class="lg:col-span-3">
                @include($activeTemplate . 'partials.installment_table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('bottom-menu')
    <li><a href="{{ route('user.loan.plans') }}">@lang('Loan Plans')</a></li>
    <li><a href="{{ route('user.loan.list') }}" class="active">@lang('My Loan List')</a></li>
@endpush