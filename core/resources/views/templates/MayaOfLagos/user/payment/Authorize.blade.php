@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .authorize-container {
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
        background: linear-gradient(90deg, #dc2626, #b91c1c);
        border-radius: 16px 16px 0 0;
    }
    
    .authorize-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        gap: 12px;
    }
    
    .authorize-logo h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #dc2626;
        margin: 0;
    }
    
    .card-preview {
        background: linear-gradient(135deg, #000063, #ffa366);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 32px;
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 200px;
    }
    
    .card-preview::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.1) 0%, transparent 70%);
        animation: shimmer 3s ease-in-out infinite;
    }
    
    @keyframes shimmer {
        0%, 100% { transform: rotate(0deg); }
        50% { transform: rotate(180deg); }
    }
    
    .card-number-display {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        margin: 24px 0;
        color: #e2e8f0;
    }
    
    .card-details {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 32px;
    }
    
    .card-holder {
        font-size: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #cbd5e0;
    }
    
    .card-expiry {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 1rem;
        font-weight: 600;
        color: #cbd5e0;
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .dark .form-label {
        color: #D1D5DB;
    }
    
    .form-control {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        color: #111827;
        font-size: 16px;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    
    .dark .form-control {
        background: rgba(0, 0, 0, 0.2);
        color: #F9FAFB;
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    .dark .form-control:focus {
        background: rgba(0, 0, 0, 0.3);
        border-color: #dc2626;
    }
    
    .input-group {
        position: relative;
    }
    
    .input-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 1.25rem;
        pointer-events: none;
    }
    
    .form-control:focus + .input-icon {
        color: #dc2626;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
    
    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .submit-btn:hover::before {
        left: 100%;
    }
    
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(220, 38, 38, 0.4);
    }
    
    .submit-btn:active {
        transform: translateY(-1px);
    }
    
    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .submit-btn.loading {
        pointer-events: none;
    }
    
    .loading-spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .security-badges {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-top: 24px;
        padding: 16px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .security-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
    }
    
    .dark .security-badge {
        color: #9ca3af;
    }
    
    .security-icon {
        font-size: 16px;
        color: #10b981;
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
        margin-top: 8px;
        display: none;
    }
    
    .dark .error-message {
        color: #fca5a5;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Authorize.Net Payment')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Trusted payment processing since 1996')</p>
        </div>

        <div class="authorize-container">
            <div class="payment-card">
                <!-- Authorize.Net Logo -->
                <div class="authorize-logo">
                    <h2>Authorize.Net</h2>
                    <i class="las la-credit-card" style="font-size: 2rem; color: #dc2626;"></i>
                </div>

                <!-- Card Preview -->
                <div class="card-preview">
                </div>

                <!-- Payment Form -->
                <form role="form" class="authorize-payment-form" id="payment-form" method="{{ $data->method }}" action="{{ $data->url }}">
                    @csrf
                    <input type="hidden" value="{{ $data->track }}" name="track">
                    
                    <div class="form-group">
                        <label class="form-label">@lang('Name on Card')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" 
                                   required autocomplete="off" autofocus placeholder="John Doe">
                            <i class="las la-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">@lang('Card Number')</label>
                        <div class="input-group">
                            <input type="tel" class="form-control" name="cardNumber" autocomplete="off" 
                                   value="{{ old('cardNumber') }}" required placeholder="1234 5678 9012 3456">
                            <i class="las la-credit-card input-icon"></i>
                        </div>
                        <div class="error-message" id="card-number-error"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">@lang('Expiration Date')</label>
                            <div class="input-group">
                                <input type="tel" class="form-control" name="cardExpiry" 
                                       value="{{ old('cardExpiry') }}" autocomplete="off" 
                                       required placeholder="MM/YY">
                                <i class="las la-calendar input-icon"></i>
                            </div>
                            <div class="error-message" id="card-expiry-error"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('CVC Code')</label>
                            <div class="input-group">
                                <input type="tel" class="form-control" name="cardCVC" 
                                       value="{{ old('cardCVC') }}" autocomplete="off" 
                                       required placeholder="123">
                                <i class="las la-lock input-icon"></i>
                            </div>
                            <div class="error-message" id="card-cvc-error"></div>
                        </div>
                    </div>

                    <button class="submit-btn" type="submit" id="authorize-submit-btn">
                        <span id="submit-content">
                            <i class="las la-shield-alt"></i>
                            @lang('Process Payment')
                        </span>
                        <span id="loading-content" class="hidden">
                            <i class="las la-spinner loading-spinner"></i>
                            @lang('Processing...')
                        </span>
                    </button>
                </form>

                <!-- Security Badges -->
                <div class="security-badges">
                    <div class="security-badge">
                        <i class="las la-shield-alt security-icon"></i>
                        PCI Compliant
                    </div>
                    <div class="security-badge">
                        <i class="las la-lock security-icon"></i>
                        SSL Secured
                    </div>
                    <div class="security-badge">
                        <i class="las la-check-circle security-icon"></i>
                        Trusted Since 1996
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/global/js/card.js') }}"></script>

<script>
    "use strict";
    (function($) {
        // Initialize card preview
        var card = new Card({
            form: '#payment-form',
            container: '.card-preview',
            formSelectors: {
                numberInput: 'input[name="cardNumber"]',
                expiryInput: 'input[name="cardExpiry"]',
                cvcInput: 'input[name="cardCVC"]',
                nameInput: 'input[name="name"]'
            },
            placeholders: {
                number: '•••• •••• •••• ••••',
                name: 'YOUR NAME',
                expiry: '••/••',
                cvc: '•••'
            }
        });

        // Card number formatting and validation
        $('input[name="cardNumber"]').on('input', function() {
            let value = $(this).val().replace(/\s/g, '');
            let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
            
            if (formattedValue.length <= 19) {
                $(this).val(formattedValue);
            }

            // Update preview
            $('#card-preview-number').text(formattedValue || '•••• •••• •••• ••••');

            // Basic validation
            if (value.length > 0 && value.length < 13) {
                showError('card-number-error', '@lang("Card number must be at least 13 digits")');
            } else {
                hideError('card-number-error');
            }
        });

        // Expiry date formatting
        $('input[name="cardExpiry"]').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            let formattedValue = value.replace(/(\d{2})(\d{0,2})/, '$1/$2');
            
            if (formattedValue.length <= 5) {
                $(this).val(formattedValue);
            }

            // Update preview
            $('#card-preview-expiry').text(formattedValue || 'MM/YY');

            // Basic validation
            if (value.length === 4) {
                let month = parseInt(value.substring(0, 2));
                let year = parseInt('20' + value.substring(2, 4));
                let currentYear = new Date().getFullYear();
                let currentMonth = new Date().getMonth() + 1;

                if (month < 1 || month > 12) {
                    showError('card-expiry-error', '@lang("Invalid month")');
                } else if (year < currentYear || (year === currentYear && month < currentMonth)) {
                    showError('card-expiry-error', '@lang("Card has expired")');
                } else {
                    hideError('card-expiry-error');
                }
            }
        });

        // CVC validation
        $('input[name="cardCVC"]').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            
            if (value.length <= 4) {
                $(this).val(value);
            }

            // Basic validation
            if (value.length > 0 && value.length < 3) {
                showError('card-cvc-error', '@lang("CVC must be 3-4 digits")');
            } else {
                hideError('card-cvc-error');
            }
        });

        // Name input
        $('input[name="name"]').on('input', function() {
            let value = $(this).val().toUpperCase();
            $('#card-preview-name').text(value || 'YOUR NAME');
        });

        // Form submission
        $('.authorize-payment-form').on('submit', function(e) {
            const submitBtn = $('#authorize-submit-btn');
            const submitContent = $('#submit-content');
            const loadingContent = $('#loading-content');
            
            // Prevent double submission
            if (submitBtn.hasClass('loading')) {
                e.preventDefault();
                return false;
            }

            // Validate form
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }

            // Show loading state
            submitBtn.addClass('loading').attr('disabled', true);
            submitContent.addClass('hidden');
            loadingContent.removeClass('hidden');

            // Re-enable form if there's an error
            setTimeout(function() {
                if (submitBtn.hasClass('loading')) {
                    submitBtn.removeClass('loading').removeAttr('disabled');
                    submitContent.removeClass('hidden');
                    loadingContent.addClass('hidden');
                }
            }, 8000);
        });

        function validateForm() {
            let isValid = true;
            
            // Validate card number
            let cardNumber = $('input[name="cardNumber"]').val().replace(/\s/g, '');
            if (cardNumber.length < 13) {
                showError('card-number-error', '@lang("Please enter a valid card number")');
                isValid = false;
            }

            // Validate expiry
            let expiry = $('input[name="cardExpiry"]').val().replace(/\D/g, '');
            if (expiry.length !== 4) {
                showError('card-expiry-error', '@lang("Please enter a valid expiry date")');
                isValid = false;
            }

            // Validate CVC
            let cvc = $('input[name="cardCVC"]').val();
            if (cvc.length < 3) {
                showError('card-cvc-error', '@lang("Please enter a valid CVC")');
                isValid = false;
            }

            return isValid;
        }

        function showError(elementId, message) {
            $('#' + elementId).text(message).show();
        }

        function hideError(elementId) {
            $('#' + elementId).hide();
        }

        // Restrict inputs to numbers only for card fields
        $('input[name="cardNumber"], input[name="cardCVC"]').on('keypress', function(e) {
            return isNumber(e);
        });

        $('input[name="cardExpiry"]').on('keypress', function(e) {
            return isNumber(e);
        });

        function isNumber(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            return !(charCode > 31 && (charCode < 48 || charCode > 57));
        }

    })(jQuery);
</script>
@endpush