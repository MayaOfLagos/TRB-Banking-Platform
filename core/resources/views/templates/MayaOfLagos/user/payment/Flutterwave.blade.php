@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .flutterwave-container {
        background: linear-gradient(135deg, 
            rgba(245, 101, 101, 0.1) 0%, 
            rgba(251, 146, 60, 0.1) 100%);
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
        background: linear-gradient(90deg, #f56565, #fb923c);
        border-radius: 16px 16px 0 0;
    }
    
    .flutterwave-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        gap: 12px;
    }
    
    .flutterwave-logo h2 {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #f56565, #fb923c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
    }
    
    .africa-badge {
        background: linear-gradient(135deg, #16a085, #27ae60);
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
            rgba(245, 101, 101, 0.1) 0%, 
            rgba(251, 146, 60, 0.1) 100%);
        border: 1px solid rgba(245, 101, 101, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        text-align: center;
    }
    
    .amount-display {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #f56565, #fb923c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
        text-align: center;
    }
    
    .payment-description {
        font-size: 1rem;
        color: #94a3b8;
        text-align: center;
        margin-bottom: 20px;
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
        margin-top: 8px;
        padding-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
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
        color: #f56565;
        font-weight: 700;
    }
    
    .pay-button {
        background: linear-gradient(135deg, #f56565 0%, #fb923c 100%);
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
        box-shadow: 0 15px 30px rgba(245, 101, 101, 0.4);
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
    
    .african-methods {
        background: rgba(22, 160, 133, 0.1);
        border: 1px solid rgba(22, 160, 133, 0.2);
        border-radius: 12px;
        padding: 16px;
        margin-top: 20px;
    }
    
    .african-methods h4 {
        color: #16a085;
        margin-bottom: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .method-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
    }
    
    .method-item {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        font-size: 12px;
        color: #16a085;
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
        color: #16a085;
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
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Flutterwave Payment')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('African payment gateway for seamless transactions')</p>
        </div>

        <div class="flutterwave-container">
            <div class="payment-card">
                <!-- Flutterwave Logo -->
                <div class="flutterwave-logo">
                    <h2>Flutterwave</h2>
                    <div class="africa-badge">
                        <i class="las la-globe-africa"></i>
                        @lang('Africa')
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="payment-summary">
                    <div class="amount-display">{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</div>
                    <div class="payment-description">@lang('You will receive') {{ showAmount($deposit->amount) }}</div>
                </div>

                <!-- Payment Details -->
                <div class="payment-details">
                    <div class="detail-row">
                        <span class="detail-label">@lang('Gateway')</span>
                        <span class="detail-value">{{ __($deposit->gateway->name) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">@lang('Charge')</span>
                        <span class="detail-value">{{ showAmount($deposit->charge) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">@lang('Total Payable')</span>
                        <span class="detail-value total">{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</span>
                    </div>
                </div>

                <!-- Payment Button -->
                <button type="button" class="pay-button" id="btn-confirm" onClick="payWithRave()">
                    <span id="pay-content">
                        <i class="las la-shield-alt"></i>
                        @lang('Pay with Flutterwave')
                    </span>
                    <span id="loading-content" class="hidden">
                        <i class="las la-spinner loading-spinner"></i>
                        @lang('Processing...')
                    </span>
                </button>

                <!-- Security Features -->
                <div class="security-features">
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div class="security-text">PCI DSS Level 1</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-globe-africa"></i>
                        </div>
                        <div class="security-text">Africa Optimized</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-mobile-alt"></i>
                        </div>
                        <div class="security-text">Mobile First</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-clock"></i>
                        </div>
                        <div class="security-text">Instant Settlement</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
<script>
    "use strict"
    var btn = document.querySelector("#btn-confirm");
    btn.setAttribute("type", "button");
    const API_publicKey = "{{ $data->API_publicKey }}";

    function payWithRave() {
        // Show loading state
        setLoading(true);
        
        var x = getpaidSetup({
            PBFPubKey: API_publicKey,
            customer_email: "{{ $data->customer_email }}",
            amount: "{{ $data->amount }}",
            customer_phone: "{{ $data->customer_phone }}",
            currency: "{{ $data->currency }}",
            txref: "{{ $data->txref }}",
            onclose: function() {
                setLoading(false);
            },
            callback: function(response) {
                var txref = response.tx.txRef;
                var status = response.tx.status;
                var chargeResponse = response.tx.chargeResponseCode;
                if (chargeResponse == "00" || chargeResponse == "0") {
                    window.location = '{{ url('ipn/flutterwave') }}/' + txref + '/' + status;
                } else {
                    window.location = '{{ url('ipn/flutterwave') }}/' + txref + '/' + status;
                }
                // x.close(); // use this to close the modal immediately after payment.
            }
        });
    }
    
    function setLoading(isLoading) {
        const payContent = document.getElementById('pay-content');
        const loadingContent = document.getElementById('loading-content');
        const submitButton = document.getElementById('btn-confirm');
        
        if (isLoading) {
            submitButton.disabled = true;
            payContent.classList.add('hidden');
            loadingContent.classList.remove('hidden');
        } else {
            submitButton.disabled = false;
            payContent.classList.remove('hidden');
            loadingContent.classList.add('hidden');
        }
    }
</script>
@endpush