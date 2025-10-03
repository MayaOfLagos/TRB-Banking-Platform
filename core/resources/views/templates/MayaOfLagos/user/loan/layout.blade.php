@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
    <div class="mx-auto px-0 sm:px-0 lg:px-0">
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

        @yield('loan-content')
    </div>
</div>
@endsection

@push('bottom-menu')
    <li><a href="{{ route('user.loan.plans') }}">@lang('Loan Plans')</a></li>
    <li><a href="{{ route('user.loan.list') }}" class="active">@lang('My Loan List')</a></li>
@endpush