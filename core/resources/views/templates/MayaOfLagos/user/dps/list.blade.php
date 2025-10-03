@extends($activeTemplate . 'user.dps.layout')
@section('dps-content')

<!-- DPS List Section -->
<div class="space-y-6">
    
    <!-- Search and Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Custom Tailwind Search Form -->
            <div class="flex-1">
                <form class="flex flex-col sm:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="flex-1 relative">
                        <input type="search" 
                               name="search" 
                               value="{{ request()->search }}"
                               placeholder="{{ __('DPS No.') }}"
                               class="w-full px-4 py-3 pl-11 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="las la-search text-gray-400 dark:text-gray-500"></i>
                        </div>
                    </div>
                    
                    <!-- Date Range Picker -->
                    <div class="relative">
                        <input type="search" 
                               name="date" 
                               value="{{ request()->date }}"
                               placeholder="{{ __('Start Date - End Date') }}"
                               autocomplete="off"
                               class="date-range w-full sm:w-64 px-4 py-3 pl-11 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="las la-calendar text-gray-400 dark:text-gray-500"></i>
                        </div>
                    </div>
                    
                    <!-- Search Button -->
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <i class="las la-search mr-2"></i>
                        @lang('Search')
                    </button>
                    
                    <!-- Clear Button -->
                    @if(request()->search || request()->date)
                    <a href="{{ route('user.dps.list') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <i class="las la-times mr-2"></i>
                        @lang('Clear')
                    </a>
                    @endif
                </form>
            </div>
            
            <!-- Export Button -->
            @if (request()->date || request()->search)
            <div class="flex items-center">
                <a href="{{ request()->fullUrlWithQuery(['download' => 'pdf']) }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    <i class="las la-download mr-2"></i>
                    @lang('Download PDF')
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- DPS Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total DPS -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="las la-piggy-bank text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $allDps->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Total DPS')</div>
                </div>
            </div>
        </div>
        
        <!-- Running DPS -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="las la-play-circle text-2xl text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $allDps->where('status', 1)->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Running')</div>
                </div>
            </div>
        </div>
        
        <!-- Total Deposited -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <i class="las la-coins text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($allDps->sum('per_installment')) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Total Deposited')</div>
                </div>
            </div>
        </div>
        
        <!-- Total Maturity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="las la-chart-line text-2xl text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($allDps->sum('final_amount')) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Total Maturity')</div>
                </div>
            </div>
        </div>
    </div>

    <!-- DPS Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('DPS Details')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Installments')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Amount & Maturity')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Next Payment')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Status')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody id="dpsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($allDps as $dps)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 dps-row" 
                        data-status="{{ $dps->status }}"
                        data-plan="{{ strtolower($dps->plan->name) }}"
                        data-dps-number="{{ $dps->dps_number }}"
                        data-created="{{ $dps->created_at->format('Y-m-d') }}">
                        
                        <!-- DPS Details -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-green-600 to-blue-600 flex items-center justify-center">
                                        <i class="las la-piggy-bank text-white text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ __($dps->plan->name) }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">#{{ $dps->dps_number }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ getAmount($dps->interest_rate) }}% @lang('Interest')</div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Installments -->
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ showAmount($dps->per_installment) }} {{ gs()->cur_text }}</div>
                                <div class="text-gray-600 dark:text-gray-400">@lang('Per') {{ $dps->installment_interval }} {{__(Str::plural('Day', $dps->installment_interval))}}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $dps->given_installment }}/{{ $dps->total_installment }} @lang('Paid')</div>
                            </div>
                        </td>
                        
                        <!-- Amount & Maturity -->
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ showAmount($dps->per_installment * $dps->given_installment) }} {{ gs()->cur_text }}</div>
                                <div class="text-green-600 dark:text-green-400">+{{ showAmount($dps->profit) }} {{ gs()->cur_text }}</div>
                                <div class="text-xs text-purple-600 dark:text-purple-400">{{ showAmount(($dps->per_installment * $dps->given_installment) + $dps->profit) }} {{ gs()->cur_text }} @lang('Total')</div>
                            </div>
                        </td>
                        
                        <!-- Next Payment -->
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                @if($dps->nextInstallment)
                                    <div class="font-medium text-gray-900 dark:text-white">{{ showDateTime($dps->nextInstallment->installment_date, 'd M Y') }}</div>
                                    @php
                                        $daysUntil = \Carbon\Carbon::parse($dps->nextInstallment->installment_date)->diffInDays(now(), false);
                                        $daysUntil = $daysUntil > 0 ? -$daysUntil : abs($daysUntil);
                                    @endphp
                                    @if($daysUntil < 0)
                                        <div class="text-xs text-red-500 dark:text-red-400">{{ abs($daysUntil) }} @lang('days overdue')</div>
                                    @elseif($daysUntil == 0)
                                        <div class="text-xs text-yellow-500 dark:text-yellow-400">@lang('Due today')</div>
                                    @else
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $daysUntil }} @lang('days remaining')</div>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-500 dark:text-gray-400">@lang('Completed')</div>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Status -->
                        <td class="px-6 py-4">
                            @if($dps->status == 1)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    <i class="las la-play-circle mr-1"></i>
                                    @lang('Running')
                                </span>
                            @elseif($dps->status == 2)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    <i class="las la-check-circle mr-1"></i>
                                    @lang('Matured')
                                </span>
                            @elseif($dps->status == 3)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                    <i class="las la-times-circle mr-1"></i>
                                    @lang('Closed')
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                    <i class="las la-clock mr-1"></i>
                                    @lang('Pending')
                                </span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('user.dps.details', $dps->dps_number) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <i class="las la-eye mr-1"></i>
                                    @lang('View')
                                </a>
                                
                                <a href="{{ route('user.dps.instalment.logs', $dps->dps_number) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <i class="las la-list mr-1"></i>
                                    @lang('Installments')
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-32 w-32 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="las la-piggy-bank text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('No DPS Found')</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">@lang('You haven\'t opened any DPS yet.')</p>
                                <a href="{{ route('user.dps.plans') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200">
                                    <i class="las la-plus mr-2"></i>
                                    @lang('Open DPS')
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700" id="mobileCards">
            @forelse($allDps as $dps)
            <div class="p-6 dps-card-mobile" 
                 data-status="{{ $dps->status }}"
                 data-plan="{{ strtolower($dps->plan->name) }}"
                 data-dps-number="{{ $dps->dps_number }}"
                 data-created="{{ $dps->created_at->format('Y-m-d') }}">
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-green-600 to-blue-600 flex items-center justify-center">
                            <i class="las la-piggy-bank text-white"></i>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ __($dps->plan->name) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">#{{ $dps->dps_number }}</div>
                        </div>
                    </div>
                    @if($dps->status == 1)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                            <i class="las la-play-circle mr-1"></i>
                            @lang('Running')
                        </span>
                    @elseif($dps->status == 2)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            <i class="las la-check-circle mr-1"></i>
                            @lang('Matured')
                        </span>
                    @elseif($dps->status == 3)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                            <i class="las la-times-circle mr-1"></i>
                            @lang('Closed')
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                            <i class="las la-clock mr-1"></i>
                            @lang('Pending')
                        </span>
                    @endif
                </div>
                
                <!-- Details Grid -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Per Installment')</div>
                        <div class="font-semibold text-gray-900 dark:text-white">{{ showAmount($dps->per_installment) }} {{ gs()->cur_text }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Progress')</div>
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $dps->given_installment }}/{{ $dps->total_installment }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Deposited')</div>
                        <div class="font-semibold text-green-600 dark:text-green-400">{{ showAmount($dps->per_installment * $dps->given_installment) }} {{ gs()->cur_text }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Maturity Amount')</div>
                        <div class="font-semibold text-purple-600 dark:text-purple-400">{{ showAmount(($dps->per_installment * $dps->given_installment) + $dps->profit) }} {{ gs()->cur_text }}</div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="{{ route('user.dps.details', $dps->dps_number) }}" 
                       class="flex-1 text-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                        @lang('View Details')
                    </a>
                    <a href="{{ route('user.dps.instalment.logs', $dps->dps_number) }}" 
                       class="flex-1 text-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                        @lang('Installments')
                    </a>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="h-32 w-32 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="las la-piggy-bank text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('No DPS Found')</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">@lang('You haven\'t opened any DPS yet.')</p>
                <a href="{{ route('user.dps.plans') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    <i class="las la-plus mr-2"></i>
                    @lang('Open DPS')
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($allDps->hasPages())
    <div class="flex items-center justify-center">
        {{ paginateLinks($allDps) }}
    </div>
    @endif
</div>

@endsection

@push('style-lib')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('style')
<style>
/* Custom daterangepicker styling for Tailwind integration */
.daterangepicker {
    border-radius: 0.5rem !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgb(209, 213, 219) !important;
    font-family: inherit !important;
}

.daterangepicker .calendar-table {
    border-radius: 0.375rem;
}

.daterangepicker td.active, .daterangepicker td.active:hover {
    background-color: rgb(59, 130, 246) !important;
    border-color: rgb(59, 130, 246) !important;
    color: white !important;
}

.daterangepicker td.in-range {
    background-color: rgba(59, 130, 246, 0.1) !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
    color: rgb(59, 130, 246) !important;
}

.daterangepicker td:hover {
    background-color: rgba(59, 130, 246, 0.1) !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
    color: rgb(59, 130, 246) !important;
}

.daterangepicker .ranges li:hover {
    background-color: rgba(59, 130, 246, 0.1) !important;
    color: rgb(59, 130, 246) !important;
}

.daterangepicker .ranges li.active {
    background-color: rgb(59, 130, 246) !important;
    color: white !important;
}

@media (prefers-color-scheme: dark) {
    .daterangepicker {
        background-color: rgb(31, 41, 55) !important;
        border-color: rgb(75, 85, 99) !important;
        color: rgb(243, 244, 246) !important;
    }
    
    .daterangepicker .calendar-table {
        background-color: rgb(31, 41, 55) !important;
        color: rgb(243, 244, 246) !important;
    }
    
    .daterangepicker .calendar-table th,
    .daterangepicker .calendar-table td {
        color: rgb(243, 244, 246) !important;
    }
    
    .daterangepicker td:hover {
        background-color: rgba(96, 165, 250, 0.1) !important;
        color: rgb(96, 165, 250) !important;
    }
    
    .daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: rgb(37, 99, 235) !important;
        border-color: rgb(37, 99, 235) !important;
    }
    
    .daterangepicker td.in-range {
        background-color: rgba(37, 99, 235, 0.1) !important;
        color: rgb(96, 165, 250) !important;
    }
    
    .daterangepicker .ranges {
        background-color: rgb(31, 41, 55) !important;
        border-color: rgb(75, 85, 99) !important;
    }
    
    .daterangepicker .ranges li {
        color: rgb(243, 244, 246) !important;
    }
    
    .daterangepicker .ranges li:hover {
        background-color: rgba(96, 165, 250, 0.1) !important;
        color: rgb(96, 165, 250) !important;
    }
    
    .daterangepicker .ranges li.active {
        background-color: rgb(37, 99, 235) !important;
        color: white !important;
    }
}
</style>
@endpush

@push('script-lib')
<script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('script')
<script>
(function ($) {
    "use strict";
    
    // Initialize date range picker with same configuration as Viser
    const datePicker = $('.date-range').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'MMMM DD, YYYY'
        },
        showDropdowns: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 15 Days': [moment().subtract(14, 'days'), moment()],
            'Last 30 Days': [moment().subtract(30, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
        },
        maxDate: moment()
    });
    
    const changeDatePickerText = (event, startDate, endDate) => {
        $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
    }

    // Handle date range selection
    $('.date-range').on('apply.daterangepicker', (event, picker) => {
        changeDatePickerText(event, picker.startDate, picker.endDate);
        // Auto-submit form when date is selected
        $(event.target).closest('form').submit();
    });

    // Handle cancel/clear
    $('.date-range').on('cancel.daterangepicker', function(event, picker) {
        $(this).val('');
        $(this).closest('form').submit();
    });

    // If there's an existing date value, set it in the picker
    if ($('.date-range').val()) {
        let dateRange = $('.date-range').val().split(' - ');
        if (dateRange.length === 2) {
            $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
            $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
        }
    }
    
})(jQuery);
</script>
@endpush