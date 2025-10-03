@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .deposit-table {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #fef3c7, #fbbf24);
        color: #92400e;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #d1fae5, #10b981);
        color: #065f46;
    }
    
    .status-rejected {
        background: linear-gradient(135deg, #fee2e2, #ef4444);
        color: #991b1b;
    }
    
    .deposit-card {
        background: linear-gradient(135deg, 
            rgba(79, 70, 229, 0.1) 0%, 
            rgba(147, 51, 234, 0.1) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .deposit-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        border-color: rgba(139, 92, 246, 0.3);
    }
    
    .search-form {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
    }
    
    .btn-deposit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-deposit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    @media (max-width: 768px) {
        .deposit-mobile-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
        }
    }
    
    .amount-display {
        font-size: 1.25rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Enhanced Pagination Styles */
    .pagination-container {
        background: linear-gradient(135deg, rgba(249, 250, 251, 0.8) 0%, rgba(243, 244, 246, 0.9) 100%);
    }
    
    .dark .pagination-container {
        background: linear-gradient(135deg, rgba(55, 65, 81, 0.8) 0%, rgba(31, 41, 55, 0.9) 100%);
    }
    
    .pagination-btn {
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
    }
    
    .pagination-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .pagination-btn.active {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
    }
    
    .pagination-btn:not(.active):hover::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%);
        pointer-events: none;
    }
    
    /* Loading animation for pagination */
    .pagination-loading {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    /* Smooth page transitions */
    .page-transition {
        transition: opacity 0.3s ease-in-out;
    }
    
    .page-transition.loading {
        opacity: 0.7;
        pointer-events: none;
    }
    
    /* Responsive pagination improvements */
    @media (max-width: 640px) {
        .pagination-btn {
            min-width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .pagination-btn span {
            display: none;
        }
        
        .pagination-btn i {
            font-size: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="mb-6 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Deposit History')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Track and manage all your deposit transactions')</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
            <!-- Search Form -->
            <div class="search-form p-2">
                <form method="GET" class="flex items-center">
                    <div class="relative flex-1">
                        <i class="las la-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request()->search }}" 
                               placeholder="@lang('Search by TRX Number...')" 
                               class="w-full pl-10 pr-4 py-2 bg-transparent border-none text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none">
                    </div>
                    <button type="submit" class="ml-2 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>
            
            <!-- Deposit Now Button -->
            <a href="{{ route('user.deposit') }}" class="btn-deposit inline-flex items-center justify-center whitespace-nowrap">
                <i class="las la-plus mr-2"></i>
                @lang('Deposit Now')
            </a>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block">
        <div class="deposit-card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @lang('Transaction')
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @lang('Amount')
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @lang('Charge')
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @lang('Total')
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @lang('Gateway')
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @lang('Date')
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @lang('Status')
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @lang('Action')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($deposits as $deposit)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm mr-3">
                                            <i class="las la-credit-card"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">#{{ $deposit->trx }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($deposit->created_at, 'M d, Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="amount-display">{{ showUserAmount($deposit->amount, auth()->user()) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-red-600 dark:text-red-400 font-medium">{{ showUserAmount($deposit->charge, auth()->user()) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ showUserAmount($deposit->amount + $deposit->charge, auth()->user()) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($deposit->gateway)
                                            <img src="{{ getImage(getFilePath('gateway') . '/' . $deposit->gateway->image) }}" 
                                                 alt="{{ $deposit->gateway->name }}" class="w-8 h-8 rounded-lg mr-2">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __($deposit->gateway->name) }}</span>
                                        @elseif($deposit->branch)
                                            <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center mr-2">
                                                <i class="las la-university text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __($deposit->branch->name) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ showDateTime($deposit->created_at) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = 'status-badge ';
                                        switch($deposit->status) {
                                            case 1:
                                                $statusClass .= 'status-approved';
                                                $statusText = 'Completed';
                                                break;
                                            case 2:
                                                $statusClass .= 'status-pending';
                                                $statusText = 'Pending';
                                                break;
                                            case 3:
                                                $statusClass .= 'status-rejected';
                                                $statusText = 'Rejected';
                                                break;
                                            default:
                                                $statusClass .= 'status-pending';
                                                $statusText = 'Unknown';
                                        }
                                    @endphp
                                    <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('user.deposit.details', $deposit->trx) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors">
                                        <i class="las la-eye mr-1"></i>
                                        @lang('Details')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                                            <i class="las la-money-bill-wave text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('No deposits found')</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mb-4">@lang('You haven\'t made any deposits yet.')</p>
                                        <a href="{{ route('user.deposit') }}" class="btn-deposit">
                                            <i class="las la-plus mr-2"></i>
                                            @lang('Make Your First Deposit')
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-4">
        @forelse($deposits as $deposit)
            <div class="deposit-mobile-card">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold mr-3">
                            <i class="las la-credit-card"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">#{{ $deposit->trx }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ showDateTime($deposit->created_at, 'M d, Y') }}</p>
                        </div>
                    </div>
                    @php
                        $statusClass = 'status-badge ';
                        switch($deposit->status) {
                            case 1:
                                $statusClass .= 'status-approved';
                                $statusText = 'Completed';
                                break;
                            case 2:
                                $statusClass .= 'status-pending';
                                $statusText = 'Pending';
                                break;
                            case 3:
                                $statusClass .= 'status-rejected';
                                $statusText = 'Rejected';
                                break;
                            default:
                                $statusClass .= 'status-pending';
                                $statusText = 'Unknown';
                        }
                    @endphp
                    <span class="{{ $statusClass }}">{{ $statusText }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">@lang('Amount')</p>
                        <p class="amount-display">{{ showUserAmount($deposit->amount, auth()->user()) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">@lang('Total')</p>
                        <p class="font-bold text-gray-900 dark:text-white">{{ showUserAmount($deposit->amount + $deposit->charge, auth()->user()) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        @if($deposit->gateway)
                            <img src="{{ getImage(getFilePath('gateway') . '/' . $deposit->gateway->image) }}" 
                                 alt="{{ $deposit->gateway->name }}" class="w-6 h-6 rounded mr-2">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __($deposit->gateway->name) }}</span>
                        @elseif($deposit->branch)
                            <div class="w-6 h-6 rounded bg-green-100 dark:bg-green-900/30 flex items-center justify-center mr-2">
                                <i class="las la-university text-green-600 dark:text-green-400 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-900 dark:text-white">{{ __($deposit->branch->name) }}</span>
                        @endif
                    </div>
                    <a href="{{ route('user.deposit.details', $deposit->trx) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium">
                        <i class="las la-eye mr-1"></i>
                        @lang('Details')
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                    <i class="las la-money-bill-wave text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('No deposits found')</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">@lang('You haven\'t made any deposits yet.')</p>
                <a href="{{ route('user.deposit') }}" class="btn-deposit">
                    <i class="las la-plus mr-2"></i>
                    @lang('Make Your First Deposit')
                </a>
            </div>
        @endforelse
    </div>

    <!-- Premium Enhanced Pagination -->
    @if ($deposits->hasPages())
        <div class="mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                <div class="px-6 py-6 border-t border-gray-200 dark:border-gray-600 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50">
                    <!-- Mobile Pagination Info -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Results Information -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="text-sm text-gray-600 dark:text-gray-300 font-medium">
                                @lang('Showing') 
                                <span class="font-bold text-primary-600 dark:text-primary-400">{{ $deposits->firstItem() }}</span> 
                                @lang('to') 
                                <span class="font-bold text-primary-600 dark:text-primary-400">{{ $deposits->lastItem() }}</span> 
                                @lang('of') 
                                <span class="font-bold text-primary-600 dark:text-primary-400">{{ $deposits->total() }}</span> 
                                @lang('deposits')
                            </div>
                            
                            <!-- Per Page Selector -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm text-gray-500 dark:text-gray-400">@lang('Show'):</label>
                                <select onchange="changePerPageDeposits(this.value)" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>

                        <!-- Enhanced Pagination Controls -->
                        <div class="flex items-center justify-center sm:justify-end">
                            <nav class="flex items-center space-x-1" aria-label="Pagination">
                                <!-- First Page -->
                                @if ($deposits->currentPage() > 3)
                                    <a href="{{ $deposits->url(1) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                                       title="@lang('First Page')">
                                        <i class="las la-angle-double-left"></i>
                                        <span class="hidden sm:inline ml-1">@lang('First')</span>
                                    </a>
                                @endif

                                <!-- Previous Page -->
                                @if ($deposits->onFirstPage())
                                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-600 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg cursor-not-allowed">
                                        <i class="las la-angle-left"></i>
                                        <span class="hidden sm:inline ml-1">@lang('Previous')</span>
                                    </span>
                                @else
                                    <a href="{{ $deposits->previousPageUrl() }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                                       title="@lang('Previous Page')">
                                        <i class="las la-angle-left"></i>
                                        <span class="hidden sm:inline ml-1">@lang('Previous')</span>
                                    </a>
                                @endif

                                <!-- Page Numbers -->
                                <div class="flex items-center space-x-1">
                                    @php
                                        $start = max(1, $deposits->currentPage() - 2);
                                        $end = min($deposits->lastPage(), $deposits->currentPage() + 2);
                                    @endphp

                                    @if ($start > 1)
                                        <span class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">...</span>
                                    @endif

                                    @for ($page = $start; $page <= $end; $page++)
                                        @if ($page == $deposits->currentPage())
                                            <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-primary-600 border border-primary-600 rounded-lg shadow-sm">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $deposits->url($page) }}" 
                                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endfor

                                    @if ($end < $deposits->lastPage())
                                        <span class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">...</span>
                                    @endif
                                </div>

                                <!-- Next Page -->
                                @if ($deposits->hasMorePages())
                                    <a href="{{ $deposits->nextPageUrl() }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                                       title="@lang('Next Page')">
                                        <span class="hidden sm:inline mr-1">@lang('Next')</span>
                                        <i class="las la-angle-right"></i>
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-600 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg cursor-not-allowed">
                                        <span class="hidden sm:inline mr-1">@lang('Next')</span>
                                        <i class="las la-angle-right"></i>
                                    </span>
                                @endif

                                <!-- Last Page -->
                                @if ($deposits->currentPage() < $deposits->lastPage() - 2)
                                    <a href="{{ $deposits->url($deposits->lastPage()) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                                       title="@lang('Last Page')">
                                        <span class="hidden sm:inline mr-1">@lang('Last')</span>
                                        <i class="las la-angle-double-right"></i>
                                    </a>
                                @endif
                            </nav>
                        </div>
                    </div>

                    <!-- Quick Jump to Page (Desktop) -->
                    <div class="hidden lg:flex items-center justify-center mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center space-x-2 text-sm">
                            <label class="text-gray-500 dark:text-gray-400">@lang('Jump to page'):</label>
                            <input type="number" 
                                   id="jumpToPageDeposits" 
                                   min="1" 
                                   max="{{ $deposits->lastPage() }}" 
                                   placeholder="{{ $deposits->currentPage() }}"
                                   class="w-20 px-3 py-1 text-center border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   onkeypress="handleJumpToPageDeposits(event)">
                            <button onclick="jumpToPageDeposits()" 
                                    class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                                @lang('Go')
                            </button>
                            <span class="text-gray-500 dark:text-gray-400">@lang('of') {{ $deposits->lastPage() }}</span>
                        </div>
                    </div>

                    <!-- Mobile Page Info -->
                    <div class="lg:hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('Page') 
                            <span class="font-bold text-primary-600 dark:text-primary-400">{{ $deposits->currentPage() }}</span> 
                            @lang('of') 
                            <span class="font-bold text-primary-600 dark:text-primary-400">{{ $deposits->lastPage() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script')
<script>
    'use strict';
    
    // Enhanced notification system for deposits
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="las ${type === 'success' ? 'la-check-circle' : type === 'error' ? 'la-exclamation-circle' : 'la-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Enhanced Pagination Functions for Deposits
    function changePerPageDeposits(perPage) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to page 1
        window.location.href = url.toString();
    }

    function jumpToPageDeposits() {
        const pageInput = document.getElementById('jumpToPageDeposits');
        const page = parseInt(pageInput.value);
        const maxPage = {{ $deposits->lastPage() }};
        
        if (page && page >= 1 && page <= maxPage) {
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        } else {
            showNotification('@lang("Please enter a valid page number between 1 and ") ' + maxPage, 'error');
            pageInput.focus();
        }
    }

    function handleJumpToPageDeposits(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            jumpToPageDeposits();
        }
    }

    // Keyboard shortcuts for deposits pagination
    document.addEventListener('keydown', function(event) {
        // Only if no input is focused
        if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            switch(event.key) {
                case 'ArrowLeft':
                    // Previous page
                    @if (!$deposits->onFirstPage())
                        window.location.href = '{{ $deposits->previousPageUrl() }}';
                    @endif
                    break;
                case 'ArrowRight':
                    // Next page
                    @if ($deposits->hasMorePages())
                        window.location.href = '{{ $deposits->nextPageUrl() }}';
                    @endif
                    break;
                case 'Home':
                    // First page
                    @if ($deposits->currentPage() > 1)
                        window.location.href = '{{ $deposits->url(1) }}';
                    @endif
                    break;
                case 'End':
                    // Last page
                    @if ($deposits->currentPage() < $deposits->lastPage())
                        window.location.href = '{{ $deposits->url($deposits->lastPage()) }}';
                    @endif
                    break;
            }
        }
    });

    // Show keyboard shortcuts hint for deposits
    function showKeyboardShortcuts() {
        showNotification('@lang("Use arrow keys to navigate pages, Home/End for first/last page")', 'info');
    }

    // Show shortcuts hint on page load (once per session)
    document.addEventListener('DOMContentLoaded', function() {
        if (!sessionStorage.getItem('depositsShortcutsShown')) {
            setTimeout(showKeyboardShortcuts, 2000);
            sessionStorage.setItem('depositsShortcutsShown', 'true');
        }
    });

    // Enhanced loading states
    function addLoadingState(element) {
        element.classList.add('pagination-loading');
    }

    function removeLoadingState(element) {
        element.classList.remove('pagination-loading');
    }

    // Add loading animation to pagination links
    document.addEventListener('DOMContentLoaded', function() {
        const paginationLinks = document.querySelectorAll('nav[aria-label="Pagination"] a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function() {
                addLoadingState(this);
                document.body.classList.add('page-transition', 'loading');
            });
        });
    });
</script>
@endpush