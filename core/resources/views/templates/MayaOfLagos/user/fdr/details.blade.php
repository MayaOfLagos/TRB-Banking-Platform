@extends($activeTemplate . 'user.fdr.layout')
@section('fdr-content')

<!-- FDR Details Section -->
<div class="space-y-6">
    
    <!-- FDR Header Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        <!-- Header with Gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">{{ __($fdr->plan->name) }}</h2>
                    <p class="text-blue-100 mt-1">FDR Number: #{{ $fdr->fdr_number }}</p>
                    <div class="flex items-center mt-2 text-sm">
                        <i class="las la-calendar mr-2"></i>
                        @lang('Created on') {{ showDateTime($fdr->created_at, 'd M Y \a\t h:i A') }}
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Status Badge -->
                    @if($fdr->status == 1)
                        <div class="bg-green-500 bg-opacity-20 border border-green-300 px-4 py-2 rounded-lg">
                            <div class="flex items-center">
                                <i class="las la-play-circle mr-2"></i>
                                <span class="font-semibold">@lang('Running')</span>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-500 bg-opacity-20 border border-gray-300 px-4 py-2 rounded-lg">
                            <div class="flex items-center">
                                <i class="las la-stop-circle mr-2"></i>
                                <span class="font-semibold">@lang('Closed')</span>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <a href="{{ route('user.fdr.instalment.logs', $fdr->fdr_number) }}" 
                           class="bg-white bg-opacity-20 hover:bg-opacity-30 border border-white border-opacity-30 px-4 py-2 rounded-lg transition-all duration-200 flex items-center">
                            <i class="las la-list mr-2"></i>
                            @lang('Installments')
                        </a>
                        
                        @if($fdr->status == 1 && now() >= $fdr->locked_date)
                        <button type="button" 
                                onclick="closeFdr()"
                                class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="las la-times-circle mr-2"></i>
                            @lang('Close FDR')
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar (for running FDRs) -->
        @if($fdr->status == 1)
            @php
                $startDate = \Carbon\Carbon::parse($fdr->created_at);
                $endDate = \Carbon\Carbon::parse($fdr->locked_date);
                $currentDate = now();
                $totalDays = $startDate->diffInDays($endDate);
                $elapsedDays = $startDate->diffInDays($currentDate);
                $remainingDays = max(0, $endDate->diffInDays($currentDate));
                $progress = min(100, ($elapsedDays / $totalDays) * 100);
            @endphp
            <div class="p-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">@lang('Lock-in Period Progress')</h4>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $remainingDays }} @lang('days remaining')
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-500" 
                         style="width: {{ $progress }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
                    <span>{{ showDateTime($fdr->created_at, 'd M Y') }}</span>
                    <span>{{ showDateTime($fdr->locked_date, 'd M Y') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- FDR Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Principal Amount -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="las la-coins text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Principal Amount')</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ showAmount($fdr->amount) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">{{ gs()->cur_text }}</div>
                </div>
            </div>
        </div>
        
        <!-- Total Profit -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="las la-chart-line text-2xl text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Total Profit')</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        +{{ showAmount($fdr->profit) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">{{ gs()->cur_text }}</div>
                </div>
            </div>
        </div>
        
        <!-- Interest Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <i class="las la-percentage text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Interest Rate')</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $fdr->plan->interest_rate }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">@lang('Per Annum')</div>
                </div>
            </div>
        </div>
        
        <!-- Maturity Value -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="las la-trophy text-2xl text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Maturity Value')</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ showAmount($fdr->amount + $fdr->profit) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">{{ gs()->cur_text }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- FDR Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="las la-info-circle mr-2 text-blue-600"></i>
                    @lang('FDR Information')
                </h3>
            </div>
            <div class="p-6 space-y-4">
                
                <!-- FDR Number -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('FDR Number')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">#{{ $fdr->fdr_number }}</span>
                </div>
                
                <!-- Plan Name -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Plan Name')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ __($fdr->plan->name) }}</span>
                </div>
                
                <!-- Investment Amount -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Investment Amount')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ showAmount($fdr->amount) }} {{ gs()->cur_text }}
                    </span>
                </div>
                
                <!-- Interest Rate -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Interest Rate')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $fdr->plan->interest_rate }}% @lang('per annum')</span>
                </div>
                
                <!-- Lock-in Period -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Lock-in Period')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $fdr->plan->locked_days }} @lang('Days')</span>
                </div>
                
                <!-- Installment Interval -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Installment Interval')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $fdr->plan->installment_interval }} @lang('Days')</span>
                </div>
                
                <!-- Created Date -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Created Date')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ showDateTime($fdr->created_at, 'd M Y \a\t h:i A') }}
                    </span>
                </div>
                
                <!-- Maturity Date -->
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Maturity Date')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ showDateTime($fdr->locked_date, 'd M Y \a\t h:i A') }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Profit Calculation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="las la-calculator mr-2 text-green-600"></i>
                    @lang('Profit Calculation')
                </h3>
            </div>
            <div class="p-6 space-y-4">
                
                <!-- Total Installments -->
                @php
                    $totalInstallments = max(1, intval($fdr->plan->locked_days / max(1, $fdr->plan->installment_interval)));
                    $profitPerInstallment = $totalInstallments > 0 ? $fdr->profit / $totalInstallments : 0;
                @endphp
                
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalInstallments }}</div>
                        <div class="text-sm text-green-700 dark:text-green-300">@lang('Total Installments')</div>
                    </div>
                </div>
                
                <!-- Per Installment Profit -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Profit per Installment')</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">
                        +{{ showAmount($profitPerInstallment) }} {{ gs()->cur_text }}
                    </span>
                </div>
                
                <!-- Total Expected Profit -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Total Expected Profit')</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">
                        +{{ showAmount($fdr->profit) }} {{ gs()->cur_text }}
                    </span>
                </div>
                
                <!-- Received Profit -->
                @php
                    $receivedProfit = $fdr->installments->sum('profit_amount');
                @endphp
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Received Profit')</span>
                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                        +{{ showAmount($receivedProfit) }} {{ gs()->cur_text }}
                    </span>
                </div>
                
                <!-- Remaining Profit -->
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Remaining Profit')</span>
                    <span class="font-semibold text-yellow-600 dark:text-yellow-400">
                        +{{ showAmount($fdr->profit - $receivedProfit) }} {{ gs()->cur_text }}
                    </span>
                </div>
                
                <!-- Calculation Formula -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">@lang('Calculation Formula')</h4>
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <div>@lang('Annual Interest') = {{ showAmount($fdr->amount) }} × {{ $fdr->plan->interest_rate }}%</div>
                        <div>@lang('Daily Interest') = @lang('Annual Interest') ÷ 365</div>
                        <div>@lang('Total Profit') = @lang('Daily Interest') × {{ $fdr->plan->locked_days }} @lang('days')</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Installments -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="las la-history mr-2 text-purple-600"></i>
                    @lang('Recent Installments')
                </h3>
                <a href="{{ route('user.fdr.instalment.logs', $fdr->fdr_number) }}" 
                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                    @lang('View All')
                </a>
            </div>
        </div>
        
        @if($fdr->installments->count() > 0)
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($fdr->installments->take(5) as $installment)
            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                            <i class="las la-check text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                @lang('Installment') #{{ $installment->installment_count }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ showDateTime($installment->given_at, 'd M Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                            +{{ showAmount($installment->profit_amount) }} {{ gs()->cur_text }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Profit Received')</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <div class="h-16 w-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="las la-clock text-2xl text-gray-400"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('No Installments Yet')</h4>
            <p class="text-gray-600 dark:text-gray-400">@lang('Installments will appear here as they are processed.')</p>
        </div>
        @endif
    </div>

    <!-- Download Options -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="las la-download mr-2 text-blue-600"></i>
            @lang('Download Options')
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button type="button" 
                    id="downloadCertificateBtn"
                    onclick="downloadFdrCertificate()"
                    class="flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg id="downloadCertificateSpinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <i id="downloadCertificateIcon" class="las la-certificate mr-2"></i>
                <span id="downloadCertificateText">@lang('FDR Certificate')</span>
            </button>
            
            <button type="button" 
                    id="downloadReportBtn"
                    onclick="downloadInstallmentReport()"
                    class="flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg id="downloadReportSpinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <i id="downloadReportIcon" class="las la-file-alt mr-2"></i>
                <span id="downloadReportText">@lang('Installment Report')</span>
            </button>
            
            <button type="button" 
                    id="downloadTaxBtn"
                    onclick="downloadTaxStatement()"
                    class="flex items-center justify-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg id="downloadTaxSpinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <i id="downloadTaxIcon" class="las la-receipt mr-2"></i>
                <span id="downloadTaxText">@lang('Tax Statement')</span>
            </button>
        </div>
    </div>
</div>

<!-- Close FDR Confirmation Modal -->
<div id="closeFdrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                <i class="las la-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-2">@lang('Close FDR')</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                @lang('Are you sure you want to close this FDR? You will receive your principal amount and any accrued profits. This action cannot be undone.')
            </p>
            <div class="flex gap-3 justify-center">
                <button type="button" 
                        onclick="cancelCloseFdr()"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-200">
                    @lang('Cancel')
                </button>
                <form method="POST" action="{{ route('user.fdr.close', $fdr->fdr_number) }}" style="display: inline;" class="close-fdr-form">
                    @csrf
                    <button type="submit" 
                            class="close-fdr-btn px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 disabled:cursor-not-allowed text-white rounded-lg transition-colors duration-200">
                        <span class="submit-text flex items-center">
                            @lang('Close FDR')
                        </span>
                        <span class="loading-text hidden flex items-center">
                            <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            @lang('Closing...')
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
// Close FDR Functions
function closeFdr() {
    document.getElementById('closeFdrModal').classList.remove('hidden');
}

function cancelCloseFdr() {
    document.getElementById('closeFdrModal').classList.add('hidden');
}

// Download Functions
function downloadFdrCertificate() {
    const btn = document.getElementById('downloadCertificateBtn');
    const spinner = document.getElementById('downloadCertificateSpinner');
    const icon = document.getElementById('downloadCertificateIcon');
    const text = document.getElementById('downloadCertificateText');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('hidden');
    icon.classList.add('hidden');
    text.textContent = '@lang("Generating...")';
    
    // Create download link
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("user.fdr.download", $fdr->fdr_number) }}';
    form.innerHTML = '<input type="hidden" name="type" value="certificate">';
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset button after download
    setTimeout(() => {
        btn.disabled = false;
        spinner.classList.add('hidden');
        icon.classList.remove('hidden');
        text.textContent = '@lang("FDR Certificate")';
    }, 3000);
}

