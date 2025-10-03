@extends($activeTemplate . 'user.dps.layout')
@section('dps-content')

<!-- DPS Plans Introduction -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 mb-8 text-white">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="lg:w-2/3">
            <h2 class="text-3xl font-bold mb-4">@lang('Deposit Pension Scheme Plans')</h2>
            <p class="text-blue-100 text-lg leading-relaxed">
                @lang('Choose from our flexible DPS plans designed to help you save systematically and earn competitive returns. Build your financial future with regular deposits and guaranteed profits.')
            </p>
            
            <!-- Key Benefits -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                        <i class="las la-shield-alt text-white"></i>
                    </div>
                    <span class="text-sm">@lang('Secure Investment')</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                        <i class="las la-chart-line text-white"></i>
                    </div>
                    <span class="text-sm">@lang('Guaranteed Returns')</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                        <i class="las la-calendar-check text-white"></i>
                    </div>
                    <span class="text-sm">@lang('Flexible Terms')</span>
                </div>
            </div>
        </div>
        
        <div class="lg:w-1/3 mt-6 lg:mt-0 text-center">
            <div class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 rounded-xl">
                <i class="las la-piggy-bank text-2xl mr-3"></i>
                <div class="text-left">
                    <div class="text-sm opacity-90">@lang('Start Your Journey')</div>
                    <div class="font-semibold">@lang('Choose a Plan Below')</div>
                </div>
            </div>
        </div>
    </div>
</div>

@include($activeTemplate . 'partials.dps_plans')

<!-- How It Works Section -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 mt-8">
    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">@lang('How DPS Works')</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="text-center">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="las la-user-plus text-2xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('1. Choose Plan')</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Select a DPS plan that fits your savings goal and budget')</p>
        </div>
        
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="las la-calendar-alt text-2xl text-green-600 dark:text-green-400"></i>
            </div>
            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('2. Regular Deposits')</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Make regular installment payments according to your chosen schedule')</p>
        </div>
        
        <div class="text-center">
            <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="las la-chart-line text-2xl text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('3. Earn Interest')</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Your deposits earn competitive interest rates over the term period')</p>
        </div>
        
        <div class="text-center">
            <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="las la-trophy text-2xl text-purple-600 dark:text-purple-400"></i>
            </div>
            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('4. Withdraw')</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Withdraw your matured amount with earned profits')</p>
        </div>
    </div>
</div>

@endsection