@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .voguepay-container {
        background: linear-gradient(135deg, 
            rgba(168, 85, 247, 0.1) 0%, 
            rgba(139, 92, 246, 0.1) 100%);
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
        background: linear-gradient(90deg, #a855f7, #8b5cf6);
        border-radius: 16px 16px 0 0;
    }
    
    .voguepay-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        gap: 12px;
    }
    
    .voguepay-logo h2 {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #a855f7, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
    }
    
    .nigeria-badge {
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .payment-summary {
        background: linear-gradient(135deg, 
            rgba(168, 85, 247, 0.1) 0%, 
            rgba(139, 92, 246, 0.1) 100%);
        border: 1px solid rgba(168, 85, 247, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        text-align: center;
    }
    
    .amount-display {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #a855f7, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
    }
    
    .currency-display {
        font-size: 1.25rem;
        font-weight: 700;
        color: #a855f7;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .payment-options {
        display: grid;
        gap: 16px;
        margin: 24px 0;
    }
    
    .payment-option {
        background: rgba(255, 255, 255, 0.03);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .payment-option:hover {
        border-color: rgba(168, 85, 247, 0.3);
        background: rgba(168, 85, 247, 0.05);
        transform: translateY(-2px);
    }
    
    .payment-option.selected {
        border-color: #a855f7;
        background: rgba(168, 85, 247, 0.1);
    }
    
    .option-icon {
        font-size: 2rem;
        color: #a855f7;
        flex-shrink: 0;
    }
    
    .option-content {
        flex: 1;
    }
    
    .option-title {
        font-weight: 700;
        color: #374151;
        margin-bottom: 4px;
    }
    
    .dark .option-title {
        color: #f9fafb;
    }
    
    .option-description {
        font-size: 14px;
        color: #6b7280;
    }
    
    .dark .option-description {
        color: #9ca3af;
    }
    
    .option-popular {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        margin-left: auto;
    }
    
    .pay-button {
        background: linear-gradient(135deg, #a855f7 0%, #8b5cf6 100%);
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
        box-shadow: 0 15px 30px rgba(168, 85, 247, 0.4);
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
    
    .nigerian-methods {
        background: rgba(5, 150, 105, 0.1);
        border: 1px solid rgba(5, 150, 105, 0.2);
        border-radius: 12px;
        padding: 16px;
        margin-top: 20px;
    }
    
    .nigerian-methods h4 {
        color: #059669;
        margin-bottom: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .method-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
    }
    
    .method-item {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        font-size: 12px;
        color: #059669;
        font-weight: 600;
    }
    
    .security-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        margin-top: 24px;
    }
    
    .security-feature {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        padding: 12px;
        text-align: center;
    }
    
    .security-icon {
        font-size: 1.25rem;
        color: #059669;
        margin-bottom: 4px;
    }
    
    .security-text {
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
    }
    
    .dark .security-text {
        color: #9ca3af;
    }
    
    @media (max-width: 768px) {
        .amount-display {
            font-size: 2rem;
        }
        
        .payment-option {
            padding: 16px;
        }
        
        .option-icon {
            font-size: 1.5rem;
        }
        
        .method-grid {
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
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('VoguePay Gateway')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Nigeria\'s leading payment solution')</p>
        </div>

        <div class="voguepay-container">
            <div class="payment-card">
                <!-- VoguePay Logo -->
                <div class="voguepay-logo">
                    <h2>VoguePay</h2>
                    <div class="nigeria-badge">
                        <i class="las la-flag"></i>
                        @lang('Nigeria')
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="payment-summary">
                    <div class="amount-display">{{ showAmount($deposit->final_amount) }}</div>
                    <div class="currency-display">{{ $deposit->method_currency }}</div>
                </div>

                <!-- Payment Options -->
                <div class="payment-options">
                    <div class="payment-option selected" data-method="card">
                        <div class="option-icon">
                            <i class="las la-credit-card"></i>
                        </div>
                        <div class="option-content">
                            <div class="option-title">@lang('Debit/Credit Card')</div>
                            <div class="option-description">@lang('Visa, Mastercard, Verve')</div>
                        </div>
                        <div class="option-popular">@lang('Popular')</div>
                    </div>
                    
                    <div class="payment-option" data-method="transfer">
                        <div class="option-icon">
                            <i class="las la-university"></i>
                        </div>
                        <div class="option-content">
                            <div class="option-title">@lang('Bank Transfer')</div>
                            <div class="option-description">@lang('Direct bank account transfer')</div>
                        </div>
                    </div>
                    
                    <div class="payment-option" data-method="ussd">
                        <div class="option-icon">
                            <i class="las la-mobile-alt"></i>
                        </div>
                        <div class="option-content">
                            <div class="option-title">@lang('USSD Payment')</div>
                            <div class="option-description">@lang('Pay with bank USSD codes')</div>
                        </div>
                    </div>
                    
                    <div class="payment-option" data-method="wallet">
                        <div class="option-icon">
                            <i class="las la-wallet"></i>
                        </div>
                        <div class="option-content">
                            <div class="option-title">@lang('VoguePay Wallet')</div>
                            <div class="option-description">@lang('Pay from VoguePay balance')</div>
                        </div>
                    </div>
                </div>

                <!-- Nigerian Payment Methods Info -->
                <div class="nigerian-methods">
                    <h4>
                        <i class="las la-star"></i>
                        @lang('Supported Nigerian Banks')
                    </h4>
                    <div class="method-grid">
                        <div class="method-item">GTBank</div>
                        <div class="method-item">First Bank</div>
                        <div class="method-item">Access Bank</div>
                        <div class="method-item">Zenith Bank</div>
                        <div class="method-item">UBA</div>
                        <div class="method-item">Fidelity Bank</div>
                        <div class="method-item">Sterling Bank</div>
                        <div class="method-item">Polaris Bank</div>
                    </div>
                </div>

                <!-- Payment Button -->
                <div id="voguepay-payment-container">
                    <button type="button" class="pay-button" id="btn-confirm">
                        <span id="pay-content">
                            <i class="las la-shield-alt"></i>
                            @lang('Pay with Voguepay')
                        </span>
                        <span id="pay-loading" style="display: none;">
                            <i class="las la-spinner la-spin"></i>
                            @lang('Processing...')
                        </span>
                    </button>
                </div>

                <!-- Security Features -->
                <div class="security-features">
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div class="security-text">SSL Secured</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-flag"></i>
                        </div>
                        <div class="security-text">Nigeria Focused</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-clock"></i>
                        </div>
                        <div class="security-text">Instant Processing</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-phone"></i>
                        </div>
                        <div class="security-text">24/7 Support</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="//pay.voguepay.com/js/voguepay.js"></script>
<script>
    "use strict";
    
    var closedFunction = function() {
        document.getElementById('pay-content').style.display = '';
        document.getElementById('pay-loading').style.display = 'none';
    }
    
    var successFunction = function(transaction_id) {
        window.location.href = '{{ route(gatewayRedirectUrl()) }}';
    }
    
    var failedFunction = function(transaction_id) {
        window.location.href = '{{ route(gatewayRedirectUrl()) }}';
    }

    function pay(item, price) {
        // Show loading state
        document.getElementById('pay-content').style.display = 'none';
        document.getElementById('pay-loading').style.display = '';
        
        // Initiate Voguepay inline payment
        Voguepay.init({
            v_merchant_id: "{{ $data->v_merchant_id }}",
            total: price,
            notify_url: "{{ $data->notify_url }}",
            cur: "{{ $data->cur }}",
            merchant_ref: "{{ $data->merchant_ref }}",
            memo: "{{ $data->memo }}",
            recurrent: true,
            frequency: 10,
            developer_code: '60a4ecd9bbc77',
            custom: "{{ $data->custom }}",
            customer: {
                name: 'Customer name',
                country: 'Country',
                address: 'Customer address',
                city: 'Customer city',
                state: 'Customer state',
                zipcode: 'Customer zip/post code',
                email: 'example@example.com',
                phone: 'Customer phone'
            },
            closed: closedFunction,
            success: successFunction,
            failed: failedFunction
        });
    }
    
    (function($) {
        $('#btn-confirm').on('click', function(e) {
            e.preventDefault();
            pay('Buy', {{ $data->Buy }});
        });
    })(jQuery);
</script>
@endpush