@extends($activeTemplate.'layouts.master')
@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Rebate History')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Track all your rebate transactions and earnings')</p>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Rebates Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['total_rebates'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Total Rebates')</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <i class="las la-list text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>

            {{-- Total Amount Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($summaryStats['total_amount']) }} <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __($general->cur_text) }}</span></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Total Amount')</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <i class="las la-money-bill-wave text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>

            {{-- Approved Count Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['approved_count'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Approved')</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <i class="las la-check-circle text-2xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
            </div>

            {{-- Approval Rate Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        @php
                            $approvalRate = $summaryStats['total_rebates'] > 0 
                                ? ($summaryStats['approved_count'] / $summaryStats['total_rebates']) * 100 
                                : 0;
                        @endphp
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($approvalRate, 1) }}%</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Approval Rate')</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <i class="las la-chart-line text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>
        </div>        {{-- Filters --}}
        <div x-data="{ 
            filtersOpen: false,
            init() {
                // Show filters by default on desktop (md and up)
                this.filtersOpen = window.innerWidth >= 768;
                // Listen for window resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 768) {
                        this.filtersOpen = true;
                    }
                });
            }
        }" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 mb-8 transition-colors duration-300">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Filter Results')</h2>
                {{-- Mobile Toggle Button --}}
                <button @click="filtersOpen = !filtersOpen" class="md:hidden inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200">
                    <span x-text="filtersOpen ? '@lang('Hide Filters')' : '@lang('Show Filters')'"></span>
                    <i class="las la-angle-down ml-2 transform transition-transform duration-200" :class="{ 'rotate-180': filtersOpen }"></i>
                </button>
            </div>
            <div class="p-6" x-show="filtersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Status')</label>
                        <select name="status" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200">
                            <option value="">@lang('All Status')</option>
                            <option value="pending" @selected(request('status') == 'pending')>@lang('Pending')</option>
                            <option value="approved" @selected(request('status') == 'approved')>@lang('Approved')</option>
                            <option value="rejected" @selected(request('status') == 'rejected')>@lang('Rejected')</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Program')</label>
                        <select name="program_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200">
                            <option value="">@lang('All Programs')</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" @selected(request('program_id') == $program->id)>
                                    {{ __($program->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Date Range')</label>
                        <input name="date_range" type="text" 
                               class="date-range w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" value="{{ request('date_range') }}" 
                               placeholder="@lang('Select date range')" autocomplete="off">
                    </div>

                    <div class="flex flex-col justify-end space-y-3">
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="las la-search mr-2"></i> @lang('Filter')
                        </button>
                        <a href="{{ route('user.rebate.history') }}" class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center">
                            <i class="las la-sync mr-2"></i> @lang('Reset')
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Rebate History --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 sm:mb-0">@lang('Your Rebates')</h2>
                <button class="bg-purple-100 hover:bg-purple-200 dark:bg-purple-900 dark:hover:bg-purple-800 text-purple-700 dark:text-purple-300 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center text-sm font-medium" id="exportBtn">
                    <i class="las la-download mr-2"></i> @lang('Export')
                </button>
            </div>
            
            @if($rebates->count() > 0)
                {{-- Desktop Table View --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Program')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Type')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Amount')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Status')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Submitted')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Processed')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($rebates as $rebate)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __($rebate->rebateCategory?->program?->name ?? 'Unknown Program') }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __($rebate->rebateCategory?->name ?? 'Uncategorized') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $typeClass = match($rebate->transaction_type) {
                                                'product_upload' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                'referral' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                                default => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $typeClass }}">
                                            {{ __(ucwords(str_replace('_', ' ', $rebate->transaction_type))) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ showAmount($rebate->rebate_amount) }} {{ $general->cur_text }}</p>
                                            @if($rebate->tier_multiplier > 1)
                                                <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-1">
                                                    <i class="las la-star mr-1"></i>
                                                    @lang('Base'): {{ showAmount($rebate->base_amount) }} × {{ $rebate->tier_multiplier }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusConfig = match($rebate->status) {
                                                'approved' => ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-800 dark:text-green-300'],
                                                'pending' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-800 dark:text-yellow-300'],
                                                'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900', 'text' => 'text-red-800 dark:text-red-300'],
                                                default => ['bg' => 'bg-gray-100 dark:bg-gray-900', 'text' => 'text-gray-800 dark:text-gray-300']
                                            };
                                        @endphp
                                        <div>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                                {{ __(ucfirst($rebate->status)) }}
                                            </span>
                                            @if($rebate->requires_review)
                                                <p class="text-xs text-yellow-600 dark:text-yellow-400 flex items-center mt-1">
                                                    <i class="las la-flag mr-1"></i> @lang('Under Review')
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ showDateTime($rebate->created_at, 'd M Y') }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($rebate->created_at, 'h:i A') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($rebate->approved_at)
                                            <div>
                                                <p class="text-sm text-gray-900 dark:text-white">{{ showDateTime($rebate->approved_at, 'd M Y') }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ diffForHumans($rebate->approved_at) }}</p>
                                            </div>
                                        @elseif($rebate->rejected_at)
                                            <div>
                                                <p class="text-sm text-gray-900 dark:text-white">{{ showDateTime($rebate->rejected_at, 'd M Y') }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ diffForHumans($rebate->rejected_at) }}</p>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">@lang('Pending')</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('user.rebate.show', $rebate->id) }}" 
                                               class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 transition-colors duration-200" 
                                               title="@lang('View Details')">
                                                <i class="las la-eye text-lg"></i>
                                            </a>
                                            
                                            @if($rebate->product_upload_id)
                                                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200 viewTransactionsBtn" 
                                                        data-id="{{ $rebate->id }}" title="@lang('View Related Upload')">
                                                    <i class="las la-exchange-alt text-lg"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View --}}
                <div class="lg:hidden">
                    @foreach($rebates as $rebate)
                        <div class="border-b border-gray-200 dark:border-gray-600 last:border-b-0 p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __($rebate->rebateCategory?->program?->name ?? 'Unknown Program') }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __($rebate->rebateCategory?->name ?? 'Uncategorized') }}</p>
                                </div>
                                @php
                                    $statusConfig = match($rebate->status) {
                                        'approved' => ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-800 dark:text-green-300'],
                                        'pending' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-800 dark:text-yellow-300'],
                                        'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900', 'text' => 'text-red-800 dark:text-red-300'],
                                        default => ['bg' => 'bg-gray-100 dark:bg-gray-900', 'text' => 'text-gray-800 dark:text-gray-300']
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    {{ __(ucfirst($rebate->status)) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Amount')</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ showAmount($rebate->rebate_amount) }} {{ $general->cur_text }}</p>
                                    @if($rebate->tier_multiplier > 1)
                                        <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-1">
                                            <i class="las la-star mr-1"></i> {{ $rebate->tier_multiplier }}x @lang('bonus')
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Type')</p>
                                    @php
                                        $typeClass = match($rebate->transaction_type) {
                                            'product_upload' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            'referral' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                            default => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $typeClass }}">
                                        {{ __(ucwords(str_replace('_', ' ', $rebate->transaction_type))) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Submitted')</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ showDateTime($rebate->created_at, 'd M Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($rebate->created_at, 'h:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Processed')</p>
                                    @if($rebate->approved_at)
                                        <p class="text-sm text-gray-900 dark:text-white">{{ showDateTime($rebate->approved_at, 'd M Y') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ diffForHumans($rebate->approved_at) }}</p>
                                    @elseif($rebate->rejected_at)
                                        <p class="text-sm text-gray-900 dark:text-white">{{ showDateTime($rebate->rejected_at, 'd M Y') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ diffForHumans($rebate->rejected_at) }}</p>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">@lang('Pending')</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex space-x-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <a href="{{ route('user.rebate.show', $rebate->id) }}" 
                                   class="flex-1 bg-purple-100 hover:bg-purple-200 dark:bg-purple-900 dark:hover:bg-purple-800 text-purple-700 dark:text-purple-300 px-4 py-2 rounded-lg transition-colors duration-200 text-center text-sm font-medium">
                                    <i class="las la-eye mr-2"></i> @lang('View Details')
                                </a>
                                
                                @if($rebate->product_upload_id)
                                    <button class="flex-1 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 px-4 py-2 rounded-lg transition-colors duration-200 text-center text-sm font-medium viewTransactionsBtn" 
                                            data-id="{{ $rebate->id }}">
                                        <i class="las la-exchange-alt mr-2"></i> @lang('View Upload')
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($rebates->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ paginateLinks($rebates) }}
                    </div>
                @endif
            @else
                <div class="p-12">
                    <div class="text-center">
                        <i class="las la-inbox text-6xl text-gray-300 dark:text-gray-600 mb-6"></i>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">@lang('No Rebates Found')</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                            @if(request()->hasAny(['status', 'program_id', 'date_range']))
                                @lang('No rebates match your current filters. Try adjusting the filter criteria or clearing all filters.')
                            @else
                                @lang('You haven\'t earned any rebates yet. Start by browsing available programs and uploading your receipts.')
                            @endif
                        </p>
                        @if(!request()->hasAny(['status', 'program_id', 'date_range']))
                            <a href="{{ route('user.rebate.programs') }}" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 inline-flex items-center">
                                <i class="las la-plus mr-2"></i> @lang('Browse Programs')
                            </a>
                        @else
                            <a href="{{ route('user.rebate.history') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-medium py-3 px-6 rounded-lg transition-colors duration-200 inline-flex items-center">
                                <i class="las la-sync mr-2"></i> @lang('Clear Filters')
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Transactions Modal --}}
<div x-data="{ showTransactions: false }" 
     x-show="showTransactions" 
     x-cloak
     @keydown.escape.window="showTransactions = false"
     class="fixed inset-0 z-50 overflow-y-auto hidden"
     id="transactionsModal"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showTransactions" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showTransactions = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showTransactions" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            
            <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">@lang('Transaction Details')</h3>
                    <button @click="showTransactions = false" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 closeModalBtn">
                        <i class="las la-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 px-6 py-4 max-h-96 overflow-y-auto">
                <div id="transactionsContent">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
                        <p class="text-gray-500 dark:text-gray-400 mt-4">@lang('Loading...')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Export Modal --}}
