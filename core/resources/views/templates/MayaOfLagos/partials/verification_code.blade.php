<div class="space-y-4">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
        @lang('Verification Code')
    </label>
    
    <!-- Hidden Input for Form Submission -->
    <input type="hidden" name="code" id="verification-code-hidden" required>
    
    <!-- Custom 6-Digit Code Input -->
    <div class="verification-code-container">
        <div class="flex justify-center space-x-2 sm:space-x-2 md:space-x-3">
            <input type="text" 
                   class="verification-digit w-10 h-12 sm:w-12 sm:h-14 md:w-12 md:h-12 text-center text-base sm:text-lg md:text-lg font-bold border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-400/30 transition-all duration-300" 
                   maxlength="1" 
                   pattern="[0-9]" 
                   inputmode="numeric"
                   data-index="0">
            <input type="text" 
                   class="verification-digit w-10 h-12 sm:w-12 sm:h-14 md:w-12 md:h-12 text-center text-base sm:text-lg md:text-lg font-bold border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-400/30 transition-all duration-300" 
                   maxlength="1" 
                   pattern="[0-9]" 
                   inputmode="numeric"
                   data-index="1">
            <input type="text" 
                   class="verification-digit w-10 h-12 sm:w-12 sm:h-14 md:w-12 md:h-12 text-center text-base sm:text-lg md:text-lg font-bold border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-400/30 transition-all duration-300" 
                   maxlength="1" 
                   pattern="[0-9]" 
                   inputmode="numeric"
                   data-index="2">
            <input type="text" 
                   class="verification-digit w-10 h-12 sm:w-12 sm:h-14 md:w-12 md:h-12 text-center text-base sm:text-lg md:text-lg font-bold border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-400/30 transition-all duration-300" 
                   maxlength="1" 
                   pattern="[0-9]" 
                   inputmode="numeric"
                   data-index="3">
            <input type="text" 
                   class="verification-digit w-10 h-12 sm:w-12 sm:h-14 md:w-12 md:h-12 text-center text-base sm:text-lg md:text-lg font-bold border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-400/30 transition-all duration-300" 
                   maxlength="1" 
                   pattern="[0-9]" 
                   inputmode="numeric"
                   data-index="4">
            <input type="text" 
                   class="verification-digit w-10 h-12 sm:w-12 sm:h-14 md:w-12 md:h-12 text-center text-base sm:text-lg md:text-lg font-bold border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-400/30 transition-all duration-300" 
                   maxlength="1" 
                   pattern="[0-9]" 
                   inputmode="numeric"
                   data-index="5">
        </div>
    </div>
    
    <!-- Error Message -->
    @error('code')
        <p class="text-red-500 text-xs text-center mt-2">{{ $message }}</p>
    @enderror
</div>

@push('style')
<style>
    .verification-digit::-webkit-outer-spin-button,
    .verification-digit::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .verification-digit[type=number] {
        -moz-appearance: textfield;
    }
    
    .verification-digit.success {
        @apply border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20;
    }
    
    .verification-digit.error {
        @apply border-red-500 bg-red-50 dark:bg-red-900/20;
    }
    
    .verification-digit.filled {
        @apply border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20;
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        'use strict';

        $(document).ready(function() {
            const digits = $('.verification-digit');
            const hiddenInput = $('#verification-code-hidden');
            
            // Focus first input on load
            digits.first().focus();
            
            digits.on('input', function(e) {
                const $this = $(this);
                const index = parseInt($this.data('index'));
                let value = $this.val();
                
                // Only allow digits
                value = value.replace(/[^0-9]/g, '');
                $this.val(value);
                
                if (value) {
                    $this.addClass('filled');
                    // Move to next input
                    if (index < 5) {
                        digits.eq(index + 1).focus();
                    }
                } else {
                    $this.removeClass('filled success error');
                }
                
                updateHiddenInput();
                checkComplete();
            });
            
            digits.on('keydown', function(e) {
                const $this = $(this);
                const index = parseInt($this.data('index'));
                
                // Handle backspace
                if (e.keyCode === 8 && !$this.val() && index > 0) {
                    digits.eq(index - 1).focus();
                }
                
                // Handle arrow keys
                if (e.keyCode === 37 && index > 0) { // Left arrow
                    digits.eq(index - 1).focus();
                }
                if (e.keyCode === 39 && index < 5) { // Right arrow
                    digits.eq(index + 1).focus();
                }
            });
            
            digits.on('paste', function(e) {
                e.preventDefault();
                const paste = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
                const numbers = paste.replace(/[^0-9]/g, '').slice(0, 6);
                
                numbers.split('').forEach((digit, index) => {
                    if (index < 6) {
                        digits.eq(index).val(digit).addClass('filled');
                    }
                });
                
                updateHiddenInput();
                checkComplete();
            });
            
            function updateHiddenInput() {
                let code = '';
                digits.each(function() {
                    code += $(this).val();
                });
                hiddenInput.val(code);
            }
            
            function checkComplete() {
                const code = hiddenInput.val();
                if (code.length === 6) {
                    // Mark all as success
                    digits.removeClass('error').addClass('success');
                    
                    // Auto-submit after short delay
                    setTimeout(() => {
                        $('#verification-form').submit();
                    }, 500);
                } else {
                    // Remove success class if incomplete
                    digits.removeClass('success error');
                    // Add filled class to inputs with values
                    digits.each(function() {
                        if ($(this).val()) {
                            $(this).addClass('filled');
                        } else {
                            $(this).removeClass('filled');
                        }
                    });
                }
            }
            
            function clearAllInputs() {
                digits.val('').removeClass('filled success error');
                hiddenInput.val('');
                digits.first().focus();
            }
            
            // Handle form validation errors and notification errors
            @if ($errors->has('code') || (session('notify') && collect(session('notify'))->where('0', 'error')->isNotEmpty()))
                digits.addClass('error');
                // Clear all inputs on error and focus first input
                clearAllInputs();
                digits.addClass('error');
                // Remove error styling after 3 seconds
                setTimeout(() => {
                    digits.removeClass('error');
                }, 3000);
            @endif
        });

    })(jQuery);
</script>
@endpush