function downloadInstallmentReport() {
    const btn = document.getElementById('downloadReportBtn');
    const spinner = document.getElementById('downloadReportSpinner');
    const icon = document.getElementById('downloadReportIcon');
    const text = document.getElementById('downloadReportText');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('hidden');
    icon.classList.add('hidden');
    text.textContent = '@lang("Generating...")';
    
    // Create download link
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
        text.textContent = '@lang("Installment Report")';
    }, 3000);
}

function downloadTaxStatement() {
    const btn = document.getElementById('downloadTaxBtn');
    const spinner = document.getElementById('downloadTaxSpinner');
    const icon = document.getElementById('downloadTaxIcon');
    const text = document.getElementById('downloadTaxText');
    
    // Show loading state
    btn.disabled = true;
    spinner.classList.remove('hidden');
    icon.classList.add('hidden');
    text.textContent = '@lang("Generating...")';
    
    // Create download link
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("user.fdr.download", $fdr->fdr_number) }}';
    form.innerHTML = '<input type="hidden" name="type" value="tax">';
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset button after download
    setTimeout(() => {
        btn.disabled = false;
        spinner.classList.add('hidden');
        icon.classList.remove('hidden');
        text.textContent = '@lang("Tax Statement")';
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('closeFdrModal').addEventListener('click', function(e) {
    if (e.target === this) {
        cancelCloseFdr();
    }
});

// Handle close FDR form loading state
$('.close-fdr-form').on('submit', function() {
    const submitBtn = $(this).find('.close-fdr-btn');
    submitBtn.prop('disabled', true);
    submitBtn.find('.submit-text').addClass('hidden');
    submitBtn.find('.loading-text').removeClass('hidden');
});

// Auto-refresh for running FDRs (every 5 minutes)
@if($fdr->status == 1)
setInterval(function() {
    // Only refresh if the page is visible
    if (!document.hidden) {
        window.location.reload();
    }
}, 300000); // 5 minutes
@endif
</script>
@endpush