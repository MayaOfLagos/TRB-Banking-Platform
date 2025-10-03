@extends($activeTemplate . 'user.dps.layout')
@section('dps-content')

<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 text-white text-center">
            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="las la-piggy-bank text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold mb-2">@lang('DPS Application Preview')</h1>
            <p class="text-blue-100">@lang('Review your DPS plan details before confirmation')</p>
        </div>
        
        <!-- Important Notice -->
        <div class="p-6 bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mr-3">
                    <i class="las la-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-yellow-800 dark:text-yellow-300">@lang('Important Notice')</h3>
                    <p class="text-yellow-700 dark:text-yellow-400 text-sm">@lang('Please review all details carefully before confirming your DPS application')</p>
                </div>
            </div>
        </div>

        <!-- Plan Details -->
        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="las la-file-alt mr-2 text-blue-600"></i>
                @lang('Plan Information')
            </h2>
            
            <div class="space-y-4">
                
                <!-- Plan Name -->
                <div class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">@lang('Plan')</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ __($plan->name) }}</span>
                </div>

                <!-- Installment Interval -->
                <div class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">@lang('Installment Interval')</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $plan->installment_interval }} {{ __(Str::plural('Day', $plan->installment_interval)) }}</span>
                </div>

                <!-- Total Installment -->
                <div class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">@lang('Total Installment')</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $plan->total_installment }}</span>
                </div>

                <!-- Per Installment -->
                <div class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">@lang('Per Installment')</span>
                    <span class="font-bold text-blue-600 dark:text-blue-400 text-lg">{{ showAmount($plan->per_installment) }} {{ gs()->cur_text }}</span>
                </div>

                <!-- Total Deposit -->
                <div class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">@lang('Total Deposit')</span>
                    <span class="font-bold text-green-600 dark:text-green-400 text-lg">{{ showAmount($plan->per_installment * $plan->total_installment) }} {{ gs()->cur_text }}</span>
                </div>

                <!-- Profit Rate -->
                <div class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-700">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">@lang('Profit Rate')</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ getAmount($plan->interest_rate) }}%</span>
                </div>

                <!-- Withdrawable Amount -->
                <div class="flex justify-between items-center py-4">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">@lang('Withdrawable Amount')</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400 text-xl">{{ showAmount($plan->final_amount) }} {{ gs()->cur_text }}</span>
                </div>
            </div>

            <!-- Profit Calculation Breakdown -->
            <div class="mt-8 p-6 bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 rounded-xl border border-green-200 dark:border-green-800">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="las la-calculator mr-2 text-green-600"></i>
                    @lang('Profit Calculation')
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($plan->per_installment * $plan->total_installment) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">@lang('You Deposit')</div>
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="las la-plus text-2xl text-gray-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ showAmount($plan->final_amount - ($plan->per_installment * $plan->total_installment)) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Profit Earned')</div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-700 text-center">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ showAmount($plan->final_amount) }} {{ gs()->cur_text }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Total Amount You Get')</div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            @if ($plan->delay_value && $plan->delay_charge)
            <div class="mt-8 p-6 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                <h3 class="font-semibold text-red-800 dark:text-red-300 mb-3 flex items-center">
                    <i class="las la-exclamation-triangle mr-2"></i>
                    @lang('Important Terms & Conditions')
                </h3>
                <div class="space-y-2 text-sm text-red-700 dark:text-red-400">
                    <p>
                        <strong>@lang('Delay Charges:')</strong> @lang('If an installment is delayed for')
                        <span class="font-bold">{{ $plan->delay_value }}</span> @lang('or more days then, an amount of')
                        <span class="font-bold">{{ $plan->delayCharge }}</span> @lang('will be applied for each day.')
                    </p>
                    <p>
                        <strong>@lang('Note:')</strong> @lang('The total charge amount will be subtracted from the withdrawable amount.')
                    </p>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('user.home') }}" 
                   class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 font-semibold rounded-lg transition-colors duration-200">
                    <i class="las la-times mr-2"></i>
                    @lang('Cancel')
                </a>
                
                <form action="{{ route('user.dps.apply.confirm', $verificationId) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <i class="las la-check-circle mr-2"></i>
                        @lang('Confirm Application')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection