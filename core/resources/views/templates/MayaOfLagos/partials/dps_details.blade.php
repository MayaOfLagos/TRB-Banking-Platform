<!-- DPS Details Information -->
<div class="space-y-4">
    
    <!-- DPS Number -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('DPS No.')</span>
        <span class="font-bold text-gray-900 dark:text-white">#{{ $dps->dps_number }}</span>
    </div>

    <!-- Plan Name -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Plan')</span>
        <span class="font-semibold text-gray-900 dark:text-white">{{ __($dps->plan->name) }}</span>
    </div>

    <!-- Interest Rate -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Interest Rate')</span>
        <span class="font-semibold text-green-600 dark:text-green-400">{{ getAmount($dps->plan->interest_rate) }}%</span>
    </div>

    <!-- Opened On -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Opened On')</span>
        <span class="font-semibold text-gray-900 dark:text-white">{{ showDateTime($dps->created_at, 'd M, Y') }}</span>
    </div>

    <!-- Installment Interval -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Installment Interval')</span>
        <span class="font-semibold text-gray-900 dark:text-white">{{ $dps->installment_interval }} {{ __(Str::plural('Day', $dps->installment_interval)) }}</span>
    </div>

    <!-- Per Installment -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Per Installment')</span>
        <span class="font-bold text-blue-600 dark:text-blue-400">{{ showAmount($dps->per_installment) }} {{ gs()->cur_text }}</span>
    </div>

    <!-- Total Installment -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Total Installment')</span>
        <span class="font-bold text-gray-900 dark:text-white">{{ $dps->total_installment }}</span>
    </div>

    <!-- Given Installment -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Given Installment')</span>
        <span class="font-bold text-purple-600 dark:text-purple-400">{{ $dps->given_installment }}</span>
    </div>

    <!-- Deposited Amount -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Deposited Amount')</span>
        <span class="font-bold text-gray-900 dark:text-white">{{ showAmount($dps->per_installment * $dps->given_installment) }} {{ gs()->cur_text }}</span>
    </div>

    @if ($dps->nextInstallment)
    <!-- Next Installment Date -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Next Installment Date')</span>
        <span class="font-bold text-orange-600 dark:text-orange-400">{{ showDateTime($dps->nextInstallment->installment_date, 'd M, Y') }}</span>
    </div>
    @endif

    <!-- DPS Amount -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('DPS Amount')</span>
        <span class="font-bold text-green-600 dark:text-green-400">{{ showAmount($dps->per_installment * $dps->given_installment) }} {{ gs()->cur_text }}</span>
    </div>

    <!-- Profit Amount -->
    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
        <span class="text-gray-600 dark:text-gray-400">@lang('Profit Amount')</span>
        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ showAmount($dps->plan->final_amount - ($dps->per_installment * $dps->given_installment)) }} {{ gs()->cur_text }}</span>
    </div>

    <!-- Maturity Amount -->
    <div class="flex justify-between items-center py-3">
        <span class="text-gray-600 dark:text-gray-400">@lang('Maturity Amount')</span>
        <span class="font-bold text-lg text-purple-600 dark:text-purple-400">{{ showAmount($dps->plan->final_amount) }} {{ gs()->cur_text }}</span>
    </div>

</div>