<div x-data="{ showExport: false }" 
     x-show="showExport" 
     x-cloak
     @keydown.escape.window="showExport = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     id="exportModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showExport" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showExport = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showExport" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <form id="exportForm">
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">@lang('Export Rebate History')</h3>
                        <button type="button" @click="showExport = false" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <i class="las la-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 px-6 py-4 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Export Format')</label>
                        <select name="format" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel (XLSX)</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Date Range')</label>
                        <input name="export_date_range" type="text" data-range="true" 
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 datepicker-here" 
                               placeholder="@lang('All time')">
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="include_transactions" id="includeTransactions" 
                                   class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="includeTransactions" class="text-gray-700 dark:text-gray-300">
                                @lang('Include transaction details')
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" @click="showExport = false" 
                            class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        @lang('Cancel')
                    </button>
                    <button type="button" id="exportConfirmBtn" 
                            class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <i class="las la-download mr-2"></i> @lang('Export')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('style')
<style>
    {{-- Alpine.js Directives --}}
    [x-cloak] { 
        display: none !important; 
    }

    /* Custom daterangepicker styling for Tailwind integration */
    .daterangepicker {
        background: white;
        border: 1px solid rgb(209 213 219);
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .daterangepicker .calendar-table {
        background: white;
        border: none;
    }

    .daterangepicker td.active,
    .daterangepicker td.active:hover {
        background-color: rgb(147 51 234) !important;
        border-color: rgb(147 51 234) !important;
        color: white !important;
    }

    .daterangepicker td.in-range {
        background-color: rgb(243 232 255) !important;
        color: rgb(107 33 168) !important;
    }

    .daterangepicker .ranges li.active {
        background-color: rgb(147 51 234);
        color: white;
    }

    /* Dark mode daterangepicker */
    .dark .daterangepicker {
        background: rgb(31 41 55);
        border-color: rgb(75 85 99);
    }

    .dark .daterangepicker .calendar-table {
        background: rgb(31 41 55);
        color: white;
    }

    .dark .daterangepicker td {
        color: rgb(209 213 219);
    }

    .dark .daterangepicker .ranges li {
        color: rgb(209 213 219);
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
(function($) {
    
    // Initialize date range picker with error handling
    if (typeof $.fn.daterangepicker !== 'undefined') {
        $('.date-range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $('.date-range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.date-range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Set initial values if present
        if ($('.date-range').val()) {
            let dateRange = $('.date-range').val().split(' - ');
            if (dateRange.length === 2) {
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }
        }
        } else {
        // Fallback to native HTML5 date inputs
        $('.date-range').attr('type', 'date');
    }

    // View transactions
    $(document).on('click', '.viewTransactionsBtn', function(e) {
        e.preventDefault();        const rebateId = $(this).data('id');
        
        if (!rebateId) {
            return;
        }
        
        // Reset modal content
        $('#transactionsContent').html(`
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
                <p class="text-gray-500 dark:text-gray-400 mt-4">@lang('Loading...')</p>
            </div>
        `);
        
        // Show modal
        const modal = document.querySelector('#transactionsModal');
        if (modal && modal.__x && modal.__x.$data) {
            modal.__x.$data.showTransactions = true;
        } else {
            $('#transactionsModal').removeClass('hidden').show();
        }
        
        // Load transactions
        $.get(`{{ url('user/rebate/transactions') }}/${rebateId}`)
            .done(function(data) {
                $('#transactionsContent').html(data);
            })
            .fail(function() {
                $('#transactionsContent').html(`
                    <div class="text-center py-8">
                        <i class="las la-exclamation-triangle text-red-500 text-6xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('Failed to Load')</h3>
                        <p class="text-gray-600 dark:text-gray-400">@lang('Unable to load transaction details')</p>
                    </div>
                `);
            });
    });

    // Close modal functionality
    $(document).on('click', '.closeModalBtn', function(e) {
        e.preventDefault();
        const modal = document.querySelector('#transactionsModal');
        if (modal && modal.__x && modal.__x.$data) {
            modal.__x.$data.showTransactions = false;
        } else {
            $('#transactionsModal').addClass('hidden').hide();
        }
    });

    // Close modal when clicking outside
    $(document).on('click', '#transactionsModal', function(e) {
        if (e.target === this) {
            $(this).find('.closeModalBtn').trigger('click');
        }
    });

    // Export functionality
    $('#exportBtn').on('click', function() {
        const exportModal = document.querySelector('#exportModal');
        if (exportModal && exportModal.__x && exportModal.__x.$data) {
            exportModal.__x.$data.showExport = true;
        } else {
            $('#exportModal').removeClass('hidden').show();
        }
    });

    $('#exportConfirmBtn').on('click', function() {
        const form = $('#exportForm');
        const formData = new FormData(form[0]);
        
        // Add current filters to export
        const currentParams = new URLSearchParams(window.location.search);
        currentParams.forEach((value, key) => {
            if (!formData.has(key)) {
                formData.append(key, value);
            }
        });

        // Create download link
        const params = new URLSearchParams(formData);
        const exportUrl = `{{ route('user.rebate.export') }}?${params.toString()}`;
        
        // Trigger download
        window.open(exportUrl, '_blank');
        
        // Hide modal
        const exportModal = document.querySelector('#exportModal');
        if (exportModal && exportModal.__x && exportModal.__x.$data) {
            exportModal.__x.$data.showExport = false;
        } else {
            $('#exportModal').addClass('hidden').hide();
        }
    });

    // Auto-refresh pending rebates every 30 seconds
    let refreshInterval;
    
    function startAutoRefresh() {
        refreshInterval = setInterval(function() {
            if (!document.hidden && $('[class*="bg-yellow"]').length > 0) {
                // Only refresh if there are pending rebates and tab is active
                const currentUrl = new URL(window.location.href);
                const params = currentUrl.searchParams;
                
                // Add a refresh parameter to prevent browser caching
                params.set('_refresh', Date.now());
                
                // Use fetch to check for updates without full page reload
                fetch(currentUrl.toString())
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTableBody = doc.querySelector('tbody');
                        const newMobileView = doc.querySelector('.lg\\:hidden');
                        
                        if (newTableBody && $('tbody').length > 0) {
                            $('tbody').html(newTableBody.innerHTML);
                        }
                        if (newMobileView && $('.lg\\:hidden').length > 0) {
                            $('.lg\\:hidden').html(newMobileView.innerHTML);
                        }
                    })
                    .catch(err => {});
            }
        }, 30000);
    }

    // Start auto-refresh
    startAutoRefresh();

    // Stop refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            startAutoRefresh();
        }
    });

    // Stop refresh when page unloads
    window.addEventListener('beforeunload', function() {
        clearInterval(refreshInterval);
    });

})(jQuery);
</script>
@endpush