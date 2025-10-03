@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- KYC Form Page -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
                    <i class="las la-user-shield text-3xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Identity Verification')</h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">@lang('Complete your KYC verification to unlock all features and secure your account. This process helps us comply with regulations and protect your funds.')</p>
            </div>
        </div>

        <!-- KYC Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 lg:px-8 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="las la-clipboard-check text-3xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-white">@lang('KYC Application Form')</h2>
                        <p class="text-blue-100 text-sm mt-1">@lang('Please provide accurate information for verification')</p>
                    </div>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="px-6 lg:px-8 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="las la-user text-white text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">@lang('Step 1')</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Identity Information')</p>
                                </div>
                            </div>
                            <div class="flex-1 h-px bg-gray-300 dark:bg-gray-600"></div>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <i class="las la-check text-white text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Step 2')</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Verification')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="px-6 lg:px-8 py-8" x-data="kycForm()">

                <!-- KYC Form -->
                <form action="{{ route('user.kyc.submit') }}" method="post" enctype="multipart/form-data" data-validate @submit="handleSubmit">
                    @csrf
                    
                    <!-- Dynamic Form Fields -->
                    <div class="space-y-6">
                        <x-viser-form identifier="act" identifierValue="kyc" />
                    </div>

                    <!-- Submit Section -->
                    <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="las la-info-circle text-2xl text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" id="agreement" x-model="agreed" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="agreement" class="ml-2 text-sm text-blue-800 dark:text-blue-200">
                                            @lang('I confirm that all information provided is accurate and complete')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="button" 
                                    @click="window.history.back()"
                                    class="flex-1 sm:flex-none px-8 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-all duration-200 flex items-center justify-center">
                                <i class="las la-arrow-left mr-2"></i>
                                @lang('Back')
                            </button>
                            
                            <button type="submit" 
                                    :disabled="!agreed || loading"
                                    :class="{ 'opacity-50 cursor-not-allowed': !agreed || loading }"
                                    class="flex-1 px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none flex items-center justify-center space-x-2">
                                <template x-if="!loading">
                                    <i class="las la-paper-plane text-lg"></i>
                                </template>
                                <template x-if="loading">
                                    <i class="las la-spinner la-spin text-lg"></i>
                                </template>
                                <span x-text="loading ? '@lang('Submitting...')' : '@lang('Submit KYC Application')'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('script')
