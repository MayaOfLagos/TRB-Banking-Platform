@extends($activeTemplate . 'layouts.master')

@php
use App\Constants\Status;
@endphp

@push('style')
<style>
    .detail-card {
        background: linear-gradient(135deg, 
            rgba(79, 70, 229, 0.1) 0%, 
            rgba(147, 51, 234, 0.1) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .status-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #fef3c7, #fbbf24);
        color: #92400e;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #d1fae5, #10b981);
        color: #065f46;
    }
    
    .status-rejected {
        background: linear-gradient(135deg, #fee2e2, #ef4444);
        color: #991b1b;
    }
    
    .info-item {
        padding: 16px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-item:hover {
        background: rgba(255, 255, 255, 0.02);
        padding-left: 8px;
        padding-right: 8px;
        margin-left: -8px;
        margin-right: -8px;
        border-radius: 8px;
    }
    
    .info-label {
        color: #9ca3af;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 4px;
    }
    
    .info-value {
        color: #111827;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .dark .info-value {
        color: #f9fafb;
    }
    
    .amount-display {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .conversion-display {
        font-size: 1.25rem;
        font-weight: 600;
        color: #059669;
    }
    
    .charge-display {
        color: #dc2626;
        font-weight: 600;
    }
    
    .btn-back {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #374151;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .dark .btn-back {
        color: #f9fafb;
    }
    
    .btn-back:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        color: inherit;
        text-decoration: none;
    }
    
    .gateway-info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 12px;
        padding: 16px;
    }
    
    .submitted-data-card {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 12px;
    }
    
    .rejection-card {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: 12px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="mb-6 lg:mb-0">
            <div class="flex items-center mb-4">
                <a href="{{ route('user.deposit.history') }}" class="btn-back inline-flex items-center mr-4">
                    <i class="las la-arrow-left mr-2"></i>
                    @lang('Back')
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Deposit Details')</h1>
                </div>
            </div>
        </div>
        
        <!-- Status Badge -->
        <div>
            @php
                $statusClass = 'status-badge ';
                switch($deposit->status) {
                    case Status::PAYMENT_SUCCESS:
                        $statusClass .= 'status-approved';
                        $statusText = 'Completed';
                        $statusIcon = 'las la-check-circle';
                        break;
                    case Status::PAYMENT_PENDING:
                        $statusClass .= 'status-pending';
                        $statusText = 'Pending';
                        $statusIcon = 'las la-clock';
                        break;
                    case Status::PAYMENT_REJECT:
                        $statusClass .= 'status-rejected';
                        $statusText = 'Rejected';
                        $statusIcon = 'las la-times-circle';
                        break;
                    default:
                        $statusClass .= 'status-pending';
                        $statusText = 'Unknown';
                        $statusIcon = 'las la-question-circle';
                }
            @endphp
            <span class="{{ $statusClass }}">
                <i class="{{ $statusIcon }} mr-2"></i>
                {{ $statusText }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main Deposit Information -->
        <div class="xl:col-span-2">
            <div class="detail-card rounded-2xl p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mr-3">
                        <i class="las la-info-circle text-blue-600 dark:text-blue-400"></i>
                    </div>
                    @lang('Transaction Information')
                </h2>
                
                <div class="space-y-0">
                    <div class="info-item">
                        <div class="info-label">@lang('Transaction ID')</div>
                        <div class="info-value font-mono">#{{ $deposit->trx }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">@lang('Requested Amount')</div>
                        <div class="amount-display">{{ showUserAmount($deposit->amount, auth()->user()) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">@lang('Processing Charge')</div>
                        <div class="charge-display">{{ showUserAmount($deposit->charge, auth()->user()) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">@lang('Total Amount')</div>
                        <div class="info-value text-lg font-bold">{{ showUserAmount($deposit->amount + $deposit->charge, auth()->user()) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">@lang('Conversion Rate')</div>
                        <div class="info-value">
                            1 {{ getUserCurrency(auth()->user())['text'] }} = {{ showAmount($deposit->rate, currencyFormat: false) }} {{ __($deposit->method_currency) }}
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">@lang('Amount in :currency', ['currency' => $deposit->method_currency])</div>
                        <div class="conversion-display">
                            {{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">@lang('Date & Time')</div>
                        <div class="info-value">{{ showDateTime($deposit->created_at) }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gateway Information -->
        <div class="xl:col-span-1">
            <div class="detail-card rounded-2xl p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mr-3">
                        <i class="las la-credit-card text-purple-600 dark:text-purple-400"></i>
                    </div>
                    @lang('Payment Gateway')
                </h2>
                
                @if ($deposit->branch)
                    <div class="gateway-info">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center mr-4">
                                <i class="las la-university text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">@lang('Branch Deposit')</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __(@$deposit->branch->name) }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            @lang('This deposit was processed through a bank branch.')
                        </div>
                    </div>
                @else
                    <div class="gateway-info">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 rounded-xl overflow-hidden mr-4">
                                <img src="{{ getImage(getFilePath('gateway') . '/' . $deposit->gateway->image) }}" 
                                     alt="{{ $deposit->gateway->name }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __(@$deposit->gateway->name) }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Payment Gateway')</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            @lang('Processed through :gateway payment system', ['gateway' => $deposit->gateway->name])
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Rejection Reason (if applicable) -->
    @if ($deposit->status == Status::PAYMENT_REJECT)
        <div class="detail-card rounded-2xl p-6 mb-6">
            <div class="rejection-card p-6">
                <h2 class="text-xl font-bold text-red-700 dark:text-red-400 mb-4 flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center mr-3">
                        <i class="las la-exclamation-triangle text-red-600 dark:text-red-400"></i>
                    </div>
                    @lang('Reason for Rejection')
                </h2>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <p class="text-red-800 dark:text-red-200">{{ $deposit->admin_feedback }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Submitted Data (for manual gateways) -->
    @if ($deposit->method_code >= 1000)
        <div class="detail-card rounded-2xl p-6">
            <div class="submitted-data-card p-6">
                <h2 class="text-xl font-bold text-green-700 dark:text-green-400 mb-6 flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center mr-3">
                        <i class="las la-file-alt text-green-600 dark:text-green-400"></i>
                    </div>
                    @lang('Submitted Information')
                </h2>
                
                @php
                    $details = $deposit->detail != null ? $deposit->detail : null;
                @endphp
                
                @if($details)
                    <div class="space-y-0">
                        @foreach ($details as $detail)
                            <div class="info-item">
                                <div class="info-label">{{ $detail->name }}</div>
                                <div class="info-value">
                                    @if ($detail->type == 'checkbox')
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($detail->value as $value)
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded text-sm">{{ $value }}</span>
                                            @endforeach
                                        </div>
                                    @elseif($detail->type == 'file')
                                        @if ($detail->value)
                                            <a href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $detail->value)) }}" 
                                               class="inline-flex items-center px-3 py-2 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 text-blue-800 dark:text-blue-200 rounded-lg transition-colors">
                                                <i class="las la-download mr-2"></i>
                                                @lang('Download Attachment')
                                            </a>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400 italic">@lang('No file uploaded')</span>
                                        @endif
                                    @else
                                        <p>{{ __($detail->value) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400">@lang('No additional information was submitted.')</p>
                @endif
            </div>
        </div>
    @endif
    
    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 mt-8">
        <a href="{{ route('user.deposit.history') }}" class="btn-back inline-flex items-center justify-center">
            <i class="las la-arrow-left mr-2"></i>
            @lang('Back to Deposits')
        </a>
        
        @if($deposit->status == Status::PAYMENT_PENDING)
            <a href="{{ route('user.deposit.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                <i class="las la-plus mr-2"></i>
                @lang('Make Another Deposit')
            </a>
        @endif
        
        @if($deposit->status == Status::PAYMENT_SUCCESS)
            <a href="{{ route('user.transaction.history') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                <i class="las la-list mr-2"></i>
                @lang('View Transactions')
            </a>
        @endif
    </div>
</div>
@endsection