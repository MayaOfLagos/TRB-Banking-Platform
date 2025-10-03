@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- DPS Navigation Header -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang('Deposit Pension Scheme')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Manage your DPS investments and track your savings progress')</p>
        </div>
        
        <!-- Navigation Buttons -->
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('user.dps.list') }}" 
               class="inline-flex items-center px-4 py-2 {{ menuActive('user.dps.list') ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }} font-semibold rounded-lg transition-colors duration-200">
                <i class="las la-list mr-2"></i>
                @lang('My DPS List')
            </a>
            
            <a href="{{ route('user.dps.plans') }}" 
               class="inline-flex items-center px-4 py-2 {{ menuActive('user.dps.plans') ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }} font-semibold rounded-lg transition-colors duration-200">
                <i class="las la-chart-pie mr-2"></i>
                @lang('DPS Plans')
            </a>
        </div>
    </div>
</div>

@yield('dps-content')

@endsection