@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .deposit-form-container {
        background: linear-gradient(135deg, 
            rgba(79, 70, 229, 0.1) 0%, 
            rgba(147, 51, 234, 0.1) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .gateway-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        transition: all 0.3s ease;
    }
    
    .gateway-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-color: rgba(139, 92, 246, 0.3);
    }
    
    .payment-method {
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .payment-method::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s;
    }
    
    .payment-method:hover::before {
        left: 100%;
    }
    
    .payment-method.selected {
        border-color: #8b5cf6;
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(167, 139, 250, 0.1));
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.2);
    }
    
    .payment-method:hover {
        border-color: rgba(139, 92, 246, 0.5);
        transform: translateY(-2px);
    }
    
    .payment-method input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    
    .payment-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
    }
    
    .amount-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
    }
    
    .amount-input {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: #111827;
        font-size: 1.125rem;
        font-weight: 600;
    }
    
    .dark .amount-input {
        color: #f9fafb;
    }
    
    .amount-input:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #9ca3af;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .info-value {
        color: #111827;
        font-weight: 600;
    }
    
    .dark .info-value {
        color: #f9fafb;
    }
    
    .btn-confirm {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.125rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn-confirm::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-confirm:hover::before {
        left: 100%;
    }
    
    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-confirm:disabled {
        background: linear-gradient(135deg, #9ca3af, #6b7280);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    .btn-confirm.loading {
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .btn-confirm.loading:hover {
        transform: none;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    .limit-info {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 8px;
        padding: 8px 12px;
        color: #2563eb;
        font-size: 0.875rem;
    }
    
    .dark .limit-info {
        color: #60a5fa;
    }
    
    .conversion-info {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 8px;
        padding: 12px;
        margin-top: 16px;
    }
    
    .crypto-notice {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.2);
        border-radius: 8px;
        padding: 12px;
        color: #d97706;
        font-size: 0.875rem;
        margin-top: 12px;
    }
    
    .dark .crypto-notice {
        color: #fbbf24;
    }
    
    .show-more-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 12px;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .dark .show-more-btn {
        color: #9ca3af;
    }
    
    .show-more-btn:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        color: #374151;
    }
    
    .dark .show-more-btn:hover {
        color: #f3f4f6;
    }
    
    .processing-fee-tooltip {
        position: relative;
        cursor: help;
    }
    
    .processing-fee-tooltip:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 50;
    }
    
    .d-none {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 md:px-4 py-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('user.deposit.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg mr-4 transition-colors">
                <i class="las la-arrow-left mr-2"></i>
                @lang('Back')
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Deposit Money')</h1>
            </div>
        </div>
    </div>

    <form action="{{ route('user.deposit.insert') }}" method="post" class="deposit-form">
        @csrf
        <input type="hidden" name="currency">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Payment Methods -->
            <div class="lg:col-span-2">
                <div class="deposit-form-container rounded-2xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mr-3">
                            <i class="las la-credit-card text-blue-600 dark:text-blue-400"></i>
                        </div>
                        @lang('Select Payment Method')
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto pr-2 payment-system-list gateway-option-list" id="payment-methods-container">
                        @foreach ($gatewayCurrency as $data)
                            <label for="{{ titleToKey($data->name) }}" class="payment-method @if ($loop->index > 5) d-none hidden @endif gateway-option" data-gateway='@json($data)'>
                                <input class="gateway-input" id="{{ titleToKey($data->name) }}" type="radio" name="gateway" value="{{ $data->method_code }}" @checked(old('gateway',$loop->first) == $data->method_code) data-min-amount="{{ showAmount($data->min_amount) }}" data-max-amount="{{ showAmount($data->max_amount) }}" data-gateway='@json($data)'>
                                
                                <div class="flex items-center">
                                    <img src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}" 
                                         alt="{{ $data->name }}" class="payment-icon mr-4 flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ __($data->name) }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ showAmount($data->min_amount) }} - {{ showAmount($data->max_amount) }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            @lang('Fee'): {{ showAmount($data->fixed_charge) }} + {{ $data->percent_charge }}%
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 ml-2">
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition-colors gateway-radio">
                                            <div class="w-3 h-3 rounded-full bg-purple-600 opacity-0 transition-opacity gateway-radio-dot"></div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                        
                        @if ($gatewayCurrency->count() > 6)
                            <div class="md:col-span-2">
                                <button type="button" class="show-more-btn w-full more-gateway-option">
                                    <div class="flex items-center justify-center">
                                        <span class="mr-2">@lang('Show All Payment Options')</span>
                                        <i class="las la-chevron-down transition-transform"></i>
                                    </div>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Amount and Summary -->
            <div class="lg:col-span-1">
                <!-- Amount Input -->
                <div class="amount-card p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <div class="w-6 h-6 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center mr-2">
                            <i class="las la-money-bill text-green-600 dark:text-green-400 text-sm"></i>
                        </div>
                        @lang('Enter Amount')
                    </h3>
                    
                    <div class="relative mb-4">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-semibold">
                            {{ gs('cur_sym') }}
                        </div>
                        <input type="number" step="any" class="amount-input w-full pl-12 pr-4 py-3 amount" name="amount" placeholder="0.00" value="{{ old('amount') }}" autocomplete="off">
                    </div>
                    
                    <div class="limit-info">
                        <i class="las la-info-circle mr-1"></i>
                        @lang('Limit'): <span class="font-semibold gateway-limit">0.00</span>
                    </div>
                </div>

                <!-- Transaction Summary -->
                <div class="amount-card p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <div class="w-6 h-6 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mr-2">
                            <i class="las la-calculator text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                        @lang('Transaction Summary')
                    </h3>
                    
                    <div class="space-y-0">
                        <div class="info-item">
                            <span class="info-label flex items-center">
                                @lang('Processing Charge')
                                <span class="processing-fee-tooltip proccessing-fee-info ml-1" data-tooltip="@lang('Processing charge for payment gateways')" data-bs-toggle="tooltip" title="@lang('Processing charge for payment gateways')">
                                    <i class="las la-info-circle text-gray-400 cursor-help"></i>
                                </span>
                            </span>
                            <span class="info-value processing-fee">0.00</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">@lang('Total Amount')</span>
                            <span class="info-value text-lg font-bold final-amount">0.00</span>
                        </div>
                        
                        <div class="info-item gateway-conversion d-none">
                            <span class="info-label">@lang('Conversion Rate')</span>
                            <span class="info-value text"></span>
                        </div>
                        
                        <div class="info-item conversion-currency d-none">
                            <span class="info-label">
                                @lang('Amount in') <span class="gateway-currency"></span>
                            </span>
                            <span class="info-value in-currency font-bold text-green-600 dark:text-green-400"></span>
                        </div>
                    </div>
                    
                    <div class="crypto-notice d-none">
                        <i class="las la-exclamation-triangle mr-2"></i>
                        @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final value will show on next step')
                    </div>
                </div>

                <!-- Confirm Button -->
                <button type="submit" class="btn-confirm w-full" disabled id="deposit-submit-btn">
                    <div class="flex items-center justify-center" id="submit-content">
                        <i class="las la-check-circle mr-2"></i>
                        @lang('Confirm Deposit')
                    </div>
                    <div class="flex items-center justify-center hidden" id="loading-content">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        @lang('Processing...')
                    </div>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
    "use strict";
    (function($) {

        var amount = parseFloat($('.amount').val() || 0);
        var gateway, minAmount, maxAmount;

        // Initialize first gateway if none selected
        if (!$('.gateway-input:checked').length && $('.gateway-input').length) {
            console.log('No gateway checked, selecting first one');
            const firstGateway = $('.gateway-input').first();
            firstGateway.prop('checked', true);
            
            // Update visual state
            firstGateway.closest('.payment-method').addClass('selected');
            firstGateway.closest('.payment-method').find('.gateway-radio').removeClass('border-gray-300').addClass('border-purple-600');
            firstGateway.closest('.payment-method').find('.gateway-radio-dot').removeClass('opacity-0').addClass('opacity-100');
            
            // Trigger change event
            firstGateway.trigger('change');
        } else if ($('.gateway-input:checked').length) {
            console.log('Gateway already checked, initializing');
            // If one is already checked, initialize it
            $('.gateway-input:checked').trigger('change');
        }

        $('.amount').focus();

        $('.amount').on('input', function(e) {
            amount = parseFloat($(this).val());
            if (!amount) {
                amount = 0;
            }
            calculation();
        });

        $('.gateway-input').on('change', function(e) {
            // Update visual selection
            $('.payment-method').removeClass('selected');
            $('.gateway-radio').removeClass('border-purple-600').addClass('border-gray-300');
            $('.gateway-radio-dot').removeClass('opacity-100').addClass('opacity-0');
            
            $(this).closest('.payment-method').addClass('selected');
            $(this).closest('.payment-method').find('.gateway-radio').removeClass('border-gray-300').addClass('border-purple-600');
            $(this).closest('.payment-method').find('.gateway-radio-dot').removeClass('opacity-0').addClass('opacity-100');
            
            gatewayChange();
        });

        function gatewayChange() {
            let gatewayElement = $('.gateway-input:checked');
            let methodCode = gatewayElement.val();

            if (!gatewayElement.length || !methodCode) {
                console.log('No gateway selected in gatewayChange');
                return;
            }

            gateway = gatewayElement.data('gateway');
            minAmount = gatewayElement.data('min-amount');
            maxAmount = gatewayElement.data('max-amount');

            if (!gateway) {
                console.log('No gateway data found in gatewayChange');
                return;
            }

            // Set currency immediately when gateway changes
            $("input[name=currency]").val(gateway.currency);

            let processingFeeInfo = `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for payment gateway processing fees`;
            $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
            $(".processing-fee-tooltip").attr("data-tooltip", processingFeeInfo);
            
            // Always call calculation when gateway changes
            calculation();
            
            console.log('Gateway changed to:', gateway.name, 'Method code:', methodCode, 'Currency:', gateway.currency);
        }

        gatewayChange();

        $(".more-gateway-option").on("click", function(e) {
            e.preventDefault();
            let paymentList = $(".gateway-option-list");
            paymentList.find(".gateway-option").removeClass("d-none hidden");
            $(this).parent().addClass('d-none hidden');
            
            // Animate scroll to show all options
            paymentList.animate({
                scrollTop: (paymentList.height() - 60)
            }, 'slow');
        });

        function calculation() {
            if (!gateway) return;
            $(".gateway-limit").html(`${minAmount} - ${maxAmount}`);

            let percentCharge = 0;
            let fixedCharge = 0;
            let totalPercentCharge = 0;

            if (amount) {
                percentCharge = parseFloat(gateway.percent_charge);
                fixedCharge = parseFloat(gateway.fixed_charge);
                totalPercentCharge = parseFloat(amount / 100 * percentCharge);
            }

            let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
            let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

            $(".final-amount").text(totalAmount.toFixed(2));
            $(".processing-fee").text(totalCharge.toFixed(2));
            $("input[name=currency]").val(gateway.currency);
            $(".gateway-currency").text(gateway.currency);

            if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
                $("#deposit-submit-btn").attr('disabled', true);
            } else {
                $("#deposit-submit-btn").removeAttr('disabled');
            }

            if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
                $(".gateway-conversion, .conversion-currency").removeClass('d-none hidden');
                $(".gateway-conversion").find('.info-value.text').html(
                    `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span> <span class="method_currency">${gateway.currency}</span>`
                );
                $('.in-currency').text(`${parseFloat(totalAmount * gateway.rate).toFixed(gateway.method.crypto == 1 ? 8 : 2)} ${gateway.currency}`)
            } else {
                $(".gateway-conversion, .conversion-currency").addClass('d-none hidden');
            }

            if (gateway.method.crypto == 1) {
                $('.crypto-notice').removeClass('d-none hidden');
            } else {
                $('.crypto-notice').addClass('d-none hidden');
            }
        }

        // Form submission handler with loading state
        $('.deposit-form').on('submit', function(e) {
            const submitBtn = $('#deposit-submit-btn');
            const submitContent = $('#submit-content');
            const loadingContent = $('#loading-content');
            const form = $(this);
            
            // Prevent double submission
            if (submitBtn.hasClass('loading')) {
                e.preventDefault();
                return false;
            }
            
            // Get form values
            const selectedGateway = $('.gateway-input:checked');
            const amountInput = $('.amount');
            const currencyInput = $("input[name=currency]");
            
            // Debug: Log form values before submission
            console.log('Form submission debug:');
            console.log('Gateway checked:', selectedGateway.length > 0);
            console.log('Gateway value:', selectedGateway.val());
            console.log('Amount value:', amountInput.val());
            console.log('Currency value:', currencyInput.val());
            console.log('Form data:');
            form.serializeArray().forEach(item => {
                console.log(item.name + ':', item.value);
            });
            
            // Pre-submission validation
            if (!selectedGateway.length || !selectedGateway.val()) {
                e.preventDefault();
                alert('@lang("Please select a payment method")');
                return false;
            }
            
            if (!amountInput.val() || parseFloat(amountInput.val()) <= 0) {
                e.preventDefault();
                alert('@lang("Please enter a valid amount")');
                amountInput.focus();
                return false;
            }
            
            if (!currencyInput.val()) {
                e.preventDefault();
                alert('@lang("Currency not set. Please select a payment method first.")');
                return false;
            }
            
            const currentAmount = parseFloat(amountInput.val());
            if (gateway && (currentAmount < Number(gateway.min_amount) || currentAmount > Number(gateway.max_amount))) {
                e.preventDefault();
                alert(`@lang("Amount must be between") ${gateway.min_amount} @lang("and") ${gateway.max_amount}`);
                amountInput.focus();
                return false;
            }
            
            // Show loading state
            submitBtn.addClass('loading').attr('disabled', true);
            submitContent.addClass('hidden');
            loadingContent.removeClass('hidden');
            
            // Re-enable form if there's an error (after a delay to allow for page navigation)
            setTimeout(function() {
                if (submitBtn.hasClass('loading')) {
                    submitBtn.removeClass('loading').removeAttr('disabled');
                    submitContent.removeClass('hidden');
                    loadingContent.addClass('hidden');
                    
                    // Recalculate to restore button state
                    calculation();
                }
            }, 8000); // 8 second timeout
        });

        // Prevent multiple clicks on submit button
        $('#deposit-submit-btn').on('click', function(e) {
            if ($(this).hasClass('loading')) {
                e.preventDefault();
                return false;
            }
        });

        // Initialize tooltips (for Bootstrap compatibility)
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }
        
        // Initialize first gateway
        $('.gateway-input').first().trigger('change');
    })(jQuery);
</script>
@endpush