@extends($activeTemplate . 'pdf.layouts.master')

@section('pdf-content')
    <!-- Certificate Header -->
    <div class="text-center mb-8 p-8 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border-l-4 border-blue-600">
        <div class="space-y-4">
            <h1 class="text-4xl font-bold text-gray-900">Deposit Pension Scheme</h1>
            <h2 class="text-xl text-blue-600 font-semibold">Investment Certificate</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-purple-600 mx-auto rounded-full"></div>
        </div>
    </div>

    <!-- Certificate Body -->
    <div class="space-y-6">
        <div class="text-center text-gray-700 text-lg">
            <p>This certifies that the following DPS investment has been registered with our institution:</p>
        </div>

        <!-- DPS Details Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
            <table class="min-w-full">
                <tbody class="divide-y divide-gray-200">
                    <tr class="bg-gray-50">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700 w-1/3">DPS Number:</td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-600 text-lg">#{{ $dps->dps_number }}</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">Plan Name:</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ __($dps->plan->name) }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">Account Holder:</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $dps->user->fullname }}</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">Opening Date:</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ showDateTime($dps->created_at, 'd M, Y') }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">Installment Interval:</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $dps->plan->installment_interval }} {{ __(Str::plural('Day', $dps->plan->installment_interval)) }}</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">Total Installments:</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $dps->plan->total_installment }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">Per Installment Amount:</td>
                        <td class="px-6 py-4 text-sm font-bold text-green-600">{{ showAmount($dps->plan->per_installment) }} {{ gs()->cur_text }}</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">Total Deposit Amount:</td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ showAmount($dps->plan->per_installment * $dps->plan->total_installment) }} {{ gs()->cur_text }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">Interest Rate:</td>
                        <td class="px-6 py-4 text-sm font-medium text-orange-600">{{ getAmount($dps->plan->interest_rate) }}% per annum</td>
                    </tr>
                    <tr class="bg-gradient-to-r from-purple-50 to-blue-50 border-l-4 border-purple-600">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">Maturity Amount:</td>
                        <td class="px-6 py-4 text-lg font-bold text-purple-600">{{ showAmount($dps->plan->final_amount) }} {{ gs()->cur_text }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Progress Section -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6 mb-6 border-l-4 border-green-600">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Investment Progress</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $dps->given_installment }}/{{ $dps->total_installment }}</div>
                    <div class="text-sm text-gray-600">Installments Paid</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ showAmount($dps->per_installment * $dps->given_installment) }}</div>
                    <div class="text-sm text-gray-600">Amount Deposited ({{ gs()->cur_text }})</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ showAmount($dps->profit) }}</div>
                    <div class="text-sm text-gray-600">Expected Profit ({{ gs()->cur_text }})</div>
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="text-center mb-6">
            @if($dps->status == 1)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Status: Running
                </span>
            @else
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                    Status: {{ $dps->statusText }}
                </span>
            @endif
        </div>

        <!-- Terms Section -->
        @if ($dps->plan->delay_value && $dps->plan->delay_charge)
        <div class="bg-yellow-50 rounded-lg p-6 border-l-4 border-yellow-400 mb-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-3">Terms and Conditions</h4>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    If an installment is delayed for {{ $dps->plan->delay_value }} or more days, a charge of {{ $dps->plan->delayCharge }} will be applied for each day.
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 mr-2 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    The total charge amount will be subtracted from the withdrawable amount.
                </li>
            </ul>
        </div>
        @endif

        <!-- Certificate Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-end">
                <div class="text-sm text-gray-600">
                    <p><strong>Generated on:</strong> {{ showDateTime(now(), 'd M Y, h:i A') }}</p>
                    <p><strong>Certificate ID:</strong> DPS-{{ $dps->id }}-{{ date('Ymd') }}</p>
                </div>
                <div class="text-center">
                    <div class="border-b-2 border-gray-300 w-48 mb-2"></div>
                    <p class="text-sm font-semibold text-gray-700">Authorized Signature</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('pdf-style')
<style>
    /* PDF-specific optimizations for DPS Certificate */
    @media print {
        /* Ensure colors are preserved in PDF */
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Prevent page breaks inside certificate sections */
        .page-break-inside-avoid {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
    
    /* Enhanced styling for certificate */
    .divide-y > * + * {
        border-top-width: 1px;
        border-color: #e5e7eb;
    }
    
    .inline-flex {
        display: inline-flex;
        align-items: center;
    }
</style>
@endpush
</div>
@endsection