<script>
function kycForm() {
    return {
        agreed: false,
        loading: false,
        
        handleSubmit(event) {
            if (!this.agreed) {
                event.preventDefault();
                this.showNotification('error', '@lang('Please confirm that all information is accurate')');
                return false;
            }
            
            if (!this.validatePhoneNumbers()) {
                event.preventDefault();
                return false;
            }
            
            this.loading = true;
        },
        
        validatePhoneNumbers() {
            let isValid = true;
            const phoneFields = document.querySelectorAll('input[name*="phone"], input[name*="mobile"], input[name*="number"], input[type="tel"], .phone-field-validated');
            
            phoneFields.forEach(field => {
                const isPhoneField = field.classList.contains('phone-field-validated') || 
                                   field.name.toLowerCase().includes('phone') || 
                                   field.name.toLowerCase().includes('mobile') || 
                                   field.name.toLowerCase().includes('number') ||
                                   field.type === 'tel';
                
                if (isPhoneField) {
                    const value = field.value.trim();
                    
                    if (value) {
                        if (!/^\d+$/.test(value)) {
                            const fieldLabel = field.previousElementSibling?.textContent || 
                                             field.getAttribute('placeholder') || 
                                             field.name || 
                                             'Phone field';
                            
                            this.showNotification('error', `${fieldLabel.replace('*', '').trim()}: @lang('Phone number must contain only digits (no spaces, dashes, or symbols)')`);
                            field.focus();
                            field.classList.remove('border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
                            field.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                            isValid = false;
                            return false;
                        }
                        
                        if (value.length < 7 || value.length > 15) {
                            const fieldLabel = field.previousElementSibling?.textContent || 
                                             field.getAttribute('placeholder') || 
                                             field.name || 
                                             'Phone field';
                            
                            this.showNotification('error', `${fieldLabel.replace('*', '').trim()}: @lang('Phone number must be between 7 and 15 digits')`);
                            field.focus();
                            field.classList.remove('border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
                            field.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                            isValid = false;
                            return false;
                        }
                        
                        field.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                        field.classList.add('border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
                    }
                }
            });
            
            return isValid;
        },
        
        showNotification(type, message) {
            if (typeof notify === 'function') {
                notify(type, message);
            } else {
                alert(message);
            }
        }
    }
}

(function($) {
    "use strict";
    
    $('label').removeClass('form-label fw-bold');
    
    $('.form-control').addClass('w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white');
    $('.form-select').addClass('w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white');
    
    $('input[type="file"]').addClass('block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-400 dark:hover:file:bg-blue-900/30');
    
    $('label').addClass('block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2');
    
    $('input[required], select[required], textarea[required]').each(function() {
        const label = $(this).siblings('label').first();
        if (label.length && !label.find('.text-red-500').length) {
            label.append(' <span class="text-red-500">*</span>');
        }
    });
    
    $('.is-invalid').removeClass('is-invalid').addClass('border-red-500 focus:ring-red-500 focus:border-red-500');
    $('.invalid-feedback').addClass('text-red-600 text-sm mt-1');
    $('.valid-feedback').addClass('text-green-600 text-sm mt-1');
    
    $('textarea').addClass('min-h-[100px]');
    
    $('input[type="checkbox"], input[type="radio"]').addClass('h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded');
    
    $(document).on('input', 'input[name*="phone"], input[name*="mobile"], input[name*="number"], input[type="tel"], .phone-field-validated', function() {
        const $this = $(this);
        const isPhoneField = $this.hasClass('phone-field-validated') || $this.attr('name') && (
            $this.attr('name').toLowerCase().includes('phone') || 
            $this.attr('name').toLowerCase().includes('mobile') || 
            $this.attr('name').toLowerCase().includes('number')
        ) || $this.attr('type') === 'tel';
        
        if (isPhoneField) {
            let value = $this.val();
            
            const cleanValue = value.replace(/[^\d]/g, '');
            
            if (value !== cleanValue) {
                $this.val(cleanValue);
                
                if (value.length > cleanValue.length) {
                    const $helpText = $this.next('.phone-help-text');
                    if ($helpText.length) {
                        $helpText.html('<i class="las la-exclamation-triangle text-red-500"></i><span class="text-red-500">@lang("Only digits allowed")</span>');
                        setTimeout(() => {
                            $helpText.html('<i class="las la-info-circle"></i>@lang("Numbers only - no spaces, dashes, or symbols")');
                        }, 2000);
                    }
                }
            }
            
            $this.removeClass('border-red-500 focus:ring-red-500 focus:border-red-500 border-green-500 focus:ring-green-500 focus:border-green-500');
            
            if (cleanValue) {
                if (cleanValue.length >= 7 && cleanValue.length <= 15) {
                    $this.addClass('border-green-500 focus:ring-green-500 focus:border-green-500');
                } else {
                    $this.addClass('border-red-500 focus:ring-red-500 focus:border-red-500');
                }
            }
        }
    });
    
    $(document).on('keypress', 'input[name*="phone"], input[name*="mobile"], input[name*="number"], input[type="tel"], .phone-field-validated', function(e) {
        const $this = $(this);
        const isPhoneField = $this.hasClass('phone-field-validated') || $this.attr('name') && (
            $this.attr('name').toLowerCase().includes('phone') || 
            $this.attr('name').toLowerCase().includes('mobile') || 
            $this.attr('name').toLowerCase().includes('number')
        ) || $this.attr('type') === 'tel';
        
        if (isPhoneField) {
            // Allow: backspace, delete, tab, escape, enter
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl+C, Command+C
                (e.keyCode === 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl+V, Command+V
                (e.keyCode === 86 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: Ctrl+X, Command+X
                (e.keyCode === 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                return;
            }
            
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
                
                const $helpText = $this.next('.phone-help-text');
                if ($helpText.length) {
                    $helpText.html('<i class="las la-exclamation-triangle text-red-500"></i><span class="text-red-500">@lang("Only digits allowed")</span>');
                    setTimeout(() => {
                        $helpText.html('<i class="las la-info-circle"></i>@lang("Numbers only - no spaces, dashes, or symbols")');
                    }, 1500);
                }
            }
        }
    });
    
    $(document).on('input', 'input[type="tel"], input[pattern*="[0-9]"]', function() {
        const $this = $(this);
        let value = $this.val();
        
        if ($this.attr('name') && ($this.attr('name').toLowerCase().includes('phone') || 
            $this.attr('name').toLowerCase().includes('mobile') || 
            $this.attr('name').toLowerCase().includes('number'))) {
            value = value.replace(/[^\d]/g, '');
            $this.val(value);
            
            if (value) {
                $this.removeClass('border-red-500 focus:ring-red-500 focus:border-red-500');
                $this.addClass('border-green-500 focus:ring-green-500 focus:border-green-500');
            } else {
                $this.removeClass('border-green-500 focus:ring-green-500 focus:border-green-500 border-red-500 focus:ring-red-500 focus:border-red-500');
            }
        }
    });
    
    function initializePhoneFields() {
        const phoneFields = $('input[name*="phone"], input[name*="mobile"], input[name*="number"], input[type="tel"]').filter(function() {
            const name = $(this).attr('name') || '';
            return name.toLowerCase().includes('phone') || 
                   name.toLowerCase().includes('mobile') || 
                   name.toLowerCase().includes('number') ||
                   $(this).attr('type') === 'tel';
        });
        
        const viserNumberFields = $('.form-control[type="number"]').filter(function() {
            const name = $(this).attr('name') || '';
            const label = $(this).siblings('label').text().toLowerCase();
            return name.toLowerCase().includes('phone') || 
                   name.toLowerCase().includes('mobile') || 
                   name.toLowerCase().includes('contact') ||
                   label.includes('phone') ||
                   label.includes('mobile') ||
                   label.includes('contact');
        });
        
        const allPhoneFields = phoneFields.add(viserNumberFields);
        
        allPhoneFields.each(function() {
            const $field = $(this);
            
            if ($field.attr('type') === 'number') {
                $field.attr('type', 'tel');
                $field.removeAttr('step'); // Remove step="any" that allows decimals
            }
            
            if (!$field.attr('type') || $field.attr('type') === 'text') {
                $field.attr('type', 'tel');
            }
            
            $field.attr('pattern', '[0-9]*');
            
            $field.attr('inputmode', 'numeric');
            
            if ($field.attr('name') && $field.attr('name').toLowerCase().includes('phone')) {
                $field.attr('autocomplete', 'tel');
            }
            
            if (!$field.next('.phone-help-text').length) {
                $field.after('<div class="phone-help-text"><i class="las la-info-circle"></i>@lang("Numbers only - no spaces, dashes, or symbols")</div>');
            }
            
            if (!$field.attr('placeholder')) {
                $field.attr('placeholder', '@lang("Enter phone number")');
            }
            
            $field.addClass('phone-field-validated');
        });
    }
    
    $(document).ready(function() {
        initializePhoneFields();
        
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    initializePhoneFields();
                }
            });
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
    });
    
    $(document).on('paste', 'input[name*="phone"], input[name*="mobile"], input[name*="number"], input[type="tel"], .phone-field-validated', function(e) {
        const $this = $(this);
        
        const isPhoneField = $this.hasClass('phone-field-validated') || ($this.attr('name') && (
            $this.attr('name').toLowerCase().includes('phone') || 
            $this.attr('name').toLowerCase().includes('mobile') || 
            $this.attr('name').toLowerCase().includes('number')));
            
        if (isPhoneField) {
            setTimeout(() => {
                let value = $this.val();
                value = value.replace(/[^\d]/g, '');
                $this.val(value);
                
                if (value) {
                    $this.removeClass('border-red-500 focus:ring-red-500 focus:border-red-500');
                    $this.addClass('border-green-500 focus:ring-green-500 focus:border-green-500');
                }
            }, 1);
        }
    });
    
})(jQuery);
</script>
@endpush

@push('style')
<style>
.kyc-form-container {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);
}

input[type="file"] {
    transition: all 0.3s ease;
}

input[type="file"]:hover {
    transform: translateY(-1px);
}

.progress-step {
    transition: all 0.3s ease;
}

.progress-step.active {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
}

.form-field {
    transition: all 0.3s ease;
}

.form-field:focus-within {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.2);
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.loading {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@media (prefers-color-scheme: dark) {
    input[type="file"]::-webkit-file-upload-button {
        background-color: rgba(59, 130, 246, 0.2);
        color: rgb(96, 165, 250);
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    input[type="file"]::-webkit-file-upload-button:hover {
        background-color: rgba(59, 130, 246, 0.3);
    }
}

@media (max-width: 768px) {
    .kyc-header {
        text-align: center;
        padding: 1rem;
    }
    
    .progress-indicator {
        flex-direction: column;
        space-y: 1rem;
    }
}

.phone-field-valid {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

.phone-field-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.phone-field-valid:focus {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
}

.phone-field-invalid:focus {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
}

.phone-help-text {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
}

.phone-help-text i {
    margin-right: 0.25rem;
}

@media (prefers-color-scheme: dark) {
    .phone-help-text {
        color: #9ca3af;
    }
}
</style>
@endpush