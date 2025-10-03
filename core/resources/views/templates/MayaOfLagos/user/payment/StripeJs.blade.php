@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="w-full px-3 sm:px-4 py-4">
    <div class="flex justify-center">
        <div class="w-full lg:w-1/2 md:w-2/3">
            <div class="stripe-js-container p-4">
                <div class="payment-card">
                    <div class="stripe-logo">
                        <i class="fab fa-stripe-s" style="font-size: 2rem; color: #635bff;"></i>
                        <h2>Stripe Payment</h2>
                        <span class="version-badge">Legacy</span>
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

                    <form action="{{ $data->url }}" method="{{ $data->method }}" class="stripe-payment-form">
                        <script src="{{ $data->src }}" class="stripe-button" 
                            @foreach ($data->val as $key => $value)
                                data-{{ $key }}="{{ $value }}"
                            @endforeach>
                        </script>
                    </form>

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
                            3D Secure
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
    .stripe-js-container {
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
    
    .version-badge {
        background: rgba(99, 91, 255, 0.1);
        color: #635bff;
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
        color: #10b981;
        font-weight: 700;
    }
    
    .stripe-payment-form {
        margin: 24px 0;
        text-align: center;
    }
    
    .stripe-button-el {
        background-image: none !important;
        background: linear-gradient(135deg, #635bff 0%, #4f46e5 100%) !important;
        border-radius: 12px !important;
        padding: 16px 32px !important;
        font-size: 1rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.5px !important;
        transition: all 0.3s ease !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(99, 91, 255, 0.3) !important;
        text-transform: uppercase !important;
        width: 100% !important;
        max-width: 300px !important;
    }
    
    .stripe-button-el:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(99, 91, 255, 0.4) !important;
    }
    
    .stripe-button-el span {
        color: #fff !important;
        font-weight: 600 !important;
    }
    
    .security-features {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 24px;
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
        
        .stripe-logo {
            flex-direction: column;
            gap: 8px;
        }
        
        .stripe-logo h2 {
            font-size: 1.3rem;
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

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Enhance Stripe button appearance and functionality
        $(document).ready(function() {
            // Wait for Stripe button to load
            setTimeout(function() {
                $('button[type="submit"]').addClass("stripe-enhanced-button");
                
                // Add loading state on click
                $('button[type="submit"]').on('click', function() {
                    $(this).prop('disabled', true);
                    $(this).html('<i class="las la-spinner la-spin"></i> Processing...');
                });
            }, 500);
        });
        
    })(jQuery);
</script>
@endpush