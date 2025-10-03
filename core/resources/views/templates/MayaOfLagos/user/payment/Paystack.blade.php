@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .paystack-container {
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
        background: linear-gradient(90deg, #0ea5e9, #06b6d4);
        border-radius: 16px 16px 0 0;
    }
    
    .paystack-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        gap: 12px;
    }
    
    .paystack-logo h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0ea5e9;
        margin: 0;
    }
    
    .version-badge {
        background: rgba(14, 165, 233, 0.1);
        color: #0ea5e9;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .payment-summary {
        background: linear-gradient(135deg, 
            rgba(14, 165, 233, 0.1) 0%, 
            rgba(6, 182, 212, 0.1) 100%);
        border: 1px solid rgba(14, 165, 233, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        text-align: center;
    }
    
    .amount-display {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #0ea5e9, #06b6d4);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
    }
    
    .currency-display {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0ea5e9;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .payment-details {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 12px;
        padding: 16px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        margin-bottom: 24px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        color: #94a3b8;
        font-size: 0.9rem;
    }
    
    .detail-value {
        color: #fff;
        font-weight: 500;
    }
    
    .detail-value.total {
        color: #0ea5e9;
        font-weight: 700;
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
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
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
        box-shadow: 0 15px 30px rgba(14, 165, 233, 0.4);
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
    
    .hidden {
        display: none !important;
    }
    
    .security-features {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }
    
    .security-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(14, 165, 233, 0.1);
        color: #0ea5e9;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
        border: 1px solid rgba(14, 165, 233, 0.2);
    }
    
    .security-icon {
        font-size: 1rem;
    }
    
    .paystack-features {
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
        color: #0ea5e9;
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
        
        .paystack-features {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="w-full px-3 sm:px-4 py-4">
    <div class="flex justify-center">
        <div class="w-full lg:w-1/2 md:w-2/3">
            <div class="paystack-container p-4">
                <div class="payment-card">
                    <!-- Paystack Logo -->
                    <div class="paystack-logo">
                        <i class="fab fa-paypal" style="font-size: 2rem; color: #0ea5e9;"></i>
                        <h2>Paystack</h2>
                        <span class="version-badge">Africa</span>
                    </div>

                    <!-- Payment Summary -->
                    <div class="payment-summary">
                        <div class="amount-display">{{ showAmount($deposit->final_amount, currencyFormat: false) }}</div>
                        <div class="currency-display">{{ $deposit->method_currency }}</div>
                    </div>

                    <!-- Payment Details -->
                    <div class="payment-details">
                        <div class="detail-row">
                            <span class="detail-label">@lang('You have to pay')</span>
                            <span class="detail-value">{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">@lang('You will get')</span>
                            <span class="detail-value total">{{ showAmount($deposit->amount) }}</span>
                        </div>
                    </div>

                    <!-- Pay Button -->
                    <form action="{{ route('ipn.'.$deposit->gateway->alias) }}" method="POST" class="paystack-form text-center">
                        @csrf
                        
                        <button type="button" class="pay-button" id="btn-confirm">
                            <span id="pay-content">
                                <i class="las la-lock"></i>
                                @lang('Pay Now')
                            </span>
                            <span id="loading-content" class="hidden">
                                <i class="las la-spinner loading-spinner"></i>
                                @lang('Processing...')
                            </span>
                        </button>
                        
                        <script src="//js.paystack.co/v1/inline.js" 
                            data-key="{{ $data->key }}" 
                            data-email="{{ $data->email }}" 
                            data-amount="{{ round($data->amount) }}" 
                            data-currency="{{ $data->currency }}" 
                            data-ref="{{ $data->ref }}" 
                            data-custom-button="btn-confirm">
                        </script>
                    </form>

                    <!-- Security Features -->
                    <div class="security-features">
                        <div class="security-badge">
                            <i class="las la-shield-alt security-icon"></i>
                            PCI DSS Level 1
                        </div>
                        <div class="security-badge">
                            <i class="las la-mobile-alt security-icon"></i>
                            Mobile Money
                        </div>
                        <div class="security-badge">
                            <i class="las la-university security-icon"></i>
                            Bank Transfer
                        </div>
                        <div class="security-badge">
                            <i class="las la-credit-card security-icon"></i>
                            Cards
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    "use strict";
    (function($) {
        // Paystack payment handling - the inline script handles most of the work
        // We just need to provide visual feedback
        
        $('#btn-confirm').on('click', function() {
            const payBtn = $(this);
            const payContent = $('#pay-content');
            const loadingContent = $('#loading-content');
            
            // Show processing state
            payBtn.addClass('loading').attr('disabled', true);
            payContent.addClass('hidden');
            loadingContent.removeClass('hidden');
            
            // Reset after a short delay if payment modal doesn't open
            setTimeout(function() {
                if (payBtn.hasClass('loading')) {
                    payBtn.removeClass('loading').removeAttr('disabled');
                    payContent.removeClass('hidden');
                    loadingContent.addClass('hidden');
                }
            }, 3000);
        });
        
    })(jQuery);
</script>
@endpush