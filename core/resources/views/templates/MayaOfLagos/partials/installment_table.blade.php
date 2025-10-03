{{-- Updated to support both loans, FDRs and DPS --}}
@php
    // Determine if we're dealing with FDR, DPS, or loan
    $isFdr = isset($fdr);
    $isDps = isset($dps);
    $isLoan = isset($loan);
    
    if ($isFdr) {
        $entity = $fdr;
        $givenInstallments = $fdr->installments->count();
        $totalInstallments = max(1, intval($fdr->plan->locked_days / max(1, $fdr->plan->installment_interval)));
    } elseif ($isDps) {
        $entity = $dps;
        $givenInstallments = $dps->given_installment;
        $totalInstallments = $dps->total_installment;
    } elseif ($isLoan) {
        $entity = $loan;
        $givenInstallments = $loan->given_installment;
        $totalInstallments = $loan->total_installment;
    } else {
        // Fallback to prevent errors
        $entity = null;
        $givenInstallments = 0;
        $totalInstallments = 1;
    }
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Table Header -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                    <i class="las la-calendar-check text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Installment Schedule')</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        @if($isFdr)
                            @lang('Track your FDR profit installments and payment history')
                        @elseif($isDps)
                            @lang('Track your DPS installment deposits and payment schedule')
                        @elseif($isLoan)
                            @lang('Track your loan payment history and upcoming installments')
                        @else
                            @lang('Track your payment history and installments')
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Progress Indicator -->
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $givenInstallments }}<span class="text-gray-400 dark:text-gray-500">/ {{ $totalInstallments }}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if($isFdr)
                        @lang('Installments Received')
                    @elseif($isDps)
                        @lang('Installments Paid')
                    @elseif($isLoan)
                        @lang('Installments Paid')
                    @else
                        @lang('Installments')
                    @endif
                </p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                <span>@lang('Progress')</span>
                <span>{{ $totalInstallments > 0 ? number_format(($givenInstallments / $totalInstallments) * 100, 1) : 0 }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-300" 
                     style="width: {{ $totalInstallments > 0 ? ($givenInstallments / $totalInstallments) * 100 : 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Responsive Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @lang('S.N.')
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @lang('Installment Date')
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @if($isFdr)
                            @lang('Given On')
                        @elseif($isDps)
                            @lang('Given On')
                        @elseif($isLoan)
                            @lang('Payment Date')
                        @else
                            @lang('Payment Date')
                        @endif
                    </th>
                    @if (!Route::is('user.fdr.instalment.logs'))
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @lang('Delay')
                    </th>
                    @endif
                    @if($isFdr || $isDps)
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @lang('Amount')
                    </th>
                    @endif
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @lang('Status')
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($installments as $installment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <!-- Serial Number -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {{ $loop->index + $installments->firstItem() }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Installment Date -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="las la-calendar mr-2 text-gray-400"></i>
                                <div>
                                    <div class="text-sm font-medium {{ !$installment->given_at && $installment->installment_date < today() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                        {{ showDateTime($installment->installment_date, 'd M, Y') }}
                                    </div>
                                    @if(!$installment->given_at && $installment->installment_date < today())
                                        <div class="text-xs text-red-500 dark:text-red-400">@lang('Overdue')</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Given On / Payment Date -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($installment->given_at)
                                <div class="flex items-center">
                                    <i class="las la-check-circle mr-2 text-green-500"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ showDateTime($installment->given_at, 'd M, Y') }}
                                        </div>
                                        <div class="text-xs text-green-600 dark:text-green-400">
                                            @if($isFdr)
                                                @lang('Received')
                                            @elseif($isDps)
                                                @lang('Paid')
                                            @elseif($isLoan)
                                                @lang('Paid')
                                            @else
                                                @lang('Completed')
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <i class="las la-clock mr-2 text-gray-400"></i>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($isFdr)
                                            @lang('Not yet')
                                        @elseif($isDps)
                                            @lang('Pending')
                                        @elseif($isLoan)
                                            @lang('Pending')
                                        @else
                                            @lang('Pending')
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </td>

                        <!-- Delay (only for non-FDR routes) -->
                        @if (!Route::is('user.fdr.instalment.logs'))
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($installment->given_at)
                                @if(method_exists($installment, 'delayInDays'))
                                    @php
                                        $delayDays = $installment->delayInDays();
                                    @endphp
                                    @if($delayDays > 0)
                                        <div class="flex items-center">
                                            <i class="las la-exclamation-triangle mr-1 text-amber-500"></i>
                                            <span class="text-sm font-medium text-amber-600 dark:text-amber-400">
                                                {{ $delayDays }} @lang('Day' . ($delayDays > 1 ? 's' : ''))
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <i class="las la-check mr-1 text-green-500"></i>
                                            <span class="text-sm text-green-600 dark:text-green-400">@lang('On Time')</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            @else
                                @if($installment->installment_date < today())
                                    @php
                                        $overdueDays = today()->diffInDays($installment->installment_date);
                                    @endphp
                                    <div class="flex items-center">
                                        <i class="las la-exclamation-circle mr-1 text-red-500"></i>
                                        <span class="text-sm font-medium text-red-600 dark:text-red-400">
                                            {{ $overdueDays }} @lang('Day' . ($overdueDays > 1 ? 's' : '')) @lang('Late')
                                        </span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">...</span>
                                @endif
                            @endif
                        </td>
                        @endif

                        <!-- Amount (for FDRs and DPS) -->
                        @if($isFdr || $isDps)
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isFdr)
                                @if($installment->given_at && isset($installment->profit_amount))
                                    <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                        +{{ showAmount($installment->profit_amount) }} {{ gs()->cur_text }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            @elseif($isDps)
                                @if($installment->given_at)
                                    <div class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                        {{ showAmount($dps->per_installment) }} {{ gs()->cur_text }}
                                    </div>
                                @else
                                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {{ showAmount($dps->per_installment) }} {{ gs()->cur_text }}
                                    </div>
                                @endif
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                            @endif
                        </td>
                        @endif

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($installment->given_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    <i class="las la-check mr-1"></i>
                                    @if($isFdr)
                                        @lang('Received')
                                    @elseif($isDps)
                                        @lang('Paid')
                                    @elseif($isLoan)
                                        @lang('Paid')
                                    @else
                                        @lang('Completed')
                                    @endif
                                </span>
                            @elseif($installment->installment_date < today())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                    <i class="las la-exclamation-triangle mr-1"></i>
                                    @lang('Overdue')
                                </span>
                            @elseif($installment->installment_date == today())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                                    <i class="las la-clock mr-1"></i>
                                    @lang('Due Today')
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                    <i class="las la-calendar mr-1"></i>
                                    @lang('Upcoming')
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="las la-calendar-times text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No Installments Found')</h3>
                                <p class="text-gray-500 dark:text-gray-400">{{ __($emptyMessage ?? 'No installment data available') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($installments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    @lang('Showing') {{ $installments->firstItem() }} @lang('to') {{ $installments->lastItem() }} 
                    @lang('of') {{ $installments->total() }} @lang('installments')
                </div>
                <div class="pagination-wrapper">
                    {{ paginateLinks($installments) }}
                </div>
            </div>
        </div>
    @endif
</div>

@push('style')
<style>
/* Custom pagination styling */
.pagination-wrapper .pagination {
    @apply flex space-x-1;
}

.pagination-wrapper .page-item {
    @apply inline-flex;
}

.pagination-wrapper .page-link {
    @apply px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors;
}

.pagination-wrapper .page-item.active .page-link {
    @apply bg-blue-600 text-white border-blue-600 hover:bg-blue-700;
}

.pagination-wrapper .page-item.disabled .page-link {
    @apply text-gray-400 dark:text-gray-500 cursor-not-allowed hover:bg-white dark:hover:bg-gray-800;
}

/* Table responsive enhancements */
@media (max-width: 768px) {
    .table-mobile {
        display: block;
    }
    
    .table-mobile thead {
        display: none;
    }
    
    .table-mobile tbody,
    .table-mobile tr,
    .table-mobile td {
        display: block;
        width: 100%;
    }
    
    .table-mobile tr {
        @apply mb-4 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700;
    }
    
    .table-mobile td {
        @apply px-4 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0;
        position: relative;
        padding-left: 35% !important;
    }
    
    .table-mobile td:before {
        content: attr(data-label) ": ";
        position: absolute;
        left: 1rem;
        width: 30%;
        @apply font-medium text-gray-600 dark:text-gray-400 text-sm;
    }
}

/* Status badge animations */
.status-badge {
    @apply transition-all duration-200;
}

.status-badge:hover {
    @apply transform scale-105;
}

/* Row hover effects */
tbody tr {
    @apply transition-all duration-150;
}

tbody tr:hover {
    @apply shadow-sm;
}
</style>
@endpush