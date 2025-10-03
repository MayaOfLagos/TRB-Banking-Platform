@extends($activeTemplate . 'pdf.layouts.master')

@section('pdf-content')
    <!-- Summary Statistics -->
    @if($allFdr->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total FDRs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $allFdr->count() }}</div>
            <div class="text-sm font-medium text-gray-600 uppercase tracking-wider">Total FDRs</div>
        </div>
        
        <!-- Running FDRs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ $allFdr->where('status', 1)->count() }}</div>
            <div class="text-sm font-medium text-gray-600 uppercase tracking-wider">Running FDRs</div>
        </div>
        
        <!-- Total Investment -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-yellow-600 mb-2">{{ showAmount($allFdr->sum('amount')) }}</div>
            <div class="text-sm font-medium text-gray-600 uppercase tracking-wider">Total Investment ({{ gs()->cur_text }})</div>
        </div>
        
        <!-- Total Profit -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ showAmount($allFdr->sum('profit')) }}</div>
            <div class="text-sm font-medium text-gray-600 uppercase tracking-wider">Total Profit ({{ gs()->cur_text }})</div>
        </div>
    </div>
    @endif

    <!-- FDR List Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FDR Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit & Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($allFdr as $fdr)
                <tr class="hover:bg-gray-50">
                    <!-- FDR Details -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm font-semibold text-gray-900">{{ __($fdr->plan->name) }}</div>
                            <div class="text-xs text-blue-600 font-medium">#{{ $fdr->fdr_number }}</div>
                            <div class="text-xs text-gray-500">{{ showDateTime($fdr->created_at, 'd M Y') }}</div>
                        </div>
                    </td>
                    
                    <!-- Investment -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm font-semibold text-gray-900">{{ showAmount($fdr->amount) }} {{ gs()->cur_text }}</div>
                            <div class="text-xs text-gray-600">Principal Amount</div>
                        </div>
                    </td>
                    
                    <!-- Profit & Rate -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm font-semibold text-green-600">+{{ showAmount($fdr->profit) }} {{ gs()->cur_text }}</div>
                            <div class="text-xs text-gray-600">{{ getAmount($fdr->interest_rate) }}% Interest</div>
                            @if($fdr->per_installment > 0)
                            <div class="text-xs text-blue-600">{{ showAmount($fdr->per_installment) }} per installment</div>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Schedule -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            @if ($fdr->status != 2 && $fdr->next_installment_date)
                            <div class="text-sm font-medium text-gray-900">{{ showDateTime($fdr->next_installment_date, 'd M Y') }}</div>
                            <div class="text-xs text-gray-600">Every {{ $fdr->installment_interval }} {{__(Str::plural('Day', $fdr->installment_interval))}}</div>
                            @else
                            <div class="text-sm text-gray-500">No pending installments</div>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Duration -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm font-medium text-gray-900">{{ showDateTime($fdr->locked_date, 'd M Y') }}</div>
                            @if($fdr->status == 1)
                                @php
                                    $remainingDays = \Carbon\Carbon::parse($fdr->locked_date)->diffInDays(now(), false);
                                    $remainingDays = $remainingDays > 0 ? $remainingDays : 0;
                                @endphp
                                <div class="text-xs text-orange-600">{{ $remainingDays }} days remaining</div>
                            @else
                                <div class="text-xs text-gray-500">Completed</div>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($fdr->status == 1)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Running
                            </span>
                        @elseif($fdr->status == 2)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Closed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Due
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="space-y-4">
                            <div class="flex justify-center">
                                <svg class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">No FDRs Found</h3>
                                <p class="text-gray-600">{{ __($emptyMessage ?? 'No FDR records match your search criteria.') }}</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Additional Information -->
    @if($allFdr->count() > 0)
    <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Report Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Investment Summary</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Average Investment:</span>
                        <span class="font-medium">{{ showAmount($allFdr->avg('amount')) }} {{ gs()->cur_text }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Average Interest Rate:</span>
                        <span class="font-medium">{{ number_format($allFdr->avg('interest_rate'), 2) }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Profit Earned:</span>
                        <span class="font-medium text-green-600">{{ showAmount($allFdr->where('status', 2)->sum('profit')) }} {{ gs()->cur_text }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Performance Metrics</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Completion Rate:</span>
                        <span class="font-medium">{{ $allFdr->count() > 0 ? number_format(($allFdr->where('status', 2)->count() / $allFdr->count()) * 100, 1) : 0 }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Active Investments:</span>
                        <span class="font-medium">{{ $allFdr->where('status', 1)->count() }} FDRs</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Portfolio Value:</span>
                        <span class="font-medium text-blue-600">{{ showAmount($allFdr->sum('amount') + $allFdr->sum('profit')) }} {{ gs()->cur_text }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('pdf-style')
<style>
    /* PDF-specific optimizations */
    @media print {
        .page-break-inside-avoid {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        .page-break-before {
            page-break-before: always;
            break-before: page;
        }
        
        /* Ensure colors are preserved in PDF */
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Optimize table for print */
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        thead {
            display: table-header-group;
        }
        
        tfoot {
            display: table-footer-group;
        }
    }
    
    /* Enhanced table styling for PDF */
    .divide-y > * + * {
        border-top-width: 1px;
        border-color: #e5e7eb;
    }
    
    .divide-gray-200 > * + * {
        border-color: #e5e7eb;
    }
    
    /* Status badge enhancements */
    .inline-flex {
        display: inline-flex;
        align-items: center;
    }
    
    /* Ensure proper spacing in PDF */
    .whitespace-nowrap {
        white-space: nowrap;
    }
</style>
@endpush