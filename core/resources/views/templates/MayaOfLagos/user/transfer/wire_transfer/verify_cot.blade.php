@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="min-h-screen py-6 px-1 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8 animate-fade-in">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-600 to-indigo-600 dark:from-purple-500 dark:to-indigo-500 rounded-full mb-4 shadow-lg animate-pulse-slow">
                <i class="las la-certificate text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">@lang('Final Verification')</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">@lang('COT Code Verification - Final Step')</p>
        </div>

        <!-- Interactive Progress Stepper -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-8 mb-8 animate-slide-up">
            <div class="relative">
                <!-- Progress Line -->
                <div class="absolute top-6 left-0 right-0 h-0.5 bg-gray-200 dark:bg-gray-700"></div>
                <div class="absolute top-6 left-0 h-0.5 bg-gradient-to-r from-green-500 via-blue-500 to-purple-500 transition-all duration-1000 ease-out progress-line" style="width: 100%;"></div>
                
                <!-- Steps -->
                <div class="relative flex justify-between">
                    <!-- Step 1: IMF (Completed) -->
                    <div class="flex flex-col items-center step-item completed" data-step="1">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 text-white flex items-center justify-center font-bold text-lg shadow-lg step-circle animate-completed">
                            <i class="las la-check"></i>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400">@lang('IMF Codes')</p>
                            <p class="text-xs text-green-500 dark:text-green-400 mt-1">@lang('Completed') ✓</p>
                        </div>
                    </div>
                    
                    <!-- Step 2: TAX (Completed) -->
                    <div class="flex flex-col items-center step-item completed" data-step="2">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 text-white flex items-center justify-center font-bold text-lg shadow-lg step-circle animate-completed">
                            <i class="las la-check"></i>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400">@lang('TAX Codes')</p>
                            <p class="text-xs text-green-500 dark:text-green-400 mt-1">@lang('Completed') ✓</p>
                        </div>
                    </div>
                    
                    <!-- Step 3: COT (Current) -->
                    <div class="flex flex-col items-center step-item active" data-step="3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 text-white flex items-center justify-center font-bold text-lg shadow-lg transform scale-110 animate-bounce-slow step-circle">
                            <i class="las la-certificate"></i>
                        </div>
                        <div class="mt-3 text-center">
                            <p class="text-sm font-semibold text-purple-600 dark:text-purple-400">@lang('COT Codes')</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">@lang('Enter your COT')</p>
                        </div>
                        <div class="mt-2 w-2 h-2 bg-purple-500 rounded-full animate-ping"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden animate-slide-up-delay">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-700 dark:from-purple-700 dark:via-purple-800 dark:to-indigo-800 px-6 py-6 sm:px-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-white mb-2">@lang('COT Code Verification')</h2>
                        <p class="text-purple-100 dark:text-purple-200">@lang('Enter your Certificate of Transfer verification codes')</p>
                    </div>
                    <div class="mt-4 sm:mt-0 bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2">
                        <p class="text-sm text-purple-100">@lang('Transfer Amount')</p>
                        <p class="text-lg font-bold text-white">{{ showAmount($amount) }} {{ gs()->cur_text }}</p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6 sm:p-8">
            @if($cotCodes->count() > 0)
                <!-- Info Alert -->
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/30 dark:to-indigo-900/30 border border-purple-200 dark:border-purple-700 rounded-xl p-4 sm:p-6 mb-8 animate-fade-in-delay">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                <i class="las la-certificate text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-purple-800 dark:text-purple-200 mb-1">@lang('Final Step - COT Verification')</h3>
                            <p class="text-sm text-purple-700 dark:text-purple-300">
                                @lang('Please enter your COT (Certificate of Transfer) verification codes to complete your wire transfer. This is the final step in the verification process.')
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('user.transfer.wire.verify.cot.submit') }}" method="POST" class="space-y-6" id="cotVerificationForm">
                    @csrf
                    
                    <div class="space-y-6">
                        @foreach($cotCodes as $index => $code)
                            <div class="code-input-group animate-slide-in" style="animation-delay: {{ $index * 0.1 }}s;">
                                <!-- Code Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <label for="billing_code_{{ $index }}" class="flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                            {{ $index + 1 }}
                                        </div>
                                        @lang('COT Code') {{ $index + 1 }}
                                        @if($code->is_required)
                                            <span class="ml-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs px-2 py-1 rounded-full">@lang('Required')</span>
                                        @else
                                            <span class="ml-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs px-2 py-1 rounded-full">@lang('Optional')</span>
                                        @endif
                                    </label>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-purple-600 dark:text-purple-400">{{ showAmount($code->amount) }} {{ gs()->cur_text }}</p>
                                    </div>
                                </div>

                                <!-- Code Input -->
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="las la-certificate text-gray-400 dark:text-gray-500 text-lg group-focus-within:text-purple-500 transition-colors duration-200"></i>
                                    </div>
                                    <input type="text" 
                                           name="billing_code_{{ $index }}" 
                                           id="billing_code_{{ $index }}"
                                           class="block w-full pl-12 pr-4 py-4 text-base border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:focus:ring-purple-400 dark:focus:border-purple-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500" 
                                           placeholder="@lang('Enter your COT verification code')"
                                           autocomplete="off"
                                           @if($code->is_required) required @endif>
                                    <!-- Success Check Icon (hidden by default) -->
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center opacity-0 success-icon">
                                        <i class="las la-check-circle text-purple-500 text-xl"></i>
                                    </div>
                                </div>

                                @if($code->description)
                                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <i class="las la-info-circle text-purple-500 mr-1"></i>
                                            {{ $code->description }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 animate-slide-up-delay-2">
                        <button type="submit" 
                                class="w-full group bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 dark:from-purple-500 dark:to-indigo-500 dark:hover:from-purple-600 dark:hover:to-indigo-600 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" 
                                id="submitButton">
                            <span class="flex items-center justify-center" id="buttonContent">
                                <i class="las la-certificate text-lg mr-2 group-hover:animate-pulse" id="buttonIcon"></i>
                                <span class="text-base" id="buttonText">@lang('Complete Transfer')</span>
                                <i class="las la-check-circle text-lg ml-2 group-hover:scale-110 transition-transform duration-200" id="buttonEndIcon"></i>
                            </span>
                        </button>
                        
                        <!-- Progress indicator for form submission -->
                        <div class="mt-4 hidden" id="submitProgress">
                            <div class="flex items-center justify-center text-purple-600 dark:text-purple-400">
                                <div class="animate-spin rounded-full h-5 w-5 border-2 border-purple-600 border-t-transparent mr-2"></div>
                                <span class="text-sm">@lang('Completing verification, please wait...')</span>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="text-center py-12 animate-fade-in-delay">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce-slow">
                        <i class="las la-check-circle text-white text-3xl"></i>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">@lang('No COT Codes Required')</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        @lang('Your account does not require COT verification codes. Your transfer verification is complete!')
                    </p>
                    
                    <a href="{{ route('user.transfer.wire.confirm') }}" 
                       class="inline-flex items-center bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg group">
                        <i class="las la-check-circle text-lg mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                        <span>@lang('Complete Transfer')</span>
                    </a>
                </div>
            @endif
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay with Blur Background -->
<div id="cotLoadingOverlay" class="fixed inset-0 z-50 hidden opacity-0">
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
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Completing Verification')</h3>
                <p class="text-gray-600 dark:text-gray-400">@lang('Please wait while we process your COT codes...')</p>
                
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
                    @lang('Final verification in progress')
                </p>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    /* Custom Animations */
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slide-in {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    @keyframes pulse-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .animate-fade-in { animation: fade-in 0.6s ease-out; }
    .animate-fade-in-delay { animation: fade-in 0.8s ease-out 0.2s both; }
    .animate-slide-up { animation: slide-up 0.6s ease-out; }
    .animate-slide-up-delay { animation: slide-up 0.8s ease-out 0.3s both; }
    .animate-slide-up-delay-2 { animation: slide-up 1s ease-out 0.5s both; }
    .animate-slide-in { animation: slide-in 0.6s ease-out both; }
    .animate-bounce-slow { animation: bounce-slow 2s infinite; }
    .animate-pulse-slow { animation: pulse-slow 2s infinite; }
    
    /* Completed step animation */
    .animate-completed {
        animation: completed-pulse 1s ease-out;
    }
    
    @keyframes completed-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    /* Step Animations */
    .step-item.active .step-circle {
        background: linear-gradient(135deg, #a855f7, #6366f1);
        box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
        animation: step-pulse 2s infinite;
    }
    
    .step-item.completed .step-circle {
        background: linear-gradient(135deg, #10b981, #059669);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }
    
    @keyframes step-pulse {
        0%, 100% { 
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
        }
        50% { 
            transform: scale(1.15);
            box-shadow: 0 6px 20px rgba(168, 85, 247, 0.6);
        }
    }
    
    /* Form Input Enhancements */
    .code-input-group input:focus + .success-icon {
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .code-input-group input:valid + .success-icon {
        opacity: 1;
        transition: opacity 0.3s ease 0.1s;
    }
    
    /* Mobile Responsive Adjustments */
    @media (max-width: 640px) {
        .step-item {
            flex: 1;
            min-width: 0;
        }
        
        .step-item .step-circle {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }
        
        .step-item p {
            font-size: 11px;
            line-height: 1.2;
        }
        
        .code-input-group label {
            font-size: 14px;
        }
        
        .code-input-group input {
            padding: 12px 16px 12px 44px;
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }
    
    /* Loading State */
    .form-loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .form-loading input {
        background-color: #f3f4f6;
    }
    
    /* Button loading state */
    button.form-loading {
        opacity: 0.75;
        cursor: not-allowed;
        transform: none !important;
    }
    
    button.form-loading:hover {
        transform: none !important;
        scale: 1 !important;
    }
    
    /* Loading overlay styles */
    #cotLoadingOverlay {
        transition: opacity 0.3s ease-in-out;
    }
    
    #cotLoadingOverlay.opacity-100 {
        opacity: 1;
    }
    
    /* Animation delays for progress dots */
    .animation-delay-150 {
        animation-delay: 0.15s;
    }
    .animation-delay-300 {
        animation-delay: 0.3s;
    }
    
    /* Loading overlay backdrop blur */
    .backdrop-blur-sm {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    
    /* Loading overlay entrance animation */
    #cotLoadingOverlay > div:first-child {
        animation: fadeInBlur 0.3s ease-out;
    }
    
    #cotLoadingOverlay .bg-white {
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
    
    /* Spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cotVerificationForm');
    const submitButton = document.getElementById('submitButton');
    const submitProgress = document.getElementById('submitProgress');
    const progressLine = document.querySelector('.progress-line');
    
    // Animate progress line on load
    setTimeout(() => {
        if (progressLine) {
            progressLine.style.width = '100%';
        }
    }, 500);
    
    // Form submission handling
    if (form) {
        form.addEventListener('submit', function(e) {
            // Show loading overlay with blur background
            showCotLoadingOverlay();
            
            // Update button state to loading
            updateButtonState(true);
            
            // Add loading class to form
            form.classList.add('form-loading');
            
            // If validation fails, hide overlay and re-enable form
            setTimeout(() => {
                if (!form.checkValidity()) {
                    hideCotLoadingOverlay();
                    updateButtonState(false);
                    form.classList.remove('form-loading');
                }
            }, 100);
        });
    }
    
    // Function to show COT loading overlay
    function showCotLoadingOverlay() {
        const overlay = document.getElementById('cotLoadingOverlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        
        // Add fade-in animation
        setTimeout(() => {
            overlay.classList.add('opacity-100');
        }, 10);
    }
    
    // Function to hide COT loading overlay
    function hideCotLoadingOverlay() {
        const overlay = document.getElementById('cotLoadingOverlay');
        overlay.classList.remove('opacity-100');
        setTimeout(() => {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }, 300);
    }
    
    // Function to update button state
    function updateButtonState(isLoading) {
        const button = document.getElementById('submitButton');
        const icon = document.getElementById('buttonIcon');
        const text = document.getElementById('buttonText');
        const endIcon = document.getElementById('buttonEndIcon');
        
        if (isLoading) {
            button.classList.add('form-loading');
            button.disabled = true;
            icon.className = 'las la-spinner-third animate-spin text-lg mr-2';
            text.textContent = '@lang("Processing...")';
            endIcon.style.display = 'none';
        } else {
            button.classList.remove('form-loading');
            button.disabled = false;
            icon.className = 'las la-certificate text-lg mr-2 group-hover:animate-pulse';
            text.textContent = '@lang("Complete Transfer")';
            endIcon.style.display = 'inline';
            endIcon.className = 'las la-check-circle text-lg ml-2 group-hover:scale-110 transition-transform duration-200';
        }
    }
    
    // Input validation feedback
    const inputs = document.querySelectorAll('input[name^="billing_code_"]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const group = this.closest('.code-input-group');
            const successIcon = group.querySelector('.success-icon');
            
            if (this.value.length >= 6) { // Assuming minimum code length
                successIcon.style.opacity = '1';
                this.classList.add('border-purple-300');
                this.classList.remove('border-gray-200');
            } else {
                successIcon.style.opacity = '0';
                this.classList.remove('border-purple-300');
                this.classList.add('border-gray-200');
            }
        });
        
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-purple-500');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-purple-500');
        });
    });
});
</script>
@endpush

@endsection