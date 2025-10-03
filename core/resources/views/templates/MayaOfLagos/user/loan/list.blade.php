@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- Loan List Page -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-6 lg:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">@lang('My Loans')</h1>
                    <p class="mt-1 lg:mt-2 text-sm text-gray-600 dark:text-gray-400">@lang('Track and manage all your loan applications and active loans')</p>
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    @if (request()->date || request()->search)
                        <a href="{{ request()->fullUrlWithQuery(['download' => 'pdf']) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="las la-file-pdf mr-2 text-lg"></i>
                            <span class="hidden sm:inline">@lang('Download PDF')</span>
                            <span class="sm:hidden">@lang('PDF')</span>
                        </a>
                    @endif
                    <a href="{{ route('user.loan.plans') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="las la-plus mr-2 text-lg"></i>
                        <span class="hidden sm:inline">@lang('Apply for Loan')</span>
                        <span class="sm:hidden">@lang('Apply')</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div x-data="{ searchOpen: false }" class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
            <!-- Mobile Toggle Header -->
            <div class="lg:hidden flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">@lang('Search & Filter')</h3>
                <button type="button" 
                        @click="searchOpen = !searchOpen; $nextTick(() => { if (searchOpen) { document.getElementById('search').focus(); } })"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="las la-filter text-xl" x-show="!searchOpen"></i>
                    <i class="las la-times text-xl" x-show="searchOpen" style="display: none;"></i>
                </button>
            </div>
            
            <!-- Search Form -->
            <div x-show="searchOpen || window.innerWidth >= 1024" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="lg:block p-4 lg:p-8"
                 style="display: none;">
                <form method="GET" class="space-y-4 lg:space-y-0 lg:flex lg:items-end lg:space-x-4">
                    <!-- Search Input -->
                    <div class="flex-1 lg:max-w-md">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Search Loans')</label>
                        <div class="relative">
                            <input type="text" 
                                   id="search"
                                   name="search" 
                                   value="{{ request()->search }}"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white pl-10" 
                                   placeholder="@lang('Search by loan number...')">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="las la-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                <!-- Date Range -->
                <div class="flex-1 lg:max-w-md">
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Date Range')</label>
                    <div class="relative">
                        <input type="text" 
                               id="date"
                               name="date" 
                               value="{{ request()->date }}"
                               class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white pr-10 cursor-pointer" 
                               placeholder="@lang('Select date range')"
                               autocomplete="off">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="las la-calendar text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="lg:max-w-xs">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Status')</label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white">
                        <option value="">@lang('All Status')</option>
                        <option value="0" {{ request()->status == '0' ? 'selected' : '' }}>@lang('Pending')</option>
                        <option value="1" {{ request()->status == '1' ? 'selected' : '' }}>@lang('Running')</option>
                        <option value="2" {{ request()->status == '2' ? 'selected' : '' }}>@lang('Paid')</option>
                        <option value="3" {{ request()->status == '3' ? 'selected' : '' }}>@lang('Rejected')</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div class="flex space-x-2">
                    <button type="submit" 
                            id="searchBtn"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center space-x-2 disabled:bg-blue-400 disabled:cursor-not-allowed">
                        <i class="las la-search" id="searchIcon"></i>
                        <i class="las la-spinner la-spin hidden" id="loadingIcon"></i>
                        <span id="searchText">@lang('Search')</span>
                    </button>
                    @if(request()->search || request()->date || request()->status)
                        <a href="{{ route('user.loan.list') }}" 
                           class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center space-x-2">
                            <i class="las la-times"></i>
                            <span>@lang('Clear')</span>
                        </a>
                    @endif
                </div>
            </form>
            </div>
        </div>

        <!-- Loans Table/Cards -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Loan Details')</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Amount & Rate')</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Installments')</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Next Payment')</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Status')</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($loans as $loan)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <!-- Loan Details -->
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">#{{ $loan->loan_number }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $loan->plan->name ?? 'N/A' }}</div>
                                    </div>
                                </td>

                                <!-- Amount & Rate -->
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ showUserAmount($loan->amount, auth()->user()) }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ getAmount($loan->interestRate()) }}% @lang('Rate')</div>
                                    </div>
                                </td>

                                <!-- Installments -->
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ showUserAmount($loan->per_installment, auth()->user()) }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $loan->given_installment }}/{{ $loan->total_installment }}</div>
                                    </div>
                                </td>

                                <!-- Next Payment -->
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        @if ($loan->nextInstallment)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ showDateTime($loan->nextInstallment->installment_date, 'd M, Y') }}</div>
                                            <div class="text-gray-500 dark:text-gray-400">{{ showUserAmount($loan->payable_amount, auth()->user()) }} @lang('total')</div>
                                        @else
                                            <span class="text-gray-400">@lang('N/A')</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="flex items-center px-6 py-4">
                                    <div>
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch($loan->status) {
                                                case 0: // LOAN_PENDING
                                                    $statusClass = 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                                                    $statusText = 'Pending';
                                                    break;
                                                case 1: // LOAN_RUNNING
                                                    $statusClass = 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700';
                                                    $statusText = 'Running';
                                                    break;
                                                case 2: // LOAN_PAID
                                                    $statusClass = 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700';
                                                    $statusText = 'Paid';
                                                    break;
                                                case 3: // LOAN_REJECTED
                                                    $statusClass = 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700';
                                                    $statusText = 'Rejected';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                                                    $statusText = 'Unknown';
                                            }
                                        @endphp
                                        <span class="flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-200 {{ $statusClass }}">
                                            <i class="las la-circle text-xs mr-1"></i>
                                            @lang($statusText)
                                        </span>
                                    </div>
                                    @if ($loan->status == 3)
                                        <button class="admin-feedback mt-1 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-lg p-1.5 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-all relative group" 
                                                @click="$dispatch('open-rejection-modal', { feedback: '{{ addslashes(__($loan->admin_feedback)) }}' })"
                                                title="@lang('View rejection reason')">
                                            <i class="las la-exclamation-circle text-lg"></i>
                                            <!-- Custom Tooltip -->
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 dark:bg-gray-700 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                @lang('View rejection reason')
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                            </div>
                                        </button>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('user.loan.details', $loan->loan_number) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-lg transition-colors">
                                            <i class="las la-eye mr-1"></i>
                                            @lang('Details')
                                        </a>
                                        
                                        @if($loan->nextInstallment)
                                            <a href="{{ route('user.loan.instalment.logs', $loan->loan_number) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 text-xs font-medium rounded-lg transition-colors">
                                                <i class="las la-list mr-1"></i>
                                                @lang('Installments')
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="las la-hand-holding-usd text-4xl text-gray-400 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No Loans Found')</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('You haven\'t applied for any loans yet.')</p>
                                        <a href="{{ route('user.loan.plans') }}" 
                                           class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                            <i class="las la-plus mr-2"></i>
                                            @lang('Apply for Loan')
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden p-4 space-y-4">
                @forelse($loans as $loan)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 dark:text-white">#{{ $loan->loan_number }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $loan->plan->name ?? 'N/A' }}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($loan->status) {
                                        case 0: // LOAN_PENDING
                                            $statusClass = 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                                            $statusText = 'Pending';
                                            break;
                                        case 1: // LOAN_RUNNING
                                            $statusClass = 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700';
                                            $statusText = 'Running';
                                            break;
                                        case 2: // LOAN_PAID
                                            $statusClass = 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700';
                                            $statusText = 'Paid';
                                            break;
                                        case 3: // LOAN_REJECTED
                                            $statusClass = 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700';
                                            $statusText = 'Rejected';
                                            break;
                                        default:
                                            $statusClass = 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                                            $statusText = 'Unknown';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border shadow-sm {{ $statusClass }}">
                                    <i class="las la-circle text-xs mr-1"></i>
                                    @lang($statusText)
                                </span>
                                
                                <!-- Rejection Icon (Mobile Only) -->
                                @if ($loan->status == 3)
                                    <button class="admin-feedback relative group p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-all" 
                                            @click="$dispatch('open-rejection-modal', { feedback: '{{ addslashes(__($loan->admin_feedback)) }}' })"
                                            title="@lang('View rejection reason')">
                                        <i class="las la-exclamation-circle text-lg"></i>
                                        <!-- Enhanced Mobile Tooltip -->
                                        <div class="absolute bottom-full right-0 mb-2 px-2 py-1 text-xs text-white bg-gray-900 dark:bg-gray-700 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                            @lang('View rejection reason')
                                            <div class="absolute top-full right-2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                        </div>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Essential Details Grid (Reduced Items) -->
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs">@lang('Amount')</span>
                                <div class="font-medium text-gray-900 dark:text-white">{{ showUserAmount($loan->amount, auth()->user()) }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs">@lang('Installment')</span>
                                <div class="font-medium text-gray-900 dark:text-white">{{ showUserAmount($loan->per_installment, auth()->user()) }}</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400">@lang('Progress')</span>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $loan->given_installment }}/{{ $loan->total_installment }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                @php
                                    $progress = $loan->total_installment > 0 ? ($loan->given_installment / $loan->total_installment) * 100 : 0;
                                @endphp
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <!-- Next Payment (Condensed) -->
                        @if ($loan->nextInstallment)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-2">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-xs text-blue-600 dark:text-blue-400">@lang('Next Payment')</div>
                                        <div class="font-medium text-blue-900 dark:text-blue-300 text-sm">{{ showDateTime($loan->nextInstallment->installment_date, 'd M') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-blue-600 dark:text-blue-400">@lang('Amount')</div>
                                        <div class="font-medium text-blue-900 dark:text-blue-300 text-sm">{{ showUserAmount($loan->payable_amount, auth()->user()) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Actions (Compact) -->
                        <div class="flex space-x-2 pt-1">
                            <a href="{{ route('user.loan.details', $loan->loan_number) }}" 
                               class="flex-1 text-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg transition-colors">
                                <i class="las la-eye mr-1"></i>@lang('View')
                            </a>
                            
                            @if($loan->nextInstallment)
                                <a href="{{ route('user.loan.instalment.logs', $loan->loan_number) }}" 
                                   class="flex-1 text-center px-3 py-2 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 text-sm font-medium rounded-lg transition-colors">
                                    <i class="las la-list mr-1"></i>@lang('Log')
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="flex flex-col items-center">
                            <i class="las la-hand-holding-usd text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No Loans Found')</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">@lang('You haven\'t applied for any loans yet.')</p>
                            <a href="{{ route('user.loan.plans') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <i class="las la-plus mr-2"></i>
                                @lang('Apply for Loan')
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($loans->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    {{ paginateLinks($loans) }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Admin Feedback Modal (Alpine.js) -->
<div x-data="{ 
    open: false, 
    feedback: '',
    openModal(event) {
        this.feedback = event.detail.feedback;
        this.open = true;
    }
}" 
     @open-rejection-modal.window="openModal($event)"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
         @click="open = false"
         x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full" 
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @click.stop>
            
            <!-- Header -->
            <div class="bg-red-500 text-white rounded-t-2xl px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">
                        <i class="las la-exclamation-triangle mr-2"></i>
                        @lang('Loan Rejection Reason')
                    </h3>
                    <button @click="open = false" class="text-white hover:text-gray-200 transition-colors">
                        <i class="las la-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="las la-times text-red-600 dark:text-red-400 text-lg"></i>
                    </div>
                    <div class="text-gray-700 dark:text-gray-300" x-text="feedback"></div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 rounded-b-2xl">
                <button @click="open = false" 
                        class="w-full px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors">
                    @lang('Close')
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script-lib')
    <script src="{{ asset('assets/global/js/vendor/flatpickr.js') }}"></script>
    <!-- Fallback CDN -->
    <script>
        if (typeof flatpickr === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"><\/script>');
        }
    </script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/vendor/flatpickr.css') }}">
    <!-- Fallback CDN CSS -->
    <style>
        @import url('https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css');
    </style>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    
    // Wait for DOM to be ready
    $(document).ready(function() {
        console.log('DOM ready, initializing date picker...');
        
        // Check and clean existing date value
        const existingDate = $('#date').val();
        if (existingDate && existingDate.length > 0) {
            console.log('Existing date value:', existingDate);
            // Validate existing date format (should be "MMMM DD, YYYY - MMMM DD, YYYY")
            if (!existingDate.includes(' - ') || existingDate.length < 25) {
                console.log('Invalid existing date format, clearing...');
                $('#date').val('');
            } else {
                const dateParts = existingDate.split(' - ');
                if (dateParts.length !== 2) {
                    console.log('Invalid existing date format, clearing...');
                    $('#date').val('');
                }
            }
        }
        
        // Initialize date picker
        const dateInput = document.getElementById('date');
        if (dateInput) {
            console.log('Date input found');
            
            // Check if Flatpickr is available
            if (typeof flatpickr !== 'undefined') {
                console.log('Flatpickr is available, initializing...');
                
                try {
                    const fp = flatpickr("#date", {
                        mode: "range",
                        dateFormat: "F j, Y",
                        maxDate: "today",
                        allowInput: true,
                        clickOpens: true,
                        locale: {
                            rangeSeparator: " - "
                        },
                        onChange: function(selectedDates, dateStr, instance) {
                            console.log('Date selected:', dateStr, 'Selected dates:', selectedDates);
                            // Validate date range before setting value
                            if (selectedDates.length === 2) {
                                const start = selectedDates[0];
                                const end = selectedDates[1];
                                
                                // Format to match backend expectations: "MMMM DD, YYYY - MMMM DD, YYYY"
                                const formatOptions = { year: 'numeric', month: 'long', day: '2-digit' };
                                const formattedStart = start.toLocaleDateString('en-US', formatOptions);
                                const formattedEnd = end.toLocaleDateString('en-US', formatOptions);
                                const finalDateStr = formattedStart + ' - ' + formattedEnd;
                                
                                $('#date').val(finalDateStr);
                                console.log('Formatted date range:', finalDateStr);
                            } else if (selectedDates.length === 1) {
                                // Single date selected, wait for second date
                                console.log('Single date selected, waiting for range completion');
                            }
                        },
                        onClose: function(selectedDates, dateStr, instance) {
                            console.log('Date picker closed:', dateStr, 'Selected dates:', selectedDates);
                            // Only set value if we have a complete range
                            if (selectedDates.length === 2) {
                                const start = selectedDates[0];
                                const end = selectedDates[1];
                                
                                // Format to match backend expectations: "MMMM DD, YYYY - MMMM DD, YYYY"
                                const formatOptions = { year: 'numeric', month: 'long', day: '2-digit' };
                                const formattedStart = start.toLocaleDateString('en-US', formatOptions);
                                const formattedEnd = end.toLocaleDateString('en-US', formatOptions);
                                const finalDateStr = formattedStart + ' - ' + formattedEnd;
                                
                                $('#date').val(finalDateStr);
                                console.log('Final date range set:', finalDateStr);
                            } else if (selectedDates.length === 1) {
                                // Single date, clear the input to prevent partial submission
                                $('#date').val('');
                                console.log('Incomplete range, cleared input');
                            }
                        },
                        onReady: function(selectedDates, dateStr, instance) {
                            console.log('Flatpickr ready');
                        }
                    });
                    
                    console.log('Flatpickr initialized successfully');
                    
                    // Remove readonly attribute to make it clickable
                    $('#date').removeAttr('readonly');
                    
                } catch (error) {
                    console.error('Error initializing Flatpickr:', error);
                    // Fallback
                    $('#date').removeAttr('readonly').attr('placeholder', 'September 22, 2025 - September 24, 2025');
                }
                } else {
                    console.warn('Flatpickr not loaded, falling back to manual input');
                    $('#date').removeAttr('readonly').attr('placeholder', 'September 22, 2025 - September 24, 2025');
                }            // Additional fallback: If Flatpickr still doesn't work, create HTML5 date inputs
            setTimeout(function() {
                if (!$('#date').hasClass('flatpickr-input')) {
                    console.log('Flatpickr did not initialize, creating HTML5 fallback');
                    createHTML5DateFallback();
                }
            }, 1000);
            
        } else {
            console.error('Date input not found!');
        }
        
        // Function to create HTML5 date input fallback
        function createHTML5DateFallback() {
            const container = $('#date').parent();
            const currentValue = $('#date').val();
            
            // Parse current value if it exists
            let startDate = '', endDate = '';
            if (currentValue && currentValue.includes(' - ')) {
                const dates = currentValue.split(' - ');
                if (dates.length === 2) {
                    // Convert from "MMMM DD, YYYY" to "YYYY-MM-DD" for HTML5 input
                    try {
                        const start = new Date(dates[0]);
                        const end = new Date(dates[1]);
                        if (!isNaN(start.getTime()) && !isNaN(end.getTime())) {
                            startDate = start.toISOString().split('T')[0];
                            endDate = end.toISOString().split('T')[0];
                        }
                    } catch (error) {
                        console.log('Error parsing existing dates:', error);
                    }
                }
            }
            
            // Create HTML5 date inputs
            const fallbackHTML = `
                <div class="flex space-x-2">
                    <input type="date" 
                           name="start_date" 
                           id="start_date"
                           value="${startDate}"
                           max="{{ date('Y-m-d') }}"
                           class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white text-sm">
                    <span class="self-center text-gray-500 dark:text-gray-400">to</span>
                    <input type="date" 
                           name="end_date" 
                           id="end_date"
                           value="${endDate}"
                           max="{{ date('Y-m-d') }}"
                           class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white text-sm">
                    <input type="hidden" name="date" id="date_hidden" value="${currentValue}">
                </div>
            `;
            
            // Replace the original input
            container.html(fallbackHTML);
            
            // Update hidden field when dates change
            $('#start_date, #end_date').on('change', function() {
                const start = $('#start_date').val();
                const end = $('#end_date').val();
                
                console.log('HTML5 date change - Start:', start, 'End:', end);
                
                if (start && end) {
                    // Validate dates are in correct format and convert to backend format
                    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
                    if (dateRegex.test(start) && dateRegex.test(end)) {
                        try {
                            const startDate = new Date(start);
                            const endDate = new Date(end);
                            
                            // Format to "MMMM DD, YYYY - MMMM DD, YYYY"
                            const formatOptions = { year: 'numeric', month: 'long', day: '2-digit' };
                            const formattedStart = startDate.toLocaleDateString('en-US', formatOptions);
                            const formattedEnd = endDate.toLocaleDateString('en-US', formatOptions);
                            const dateRange = formattedStart + ' - ' + formattedEnd;
                            
                            $('#date_hidden').val(dateRange);
                            console.log('HTML5 date range set:', dateRange);
                        } catch (error) {
                            $('#date_hidden').val('');
                            console.log('Error formatting HTML5 dates:', error);
                        }
                    } else {
                        $('#date_hidden').val('');
                        console.log('Invalid HTML5 date format');
                    }
                } else {
                    $('#date_hidden').val('');
                    console.log('HTML5 dates cleared');
                }
            });
        }
    });
    
    // Auto-submit form on status change
    $('#status').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // Validate form before submission
    $('form').on('submit', function(e) {
        const dateValue = $('#date').val();
        const searchBtn = $('#searchBtn');
        const searchIcon = $('#searchIcon');
        const loadingIcon = $('#loadingIcon');
        const searchText = $('#searchText');
        
        if (dateValue && dateValue.length > 0) {
            // Check if it's a valid date range format (MMMM DD, YYYY - MMMM DD, YYYY)
            if (!dateValue.includes(' - ') || dateValue.length < 25) {
                e.preventDefault();
                alert('Please select a complete date range before searching.');
                console.log('Invalid date format prevented submission:', dateValue);
                return false;
            }
            
            // Validate date format
            const dateParts = dateValue.split(' - ');
            if (dateParts.length !== 2) {
                e.preventDefault();
                alert('Please select a valid date range.');
                console.log('Invalid date range prevented submission:', dateValue);
                return false;
            }
            
            // Basic validation - check if dates can be parsed
            try {
                const startDate = new Date(dateParts[0]);
                const endDate = new Date(dateParts[1]);
                if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                    e.preventDefault();
                    alert('Please select valid dates.');
                    console.log('Invalid date values prevented submission:', dateValue);
                    return false;
                }
            } catch (error) {
                e.preventDefault();
                alert('Please select valid dates.');
                console.log('Date parsing error prevented submission:', error);
                return false;
            }
        }
        
        // Show loading state
        if (searchBtn.length) {
            searchBtn.prop('disabled', true);
            searchIcon.addClass('hidden');
            loadingIcon.removeClass('hidden');
            searchText.text('@lang("Searching...")');
        }
        
        console.log('Form submission validated, proceeding...');
    });
    
    // Clear search
    $(document).on('click', '.clear-search', function() {
        window.location.href = "{{ route('user.loan.list') }}";
    });
    
})(jQuery);
</script>
@endpush

@push('style')
<style>
/* Table hover effects */
tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Loading animation */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

/* Mobile card animations */
.lg\:hidden > div {
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Focus states for better accessibility */
input:focus, select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Rejection reason button styling */
.admin-feedback {
    @apply transition-all duration-200;
}

.admin-feedback:hover {
    @apply shadow-sm;
}

/* Custom tooltip styling */
.admin-feedback .group:hover .absolute {
    animation: tooltipFadeIn 0.2s ease-out;
}

@keyframes tooltipFadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, 5px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

/* Ensure tooltip appears above other elements */
.admin-feedback {
    position: relative;
    z-index: 1;
}

.admin-feedback:hover {
    z-index: 10;
}

/* Flatpickr custom styling for dark mode */
.flatpickr-calendar {
    @apply bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-lg;
}

.flatpickr-calendar .flatpickr-months {
    @apply bg-gray-50 dark:bg-gray-700;
}

.flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-calendar .flatpickr-current-month .numInputWrapper input {
    @apply bg-transparent text-gray-900 dark:text-white;
}

.flatpickr-calendar .flatpickr-weekdays {
    @apply bg-gray-100 dark:bg-gray-600;
}

.flatpickr-calendar .flatpickr-weekday {
    @apply text-gray-600 dark:text-gray-300;
}

.flatpickr-calendar .flatpickr-day {
    @apply text-gray-900 dark:text-white border-gray-200 dark:border-gray-600;
}

.flatpickr-calendar .flatpickr-day:hover {
    @apply bg-gray-100 dark:bg-gray-600;
}

.flatpickr-calendar .flatpickr-day.selected {
    @apply bg-blue-500 text-white;
}

.flatpickr-calendar .flatpickr-day.inRange {
    @apply bg-blue-100 dark:bg-blue-900/30 text-blue-900 dark:text-blue-300;
}

/* Mobile-specific enhancements */
@media (max-width: 1024px) {
    /* Ensure mobile cards are touch-friendly */
    .admin-feedback {
        min-height: 44px;
        min-width: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Mobile tooltip positioning */
    .admin-feedback .group-hover\:opacity-100 {
        right: 0;
        left: auto;
        transform: translateX(0);
    }
    
    /* Improve touch targets for mobile */
    .lg\:hidden a,
    .lg\:hidden button {
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Compact progress bar on mobile */
    .lg\:hidden .h-2 {
        height: 6px;
    }
    
    /* Better spacing for mobile cards */
    .lg\:hidden .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    /* Responsive grid adjustments */
    .lg\:hidden .grid-cols-2 {
        gap: 0.75rem;
    }
    
    /* Search toggle animation */
    .search-form-container {
        transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
        overflow: hidden;
    }
    
    /* Filter button active state */
    .filter-button-active {
        background-color: rgba(59, 130, 246, 0.1);
        color: rgb(59, 130, 246);
    }
}

.flatpickr-calendar .flatpickr-day.startRange,
.flatpickr-calendar .flatpickr-day.endRange {
    @apply bg-blue-500 text-white;
}

/* Date input styling */
#date {
    cursor: pointer;
}

#date:focus {
    @apply ring-2 ring-blue-500 border-blue-500;
}

/* Enhanced toggle transitions */
[x-cloak] {
    display: none !important;
}

/* Smooth transitions for search form */
.search-toggle-enter {
    opacity: 0;
    transform: translateY(-10px);
}

.search-toggle-enter-active {
    opacity: 1;
    transform: translateY(0);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.search-toggle-leave {
    opacity: 1;
    transform: translateY(0);
}

.search-toggle-leave-active {
    opacity: 0;
    transform: translateY(-10px);
    transition: opacity 0.3s ease, transform 0.3s ease;
}
</style>
@endpush

@push('bottom-menu')
    <li><a href="{{ route('user.loan.plans') }}">@lang('Loan Plans')</a></li>
    <li><a class="active" href="{{ route('user.loan.list') }}">@lang('My Loan List')</a></li>
@endpush