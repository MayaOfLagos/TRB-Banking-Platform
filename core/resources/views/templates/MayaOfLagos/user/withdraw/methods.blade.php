@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-2xl p-6 mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                <i class="las la-hand-holding-usd text-red-600 dark:text-red-400 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('Withdraw Money')</h1>
                <p class="text-gray-600 dark:text-gray-400">@lang('Choose your preferred withdrawal method')</p>
            </div>
        </div>
    </div>

    <form action="{{ route('user.withdraw.apply') }}" method="post" class="withdraw-form">
        @csrf
        <div class="grid lg:grid-cols-12 gap-8">
            <div class="lg:col-span-5">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Select Withdrawal Method')</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Choose from available withdrawal options')</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4 payment-system-list is-scrollable gateway-option-list">
                            @foreach ($withdrawMethod as $data)
                                <label for="{{ titleToKey($data->name) }}" class="gateway-option @if ($loop->index > 3) hidden @endif">
                                    <input class="payment-item__radio gateway-input hidden" 
                                           id="{{ titleToKey($data->name) }}" 
                                           data-gateway='@json($data)' 
                                           type="radio" 
                                           name="method_code" 
                                           value="{{ $data->id }}" 
                                           @checked(old('method_code', $loop->first) == $data->id) 
                                           data-min-amount="{{ showUserAmount($data->min_limit, auth()->user()) }}" 
                                           data-max-amount="{{ showUserAmount($data->max_limit, auth()->user()) }}">
                                    <div class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-red-300 dark:hover:border-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 cursor-pointer group">
                                        <div class="flex items-center flex-1 space-x-4">
                                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                <img src="{{ getImage(getFilePath('withdrawMethod') . '/' . $data->image) }}" 
                                                     alt="{{ __($data->name) }}" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 dark:text-white truncate">{{ __($data->name) }}</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    @lang('Limit'): {{ showUserAmount($data->min_limit, auth()->user()) }} - {{ showUserAmount($data->max_limit, auth()->user()) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-4">
                                            <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded-full group-hover:border-red-500 dark:group-hover:border-red-400 transition-colors duration-200 payment-item__check"></div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                            
                            @if ($withdrawMethod->count() > 4)
                                <button type="button" class="more-gateway-option w-full p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-red-400 dark:hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-95 transition-all duration-200 group">
                                    <div class="flex items-center justify-center space-x-2">
                                        <span class="text-gray-600 dark:text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400 font-medium">@lang('Show All Payment Options')</span>
                                        <i class="las la-chevron-down text-gray-400 group-hover:text-red-500 dark:group-hover:text-red-400 transition-colors duration-200 transform group-hover:translate-y-0.5"></i>
                                    </div>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Withdrawal Amount')</h3>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@lang('Current Balance')</p>
                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ showUserAmount(auth()->user()->balance, auth()->user()) }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Enter Amount')</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">{{ getUserCurrency(auth()->user())['symbol'] }}</span>
                                        <input type="number" 
                                               step="any" 
                                               class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 amount" 
                                               name="amount" 
                                               placeholder="0.00" 
                                               value="{{ old('amount') }}" 
                                               autocomplete="off">
                                        <input type="hidden" name="currency" value="{{ getUserCurrency(auth()->user())['text'] }}">
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        @lang('Limit'): <span class="gateway-limit text-red-600 dark:text-red-400 font-medium">@lang('Select a method')</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">@lang('Withdrawal Summary')</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-700 dark:text-gray-300">@lang('Processing Charge')</span>
                                        <button type="button" 
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200 proccessing-fee-info" 
                                                data-bs-toggle="tooltip" 
                                                title="@lang('Processing charge for withdraw method')">
                                            <i class="las la-info-circle"></i>
                                        </button>
                                    </div>
                                    <span class="text-red-600 dark:text-red-400 font-medium">
                                        {{ getUserCurrency(auth()->user())['symbol'] }}<span class="processing-fee">0.00</span> {{ getUserCurrency(auth()->user())['text'] }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-gray-700 dark:text-gray-300">@lang('You Will Receive')</span>
                                    <span class="text-green-600 dark:text-green-400 font-bold text-lg">
                                        {{ getUserCurrency(auth()->user())['symbol'] }}<span class="final-amount">0.00</span> {{ getUserCurrency(auth()->user())['text'] }}
                                    </span>
                                </div>

                                <div class="gateway-conversion hidden py-3 border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">@lang('Conversion Rate')</span>
                                        <span class="text-blue-600 dark:text-blue-400 font-medium">
                                            <span class="conversion-rate"></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="conversion-currency hidden py-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">
                                            @lang('Amount in') <span class="gateway-currency font-medium"></span>
                                        </span>
                                        <span class="text-blue-600 dark:text-blue-400 font-bold text-lg">
                                            <span class="in-currency"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (checkIsOtpEnable())
                        <div id="otpFieldContainer" class="hidden">
                            @include($activeTemplate . 'partials.otp_field')
                        </div>
                    @endif

                    <div class="sticky bottom-0 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        @if (checkIsOtpEnable())
                            <button type="button" 
                                    id="proceedWithdrawBtn"
                                    class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100" 
                                    disabled>
                                <i class="las la-paper-plane mr-2"></i>
                                @lang('Proceed to Withdraw')
                            </button>
                        @else
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100" 
                                    disabled>
                                <i class="las la-paper-plane mr-2"></i>
                                @lang('Proceed to Withdraw')
                            </button>
                        @endif
                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-3">
                            @lang('Please review all details before proceeding')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@if (checkIsOtpEnable())
<div id="otpModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-shield-alt text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Security Verification')</h3>
                    </div>
                    <button type="button" id="closeOtpModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="las la-times text-2xl"></i>
                    </button>
                </div>

                <form id="otpForm" method="post">
                    @csrf
                    
                    <div id="hiddenFormData"></div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            @lang('Select Verification Method')
                        </label>
                        @include($activeTemplate . 'partials.otp_field')
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start space-x-3">
                            <i class="las la-info-circle text-blue-600 dark:text-blue-400 text-xl mt-0.5"></i>
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    @lang('After clicking proceed, you will be redirected to enter your verification code on the next page.')
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" 
                                id="cancelOtp"
                                class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-3 px-4 rounded-xl transition-all duration-200">
                            @lang('Cancel')
                        </button>
                        <button type="submit" 
                                id="submitWithOtp"
                                class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            <i class="las la-arrow-right mr-2"></i>
                            @lang('Proceed')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('style')
<style>
    .payment-system-list.is-scrollable {
        transition: max-height 0.5s ease;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .payment-system-list.is-scrollable::-webkit-scrollbar {
        width: 0px;
        background: transparent;
    }
    
    .gateway-option {
        transition: all 0.3s ease;
    }
    
    .payment-item__check {
        position: relative;
        transition: all 0.2s ease;
    }
    
    .gateway-input:checked + div .payment-item__check {
        border-color: rgb(239, 68, 68) !important;
        background-color: rgb(254, 242, 242);
    }
    
    .dark .gateway-input:checked + div .payment-item__check {
        border-color: rgb(248, 113, 113) !important;
        background-color: rgba(239, 68, 68, 0.2);
    }
    
    .gateway-input:checked + div .payment-item__check::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 10px;
        height: 10px;
        background: rgb(239, 68, 68);
        border-radius: 50%;
        animation: scaleIn 0.2s ease;
    }
    
    .dark .gateway-input:checked + div .payment-item__check::after {
        background: rgb(248, 113, 113);
    }
    
    @keyframes scaleIn {
        from {
            transform: translate(-50%, -50%) scale(0);
        }
        to {
            transform: translate(-50%, -50%) scale(1);
        }
    }
    
    .gateway-input:checked + div {
        border-color: rgb(239, 68, 68) !important;
        background-color: rgb(254, 242, 242) !important;
        transform: scale(1.02);
    }
    
    .dark .gateway-input:checked + div {
        border-color: rgb(248, 113, 113) !important;
        background-color: rgba(239, 68, 68, 0.1) !important;
    }
    
    input[name="currency"] {
        display: none;
    }
    
    .withdraw-form.adjust-height {
        min-height: auto;
    }
    
    .tooltip {
        font-size: 0.75rem;
    }
    
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
    }
</style>
@endpush

@push('script')
<script>
    "use strict";
    (function($) {
        var amount = parseFloat($('.amount').val() || 0);
        var gateway, minAmount, maxAmount;

        $('.amount').on('input', function(e) {
            amount = parseFloat($(this).val());
            if (!amount) {
                amount = 0;
            }
            calculation();
        });

        $('.gateway-input').on('change', function(e) {
            gatewayChange();
        });

        function gatewayChange() {
            let gatewayElement = $('.gateway-input:checked');
            let methodCode = gatewayElement.val();

            gateway = gatewayElement.data('gateway');
            minAmount = gatewayElement.data('min-amount');
            maxAmount = gatewayElement.data('max-amount');

            let processingFeeInfo = `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ getUserCurrency(auth()->user())['symbol'] }} charge for processing fees`;
            $(".proccessing-fee-info").attr("title", processingFeeInfo).attr("data-bs-original-title", processingFeeInfo);

            calculation();
        }

        gatewayChange();

        $(".more-gateway-option").on("click", function(e) {
            e.preventDefault();
            const $button = $(this);
            const $paymentList = $(".gateway-option-list");
            const $hiddenOptions = $paymentList.find(".gateway-option.hidden");
            const $buttonText = $button.find('span');
            const $buttonIcon = $button.find('i');
            
            $button.prop('disabled', true);
            $buttonText.text('@lang("Loading...")');
            $buttonIcon.removeClass('la-chevron-down').addClass('la-spinner la-spin');
            
            setTimeout(() => {
                $hiddenOptions.removeClass("hidden");
                
                $button.addClass('opacity-0 scale-95');
                setTimeout(() => {
                    $button.addClass('hidden');
                }, 200);
                
                $paymentList.animate({
                    scrollTop: $paymentList[0].scrollHeight - $paymentList.height()
                }, 'slow', 'swing');
                
                $hiddenOptions.addClass('scale-95 opacity-50');
                setTimeout(() => {
                    $hiddenOptions.removeClass('scale-95 opacity-50').addClass('scale-100 opacity-100');
                }, 100);
            }, 300);
        });

        function calculation() {
            if (!gateway) return;
            $(".gateway-limit").text(minAmount + " - " + maxAmount);
            
            let percentCharge = 0;
            let fixedCharge = 0;
            let totalPercentCharge = 0;

            if (amount) {
                percentCharge = parseFloat(gateway.percent_charge);
                fixedCharge = parseFloat(gateway.fixed_charge);
                totalPercentCharge = parseFloat(amount / 100 * percentCharge);
            }

            let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
            let totalAmount = parseFloat((amount || 0) - totalPercentCharge - fixedCharge);

            $(".final-amount").text(totalAmount.toFixed(2));
            $(".processing-fee").text(totalCharge.toFixed(2));
            $("input[name=currency]").val(gateway.currency);
            $(".gateway-currency").text(gateway.currency);

            if (amount < Number(gateway.min_limit) || amount > Number(gateway.max_limit)) {
                $(".withdraw-form button[type=submit], #proceedWithdrawBtn").attr('disabled', true);
            } else {
                $(".withdraw-form button[type=submit], #proceedWithdrawBtn").removeAttr('disabled');
            }

            if (gateway.currency != "{{ getUserCurrency(auth()->user())['text'] }}") {
                $('.withdraw-form').addClass('adjust-height');
                $(".gateway-conversion, .conversion-currency").removeClass('hidden');
                $(".conversion-rate").html(
                    `1 {{ getUserCurrency(auth()->user())['text'] }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span> <span class="method_currency">${gateway.currency}</span>`
                );
                $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(2) + ' ' + gateway.currency);
            } else {
                $(".gateway-conversion, .conversion-currency").addClass('hidden');
                $('.withdraw-form').removeClass('adjust-height');
            }
        }

        const $otpModal = $('#otpModal');
        const $proceedBtn = $('#proceedWithdrawBtn');
        const $closeModal = $('#closeOtpModal, #cancelOtp');
        const $authModeSelect = $('#otpModal select[name="auth_mode"]');
        const $mainForm = $('.withdraw-form');
        const $otpForm = $('#otpForm');
        const $hiddenFormData = $('#hiddenFormData');

        $proceedBtn.on('click', function(e) {
            e.preventDefault();
            
            if (!$('input[name="method_code"]:checked').length) {
                alert('@lang("Please select a withdrawal method")');
                return;
            }
            
            if (!$('input[name="amount"]').val() || parseFloat($('input[name="amount"]').val()) <= 0) {
                alert('@lang("Please enter a valid amount")');
                return;
            }
            
            const formData = $mainForm.serializeArray();
            $hiddenFormData.empty();
            
            $.each(formData, function(i, field) {
                if (field.name !== 'auth_mode') {
                    $hiddenFormData.append(`<input type="hidden" name="${field.name}" value="${field.value}">`);
                }
            });
            
            $otpModal.removeClass('hidden');
            document.body.style.overflow = 'hidden';
        });

        $closeModal.on('click', function() {
            $otpModal.addClass('hidden');
            document.body.style.overflow = 'auto';
            $authModeSelect.val('');
        });

        $otpForm.on('submit', function(e) {
            const selectedMode = $authModeSelect.val();
            const $submitBtn = $('#submitWithOtp');
            
            if (!selectedMode) {
                e.preventDefault();
                alert('@lang("Please select an authentication method")');
                return;
            }
            
            $submitBtn.prop('disabled', true);
            $submitBtn.html('<i class="las la-spinner la-spin mr-2"></i>@lang("Sending OTP...")');
            
            $otpForm.attr('action', '{{ route("user.withdraw.apply") }}');
            $otpForm.attr('method', 'POST');
        });

        $otpModal.on('click', function(e) {
            if (e.target === this) {
                $closeModal.first().click();
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !$otpModal.hasClass('hidden')) {
                $closeModal.first().click();
            }
            
            if (e.key === 'Enter' && !$otpModal.hasClass('hidden')) {
                const $activeInput = $(':focus');
                if ($activeInput.is('#otp_code, #authenticator_code')) {
                    e.preventDefault();
                    $('#submitWithOtp').click();
                }
            }
        });

        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        $('.gateway-input').change();
    })(jQuery);
</script>
@endpush