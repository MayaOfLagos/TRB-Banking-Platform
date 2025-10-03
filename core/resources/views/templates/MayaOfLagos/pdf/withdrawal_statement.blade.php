<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Withdrawal Statement</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            color: #333; 
            line-height: 1.4;
        }
        .header { 
            border-bottom: 3px solid #4F46E5; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
        }
        .company-info { 
            text-align: center; 
        }
        .company-name { 
            font-size: 28px; 
            font-weight: bold; 
            color: #4F46E5; 
            margin-bottom: 10px; 
        }
        .company-details {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .statement-title { 
            text-align: center; 
            font-size: 22px; 
            font-weight: bold; 
            margin: 30px 0; 
            color: #1F2937;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .user-info { 
            background: #F8FAFC; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 25px;
            border-left: 4px solid #4F46E5;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row { 
            display: table-row;
        }
        .info-row .label { 
            display: table-cell;
            font-weight: bold; 
            width: 30%;
            padding: 5px 10px 5px 0;
            color: #374151;
        }
        .info-row .value {
            display: table-cell;
            padding: 5px 0;
            color: #1F2937;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
            font-size: 12px;
        }
        th, td { 
            border: 1px solid #E5E7EB; 
            padding: 10px 8px; 
            text-align: left; 
        }
        th { 
            background-color: #4F46E5; 
            color: white; 
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .text-right { 
            text-align: right; 
        }
        .text-center {
            text-align: center;
        }
        .status-approved { 
            color: #059669; 
            font-weight: bold;
            background: #ECFDF5;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
        }
        .status-pending { 
            color: #D97706; 
            font-weight: bold;
            background: #FFFBEB;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
        }
        .status-rejected { 
            color: #DC2626; 
            font-weight: bold;
            background: #FEF2F2;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
        }
        .summary { 
            margin-top: 30px; 
            background: #F8FAFC; 
            padding: 20px; 
            border-radius: 8px;
            border-left: 4px solid #10B981;
        }
        .summary-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #1F2937;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-row {
            display: table-row;
        }
        .summary-label {
            display: table-cell;
            font-weight: bold;
            width: 60%;
            padding: 8px 10px 8px 0;
            color: #374151;
        }
        .summary-value {
            display: table-cell;
            text-align: right;
            padding: 8px 0;
            color: #1F2937;
            font-weight: 600;
        }
        .footer { 
            margin-top: 50px; 
            text-align: center; 
            font-size: 11px; 
            color: #6B7280;
            border-top: 1px solid #E5E7EB;
            padding-top: 20px;
        }
        .footer p {
            margin: 5px 0;
        }
        .page-break {
            page-break-before: always;
        }
        .currency {
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .amount-positive {
            color: #059669;
        }
        .amount-negative {
            color: #DC2626;
        }
        .filters-applied {
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .filters-title {
            font-weight: bold;
            color: #1E40AF;
            margin-bottom: 8px;
        }
        .filter-item {
            display: inline-block;
            background: #3B82F6;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            margin: 2px 4px 2px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $company->site_name ?? 'Company Name' }}</div>
            <div class="company-details">{{ $company->address ?? 'Company Address' }}</div>
            <div class="company-details">
                Phone: {{ $company->phone ?? 'N/A' }} | 
                Email: {{ $company->email ?? 'N/A' }} | 
                Website: {{ $company->website ?? url('/') }}
            </div>
        </div>
    </div>

    <div class="statement-title">Withdrawal Statement</div>

    <!-- Applied Filters (if any) -->
    @if(collect($filters)->filter()->count() > 0)
        <div class="filters-applied">
            <div class="filters-title">Applied Filters:</div>
            @if(!empty($filters['search']))
                <span class="filter-item">Search: {{ $filters['search'] }}</span>
            @endif
            @if(!empty($filters['status']))
                <span class="filter-item">Status: {{ 
                    $filters['status'] == 1 ? 'Approved' : 
                    ($filters['status'] == 2 ? 'Pending' : 'Rejected') 
                }}</span>
            @endif
            @if(!empty($filters['method']))
                <span class="filter-item">Method: {{ $filters['method'] }}</span>
            @endif
            @if(!empty($filters['date_from']))
                <span class="filter-item">From: {{ date('M d, Y', strtotime($filters['date_from'])) }}</span>
            @endif
            @if(!empty($filters['date_to']))
                <span class="filter-item">To: {{ date('M d, Y', strtotime($filters['date_to'])) }}</span>
            @endif
        </div>
    @endif

    <!-- User Information -->
    <div class="user-info">
        <h3 style="margin-top: 0; color: #1F2937; font-size: 16px;">Account Holder Information</h3>
        <div class="info-section">
            <div class="info-row">
                <div class="label">Full Name:</div>
                <div class="value">{{ $user->firstname }} {{ $user->lastname }}</div>
            </div>
            <div class="info-row">
                <div class="label">Username:</div>
                <div class="value">{{ $user->username }}</div>
            </div>
            <div class="info-row">
                <div class="label">Email Address:</div>
                <div class="value">{{ $user->email }}</div>
            </div>
            @if($user->mobile)
            <div class="info-row">
                <div class="label">Mobile Number:</div>
                <div class="value">{{ $user->mobile }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="label">Account Status:</div>
                <div class="value">{{ $user->status ? 'Active' : 'Inactive' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Statement Generated:</div>
                <div class="value">{{ $generated_at->format('F d, Y \a\t H:i:s T') }}</div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Transaction ID</th>
                <th style="width: 12%;">Date</th>
                <th style="width: 15%;">Method</th>
                <th style="width: 12%;">Amount</th>
                <th style="width: 12%;">Charge</th>
                <th style="width: 12%;">Received</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 10%;">Currency</th>
            </tr>
        </thead>
        <tbody>
            @forelse($withdraws as $withdraw)
                <tr>
                    <td style="font-family: monospace; font-size: 10px;">#{{ $withdraw->trx }}</td>
                    <td>{{ $withdraw->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($withdraw->method)
                            {{ $withdraw->method->name }}
                        @elseif($withdraw->branch)
                            {{ $withdraw->branch->name }} (Branch)
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-right currency">{{ showAmount($withdraw->amount) }}</td>
                    <td class="text-right currency amount-negative">-{{ showAmount($withdraw->charge) }}</td>
                    <td class="text-right currency amount-positive">{{ showAmount($withdraw->after_charge) }}</td>
                    <td class="text-center">
                        @if($withdraw->status == 1)
                            <span class="status-approved">APPROVED</span>
                        @elseif($withdraw->status == 2)
                            <span class="status-pending">PENDING</span>
                        @elseif($withdraw->status == 3)
                            <span class="status-rejected">REJECTED</span>
                        @else
                            <span class="status-pending">PROCESSING</span>
                        @endif
                    </td>
                    <td class="text-center">{{ gs('cur_text') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: #6B7280;">
                        No withdrawal transactions found for the selected criteria
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-title">Transaction Summary</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-label">Total Withdrawal Amount:</div>
                <div class="summary-value currency">{{ showAmount($total_amount) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Processing Charges:</div>
                <div class="summary-value currency amount-negative">-{{ showAmount($total_charge) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Amount Received:</div>
                <div class="summary-value currency amount-positive"><strong>{{ showAmount($total_received) }}</strong></div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Number of Transactions:</div>
                <div class="summary-value">{{ $withdraws->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Statement Period:</div>
                <div class="summary-value">
                    @if($withdraws->count() > 0)
                        {{ $withdraws->min('created_at')->format('M d, Y') }} - {{ $withdraws->max('created_at')->format('M d, Y') }}
                    @else
                        N/A
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Important Notice:</strong> This is a computer-generated statement and does not require a signature.</p>
        <p>All amounts are displayed in {{ gs('cur_text') }} ({{ gs('cur_sym') }})</p>
        <p>For any queries regarding this statement, please contact our support team.</p>
        <p>Generated on {{ $generated_at->format('F d, Y \a\t H:i:s T') }} | Document ID: WS-{{ date('Ymd') }}-{{ $user->id }}</p>
        <p style="margin-top: 15px;">© {{ date('Y') }} {{ $company->site_name ?? 'Company Name' }}. All rights reserved.</p>
    </div>
</body>
</html>