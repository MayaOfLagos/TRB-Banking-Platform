@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')
<div class="max-w-3xl mx-auto px-2 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="las la-key text-white text-3xl"></i>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Transfer PIN Verification')</h1>
        <p class="text-gray-600 dark:text-gray-400">@lang('Enter your 4-digit PIN to continue with the wire transfer')</p>
    </div>

    <!-- Transfer Info -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Transfer Amount')</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($amount) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                <i class="las la-dollar-sign text-green-600 dark:text-green-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- PIN Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8">
        <form method="POST" action="{{ route('user.transfer.wire.verify.pin.submit') }}" class="space-y-6" id="pinVerificationForm">
            @csrf
            
            <!-- PIN Input -->
            <div class="text-center">
                <label class="block text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                    @lang('Enter your 4-digit Transfer PIN')
                </label>
                
                <div class="flex justify-center space-x-3 mb-6">
                    <input type="password" 
                           name="transfer_pin"
                           maxlength="4"
                           pattern="[0-9]{4}"
                           class="w-full max-w-xs px-4 py-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-center text-2xl tracking-widest font-mono"
                           placeholder="••••"
                           required
                           autocomplete="off">
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @lang('Your PIN is required for additional security verification')
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pt-6">
                <button type="button" 
                        onclick="history.back()"
                        class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                    @lang('Back')
                </button>
                <button type="submit" 
                        id="verifyPinBtn"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                    <i class="las la-check" id="verifyIcon"></i>
                    <span id="verifyText">@lang('Verify PIN')</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Security Notice -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 mt-6">
        <div class="flex items-start space-x-3">
            <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="las la-info text-blue-600 dark:text-blue-400 text-sm"></i>
            </div>
            <div>
                <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-1">@lang('Security Information')</h4>
                <p class="text-sm text-blue-700 dark:text-blue-200">
                    @lang('Your Transfer PIN adds an extra layer of security to prevent unauthorized transactions. Never share your PIN with anyone.')
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 z-50 hidden">
    <!-- Blurred Background -->
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm transition-all duration-300"></div>
    
    <!-- Loading Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-8 max-w-sm w-full text-center">
            <!-- Animated Spinner -->
            <div class="w-16 h-16 mx-auto mb-6">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-purple-200 dark:border-purple-800 rounded-full"></div>
                    <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-purple-500 rounded-full animate-spin"></div>
                </div>
            </div>
            
            <!-- Loading Text -->
            <div class="space-y-3">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Verifying PIN')</h3>
                <p class="text-gray-600 dark:text-gray-400">@lang('Please wait while we verify your transfer PIN...')</p>
                
                <!-- Countdown Timer -->
                <div class="mt-4">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="countdownTimer">3</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">@lang('seconds remaining')</div>
                </div>
                
                <!-- Progress Dots -->
                <div class="flex justify-center space-x-1 mt-4">
                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse animation-delay-150"></div>
                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse animation-delay-300"></div>
                </div>
            </div>
            
            <!-- Security Message -->
            <div class="mt-6 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <i class="las la-shield-alt mr-1"></i>
                    @lang('Secure verification in progress')
                </p>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
.animation-delay-150 {
    animation-delay: 0.15s;
}
.animation-delay-300 {
    animation-delay: 0.3s;
}

#loadingOverlay {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

#loadingOverlay.opacity-100 {
    opacity: 1;
}

#loadingOverlay .fixed {
    display: flex;
    align-items: center;
    justify-content: center;
}

.backdrop-blur-sm {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

@keyframes pulse-scale {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.7;
    }
}

.animate-pulse-scale {
    animation: pulse-scale 2s ease-in-out infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

#loadingOverlay > div:first-child {
    animation: fadeInBlur 0.3s ease-out;
}

#loadingOverlay .bg-white {
    animation: slideInUp 0.4s ease-out;
}

@keyframes fadeInBlur {
    from {
        opacity: 0;
        backdrop-filter: blur(0px);
        -webkit-backdrop-filter: blur(0px);
    }
    to {
        opacity: 1;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

#verifyPinBtn.loading {
    position: relative;
    pointer-events: none;
}

.select-none {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
</style>
@endpush

@push('script')
<script>
'use strict';
(function($) {
    $('input[name="transfer_pin"]').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        if (value.length > 4) {
            value = value.slice(0, 4);
        }
        $(this).val(value);
    });

    $('input[name="transfer_pin"]').on('input', function() {
        if ($(this).val().length === 4) {
            $(this).closest('form').submit();
        }
    });

    // Handle form submission with loading overlay and delay
    $('#pinVerificationForm').on('submit', function(e) {
        e.preventDefault();
        
        const pinInput = $('input[name="transfer_pin"]');
        const pinValue = pinInput.val();
        
        if (pinValue.length !== 4) {
            pinInput.focus();
            return false;
        }
        
        showLoadingOverlay();
        
        updateButtonState(true);
        
        // Prevent double submission
        $('#verifyPinBtn').prop('disabled', true);
        
        setTimeout(() => {
            const form = $('#pinVerificationForm')[0];
            $(form).off('submit');
            form.submit();
        }, 5500); // 5.5 seconds delay
        
        return false;
    });

    function showLoadingOverlay() {
        $('#loadingOverlay').removeClass('hidden').addClass('flex');
        
        setTimeout(() => {
            $('#loadingOverlay').addClass('opacity-100');
        }, 10);
        
        startCountdown();
    }
    
    function startCountdown() {
        let countdown = 3;
        const countdownElement = $('#countdownTimer');
        
        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.text(countdown);
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                countdownElement.text('0');
            }
        }, 1000);
    }

    function hideLoadingOverlay() {
        $('#loadingOverlay').removeClass('opacity-100');
        setTimeout(() => {
            $('#loadingOverlay').addClass('hidden').removeClass('flex');
        }, 300);
    }

    function updateButtonState(isLoading) {
        const button = $('#verifyPinBtn');
        const icon = $('#verifyIcon');
        const text = $('#verifyText');
        
        if (isLoading) {
            button.addClass('opacity-75 cursor-not-allowed');
            icon.removeClass('las la-check').addClass('las la-spinner-third animate-spin');
            text.text('@lang("Verifying...")');
        } else {
            button.removeClass('opacity-75 cursor-not-allowed');
            icon.removeClass('las la-spinner-third animate-spin').addClass('las la-check');
            text.text('@lang("Verify PIN")');
            button.prop('disabled', false);
        }
    }

    $(window).on('beforeunload', function() {
        hideLoadingOverlay();
    });

    $(document).ready(function() {
        if ($('.alert-danger').length > 0) {
            hideLoadingOverlay();
            updateButtonState(false);
        }
    });

    $('input[name="transfer_pin"]').on('focus', function() {
        $(this).select();
    });

    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) {
            if (!$('#loadingOverlay').hasClass('hidden')) {
                e.preventDefault();
                return false;
            }
        }
    });

})(jQuery);
</script>
@endpush
@endsection
