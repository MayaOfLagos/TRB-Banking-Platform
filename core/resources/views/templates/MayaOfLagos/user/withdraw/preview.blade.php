@extends($activeTemplate . 'layouts.master')
@section('content')

@php
    $user = auth()->user();
    $canWithdraw = $user->canWithdraw();
    $withdrawalStatus = $user->getWithdrawalStatus();
    $withdrawalReason = $user->getWithdrawalBlockReason();
    $statusLabels = \App\Models\WithdrawalControl::getStatuses();
@endphp

<div class="max-w-4xl mx-auto">
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl p-6 mb-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <i class="las la-file-invoice text-blue-600 dark:text-blue-400 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('Withdrawal Preview')</h1>
                <p class="text-gray-600 dark:text-gray-400">@lang('Please confirm your withdrawal details')</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        <div class="lg:col-span-5">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <img src="{{ getImage(getFilePath('withdrawMethod') . '/' . $withdraw->method->image) }}" 
                                 alt="{{ __($withdraw->method->name) }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __($withdraw->method->name) }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('Withdrawal Method')</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Withdrawal Amount')</span>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ showUserAmount($withdraw->amount, auth()->user()) }}</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">@lang('Processing Fee')</p>
                                    <p class="text-sm font-semibold text-red-600 dark:text-red-400">{{ showUserAmount($withdraw->charge, auth()->user()) }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">@lang('You Will Receive')</p>
                                    <p class="text-sm font-semibold text-green-600 dark:text-green-400">{{ showUserAmount($withdraw->final_amount, auth()->user()) }}</p>
                                </div>
                            </div>
                            
                            @if ($withdraw->currency != getUserCurrency(auth()->user())['text'])
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Amount in') {{ __($withdraw->currency) }}</span>
                                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                            {{ showAmount($withdraw->final_amount, currencyFormat: false) }} {{ __($withdraw->currency) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @lang('Rate'): 1 {{ getUserCurrency(auth()->user())['text'] }} = {{ showAmount($withdraw->rate) }} {{ __($withdraw->currency) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6 mb-8">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="las la-info-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">@lang('Withdrawal Instructions')</h3>
                                    <div class="text-blue-800 dark:text-blue-200 text-sm leading-relaxed">
                                        @php
                                            echo $withdraw->method->description;
                                        @endphp
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4">
                            <div class="flex items-start space-x-3">
                                <i class="las la-clock text-amber-600 dark:text-amber-400 text-lg mt-0.5"></i>
                                <div>
                                    <h4 class="font-medium text-amber-900 dark:text-amber-100 text-sm mb-1">@lang('Processing Time')</h4>
                                    <p class="text-xs text-amber-700 dark:text-amber-300">
                                        @lang('Withdrawals are typically processed within 24-48 hours during business days')
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-8">
                    <form action="{{ route('user.withdraw.submit') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Required Information')</h4>
                            <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form->id }}" />
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('user.withdraw') }}" 
                               class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-4 px-6 rounded-xl transition-all duration-200 text-center">
                                <i class="las la-arrow-left mr-2"></i>
                                @lang('Back')
                            </a>
                            
                            <button type="submit" 
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg">
                                <i class="las la-check-circle mr-2"></i>
                                @lang('Confirm')
                            </button>
                        </div>

                        <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl">
                            <div class="flex items-start space-x-3">
                                <i class="las la-shield-alt text-green-600 dark:text-green-400 text-lg mt-0.5"></i>
                                <div>
                                    <h4 class="font-medium text-green-900 dark:text-green-100 text-sm mb-1">@lang('Secure Transaction')</h4>
                                    <p class="text-xs text-green-700 dark:text-green-300">
                                        @lang('Your withdrawal request is secured with SSL encryption and will be processed through our verified payment channels.')
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: rgb(55, 65, 81);
        margin-bottom: 0.5rem;
    }
    
    .dark .form-group label {
        color: rgb(209, 213, 219);
    }
    
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 1px solid rgb(209, 213, 219);
        border-radius: 0.75rem;
        background-color: white;
        color: rgb(17, 24, 39);
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }
    
    .dark .form-group input,
    .dark .form-group textarea,
    .dark .form-group select {
        border-color: rgb(75, 85, 99);
        background-color: rgb(55, 65, 81);
        color: white;
    }
    
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: rgb(59, 130, 246);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }
    
    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .form-group .file-upload-wrapper {
        position: relative;
        display: block;
        overflow: hidden;
        border-radius: 0.75rem;
        border: 2px dashed rgb(209, 213, 219);
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        width: 100%;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        margin-top: 0.5rem;
    }
    
    .dark .form-group .file-upload-wrapper {
        border-color: rgb(75, 85, 99);
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    }
    
    .form-group .file-upload-wrapper:hover,
    .form-group .file-upload-wrapper.border-blue-400 {
        border-color: rgb(59, 130, 246);
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }
    
    .dark .form-group .file-upload-wrapper:hover,
    .dark .form-group .file-upload-wrapper.border-blue-400 {
        border-color: rgb(96, 165, 250);
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
    }
    
    .form-group input[type="file"] {
        display: none !important;
    }
    
    /* Override Viser form styles for file inputs */
    .form-group input[type="file"].form-control {
        display: none !important;
    }
    
    .withdrawal-summary {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }
    
    .dark .withdrawal-summary {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }
    
    .amount-badge {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border: 1px solid #d1d5db;
    }
    
    .dark .amount-badge {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
        border-color: #4b5563;
    }
    
    .btn-enhanced {
        position: relative;
        overflow: hidden;
    }
    
    .btn-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-enhanced:hover::before {
        left: 100%;
    }
    
    .sticky-summary {
        transition: all 0.3s ease;
    }
    
    @media (min-width: 1024px) {
        .sticky-summary.scrolled {
            transform: scale(0.98);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    }
    
    /* Form field validation states */
    .form-group.has-error input,
    .form-group.has-error textarea,
    .form-group.has-error select {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .form-group.has-success input,
    .form-group.has-success textarea,
    .form-group.has-success select {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .btn-loading {
        position: relative;
        color: transparent !important;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border-radius: 50%;
        border: 2px solid transparent;
        border-top-color: currentColor;
        animation: button-loading-spinner 1s ease infinite;
    }
    
    @keyframes button-loading-spinner {
        from {
            transform: rotate(0turn);
        }
        to {
            transform: rotate(1turn);
        }
    }
    
    .card-elevated {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }
    
    .card-elevated:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
</style>
@endpush

@push('script')
<script>
    "use strict";
    (function($) {
        function initializeForm() {
            $('label').removeClass('fw-bold');
            
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        enhanceFileInputs();
                        enhanceFormFields();
                    }
                });
            });
            
            const formContainer = document.querySelector('.space-y-6');
            if (formContainer) {
                observer.observe(formContainer, {
                    childList: true,
                    subtree: true
                });
            }
            
            setTimeout(() => {
                enhanceFileInputs();
                enhanceFormFields();
            }, 500);
        }
        
        function enhanceFileInputs() {
            $('input[type="file"]').not('.enhanced').each(function() {
                const $this = $(this);
                $this.addClass('enhanced');
                
                if ($this.closest('.file-upload-wrapper').length > 0) {
                    return;
                }
                
                const acceptAttr = $this.attr('accept') || '';
                const extensions = $this.data('extensions') || '';
                
                let supportedFormats = 'JPG, PNG, PDF (Max: 5MB)'; // Default fallback
                if (extensions) {
                    supportedFormats = extensions.toUpperCase() + ' (Max: 5MB)';
                } else if (acceptAttr) {
                    const exts = acceptAttr.split(',').map(ext => {
                        return ext.trim().replace(/\./g, '').replace(/image\//g, '').replace(/application\//g, '').toUpperCase();
                    }).join(', ');
                    supportedFormats = exts + ' (Max: 5MB)';
                }
                
                const $wrapper = $('<div class="file-upload-wrapper"></div>');
                const $icon = $('<i class="las la-cloud-upload-alt text-3xl text-blue-400 mb-3"></i>');
                const $text = $('<div class="text-sm text-gray-600 dark:text-gray-400"><span class="font-medium text-blue-600 dark:text-blue-400">Choose file</span> or drag and drop</div>');
                const $hint = $(`<div class="text-xs text-gray-500 dark:text-gray-500 mt-2">Supported formats: ${supportedFormats}</div>`);
                
                $this.hide();
                
                $this.after($wrapper);
                $wrapper.append($icon, $text, $hint);
                
                $wrapper.on('click', function() {
                    $this.trigger('click');
                });
                
                $wrapper.on('dragover dragenter', function(e) {
                    e.preventDefault();
                    $(this).addClass('border-blue-400 bg-blue-50 dark:bg-blue-900/20');
                });
                
                $wrapper.on('dragleave', function(e) {
                    e.preventDefault();
                    $(this).removeClass('border-blue-400 bg-blue-50 dark:bg-blue-900/20');
                });
                
                $wrapper.on('drop', function(e) {
                    e.preventDefault();
                    $(this).removeClass('border-blue-400 bg-blue-50 dark:bg-blue-900/20');
                    
                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        $this[0].files = files;
                        $this.trigger('change');
                    }
                });
                
                $this.on('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const fileName = file.name;
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        $text.html(`<span class="font-medium text-green-600 dark:text-green-400">${fileName}</span> <span class="text-xs text-gray-500">(${fileSize}MB)</span>`);
                        $icon.removeClass('text-blue-400 la-cloud-upload-alt').addClass('text-green-500 la-check-circle');
                    } else {
                        $text.html('<span class="font-medium text-blue-600 dark:text-blue-400">Choose file</span> or drag and drop');
                        $icon.removeClass('text-green-500 la-check-circle').addClass('text-blue-400 la-cloud-upload-alt');
                    }
                });
            });
        }
        
        function enhanceFormFields() {
            $('input, textarea, select').not('[type="file"], [type="hidden"], .enhanced-field').each(function() {
                const $this = $(this);
                $this.addClass('enhanced-field');
                
                let $group = $this.closest('.form-group');
                if ($group.length === 0) {
                    $group = $this.parent();
                    $group.addClass('form-group');
                }
                
                $this.on('focus', function() {
                    $group.addClass('focused');
                }).on('blur', function() {
                    $group.removeClass('focused');
                    validateField($this);
                }).on('input change', function() {
                    validateField($this);
                });
            });
        }
        
        function validateField($field) {
            const $group = $field.closest('.form-group');
            const value = $field.val().trim();
            const isRequired = $field.attr('required') !== undefined;
            
            $group.removeClass('has-error has-success');
            
            if (isRequired && !value) {
                $group.addClass('has-error');
            } else if (value) {
                $group.addClass('has-success');
            }
        }
        
        function initializeStickyEffects() {
            const $summary = $('.sticky-summary');
            
            $(window).on('scroll', function() {
                const scrollTop = $(this).scrollTop();
                if (scrollTop > 100) {
                    $summary.addClass('scrolled');
                } else {
                    $summary.removeClass('scrolled');
                }
            });
        }

        $('form').on('submit', function(e) {
            let isValid = true;
            
            $('input[required], textarea[required], select[required]').each(function() {
                const $field = $(this);
                if (!$field.val().trim()) {
                    validateField($field);
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('error', '@lang("Please fill all required fields")');
                return false;
            }
            
            const $submitBtn = $('button[type="submit"]');
            const originalText = $submitBtn.html();
            
            $submitBtn.prop('disabled', true).html('<i class="las la-spinner la-spin mr-2"></i>@lang("Processing...")');
            
            setTimeout(() => {
                $submitBtn.prop('disabled', false).html(originalText);
            }, 10000);
        });
        
        function showToast(type, message) {
            const bgColor = type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500';
            const icon = type === 'error' ? 'la-exclamation-triangle' : type === 'success' ? 'la-check-circle' : 'la-info-circle';
            
            const toast = $(`
                <div class="fixed top-4 right-4 z-50 ${bgColor} text-white px-6 py-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300">
                    <div class="flex items-center space-x-3">
                        <i class="las ${icon} text-xl"></i>
                        <span class="font-medium">${message}</span>
                    </div>
                </div>
            `);
            
            $('body').append(toast);
            
            setTimeout(() => {
                toast.removeClass('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                toast.addClass('translate-x-full');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        $(document).ready(function() {
            initializeForm();
            initializeStickyEffects();

            // Withdrawal Control Check
            @if(!$canWithdraw)
                $('form').on('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        icon: 'error',
                        title: '{{ $statusLabels[$withdrawalStatus] ?? 'Withdrawal Restricted' }}',
                        html: '<div style="text-align: left; margin-top: 15px;"><strong>Reason:</strong><br>{{ $withdrawalReason ?? 'Your withdrawal has been restricted. Please contact support for assistance.' }}</div>',
                        confirmButtonText: 'Understood',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'dark:bg-gray-800',
                            title: 'dark:text-white',
                            htmlContainer: 'dark:text-gray-300',
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Save withdrawal attempt and notify user
                            $.ajax({
                                url: '{{ route('user.withdraw.blocked.attempt') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    withdrawal_id: '{{ $withdraw->id }}',
                                    status: '{{ $withdrawalStatus }}',
                                    reason: '{{ addslashes($withdrawalReason ?? '') }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        window.location.href = '{{ route('user.withdraw.history') }}';
                                    }
                                }
                            });
                        }
                    });
                    
                    return false;
                });
            @endif
        });
        
    })(jQuery);
</script>

{{-- SweetAlert2 Library --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush