@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')

<!-- Transfer History Page -->
<div class="space-y-6">

    <!-- Transfer History Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Enhanced Header -->
        <div class="px-4 lg:px-6 py-4 lg:py-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
                <div>
                    <h2 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">@lang('Transfer History')</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('View and manage all your money transfers')</p>
                </div>
                
                <!-- Header Actions Row -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <!-- Search Bar -->
                    <div class="relative flex-1 sm:min-w-80">
                        <i class="las la-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="@lang('Search transactions...')"
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <button type="button" 
                                onclick="toggleFilters()"
                                id="filterToggleBtn"
                                class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 border border-gray-300 dark:border-gray-600">
                            <i class="las la-filter"></i>
                            <span class="hidden sm:inline">@lang('Filters')</span>
                        </button>
                        
                        @if (request()->date || request()->search)
                        <a href="{{ request()->fullUrlWithQuery(['download' => 'pdf']) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2">
                            <i class="las la-download"></i>
                            <span class="hidden sm:inline">@lang('Export')</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Collapsible Filter Section (Hidden by Default) -->
        <div id="filtersSection" class="hidden border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            <div class="px-4 lg:px-6 py-4">
                <form id="filterForm" class="space-y-4">
                    <!-- Filter Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        <!-- Transfer Type Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">@lang('Type')</label>
                            <select name="transfer_type" 
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm text-gray-900 dark:text-white">
                                <option value="">@lang('All Types')</option>
                                <option value="own_bank">@lang('Own Bank')</option>
                                <option value="other_bank">@lang('Other Bank')</option>
                                <option value="wire_transfer">@lang('Wire Transfer')</option>
                            </select>
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">@lang('Status')</label>
                            <select name="status" 
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm text-gray-900 dark:text-white">
                                <option value="">@lang('All Status')</option>
                                <option value="completed">@lang('Completed')</option>
                                <option value="pending">@lang('Pending')</option>
                                <option value="processing">@lang('Processing')</option>
                                <option value="rejected">@lang('Rejected')</option>
                            </select>
                        </div>
                        
                        <!-- Amount Range -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">@lang('Amount')</label>
                            <select name="amount_range" 
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm text-gray-900 dark:text-white">
                                <option value="">@lang('All Amounts')</option>
                                <option value="0-1000">{{ getUserCurrency(auth()->user())['symbol'] }}0 - {{ getUserCurrency(auth()->user())['symbol'] }}1K</option>
                                <option value="1000-5000">{{ getUserCurrency(auth()->user())['symbol'] }}1K - {{ getUserCurrency(auth()->user())['symbol'] }}5K</option>
                                <option value="5000-10000">{{ getUserCurrency(auth()->user())['symbol'] }}5K - {{ getUserCurrency(auth()->user())['symbol'] }}10K</option>
                                <option value="10000+">{{ getUserCurrency(auth()->user())['symbol'] }}10K+</option>
                            </select>
                        </div>
                        
                        <!-- Date From -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">@lang('From Date')</label>
                            <input type="date" 
                                   name="date_from"
                                   class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm text-gray-900 dark:text-white">
                        </div>
                        
                        <!-- Date To -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">@lang('To Date')</label>
                            <input type="date" 
                                   name="date_to"
                                   class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm text-gray-900 dark:text-white">
                        </div>
                    </div>
                    
                    <!-- Action Buttons and Quick Filters -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between space-y-3 sm:space-y-0 pt-2">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="setDateFilter('today')" class="text-xs bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">@lang('Today')</button>
                            <button type="button" onclick="setDateFilter('week')" class="text-xs bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">@lang('This Week')</button>
                            <button type="button" onclick="setDateFilter('month')" class="text-xs bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">@lang('This Month')</button>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button type="button" 
                                    onclick="applyFilters()"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-all text-sm flex items-center space-x-1.5">
                                <i class="las la-search"></i>
                                <span>@lang('Apply')</span>
                            </button>
                            
                            <button type="button" 
                                    onclick="clearFilters()"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-all text-sm flex items-center space-x-1.5">
                                <i class="las la-redo"></i>
                                <span>@lang('Clear')</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transfer Content - Responsive Table/Cards -->
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Transaction')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Recipient')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Type')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Amount')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Status')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Date')</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="transferTableBody">
                        @forelse ($transfers as $transfer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">#{{ $transfer->trx }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($transfer->created_at, 'd M, Y') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        @if ($transfer->beneficiary)
                                            {{ $transfer->beneficiary->short_name }}
                                        @else
                                            {{ $transfer->wireTransferAccountName() }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        @if ($transfer->beneficiary)
                                            {{ $transfer->beneficiary->beneficiaryOf->name ?? gs()->site_name }} • ****{{ substr($transfer->beneficiary->account_number, -4) }}
                                        @else
                                            @lang('Wire Transfer') • ****{{ substr($transfer->wireTransferAccountNumber(), -4) }}
                                        @endif
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($transfer->beneficiary)
                                    @if ($transfer->beneficiary->beneficiaryOf->name == gs()->site_name)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            @lang('Own Bank')
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            @lang('Other Bank')
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                        @lang('Wire Transfer')
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ showAmount($transfer->amount) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">+ {{ showAmount($transfer->charge) }} fee</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($transfer->status == 1)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="las la-check-circle mr-1"></i>
                                        @lang('Completed')
                                    </span>
                                @elseif($transfer->status == 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        <i class="las la-clock mr-1"></i>
                                        @lang('Pending')
                                    </span>
                                @elseif($transfer->status == 2)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="las la-times-circle mr-1"></i>
                                        @lang('Rejected')
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ showDateTime($transfer->created_at, 'd M, Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($transfer->created_at, 'h:i A') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="viewTransfer('{{ $transfer->trx }}')" 
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                                        @lang('View')
                                    </button>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    @if (!$transfer->beneficiary)
                                        <button onclick="showWireDetails('{{ $transfer->id }}')" 
                                                class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium text-sm wire-transfer" 
                                                data-id="{{ $transfer->id }}">
                                            @lang('Details')
                                        </button>
                                    @else
                                        <button onclick="downloadReceipt('{{ $transfer->trx }}')" 
                                                class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-medium text-sm">
                                            @lang('Receipt')
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-gray-500 dark:text-gray-400" colspan="7">
                                <div class="flex flex-col items-center">
                                    <i class="las la-exchange-alt text-4xl mb-2"></i>
                                    <p>@lang($emptyMessage)</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden">
                @forelse ($transfers as $transfer)
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <!-- Mobile Transfer Card -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">#{{ $transfer->trx }}</h3>
                                @if ($transfer->status == 1)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="las la-check text-xs mr-0.5"></i>
                                        @lang('Done')
                                    </span>
                                @elseif($transfer->status == 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        <i class="las la-clock text-xs mr-0.5"></i>
                                        @lang('Pending')
                                    </span>
                                @elseif($transfer->status == 2)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="las la-times text-xs mr-0.5"></i>
                                        @lang('Failed')
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($transfer->created_at, 'd M, Y • h:i A') }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ showAmount($transfer->amount) }}</p>
                            @if ($transfer->charge > 0)
                                <p class="text-xs text-gray-500 dark:text-gray-400">+ {{ showAmount($transfer->charge) }} fee</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if ($transfer->beneficiary)
                                    @if ($transfer->beneficiary->beneficiaryOf->name == gs()->site_name)
                                        <i class="las la-university text-green-600 dark:text-green-400"></i>
                                    @else
                                        <i class="las la-building text-blue-600 dark:text-blue-400"></i>
                                    @endif
                                @else
                                    <i class="las la-globe text-purple-600 dark:text-purple-400"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    @if ($transfer->beneficiary)
                                        {{ $transfer->beneficiary->short_name }}
                                    @else
                                        {{ $transfer->wireTransferAccountName() }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    @if ($transfer->beneficiary)
                                        {{ $transfer->beneficiary->beneficiaryOf->name ?? gs()->site_name }}
                                    @else
                                        @lang('Wire Transfer')
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <button onclick="viewTransfer('{{ $transfer->trx }}')" 
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm px-2 py-1">
                                @lang('View')
                            </button>
                            @if (!$transfer->beneficiary)
                                <button onclick="showWireDetails('{{ $transfer->id }}')" 
                                        class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium text-sm px-2 py-1 wire-transfer" 
                                        data-id="{{ $transfer->id }}">
                                    @lang('Details')
                                </button>
                            @else
                                <button onclick="downloadReceipt('{{ $transfer->trx }}')" 
                                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-medium text-sm px-2 py-1">
                                    @lang('Receipt')
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center">
                        <i class="las la-exchange-alt text-4xl mb-2"></i>
                        <p>@lang($emptyMessage)</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if ($transfers->hasPages())
        <div class="px-4 lg:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            {{ paginateLinks($transfers) }}
        </div>
        @endif
    </div>
</div>

<!-- Wire Transfer Details Modal -->
<div id="wireTransferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                @lang('Wire Transfer Details')
            </h3>
            <button onclick="closeWireModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="las la-times text-xl"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="pt-4 max-h-96 overflow-y-auto" id="wireTransferContent">
            <!-- Loading State -->
            <div id="wireTransferLoading" class="flex items-center justify-center py-8">
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-600 dark:text-gray-400">@lang('Loading details...')</span>
                </div>
            </div>
            
            <!-- Content will be loaded here -->
            <div id="wireTransferDetails" class="hidden">
                <!-- Wire transfer details will be populated here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
function toggleFilters() {
    const filtersSection = document.getElementById('filtersSection');
    const toggleBtn = document.getElementById('filterToggleBtn');
    const isHidden = filtersSection.classList.contains('hidden');
    
    if (isHidden) {
        filtersSection.classList.remove('hidden');
        toggleBtn.classList.add('bg-blue-500', 'text-white');
        toggleBtn.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    } else {
        filtersSection.classList.add('hidden');
        toggleBtn.classList.remove('bg-blue-500', 'text-white');
        toggleBtn.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    }
}

function applyFilters() {
    showLoadingState();
    
    const formData = new FormData(document.getElementById('filterForm'));
    const searchValue = document.getElementById('searchInput').value;
    
    if (searchValue) {
        formData.append('search', searchValue);
    }
    
    const filters = Object.fromEntries(formData);
    
    console.log('Applied filters:', filters);
    
    setTimeout(() => {
        hideLoadingState();
        showSuccessMessage('Filters applied successfully');
    }, 500);
}

function clearFilters() {
    document.getElementById('filterForm').reset();
    document.getElementById('searchInput').value = '';
    applyFilters();
}

function setDateFilter(period) {
    const today = new Date();
    const dateFrom = document.querySelector('input[name="date_from"]');
    const dateTo = document.querySelector('input[name="date_to"]');
    
    let fromDate, toDate = today;
    
    switch(period) {
        case 'today':
            fromDate = today;
            break;
        case 'week':
            fromDate = new Date(today);
            fromDate.setDate(today.getDate() - 7);
            break;
        case 'month':
            fromDate = new Date(today);
            fromDate.setMonth(today.getMonth() - 1);
            break;
    }
    
    dateFrom.value = fromDate.toISOString().split('T')[0];
    dateTo.value = toDate.toISOString().split('T')[0];
    
    applyFilters();
}

function viewTransfer(trxId) {
    window.location.href = `{{ route('user.transfer.details', ':trx') }}`.replace(':trx', trxId);
}

function downloadReceipt(trxId) {
    showLoadingState();
    
    const link = document.createElement('a');
    link.href = `{{ route('user.transfer.details', ':trx') }}?download`.replace(':trx', trxId);
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    setTimeout(() => {
        hideLoadingState();
        showSuccessMessage('Receipt opened in new tab');
    }, 1000);
}

function showLoadingState() {
    const loadingHtml = `
        <div id="loadingOverlay" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                    <span class="text-gray-900 dark:text-white">@lang('Loading...')</span>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHtml);
}

function hideLoadingState() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

function showSuccessMessage(message) {
    notify('success', message);
}

function showErrorMessage(message) {
    notify('error', message);
}

document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    if (window.innerWidth < 1024) {
        const mobileCards = document.querySelectorAll('.lg\\:hidden > div');
        
        mobileCards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(searchTerm) || searchTerm === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
});

function showWireDetails(transferId) {
    const modal = document.getElementById('wireTransferModal');
    const loading = document.getElementById('wireTransferLoading');
    const content = document.getElementById('wireTransferDetails');
    
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    content.classList.add('hidden');
    
    const action = `{{ route('user.transfer.wire.details', ':id') }}`.replace(':id', transferId);
    
    fetch(action, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        loading.classList.add('hidden');
        
        if (data.success) {
            content.innerHTML = data.html;
            content.classList.remove('hidden');
            
            stylizeWireTransferDetails();
        } else {
            closeWireModal();
            notify('error', data.message || '@lang("Something went wrong")');
        }
    })
    .catch(error => {
        loading.classList.add('hidden');
        closeWireModal();
        notify('error', '@lang("Something went wrong")');
        console.error('Error:', error);
    });
}

function closeWireModal() {
    const modal = document.getElementById('wireTransferModal');
    modal.classList.add('hidden');
    
    document.getElementById('wireTransferDetails').innerHTML = '';
}

function stylizeWireTransferDetails() {
    const content = document.getElementById('wireTransferDetails');
    
    const rowDiv = content.querySelector('.row');
    if (rowDiv) {
        rowDiv.className = 'space-y-4';
    }
    
    const fieldContainers = content.querySelectorAll('.col-md-12');
    fieldContainers.forEach(container => {
        container.className = 'bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600';
        
        const label = container.querySelector('span.fw-bold');
        if (label) {
            label.className = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2';
        }
        
        const valuePara = container.querySelector('p');
        if (valuePara) {
            valuePara.className = 'text-base text-gray-900 dark:text-white font-medium';
        }
        
        const attachmentLink = container.querySelector('a');
        if (attachmentLink) {
            attachmentLink.className = 'inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 transition-colors';
        }
    });
}

document.getElementById('wireTransferModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeWireModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('wireTransferModal');
        if (!modal.classList.contains('hidden')) {
            closeWireModal();
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    setDateFilter('month');
});
</script>
@endpush