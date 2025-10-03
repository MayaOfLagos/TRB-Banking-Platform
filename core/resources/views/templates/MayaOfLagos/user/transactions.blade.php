@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="space-y-6 pb-20 lg:pb-6">
    <!-- Page Header with Stats -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 rounded-3xl p-6 lg:p-8 text-white shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-2xl lg:text-3xl font-bold mb-2">@lang('Transaction History')</h1>
                <p class="text-primary-100">@lang('Track all your account activities')</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                    <div class="text-primary-100 text-sm">@lang('Current Balance')</div>
                    <div class="text-2xl font-bold">{{ showAmount(auth()->user()->balance ?: 0) }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                    <div class="text-primary-100 text-sm">@lang('Total Transactions')</div>
                    <div class="text-2xl font-bold">{{ $transactions->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Toggle Button -->
    <div class="flex justify-end">
        <button class="showFilterBtn bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center space-x-2 shadow-sm" type="button">
            <i class="las la-filter text-lg"></i>
            <span class="font-medium">@lang('Filter')</span>
        </button>
    </div>

    <!-- Enhanced Filters -->
    <div class="responsive-filter-card hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Filter Transactions')</h3>
            <form action="" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search by TRX -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Search Transaction')</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request()->search }}" 
                                   placeholder="@lang('TRX Number')" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="las la-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Date Range')</label>
                        <input type="text" name="date" value="{{ request()->date }}" 
                               placeholder="@lang('Start Date - End Date')"
                               class="date-range w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               autocomplete="off" readonly>
                    </div>

                    <!-- Transaction Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Transaction Type')</label>
                        <select name="trx_type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">@lang('All Transactions')</option>
                            <option value="+" @selected(request()->trx_type == '+')>@lang('Credit (+)')</option>
                            <option value="-" @selected(request()->trx_type == '-')>@lang('Debit (-)')</option>
                        </select>
                    </div>

                    <!-- Remark Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Category')</label>
                        <select name="remark" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">@lang('All Categories')</option>
                            @foreach($remarks as $remark)
                                <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors flex items-center justify-center space-x-2">
                        <i class="las la-filter"></i>
                        <span>@lang('Apply Filters')</span>
                    </button>
                    <a href="{{ route('user.transaction.history') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors flex items-center justify-center space-x-2">
                        <i class="las la-redo-alt"></i>
                        <span>@lang('Reset')</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
    <!-- Premium Transactions List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        @if($transactions->count() > 0)
            <!-- Desktop View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">@lang('Transaction')</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">@lang('Details')</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">@lang('Amount')</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">@lang('Balance After')</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($transactions as $trx)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-xl {{ $trx->trx_type == '+' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' }} flex items-center justify-center">
                                            <i class="las {{ $trx->trx_type == '+' ? 'la-arrow-down' : 'la-arrow-up' }} text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">#{{ $trx->trx }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($trx->created_at, 'M d, Y h:i A') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ __($trx->details) }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span class="inline-flex px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md">{{ __(keyToTitle($trx->remark)) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base font-bold {{ $trx->trx_type == '+' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $trx->trx_type }}{{ showAmount($trx->amount ?: 0) }}
                                        </span>
                                        @if(($trx->charge ?: 0) > 0)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                @lang('Fee'): {{ showAmount($trx->charge ?: 0) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ showAmount($trx->post_balance ?: 0) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="showTransactionDetails({{ json_encode($trx) }})"
                                            class="bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-600 dark:text-primary-400 px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-1 group-hover:shadow-md">
                                        <i class="las la-eye"></i>
                                        <span>@lang('View')</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Mobile View -->
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-600">
                @foreach($transactions as $trx)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-xl {{ $trx->trx_type == '+' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' }} flex items-center justify-center">
                                    <i class="las {{ $trx->trx_type == '+' ? 'la-arrow-down' : 'la-arrow-up' }} text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ __($trx->details) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">#{{ $trx->trx }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span class="inline-flex px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md">{{ __(keyToTitle($trx->remark)) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-bold {{ $trx->trx_type == '+' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $trx->trx_type }}{{ showAmount($trx->amount ?: 0) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ showDateTime($trx->created_at, 'M d, h:i A') }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-600">
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                @lang('Balance'): <span class="font-medium text-gray-900 dark:text-white">{{ showAmount($trx->post_balance ?: 0) }}</span>
                            </div>
                            <button onclick="showTransactionDetails({{ json_encode($trx) }})"
                                    class="bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-600 dark:text-primary-400 px-3 py-2 rounded-lg text-xs font-medium transition-colors flex items-center space-x-1">
                                <i class="las la-eye"></i>
                                <span>@lang('Details')</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Premium Enhanced Pagination -->
            @if ($transactions->hasPages())
                <div class="px-6 py-6 border-t border-gray-200 dark:border-gray-600 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50">
                    <!-- Mobile Pagination Info -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Results Information -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="text-sm text-gray-600 dark:text-gray-300 font-medium">
                                @lang('Showing') 
                                <span class="font-bold text-primary-600 dark:text-primary-400">{{ $transactions->firstItem() }}</span> 
                                @lang('to') 
                                <span class="font-bold text-primary-600 dark:text-primary-400">{{ $transactions->lastItem() }}</span> 
                                @lang('of') 
                                <span class="font-bold text-primary-600 dark:text-primary-400">{{ $transactions->total() }}</span> 
                                @lang('results')
                            </div>
                            
                            <!-- Per Page Selector -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm text-gray-500 dark:text-gray-400">@lang('Show'):</label>
                                <select onchange="changePerPage(this.value)" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
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
                                @if ($transactions->currentPage() > 3)
                                    <a href="{{ $transactions->url(1) }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                                       title="@lang('First Page')">
                                        <i class="las la-angle-double-left"></i>
                                        <span class="hidden sm:inline ml-1">@lang('First')</span>
                                    </a>
                                @endif

                                <!-- Previous Page -->
                                @if ($transactions->onFirstPage())
                                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-600 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg cursor-not-allowed">
                                        <i class="las la-angle-left"></i>
                                        <span class="hidden sm:inline ml-1">@lang('Previous')</span>
                                    </span>
                                @else
                                    <a href="{{ $transactions->previousPageUrl() }}" 
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                                       title="@lang('Previous Page')">
                                        <i class="las la-angle-left"></i>
                                        <span class="hidden sm:inline ml-1">@lang('Previous')</span>
                                    </a>
                                @endif

                                <!-- Page Numbers -->
                                <div class="flex items-center space-x-1">
                                    @php
                                        $start = max(1, $transactions->currentPage() - 2);
                                        $end = min($transactions->lastPage(), $transactions->currentPage() + 2);
                                    @endphp

                                    @if ($start > 1)
                                        <span class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">...</span>
                                    @endif

                                    @for ($page = $start; $page <= $end; $page++)
                                        @if ($page == $transactions->currentPage())
                                            <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-primary-600 border border-primary-600 rounded-lg shadow-sm">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $transactions->url($page) }}" 
                                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endfor

                                    @if ($end < $transactions->lastPage())
                                        <span class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">...</span>
                                    @endif
                                </div>

                                <!-- Next Page -->
                                @if ($transactions->hasMorePages())
                                    <a href="{{ $transactions->nextPageUrl() }}" 
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
                                @if ($transactions->currentPage() < $transactions->lastPage() - 2)
                                    <a href="{{ $transactions->url($transactions->lastPage()) }}" 
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
                                   id="jumpToPage" 
                                   min="1" 
                                   max="{{ $transactions->lastPage() }}" 
                                   placeholder="{{ $transactions->currentPage() }}"
                                   class="w-20 px-3 py-1 text-center border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   onkeypress="handleJumpToPage(event)">
                            <button onclick="jumpToPage()" 
                                    class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                                @lang('Go')
                            </button>
                            <span class="text-gray-500 dark:text-gray-400">@lang('of') {{ $transactions->lastPage() }}</span>
                        </div>
                    </div>

                    <!-- Mobile Page Info -->
                    <div class="lg:hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('Page') 
                            <span class="font-bold text-primary-600 dark:text-primary-400">{{ $transactions->currentPage() }}</span> 
                            @lang('of') 
                            <span class="font-bold text-primary-600 dark:text-primary-400">{{ $transactions->lastPage() }}</span>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Enhanced Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="las la-receipt text-4xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('No transactions found')</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">@lang('You haven\'t made any transactions yet. Start by making a deposit or transfer.')</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('user.deposit.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-medium transition-colors flex items-center space-x-2">
                        <i class="las la-plus-circle"></i>
                        <span>@lang('Make Deposit')</span>
                    </a>
                    <a href="{{ route('user.home') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-medium transition-colors flex items-center space-x-2">
                        <i class="las la-home"></i>
                        <span>@lang('Go to Dashboard')</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Enhanced Transaction Details Modal -->
<div id="transactionModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeTransactionModal()"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div id="modal-icon" class="w-12 h-12 rounded-xl flex items-center justify-center">
                        <i id="modal-icon-class" class="text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">@lang('Transaction Details')</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="modal-subtitle"></p>
                    </div>
                </div>
                <button onclick="closeTransactionModal()" type="button" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <i class="las la-times text-xl"></i>
                </button>
            </div>

            <!-- Transaction Details -->
            <div class="space-y-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">@lang('Transaction ID')</label>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" id="modal-trx-id"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">@lang('Date & Time')</label>
                            <div class="text-sm text-gray-900 dark:text-white" id="modal-date"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Amount')</span>
                        <span class="text-lg font-bold" id="modal-amount"></span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Transaction Fee')</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white" id="modal-charge"></span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Balance After')</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white" id="modal-post-balance"></span>
                    </div>
                    
                    <div class="py-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">@lang('Transaction Details')</span>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <p class="text-sm text-gray-900 dark:text-white" id="modal-details"></p>
                        </div>
                    </div>
                    
                    <div class="py-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">@lang('Category')</span>
                        <span id="modal-remark" class="inline-flex px-3 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-full text-sm font-medium"></span>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="mt-6 flex space-x-3">
                <button onclick="closeTransactionModal()" type="button" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-xl font-medium transition-colors">
                    @lang('Close')
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('style')
    <style>
        /* Enhanced Filter Animation */
        .responsive-filter-card {
            transition: all 0.3s ease-in-out;
            transform: translateY(-10px);
            opacity: 0;
        }
        
        .responsive-filter-card.show {
            transform: translateY(0);
            opacity: 1;
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
        
        /* Custom Daterangepicker Styling */
        .daterangepicker {
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
        }
        
        .daterangepicker .calendar-table {
            border-radius: 8px !important;
        }
        
        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            background-color: #0ea5e9 !important;
            border-color: #0ea5e9 !important;
            color: white !important;
        }
        
        .daterangepicker td.in-range {
            background-color: rgba(14, 165, 233, 0.2) !important;
            border-color: rgba(14, 165, 233, 0.2) !important;
        }
        
        .daterangepicker .ranges li.active {
            background-color: #0ea5e9 !important;
            color: white !important;
        }
        
        /* Dark mode daterangepicker */
        .dark .daterangepicker {
            background: #374151 !important;
            border-color: #4b5563 !important;
            color: #f9fafb !important;
        }
        
        .dark .daterangepicker .calendar-table {
            background: #374151 !important;
        }
        
        .dark .daterangepicker td {
            color: #f9fafb !important;
        }
        
        .dark .daterangepicker .ranges li {
            color: #f9fafb !important;
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

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('script')
    <script>
        'use strict';
        
        // Enhanced Filter Toggle
        $(document).ready(function() {
            $('.showFilterBtn').on('click', function() {
                const filterCard = $('.responsive-filter-card');
                const isHidden = filterCard.hasClass('hidden');
                
                if (isHidden) {
                    filterCard.removeClass('hidden');
                    setTimeout(() => {
                        filterCard.addClass('show');
                    }, 10);
                    $(this).find('span').text('@lang("Hide Filter")');
                    $(this).find('i').removeClass('la-filter').addClass('la-times');
                } else {
                    filterCard.removeClass('show');
                    setTimeout(() => {
                        filterCard.addClass('hidden');
                    }, 300);
                    $(this).find('span').text('@lang("Filter")');
                    $(this).find('i').removeClass('la-times').addClass('la-filter');
                }
            });

            // Initialize Enhanced Date Range Picker
            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Apply',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom Range',
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

            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));

            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }
        });

        // Enhanced Transaction Details Modal
        function showTransactionDetails(transaction) {
            const modal = document.getElementById('transactionModal');
            const isCredit = transaction.trx_type === '+';
            
            // Update modal icon and colors
            const modalIcon = document.getElementById('modal-icon');
            const modalIconClass = document.getElementById('modal-icon-class');
            
            if (isCredit) {
                modalIcon.className = 'w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center';
                modalIconClass.className = 'las la-arrow-down text-2xl';
            } else {
                modalIcon.className = 'w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center';
                modalIconClass.className = 'las la-arrow-up text-2xl';
            }
            
            // Update modal content
            document.getElementById('modal-subtitle').textContent = isCredit ? '@lang("Credit Transaction")' : '@lang("Debit Transaction")';
            document.getElementById('modal-trx-id').textContent = '#' + transaction.trx;
            document.getElementById('modal-date').textContent = new Date(transaction.created_at).toLocaleString();
            
            const amount = document.getElementById('modal-amount');
            amount.textContent = (isCredit ? '+' : '-') + '{{ getUserCurrency(auth()->user())['symbol'] }}' + parseFloat(transaction.amount).toFixed(2);
            amount.className = 'text-lg font-bold ' + (isCredit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400');
            
            document.getElementById('modal-charge').textContent = '{{ getUserCurrency(auth()->user())['symbol'] }}' + parseFloat(transaction.charge || 0).toFixed(2);
            document.getElementById('modal-post-balance').textContent = '{{ getUserCurrency(auth()->user())['symbol'] }}' + parseFloat(transaction.post_balance).toFixed(2);
            document.getElementById('modal-details').textContent = transaction.details;
            document.getElementById('modal-remark').textContent = transaction.remark.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            
            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTransactionModal() {
            const modal = document.getElementById('transactionModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Enhanced notification system
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

        // Enhanced Pagination Functions
        function changePerPage(perPage) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', perPage);
            url.searchParams.delete('page'); // Reset to page 1
            window.location.href = url.toString();
        }

        function jumpToPage() {
            const pageInput = document.getElementById('jumpToPage');
            const page = parseInt(pageInput.value);
            const maxPage = {{ $transactions->lastPage() }};
            
            if (page && page >= 1 && page <= maxPage) {
                const url = new URL(window.location);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
            } else {
                showNotification('@lang("Please enter a valid page number between 1 and ") ' + maxPage, 'error');
                pageInput.focus();
            }
        }

        function handleJumpToPage(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                jumpToPage();
            }
        }

        // Keyboard shortcuts for pagination
        document.addEventListener('keydown', function(event) {
            // Only if no input is focused
            if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                switch(event.key) {
                    case 'ArrowLeft':
                        // Previous page
                        @if (!$transactions->onFirstPage())
                            window.location.href = '{{ $transactions->previousPageUrl() }}';
                        @endif
                        break;
                    case 'ArrowRight':
                        // Next page
                        @if ($transactions->hasMorePages())
                            window.location.href = '{{ $transactions->nextPageUrl() }}';
                        @endif
                        break;
                    case 'Home':
                        // First page
                        @if ($transactions->currentPage() > 1)
                            window.location.href = '{{ $transactions->url(1) }}';
                        @endif
                        break;
                    case 'End':
                        // Last page
                        @if ($transactions->currentPage() < $transactions->lastPage())
                            window.location.href = '{{ $transactions->url($transactions->lastPage()) }}';
                        @endif
                        break;
                }
            }
        });

        // Show keyboard shortcuts hint
        function showKeyboardShortcuts() {
            showNotification('@lang("Use arrow keys to navigate pages, Home/End for first/last page")', 'info');
        }

        // Show shortcuts hint on page load (once per session)
        $(document).ready(function() {
            if (!sessionStorage.getItem('shortcutsShown')) {
                setTimeout(showKeyboardShortcuts, 2000);
                sessionStorage.setItem('shortcutsShown', 'true');
            }
        });
    </script>
@endpush