@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-2xl p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-history text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('Withdrawal History')</h1>
                    <p class="text-gray-600 dark:text-gray-400">@lang('Track all your withdrawal transactions')</p>
                </div>
            </div>
            <a href="{{ route('user.withdraw') }}" 
               class="inline-flex items-center bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg">
                <i class="las la-plus mr-2"></i>
                @lang('New Withdrawal')
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('user.withdraw.history') }}" id="searchForm">
                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="las la-search text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="@lang('Search by TRX number, method, or status...')"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            @if(request('search'))
                                <button type="button" 
                                        onclick="clearSearch()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="las la-times text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <!-- Filter Button -->
                        <button type="button" 
                                id="filterBtn"
                                class="inline-flex items-center px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-all duration-200 border border-gray-300 dark:border-gray-600">
                            <i class="las la-filter mr-2"></i>
                            @lang('Filter')
                            @if(request()->hasAny(['status', 'method', 'date_from', 'date_to']))
                                <span class="ml-2 w-2 h-2 bg-indigo-500 rounded-full"></span>
                            @endif
                        </button>
                        
                        <!-- Export Button -->
                        <button type="button" 
                                id="exportBtn"
                                class="inline-flex items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg">
                            <i class="las la-download mr-2"></i>
                            @lang('Export PDF')
                        </button>
                        
                        <!-- Search Button -->
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg">
                            <i class="las la-search mr-2"></i>
                            @lang('Search')
                        </button>
                    </div>
                </div>
                
                <!-- Filter Panel (Hidden by default) -->
                <div id="filterPanel" class="hidden mt-6 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Status')
                            </label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">@lang('All Status')</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>@lang('Approved')</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>@lang('Pending')</option>
                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>@lang('Rejected')</option>
                            </select>
                        </div>
                        
                        <!-- Method Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Method')
                            </label>
                            <select name="method" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">@lang('All Methods')</option>
                                <!-- Dynamic methods will be populated via AJAX or passed from controller -->
                            </select>
                        </div>
                        
                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('From Date')
                            </label>
                            <input type="date" 
                                   name="date_from" 
                                   value="{{ request('date_from') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('To Date')
                            </label>
                            <input type="date" 
                                   name="date_to" 
                                   value="{{ request('date_to') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <!-- Filter Actions -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" 
                                onclick="clearFilters()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                            @lang('Clear All')
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                            @lang('Apply Filters')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @lang('Transaction')
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @lang('Amount')
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @lang('Charge')
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @lang('Receivable')
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @lang('Method')
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @lang('Date')
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @lang('Status')
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            @lang('Action')
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($withdraws as $withdraw)
                        @php
                            $details = [];
                            foreach ($withdraw->withdraw_information ?? [] as $key => $info) {
                                $details[] = $info;
                                if ($info->type == 'file' && @$details[$key]) {
                                    $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <!-- Transaction Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                        <i class="las la-arrow-up text-indigo-600 dark:text-indigo-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-mono text-sm font-medium text-gray-900 dark:text-white">#{{ $withdraw->trx }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($withdraw->created_at, 'd M Y') }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ showUserAmount($withdraw->amount, auth()->user()) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Requested')</p>
                                </div>
                            </td>

                            <!-- Charge -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-red-600 dark:text-red-400">{{ showUserAmount($withdraw->charge, auth()->user()) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Fee')</p>
                                </div>
                            </td>

                            <!-- Receivable -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-bold text-green-600 dark:text-green-400">{{ showUserAmount($withdraw->after_charge, auth()->user()) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Final Amount')</p>
                                </div>
                            </td>

                            <!-- Method -->
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @if ($withdraw->branch)
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <span class="font-medium text-blue-600 dark:text-blue-400">{{ __(@$withdraw->branch->name) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">@lang('Branch')</p>
                                    @else
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                            <span class="font-medium text-purple-600 dark:text-purple-400">{{ __(@$withdraw->method->name) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">@lang('Gateway')</p>
                                    @endif
                                </div>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <p class="font-medium">{{ showDateTime($withdraw->created_at, 'd M Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($withdraw->created_at, 'h:i A') }}</p>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @php echo $withdraw->statusBadge @endphp
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <a href="{{ route('user.withdraw.details', $withdraw->trx) }}" 
                                   class="inline-flex items-center px-3 py-2 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/30 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 rounded-lg transition-colors duration-200">
                                    <i class="las la-eye mr-1"></i>
                                    @lang('View Details')
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="las la-inbox text-gray-400 dark:text-gray-500 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No withdrawals found')</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __($emptyMessage) }}</p>
                                    <a href="{{ route('user.withdraw') }}" 
                                       class="inline-flex items-center bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg">
                                        <i class="las la-plus mr-2"></i>
                                        @lang('Make Your First Withdrawal')
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enhanced Pagination -->
    @if ($withdraws->hasPages())
        <div class="mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <!-- Results Info -->
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        @lang('Showing') 
                        <span class="font-medium text-gray-900 dark:text-white">{{ $withdraws->firstItem() }}</span>
                        @lang('to') 
                        <span class="font-medium text-gray-900 dark:text-white">{{ $withdraws->lastItem() }}</span>
                        @lang('of') 
                        <span class="font-medium text-gray-900 dark:text-white">{{ $withdraws->total() }}</span>
                        @lang('results')
                    </div>

                    <!-- Pagination Links -->
                    <div class="flex items-center space-x-2">
                        <nav class="flex items-center space-x-1">
                            <!-- Previous Page Link -->
                            @if ($withdraws->onFirstPage())
                                <span class="px-3 py-2 text-sm text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-700 rounded-lg cursor-not-allowed">
                                    <i class="las la-angle-left"></i>
                                </span>
                            @else
                                <a href="{{ $withdraws->previousPageUrl() }}" 
                                   class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                    <i class="las la-angle-left"></i>
                                </a>
                            @endif

                            <!-- Page Numbers -->
                            @foreach ($withdraws->getUrlRange(max(1, $withdraws->currentPage() - 2), min($withdraws->lastPage(), $withdraws->currentPage() + 2)) as $page => $url)
                                @if ($page == $withdraws->currentPage())
                                    <span class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" 
                                       class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            <!-- Next Page Link -->
                            @if ($withdraws->hasMorePages())
                                <a href="{{ $withdraws->nextPageUrl() }}" 
                                   class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                    <i class="las la-angle-right"></i>
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-700 rounded-lg cursor-not-allowed">
                                    <i class="las la-angle-right"></i>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('style')
<style>
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .badge--success {
        background-color: rgb(220, 252, 231);
        color: rgb(22, 101, 52);
    }
    
    .dark .badge--success {
        background-color: rgba(34, 197, 94, 0.1);
        color: rgb(134, 239, 172);
    }
    
    .badge--warning {
        background-color: rgb(254, 243, 199);
        color: rgb(146, 64, 14);
    }
    
    .dark .badge--warning {
        background-color: rgba(245, 158, 11, 0.1);
        color: rgb(252, 211, 77);
    }
    
    .badge--danger {
        background-color: rgb(254, 226, 226);
        color: rgb(153, 27, 27);
    }
    
    .dark .badge--danger {
        background-color: rgba(239, 68, 68, 0.1);
        color: rgb(248, 113, 113);
    }
    
    /* Filter panel animation */
    .filter-panel-enter {
        opacity: 0;
        transform: translateY(-10px);
    }
    
    .filter-panel-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: all 0.3s ease;
    }
    
    .filter-panel-exit {
        opacity: 1;
        transform: translateY(0);
    }
    
    .filter-panel-exit-active {
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('script')
<script>
    "use strict";
    (function($) {
        // Filter Panel Toggle
        $('#filterBtn').on('click', function() {
            const $panel = $('#filterPanel');
            const $btn = $(this);
            
            if ($panel.hasClass('hidden')) {
                $panel.removeClass('hidden').addClass('filter-panel-enter');
                setTimeout(() => {
                    $panel.removeClass('filter-panel-enter').addClass('filter-panel-enter-active');
                }, 10);
                $btn.find('i').removeClass('la-filter').addClass('la-times');
            } else {
                $panel.addClass('filter-panel-exit-active');
                setTimeout(() => {
                    $panel.removeClass('filter-panel-enter-active filter-panel-exit-active').addClass('hidden');
                }, 300);
                $btn.find('i').removeClass('la-times').addClass('la-filter');
            }
        });
        
        // Clear Search Function
        window.clearSearch = function() {
            $('input[name="search"]').val('');
            $('#searchForm').submit();
        };
        
        // Clear Filters Function
        window.clearFilters = function() {
            $('#filterPanel select').val('');
            $('#filterPanel input[type="date"]').val('');
            $('#searchForm').submit();
        };
        
        // Export PDF Function
        $('#exportBtn').on('click', function() {
            const $btn = $(this);
            const originalText = $btn.html();
            
            // Show loading state
            $btn.prop('disabled', true).html('<i class="las la-spinner la-spin mr-2"></i>@lang("Generating PDF...")');
            
            // Get current filters
            const searchParams = new URLSearchParams();
            
            // Add current form values to export
            $('#searchForm').find('input, select').each(function() {
                if ($(this).val() && $(this).attr('name')) {
                    searchParams.append($(this).attr('name'), $(this).val());
                }
            });
            
            // Use the new export PDF route
            const exportUrl = '{{ route("user.withdraw.export.pdf") }}?' + searchParams.toString();
            
            // Use fetch API for better error handling
            fetch(exportUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/pdf'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('content-type'));
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Check if the response is actually a PDF
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/pdf')) {
                    // If not PDF, try to read as text to see the error
                    return response.text().then(text => {
                        console.log('Non-PDF response:', text.substring(0, 500));
                        throw new Error('Response is not a PDF. Got: ' + contentType);
                    });
                }
                
                return response.blob();
            })
            .then(blob => {
                console.log('Blob size:', blob.size, 'Blob type:', blob.type);
                
                // Check if blob is valid
                if (blob.size === 0) {
                    throw new Error('PDF blob is empty');
                }
                
                // Create blob link to download
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'withdrawal-statement-' + new Date().toISOString().split('T')[0] + '.pdf';
                document.body.appendChild(link);
                link.click();
                
                // Clean up
                window.URL.revokeObjectURL(url);
                document.body.removeChild(link);
                
                // Restore button state
                $btn.prop('disabled', false).html(originalText);
                
                // Show success message
                showToast('success', '@lang("PDF downloaded successfully!")');
            })
            .catch(error => {
                console.error('Export error:', error);
                
                // Restore button state
                $btn.prop('disabled', false).html(originalText);
                
                // Show detailed error message
                if (error.message.includes('not a PDF')) {
                    showToast('error', '@lang("Server returned invalid PDF. Check backend configuration.")');
                } else if (error.message.includes('HTTP error')) {
                    showToast('error', '@lang("Server error occurred. Please try again later.")');
                } else {
                    showToast('error', '@lang("Export failed: ") ' + error.message);
                }
                
                // Optional: Fallback to opening in new window for debugging
                console.log('Opening URL in new window for debugging:', exportUrl);
                window.open(exportUrl, '_blank');
            });
        });
        
        // Auto-submit on Enter key
        $('#searchForm input[name="search"]').on('keypress', function(e) {
            if (e.which === 13) {
                $('#searchForm').submit();
            }
        });
        
        // Show loading state on form submit
        $('#searchForm').on('submit', function() {
            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="las la-spinner la-spin mr-2"></i>@lang("Searching...")');
        });
        
        // Toast notification function
        function showToast(type, message) {
            const bgColor = type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500';
            const icon = type === 'error' ? 'la-exclamation-triangle' : type === 'success' ? 'la-check-circle' : 'la-info-circle';
            
            const toast = $(`
                <div class="fixed top-4 right-4 z-50 ${bgColor} text-white px-6 py-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300">
                    <div class="flex items-center space-x-3">
                        <i class="las ${icon} text-xl"></i>
                        <span class="font-medium">${message}</span>
                    </div>
                </div>
            `);
            
            $('body').append(toast);
            
            setTimeout(() => {
                toast.removeClass('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                toast.addClass('translate-x-full');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }
        
        // Load withdrawal methods for filter dropdown
        loadWithdrawMethods();
        
        function loadWithdrawMethods() {
            // This would typically be loaded from the controller or via AJAX
            // For now, we'll populate with common methods
            const methods = [
                { id: 'bank', name: '@lang("Bank Transfer")' },
                { id: 'paypal', name: '@lang("PayPal")' },
                { id: 'crypto', name: '@lang("Cryptocurrency")' },
                { id: 'mobile', name: '@lang("Mobile Money")' }
            ];
            
            const $methodSelect = $('select[name="method"]');
            const currentMethod = '{{ request("method") }}';
            
            methods.forEach(method => {
                const selected = currentMethod === method.id ? 'selected' : '';
                $methodSelect.append(`<option value="${method.id}" ${selected}>${method.name}</option>`);
            });
        }
        
    })(jQuery);
</script>
@endpush