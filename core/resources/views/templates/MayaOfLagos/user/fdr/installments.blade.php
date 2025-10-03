@extends($activeTemplate . 'user.fdr.layout')
@section('fdr-content')

<!-- FDR Installments Section -->
<div class="space-y-6">
    
    <!-- FDR Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __($fdr->plan->name) }} @lang('Installments')</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">FDR Number: #{{ $fdr->fdr_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($fdr->status == 1)
                    <span class="status-badge status-running">
                        <i class="las la-play-circle mr-1"></i>
                        @lang('Running')
                    </span>
                @else
                    <span class="status-badge status-closed">
                        <i class="las la-stop-circle mr-1"></i>
                        @lang('Closed')
                    </span>
                @endif
                
                <a href="{{ route('user.fdr.details', $fdr->fdr_number) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="las la-arrow-left mr-2"></i>
                    @lang('Back to Details')
                </a>
            </div>
        </div>
    </div>

    <!-- Installment Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        @php
            $totalInstallments = max(1, intval($fdr->plan->locked_days / max(1, $fdr->plan->installment_interval)));
            $completedInstallments = $fdr->installments->count();
            $remainingInstallments = max(0, $totalInstallments - $completedInstallments);
            $totalProfitReceived = $fdr->installments->sum('profit_amount');
            $remainingProfit = $fdr->profit - $totalProfitReceived;
        @endphp
        
        <!-- Total Installments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="las la-list-ol text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalInstallments }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Total Installments')</div>
                </div>
            </div>
        </div>
        
        <!-- Completed Installments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="las la-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $completedInstallments }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Completed')</div>
                </div>
            </div>
        </div>
        
        <!-- Remaining Installments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <i class="las la-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $remainingInstallments }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Remaining')</div>
                </div>
            </div>
        </div>
        
        <!-- Profit Received -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="las la-coins text-2xl text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ showAmount($totalProfitReceived) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Profit Received')</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="las la-chart-bar mr-2 text-blue-600"></i>
            @lang('Installment Progress')
        </h3>
        
        <div class="space-y-4">
            <!-- Overall Progress -->
            <div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <span>@lang('Overall Progress')</span>
                    <span>{{ $completedInstallments }} / {{ $totalInstallments }} (@lang('{{percentage}}% Complete', ['percentage' => $totalInstallments > 0 ? round(($completedInstallments / $totalInstallments) * 100) : 0]))</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-500" 
                         style="width: {{ $totalInstallments > 0 ? ($completedInstallments / $totalInstallments) * 100 : 0 }}%"></div>
                </div>
            </div>
            
            <!-- Profit Progress -->
            <div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <span>@lang('Profit Progress')</span>
                    <span>{{ showAmount($totalProfitReceived) }} / {{ showAmount($fdr->profit) }} {{ gs()->cur_text }}</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-500" 
                         style="width: {{ $fdr->profit > 0 ? ($totalProfitReceived / $fdr->profit) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- FDR Summary (matching crystal_sky structure) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="las la-info-circle mr-2 text-blue-600"></i>
                @lang('FDR Summary')
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                
                <!-- FDR Number -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('FDR Number')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">#{{ $fdr->fdr_number }}</span>
                </div>
                
                <!-- Plan -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Plan')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ __($fdr->plan->name) }}</span>
                </div>
                
                <!-- Deposited -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Deposited')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ showAmount($fdr->amount) }} {{ gs()->cur_text }}</span>
                </div>
                
                <!-- Interest Rate -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Interest Rate')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        @if(isset($fdr->interest_rate))
                            {{ getAmount($fdr->interest_rate) }}%
                        @else
                            {{ getAmount($fdr->plan->interest_rate) }}%
                        @endif
                    </span>
                </div>
                
                <!-- Per Installment -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Per Installment')</span>
                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                        @if(isset($fdr->per_installment))
                            {{ showAmount($fdr->per_installment) }}
                        @else
                            {{ showAmount($profitAmount) }}
                        @endif
                         {{ gs()->cur_text }}
                    </span>
                </div>
                
                <!-- Received Installment -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Received Installments')</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">{{ $fdr->installments->count() }}</span>
                </div>
                
                <!-- Profit Received -->
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Profit Received')</span>
                    <span class="font-semibold text-purple-600 dark:text-purple-400">{{ showAmount($totalProfitReceived) }} {{ gs()->cur_text }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Installment Schedule -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="las la-calendar-alt mr-2 text-green-600"></i>
                    @lang('Installment Schedule')
                </h3>
                
                <!-- Filter Options -->
                <div class="flex items-center space-x-3">
                    <select id="statusFilter" 
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">@lang('All Status')</option>
                        <option value="received">@lang('Received')</option>
                        <option value="pending">@lang('Pending')</option>
                    </select>
                    
                    <button type="button" 
                            id="downloadInstallmentBtn"
                            onclick="downloadInstallmentReport()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg id="downloadInstallmentSpinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <i id="downloadInstallmentIcon" class="las la-download mr-2"></i>
                        <span id="downloadInstallmentText">@lang('Download Report')</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Installment')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Due Date')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Profit Amount')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Status')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Received Date')</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @for($i = 1; $i <= $totalInstallments; $i++)
                        @php
                            $dueDate = \Carbon\Carbon::parse($fdr->created_at)->addDays($i * $fdr->plan->installment_interval);
                            $installment = $fdr->installments->where('installment_count', $i)->first();
                            $profitAmount = $totalInstallments > 0 ? $fdr->profit / $totalInstallments : 0;
                            $isReceived = $installment ? true : false;
                            $isPending = !$isReceived && $dueDate <= now();
                            $isFuture = !$isReceived && $dueDate > now();
                        @endphp
                        
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 installment-row" 
                            data-status="{{ $isReceived ? 'received' : 'pending' }}">
                            
                            <!-- Installment Number -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $isReceived ? 'bg-green-100 dark:bg-green-900' : ($isPending ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-gray-100 dark:bg-gray-700') }}">
                                            @if($isReceived)
                                                <i class="las la-check text-green-600 dark:text-green-400"></i>
                                            @elseif($isPending)
                                                <i class="las la-clock text-yellow-600 dark:text-yellow-400"></i>
                                            @else
                                                <i class="las la-calendar text-gray-500 dark:text-gray-400"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">@lang('Installment') #{{ $i }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $i }}/{{ $totalInstallments }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Due Date -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $dueDate->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $dueDate->format('h:i A') }}</div>
                            </td>
                            
                            <!-- Profit Amount -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ showAmount($profitAmount) }} {{ gs()->cur_text }}
                                </div>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($isReceived)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        <i class="las la-check-circle mr-1"></i>
                                        @lang('Received')
                                    </span>
                                @elseif($isPending)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                        <i class="las la-clock mr-1"></i>
                                        @lang('Pending')
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        <i class="las la-calendar mr-1"></i>
                                        @lang('Scheduled')
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Received Date -->
                            <td class="px-6 py-4">
                                @if($installment)
                                    <div class="text-sm text-gray-900 dark:text-white">{{ showDateTime($installment->given_at, 'd M Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($installment->given_at, 'h:i A') }}</div>
                                @else
                                    <div class="text-sm text-gray-500 dark:text-gray-400">-</div>
                                @endif
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @for($i = 1; $i <= $totalInstallments; $i++)
                @php
                    $dueDate = \Carbon\Carbon::parse($fdr->created_at)->addDays($i * $fdr->plan->installment_interval);
                    $installment = $fdr->installments->where('installment_count', $i)->first();
                    $profitAmount = $totalInstallments > 0 ? $fdr->profit / $totalInstallments : 0;
                    $isReceived = $installment ? true : false;
                    $isPending = !$isReceived && $dueDate <= now();
                    $isFuture = !$isReceived && $dueDate > now();
                @endphp
                
                <div class="p-6 installment-card-mobile" data-status="{{ $isReceived ? 'received' : 'pending' }}">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $isReceived ? 'bg-green-100 dark:bg-green-900' : ($isPending ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-gray-100 dark:bg-gray-700') }}">
                                @if($isReceived)
                                    <i class="las la-check text-green-600 dark:text-green-400"></i>
                                @elseif($isPending)
                                    <i class="las la-clock text-yellow-600 dark:text-yellow-400"></i>
                                @else
                                    <i class="las la-calendar text-gray-500 dark:text-gray-400"></i>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">@lang('Installment') #{{ $i }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $i }}/{{ $totalInstallments }}</div>
                            </div>
                        </div>
                        
                        @if($isReceived)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                @lang('Received')
                            </span>
                        @elseif($isPending)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                @lang('Pending')
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                @lang('Scheduled')
                            </span>
                        @endif
                    </div>
                    
                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Due Date')</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $dueDate->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Profit Amount')</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ showAmount($profitAmount) }} {{ gs()->cur_text }}</div>
                        </div>
                        @if($installment)
                        <div class="col-span-2">
                            <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Received Date')</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ showDateTime($installment->given_at, 'd M Y \a\t h:i A') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="las la-calculator mr-2 text-purple-600"></i>
            @lang('Financial Summary')
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ showAmount($fdr->amount) }}</div>
                <div class="text-sm text-blue-700 dark:text-blue-300">@lang('Principal Amount')</div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ showAmount($totalProfitReceived) }}</div>
                <div class="text-sm text-green-700 dark:text-green-300">@lang('Profit Received')</div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ showAmount($remainingProfit) }}</div>
                <div class="text-sm text-yellow-700 dark:text-yellow-300">@lang('Remaining Profit')</div>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ showAmount($fdr->amount + $fdr->profit) }}</div>
                <div class="text-sm text-purple-700 dark:text-purple-300">@lang('Maturity Value')</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
