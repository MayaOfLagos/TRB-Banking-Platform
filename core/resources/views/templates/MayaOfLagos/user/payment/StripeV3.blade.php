@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .stripe-v3-container {
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
        background: linear-gradient(90deg, #635bff, #4f46e5);
        border-radius: 16px 16px 0 0;
    }
    
    .stripe-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        gap: 12px;
    }
    
    .stripe-logo h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #635bff;
        margin: 0;
    }
    
    .stripe-logo .version-badge {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .payment-summary {
        background: linear-gradient(135deg, 
            rgba(99, 91, 255, 0.1) 0%, 
            rgba(79, 70, 229, 0.1) 100%);
        border: 1px solid rgba(99, 91, 255, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        text-align: center;
    }
    
    .amount-display {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #635bff, #4f46e5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
    }
    
    .currency-display {
        font-size: 1.25rem;
        font-weight: 700;
        color: #635bff;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .stripe-payment-element-container {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 24px;
        margin: 24px 0;
    }
    
    .element-label {
        display: block;
        margin-bottom: 12px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .dark .element-label {
        color: #D1D5DB;
    }
    
    #payment-element {
        margin-bottom: 20px;
    }
    
    .pay-button {
        background: linear-gradient(135deg, #635bff 0%, #4f46e5 100%);
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
        box-shadow: 0 15px 30px rgba(99, 91, 255, 0.4);
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
    
    .error-message {
        background: linear-gradient(135deg, 
            rgba(239, 68, 68, 0.1) 0%, 
            rgba(220, 38, 38, 0.1) 100%);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 8px;
        padding: 12px 16px;
        color: #dc2626;
        font-size: 14px;
        margin-top: 12px;
        display: none;
    }
    
    .dark .error-message {
        color: #fca5a5;
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
        color: #10b981;
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
    
    .stripe-v3-badge {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 16px;
    }
    
    @media (max-width: 768px) {
        .amount-display {
            font-size: 2rem;
        }
        
        .security-features {
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
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Stripe Payment Intent')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Next-generation payment processing with SCA compliance')</p>
        </div>

        <div class="stripe-v3-container">
            <div class="payment-card">
                <!-- Stripe Logo with Version -->
                <div class="stripe-logo">
                    <h2>Stripe</h2>
                    <span class="version-badge">v3.0</span>
                    <i class="las la-star" style="font-size: 1.5rem; color: #635bff;"></i>
                </div>

                <!-- Stripe V3 Badge -->
                <div class="text-center">
                    <div class="stripe-v3-badge">
                        <i class="las la-shield-alt"></i>
                        @lang('Payment Intent API with SCA Ready')
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="payment-summary">
                    <div class="amount-display">{{ showAmount($deposit->final_amount) }}</div>
                    <div class="currency-display">{{ $deposit->method_currency }}</div>
                </div>

                <!-- Payment Button -->
                <form id="stripe-payment-form">
                    @csrf
                    <input type="hidden" value="{{ $deposit->trx }}" name="track">
                    
                    <!-- Info Message -->
                    <div class="stripe-payment-element-container">
                        <div class="text-center mb-4">
                            <p class="text-gray-600 dark:text-gray-400">
                                @lang('You will be redirected to Stripe\'s secure checkout page to complete your payment.')
                            </p>
                        </div>
                    </div>

                    <div class="error-message" id="payment-errors" role="alert"></div>

                    <button type="submit" class="pay-button" id="stripe-submit-btn">
                        <span id="pay-content">
                            <i class="las la-lock"></i>
                            @lang('Continue to Stripe Checkout')
                        </span>
                        <span id="loading-content" class="hidden">
                            <i class="las la-spinner loading-spinner"></i>
                            @lang('Redirecting...')
                        </span>
                    </button>
                </form>

                <!-- Security Features -->
                <div class="security-features">
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div class="security-text">SCA Compliant</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-eye-slash"></i>
                        </div>
                        <div class="security-text">3D Secure 2.0</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-robot"></i>
                        </div>
                        <div class="security-text">Radar Fraud AI</div>
                    </div>
                    
                    <div class="security-feature">
                        <div class="security-icon">
                            <i class="las la-lock"></i>
                        </div>
                        <div class="security-text">256-bit SSL</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://js.stripe.com/v3/"></script>

<script>
    "use strict";
    (function() {
        @php
            $publishable_key = $data->StripeJSAcc->publishable_key;
            $sessionId = $data->session->id;
        @endphp

        // Initialize Stripe
        const stripe = Stripe('{{ $publishable_key }}');
        
        // Form elements
        const form = document.getElementById('stripe-payment-form');
        const submitButton = document.getElementById('stripe-submit-btn');
        const payContent = document.getElementById('pay-content');
        const loadingContent = document.getElementById('loading-content');

        // Handle form submission
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Prevent double submission
            if (submitButton.classList.contains('loading')) {
                return;
            }

            // Show loading state
            setLoading(true);

            // Redirect to Stripe Checkout
            stripe.redirectToCheckout({
                sessionId: '{{ $sessionId }}'
            }).then(function(result) {
                if (result.error) {
                    showError(result.error.message);
                    setLoading(false);
                }
            });
        });

        function setLoading(isLoading) {
            if (isLoading) {
                submitButton.classList.add('loading');
                submitButton.disabled = true;
                payContent.classList.add('hidden');
                loadingContent.classList.remove('hidden');
            } else {
                submitButton.classList.remove('loading');
                submitButton.disabled = false;
                payContent.classList.remove('hidden');
                loadingContent.classList.add('hidden');
            }
        }

        function showError(message) {
            const errorElement = document.getElementById('payment-errors');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

    })();
</script>
@endpush