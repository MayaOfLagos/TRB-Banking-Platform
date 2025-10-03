@extends($activeTemplate . 'pdf.layouts.master')

@section('pdf-content')
    <!-- Summary Statistics -->
    @if($allDps->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total DPS -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $allDps->count() }}</div>
            <div class="text-sm font-medium text-gray-600 uppercase tracking-wider">Total DPS</div>
        </div>
        
        <!-- Total Deposited -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ showAmount($allDps->sum('depositedAmount')) }}</div>
            <div class="text-sm font-medium text-gray-600 uppercase tracking-wider">Total Deposited ({{ gs()->cur_text }})</div>
        </div>
        
        <!-- Expected Maturity -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ showAmount($allDps->sum('final_amount')) }}</div>
            <div class="text-sm font-medium text-gray-600 uppercase tracking-wider">Expected Maturity ({{ gs()->cur_text }})</div>
        </div>
    </div>
    @endif

    <!-- DPS List Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DPS Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($allDps as $dps)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm font-semibold text-gray-900">#{{ $dps->dps_number }}</div>
                            <div class="text-xs text-gray-500">{{ showDateTime($dps->created_at, 'd M Y') }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm font-medium text-gray-900">{{ __($dps->plan->name) }}</div>
                            <div class="text-xs text-gray-600">{{ getAmount($dps->interest_rate) }}% Interest</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm font-semibold text-blue-600">{{ showAmount($dps->per_installment) }} {{ gs()->cur_text }}</div>
                            <div class="text-xs text-gray-600">Per {{ $dps->installment_interval }} {{ __(Str::plural('Day', $dps->installment_interval)) }}</div>
                            <div class="text-xs text-orange-600">{{ $dps->given_installment }}/{{ $dps->total_installment }} Paid</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($dps->nextInstallment)
                            <div class="text-sm font-medium text-gray-900">{{ showDateTime($dps->nextInstallment->installment_date, 'd M Y') }}</div>
                        @else
                            <div class="text-sm text-gray-500">Completed</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-sm font-semibold text-gray-900">{{ showAmount($dps->per_installment * $dps->given_installment) }} {{ gs()->cur_text }}</div>
                            <div class="text-xs text-green-600">+{{ showAmount($dps->profit) }} {{ gs()->cur_text }}</div>
                            <div class="text-xs text-purple-600 font-medium">{{ showAmount(($dps->per_installment * $dps->given_installment) + $dps->profit) }} {{ gs()->cur_text }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($dps->status == 1)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Running
                            </span>
                        @elseif($dps->status == 2)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Completed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $dps->statusText }}
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">No DPS Found</h3>
                                <p class="text-gray-600">No DPS records found for the selected criteria.</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Report Information -->
    @if($allDps->count() > 0)
    <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Report Information</h3>
        <div class="text-sm text-gray-600 space-y-2">
            <p>This is a computer-generated report and does not require a signature.</p>
            <p>For any queries, please contact our customer service.</p>
            @if($allDps->count() > 0)
                <p class="mt-4 pt-4 border-t border-gray-300">
                    <strong>Summary:</strong> {{ $allDps->count() }} DPS records • 
                    Total Deposited: {{ showAmount($allDps->sum('depositedAmount')) }} {{ gs()->cur_text }} • 
                    Expected Maturity: {{ showAmount($allDps->sum('final_amount')) }} {{ gs()->cur_text }}
                </p>
            @endif
        </div>
    </div>
    @endif
@endsection

@push('pdf-style')
<style>
    /* PDF-specific optimizations for DPS List */
    @media print {
        .page-break-inside-avoid {
            page-break-inside: avoid;
            break-inside: avoid;
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
    }
    
    /* Enhanced styling for PDF */
    .divide-y > * + * {
        border-top-width: 1px;
        border-color: #e5e7eb;
    }
    
    .inline-flex {
        display: inline-flex;
        align-items: center;
    }
    
    .whitespace-nowrap {
        white-space: nowrap;
    }
</style>
@endpush