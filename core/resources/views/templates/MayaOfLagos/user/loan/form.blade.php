@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
    <div class="mx-auto px-0 sm:px-0 lg:px-0">
        <!-- Navigation Pills -->
        <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
            <div class="flex gap-3">
                <a href="{{ route('user.loan.list') }}" 
                   class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ menuActive('user.loan.list') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                    <i class="las la-list mr-2"></i>@lang('My Loan List')
                </a>
                <a href="{{ route('user.loan.plans') }}" 
                   class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ menuActive('user.loan.plans') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                    <i class="las la-clipboard-list mr-2"></i>@lang('Loan Plans')
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Loan Summary Card -->
            <div class="xl:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="las la-hand-holding-usd text-3xl text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">@lang('Loan Application')</h3>
                            <p class="text-blue-100 text-sm">@lang('Review your loan details before confirming')</p>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800">
                        <div class="flex items-center">
                            <i class="las la-exclamation-triangle text-amber-500 mr-2"></i>
                            <p class="text-sm text-amber-800 dark:text-amber-300 font-medium">@lang('Be Sure Before Confirm')</p>
                        </div>
                    </div>

                    <!-- Loan Details -->
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">@lang('Plan Name')</span>
                                <span class="text-gray-900 dark:text-white font-semibold">{{ __($plan->name) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">@lang('Loan Amount')</span>
                                <span class="text-gray-900 dark:text-white font-semibold">{{ showUserAmount($amount, auth()->user()) }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">@lang('Total Installments')</span>
                                <span class="text-gray-900 dark:text-white font-semibold">{{ $plan->total_installment }}</span>
                            </div>

                            @php $perInstallment = $amount * $plan->per_installment / 100; @endphp

                            <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">@lang('Per Installment')</span>
                                <span class="text-gray-900 dark:text-white font-semibold">{{ showUserAmount($perInstallment, auth()->user()) }}</span>
                            </div>

                            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                                <div class="flex justify-between items-center">
                                    <span class="text-red-800 dark:text-red-300 font-bold">@lang('Total You Need To Pay')</span>
                                    <span class="text-red-800 dark:text-red-300 font-bold text-lg">{{ showUserAmount($perInstallment * $plan->total_installment, auth()->user()) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Delay Charge Warning -->
                        @if($plan->delay_value && getAmount($plan->delay_charge))
                        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                            <div class="flex items-start">
                                <i class="las la-exclamation-triangle text-amber-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                <div>
                                    <p class="text-xs text-amber-800 dark:text-amber-300 font-medium mb-1">@lang('Late Payment Penalty')</p>
                                    <p class="text-xs text-amber-700 dark:text-amber-400">
                                        @lang('If an installment is delayed for') <span class="font-bold">{{ $plan->delay_value }}</span> @lang('or more days, an amount of') <span class="font-bold">{{ showUserAmount($plan->delay_charge, auth()->user()) }}</span> @lang('will be applied for each day.')
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Application Form -->
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
                    <!-- Form Header -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-4">
                                <i class="las la-file-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Application Form')</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">@lang('Complete the form below to apply for your loan')</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Body -->
                    <div class="p-6">
                        <form action="{{ route('user.loan.apply.confirm') }}" method="post" enctype="multipart/form-data" id="loanApplicationForm">
                            @csrf

                            <!-- Plan Instructions -->
                            @if($plan->instruction)
                            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-start">
                                    <i class="las la-info-circle text-blue-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">@lang('Important Instructions')</h4>
                                        <div class="text-sm text-blue-700 dark:text-blue-400 prose dark:prose-invert max-w-none">
                                            @php echo $plan->instruction; @endphp
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Dynamic Form Fields -->
                            <div class="space-y-6">
                                <x-viser-form identifier="id" identifierValue="{{ $plan->form_id }}" />
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit" 
                                        id="submitBtn"
                                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-500 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] disabled:transform-none disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                                    <div id="btnContent" class="flex items-center space-x-2">
                                        <i class="las la-check-circle text-lg"></i>
                                        <span>@lang('Submit Loan Application')</span>
                                    </div>
                                    <div id="btnLoading" class="hidden items-center space-x-2">
                                        <i class="las la-spinner la-spin text-lg"></i>
                                        <span>@lang('Processing Application...')</span>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('bottom-menu')
    <li><a href="{{ route('user.loan.plans') }}">@lang('Loan Plans')</a></li>
    <li><a href="{{ route('user.loan.list') }}">@lang('My Loan List')</a></li>
@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loanApplicationForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnContent = document.getElementById('btnContent');
    const btnLoading = document.getElementById('btnLoading');
    let isSubmitting = false;
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Validate form first
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            // If already submitting, prevent double submission
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            // Set loading state
            isSubmitting = true;
            submitBtn.disabled = true;
            btnContent.style.display = 'none';
            btnLoading.classList.remove('hidden');
            btnLoading.classList.add('flex');
            
            // Add timeout fallback
            setTimeout(() => {
                if (isSubmitting) {
                    resetButton();
                    showNotification('error', '@lang('Something went wrong. Please try again.')');
                }
            }, 30000); // 30 seconds timeout
        });
        
        // Reset button state on page show (when navigating back)
        window.addEventListener('pageshow', resetButton);
    }
    
    function validateForm() {
        const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        for (let field of requiredFields) {
            if (!field.value.trim()) {
                field.focus();
                showNotification('error', '@lang('Please fill in all required fields')');
                return false;
            }
        }
        return true;
    }
    
    function resetButton() {
        isSubmitting = false;
        submitBtn.disabled = false;
        btnContent.style.display = 'flex';
        btnLoading.classList.add('hidden');
        btnLoading.classList.remove('flex');
    }
    
    function showNotification(type, message) {
        if (typeof notify === 'function') {
            notify(type, message);
        } else {
            alert(message);
        }
    }
});
</script>
@endpush

@push('style')
<style>
/* Custom form styling */
.viser-form .form-group {
    @apply mb-6;
}

.viser-form label {
    @apply block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2;
}

.viser-form input[type="text"],
.viser-form input[type="email"],
.viser-form input[type="number"],
.viser-form input[type="tel"],
.viser-form input[type="date"],
.viser-form input[type="datetime-local"],
.viser-form textarea,
.viser-form select {
    @apply w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white;
}

.viser-form textarea {
    @apply min-h-[100px] resize-y;
}

.viser-form .input-group {
    @apply relative;
}

.viser-form .input-group-text {
    @apply absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm;
}

.viser-form .file-upload-wrapper {
    @apply border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors;
}

.viser-form .form-check {
    @apply flex items-center space-x-3;
}

.viser-form .form-check-input {
    @apply w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600;
}

.viser-form .form-check-label {
    @apply text-sm text-gray-700 dark:text-gray-300;
}

/* Required field indicator */
.viser-form .required {
    @apply after:content-['*'] after:text-red-500 after:ml-1;
}

/* Error states */
.viser-form .is-invalid {
    @apply border-red-500 dark:border-red-400 bg-red-50 dark:bg-red-900/20;
}

.viser-form .invalid-feedback {
    @apply text-red-600 dark:text-red-400 text-sm mt-1;
}

/* Animation for submit button */
@keyframes pulse-loading {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.loading {
    animation: pulse-loading 1.5s ease-in-out infinite;
}
</style>
@endpush