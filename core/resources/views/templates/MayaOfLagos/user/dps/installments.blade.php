@extends($activeTemplate . 'user.dps.layout')
@section('dps-content')

<div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
    
    <!-- DPS Summary Card -->
    <div class="xl:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="las la-piggy-bank mr-2 text-blue-600"></i>
                @lang('DPS Summary')
            </h3>
            
            <div class="space-y-4">
                
                <!-- DPS Number -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('DPS Number')</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $dps->dps_number }}</span>
                </div>

                <!-- Plan -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Plan')</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $dps->plan->name }}</span>
                </div>

                <!-- Interest Rate -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Interest Rate')</span>
                    <span class="font-bold text-green-600 dark:text-green-400">{{ getAmount($dps->interest_rate) }}%</span>
                </div>

                <!-- Per Installment -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Per Installment')</span>
                    <span class="font-bold text-blue-600 dark:text-blue-400">{{ showAmount($dps->per_installment) }} {{ gs()->cur_text }}</span>
                </div>

                <!-- Given Installment -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Given Installment')</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400">{{ $dps->given_installment }}</span>
                </div>

                <!-- Total Installment -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Total Installment')</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $dps->total_installment }}</span>
                </div>

                <!-- Total Deposit -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Total Deposit')</span>
                    <span class="font-bold text-green-600 dark:text-green-400">{{ showAmount($dps->per_installment * $dps->given_installment) }} {{ gs()->cur_text }}</span>
                </div>

                <!-- Profit -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Profit')</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ showAmount($dps->profit) }} {{ gs()->cur_text }}</span>
                </div>

                <!-- Including Profit -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Including Profit')</span>
                    <span class="font-bold text-lg text-purple-600 dark:text-purple-400">{{ showAmount(($dps->per_installment * $dps->given_installment) + $dps->profit) }} {{ gs()->cur_text }}</span>
                </div>

                @if (getAmount($dps->charge_per_installment))
                <!-- Delay Charge Information -->
                <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                    <div class="flex items-center mb-2">
                        <i class="las la-exclamation-triangle text-red-600 dark:text-red-400 mr-2"></i>
                        <span class="font-semibold text-red-800 dark:text-red-300">@lang('Delay Charge')</span>
                    </div>
                    <div class="text-sm text-red-700 dark:text-red-300">
                        <div class="font-semibold">{{ showAmount($dps->charge_per_installment) }} {{ gs()->cur_text }}/@lang('Day')</div>
                        <div class="mt-1">@lang('Charge will be applied if an installment delayed for') {{ $dps->delay_value }} @lang('or more days')</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Installment Schedule -->
    <div class="xl:col-span-3">
        @include($activeTemplate . 'partials.installment_table')
    </div>
</div>

@endsection