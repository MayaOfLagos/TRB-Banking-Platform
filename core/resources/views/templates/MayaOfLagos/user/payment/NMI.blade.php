@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="w-full px-3 sm:px-4 py-4">
    <div class="flex justify-center">
        <div class="w-full lg:w-1/2 md:w-2/3">
            <div class="nmi-container p-4">
                <div class="payment-card">
                    <div class="nmi-logo">
                        <i class="fas fa-credit-card" style="font-size: 2rem; color: #2563eb;"></i>
                        <h2>NMI Payment</h2>
                        <span class="version-badge">Secure</span>
                    </div>

                    <div class="payment-summary">
                        <div class="amount-display">
                            <div class="payment-amount">
                                <span class="currency">{{ __($deposit->method_currency) }}</span>
                                <span class="amount">{{ showAmount($deposit->final_amount, currencyFormat: false) }}</span>
                            </div>
                            <div class="payment-description">
                                @lang('You will receive') {{ showAmount($deposit->amount) }}
                            </div>
                        </div>

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
                    </div>

                    <!-- Payment Form -->
                    <form role="form" class="disableSubmission appPayment" id="payment-form" method="{{ $data->method }}" action="{{ $data->url }}">
                        @csrf
                        
                        <div class="card-form">
                            <!-- Card Number -->
                            <div class="form-group">
                                <label class="form-label">@lang('Card Number')</label>
                                <div class="input-group">
                                    <input type="tel" 
                                           class="form-input" 
                                           name="billing-cc-number" 
                                           autocomplete="off" 
                                           value="{{ old('billing-cc-number') }}" 
                                           placeholder="1234 5678 9012 3456"
                                           required 
                                           autofocus />
                                    <span class="input-group-icon">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Expiry and CVV Row -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">@lang('Expiration Date')</label>
                                    <input type="tel" 
                                           class="form-input" 
                                           name="billing-cc-exp" 
                                           value="{{ old('billing-cc-exp') }}" 
                                           placeholder="MM/YY" 
                                           autocomplete="off" 
                                           required />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('CVC Code')</label>
                                    <input type="tel" 
                                           class="form-input" 
                                           name="billing-cc-cvv" 
                                           value="{{ old('billing-cc-cvv') }}" 
                                           placeholder="123"
                                           autocomplete="off" 
                                           required />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="pay-button" id="submit-btn">
                            <span id="pay-content">
                                <i class="fas fa-lock"></i>
                                @lang('Pay Securely')
                            </span>
                            <span id="loading-content" class="hidden">
                                <i class="las la-spinner la-spin"></i>
                                @lang('Processing...')
                            </span>
                        </button>
                    </form>

                    <!-- Security Features -->
                    <div class="security-features">
                        <div class="security-badge">
                            <i class="las la-shield-alt security-icon"></i>
                            PCI DSS Level 1
                        </div>
                        <div class="security-badge">
                            <i class="las la-lock security-icon"></i>
                            256-bit SSL
                        </div>
                        <div class="security-badge">
                            <i class="las la-eye-slash security-icon"></i>
                            3D Secure 2.0
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .nmi-container {
        background: linear-gradient(135deg, 
            rgba(30, 64, 175, 0.1) 0%, 
            rgba(37, 99, 235, 0.1) 100%);
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
        background: linear-gradient(90deg, #1e40af, #2563eb);
        border-radius: 16px 16px 0 0;
    }
    
    .nmi-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        gap: 12px;
    }
    
    .nmi-logo h2 {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #1e40af, #2563eb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
    }
    
    .version-badge {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .payment-summary {
        margin-bottom: 24px;
    }
    
    .amount-display {
        text-align: center;
        margin-bottom: 20px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .payment-amount {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
    }
    
    .currency {
        font-size: 1.2rem;
        opacity: 0.8;
        margin-right: 8px;
    }
    
    .payment-description {
        color: #94a3b8;
        font-size: 0.9rem;
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
        color: #2563eb;
        font-weight: 700;
    }
    
    .card-form {
        margin-bottom: 24px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #e2e8f0;
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .input-group {
        position: relative;
    }
    
    .form-input {
        width: 100%;
        padding: 16px 20px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #fff;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #2563eb;
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .form-input::placeholder {
        color: #94a3b8;
    }
    
    .input-group-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 1.2rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    
    .pay-button {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
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
        margin-bottom: 24px;
    }
    
    .pay-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }
    
    .pay-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .hidden {
        display: none !important;
    }
    
    .security-features {
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    
    .security-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    
    .security-icon {
        font-size: 1rem;
    }
    
    @media (max-width: 768px) {
        .payment-card {
            padding: 24px 20px;
        }
        
        .payment-amount {
            font-size: 1.5rem;
        }
        
        .nmi-logo {
            flex-direction: column;
            gap: 8px;
        }
        
        .nmi-logo h2 {
            font-size: 1.3rem;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .security-features {
            gap: 8px;
        }
        
        .security-badge {
            font-size: 0.7rem;
            padding: 6px 8px;
        }
    }
</style>
@endpush

@if ($deposit->from_api)
    @push('script')
        <script>
            (function($) {
                "use strict";

                $('.appPayment').on('submit', function() {
                    $(this).find('[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                });
            })(jQuery);
        </script>
    @endpush
@endif

@push('script')
<script>
    "use strict";
    (function() {
        
        // Card number formatting
        const cardNumberInput = document.querySelector('input[name="billing-cc-number"]');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ');
                if (formattedValue) {
                    e.target.value = formattedValue;
                }
            });
        }
        
        // Expiry date formatting
        const expiryInput = document.querySelector('input[name="billing-cc-exp"]');
        if (expiryInput) {
            expiryInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0,2) + '/' + value.substring(2,4);
                }
                e.target.value = value;
            });
        }
        
        // CVV formatting
        const cvvInput = document.querySelector('input[name="billing-cc-cvv"]');
        if (cvvInput) {
            cvvInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
            });
        }
        
        // Form submission handling
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-btn');
        const payContent = document.getElementById('pay-content');
        const loadingContent = document.getElementById('loading-content');
        
        form.addEventListener('submit', function(e) {
            // Show loading state
            submitButton.disabled = true;
            payContent.classList.add('hidden');
            loadingContent.classList.remove('hidden');
        });
        
    })();
</script>
@endpush