// Filter Function
function filterInstallments() {
    const statusFilter = document.getElementById('statusFilter').value;
    
    // Desktop table rows
    const tableRows = document.querySelectorAll('.installment-row');
    // Mobile cards
    const mobileCards = document.querySelectorAll('.installment-card-mobile');
    
    [...tableRows, ...mobileCards].forEach(row => {
        const status = row.getAttribute('data-status');
        let showRow = true;
        
        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Download Report Function
function downloadInstallmentReport() {
    const btn = document.getElementById('downloadInstallmentBtn');
    const spinner = document.getElementById('downloadInstallmentSpinner');
    const icon = document.getElementById('downloadInstallmentIcon');
    const text = document.getElementById('downloadInstallmentText');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('hidden');
    icon.classList.add('hidden');
    text.textContent = '@lang("Generating...")';
    
    // Create form for report download
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("user.fdr.download", $fdr->fdr_number) }}';
    form.innerHTML = '<input type="hidden" name="type" value="installments">';
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset button after download
    setTimeout(() => {
        btn.disabled = false;
        spinner.classList.add('hidden');
        icon.classList.remove('hidden');
        text.textContent = '@lang("Download Report")';
    }, 3000);
}

// Event Listeners
document.getElementById('statusFilter').addEventListener('change', filterInstallments);

// Auto-refresh for running FDRs (every 5 minutes)
@if($fdr->status == 1)
setInterval(function() {
    // Only refresh if the page is visible
    if (!document.hidden) {
        window.location.reload();
    }
}, 300000); // 5 minutes
@endif

// Smooth scrolling for hash links
document.addEventListener('DOMContentLoaded', function() {
    // Add click animations
    const cards = document.querySelectorAll('.installment-card-mobile, .installment-row');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
});
</script>
@endpush