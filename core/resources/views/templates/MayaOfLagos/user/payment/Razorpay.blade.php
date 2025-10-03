@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .razorpay-container {
        background: linear-gradient(135deg, 
            rgba(79, 70, 229, 0.1) 0%, 
            rgba(147, 51, 234, 0.1) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
    }
    
    .payment-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 32px;
        position: relative;
        overflow: hidden;
    }
    
    .payment-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        border-radius: 16px 16px 0 0;
    }
    
    .razorpay-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        gap: 12px;
    }
    
    .razorpay-logo h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #3b82f6;
        margin: 0;
    }
    
    .payment-summary {
        background: linear-gradient(135deg, 
            rgba(59, 130, 246, 0.1) 0%, 
            rgba(29, 78, 216, 0.1) 100%);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        text-align: center;
    }
    
    .amount-display {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
    }
    
    .currency-display {
        font-size: 1.25rem;
        font-weight: 700;
        color: #3b82f6;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .payment-info {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 20px;
        margin: 24px 0;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #6b7280;
        font-weight: 500;
    }
    
    .dark .info-label {
        color: #9ca3af;
    }
    
    .info-value {
        color: #111827;
        font-weight: 600;
    }
    
    .dark .info-value {
        color: #f9fafb;
    }
    
    .pay-button {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border: none;
        color: white;
        padding: 18px 32px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        position: relative;
        overflow: hidden;
    }
    
    .pay-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .pay-button:hover::before {
        left: 100%;
    }
    
    .pay-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.4);
    }
    
    .pay-button:active {
        transform: translateY(-1px);
    }
    
    .pay-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .pay-button.loading {
        pointer-events: none;
    }
    
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .razorpay-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin: 24px 0;
    }
    
    .feature-item {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }
    
    .feature-icon {
        font-size: 2rem;
        margin-bottom: 8px;
        color: #3b82f6;
    }
    
    .feature-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
        font-size: 14px;
    }
    
    .dark .feature-title {
        color: #f9fafb;
    }
    
    .feature-desc {
        font-size: 12px;
        color: #6b7280;
    }
    
    .dark .feature-desc {
        color: #9ca3af;
    }
    
    .security-note {
        background: linear-gradient(135deg, 
            rgba(16, 185, 129, 0.1) 0%, 
            rgba(5, 150, 105, 0.1) 100%);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 12px;
        padding: 16px;
        margin-top: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .security-icon {
        font-size: 1.5rem;
        color: #10b981;
    }
    
    .security-text {
        color: #374151;
        font-size: 14px;
    }
    
    .dark .security-text {
        color: #f9fafb;
    }
    
    @media (max-width: 768px) {
        .amount-display {
            font-size: 2rem;
        }
        
        .razorpay-features {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Razorpay Payment')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('India\'s leading payment gateway')</p>
        </div>

        <div class="razorpay-container">
            <div class="payment-card">
                <!-- Razorpay Logo -->
                <div class="razorpay-logo">
                    <h2>Razorpay</h2>
                    <i class="las la-bolt" style="font-size: 2rem; color: #3b82f6;"></i>
                </div>

                <!-- Payment Summary -->
                <div class="payment-summary">
                    <div class="amount-display">{{ showAmount($deposit->final_amount) }}</div>
                    <div class="currency-display">{{ $deposit->method_currency }}</div>
                </div>

                <!-- Payment Info -->
                <div class="payment-info">
                    <div class="info-row">
                        <span class="info-label">@lang('Requested Amount')</span>
                        <span class="info-value">{{ showAmount($deposit->amount) }} {{ gs('cur_text') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">@lang('Processing Fee')</span>
                        <span class="info-value">{{ showAmount($deposit->charge) }} {{ gs('cur_text') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">@lang('Exchange Rate')</span>
                        <span class="info-value">1 {{ gs('cur_text') }} = {{ showAmount($deposit->rate) }} {{ $deposit->method_currency }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">@lang('Total Payable')</span>
                        <span class="info-value">{{ showAmount($deposit->final_amount) }} {{ $deposit->method_currency }}</span>
                    </div>
                </div>

                <!-- Pay Button -->
                <form action="{{ $data->url }}" method="{{ $data->method }}" class="razorpay-form">
                    @csrf
                    <input type="hidden" custom="{{ $data->custom }}" name="hidden">
                    
                    <script src="{{ $data->checkout_js }}" 
                        @foreach ($data->val as $key => $value)
                            data-{{ $key }}="{{ $value }}"
                        @endforeach>
                    </script>
                </form>

                <!-- Security Note -->
                <div class="security-note">
                    <i class="las la-shield-alt security-icon"></i>
                    <div class="security-text">
                        <strong>@lang('RBI Approved'):</strong> @lang('Razorpay is RBI approved and follows the highest security standards with 256-bit SSL encryption.')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        // Style the Razorpay submit button with our custom styling
        $('input[type="submit"]').addClass("pay-button w-100").html('<i class="las la-bolt"></i> @lang("Pay with Razorpay")');
        
        // Add loading state when Razorpay form is submitted
        $('form').on('submit', function() {
            const submitBtn = $('input[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> @lang("Processing...")');
        });
    })(jQuery);
</script>
@endpush