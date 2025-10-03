@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- Loan Plans Page -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">@lang('Loan Plans')</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">@lang('Choose the perfect loan plan that fits your financial needs')</p>
                </div>
                
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('user.loan.list') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="las la-list mr-2 text-lg"></i>
                        @lang('My Loans')
                    </a>
                </div>
            </div>
        </div>

        <!-- Loan Plans Grid -->
        @include($activeTemplate . 'partials.loan_plans')
    </div>
</div>

@endsection

@push('bottom-menu')
    <li><a href="{{ route('user.loan.plans') }}" class="active">@lang('Loan Plans')</a></li>
    <li><a href="{{ route('user.loan.list') }}">@lang('My Loan List')</a></li>
@endpush