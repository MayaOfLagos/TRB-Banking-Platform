@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')
<div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-6">
    <!-- Mobile Header -->
    <div class="lg:hidden mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Wire Transfer')</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">@lang('International wire transfers')</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                <i class="las la-globe text-purple-600 dark:text-purple-400"></i>
            </div>
        </div>
        
        <!-- Mobile Balance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Available Balance')</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ showAmount(auth()->user()->balance) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="las la-wallet text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Header -->
    <div class="hidden lg:flex lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Wire Transfer')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Send money internationally through SWIFT network')</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-wallet text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Available Balance')</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ showAmount(auth()->user()->balance) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Transfer Limits Sidebar -->
        <div class="lg:order-2 space-y-6">
            <!-- Transfer Limits -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="las la-info-circle mr-2"></i>
                        @lang('Transfer Limits')
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Minimum Per Transaction')</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ showAmount(@$setting->minimum_limit) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Maximum Per Transaction')</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ showAmount(@$setting->maximum_limit) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Daily Maximum')</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ showAmount(@$setting->daily_maximum_limit) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Monthly Maximum')</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ showAmount(@$setting->monthly_maximum_limit) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Daily Max Transactions')</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ @$setting->daily_total_transaction }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Monthly Max Transactions')</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ @$setting->monthly_total_transaction }}</span>
                        </div>
                    </div>

                    @php $transferCharge = $setting->chargeText(); @endphp
                    @if ($transferCharge)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                            <p class="text-sm text-red-600 dark:text-red-400 font-medium">
                                <i class="las la-exclamation-triangle mr-1"></i>
                                @lang('Charge'): {{ $transferCharge }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Instructions -->
            @if ($setting->instruction)
            <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="las la-clipboard-list mr-2"></i>
                        @lang('Instructions')
                    </h3>
                </div>
                <div class="p-6">
                    <div class="prose prose-sm dark:prose-invert text-gray-600 dark:text-gray-400">
                        @php echo $setting->instruction; @endphp
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Transfer Form -->
        <div class="lg:col-span-2 lg:order-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 md:px-8 py-6 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-transparent via-white/20 to-transparent transform rotate-12"></div>
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/10 rounded-full"></div>
                    </div>
                    
                    <div class="relative">
                        <h2 class="text-xl md:text-2xl font-bold text-white mb-2">@lang('International Wire Transfer')</h2>
                        <p class="text-purple-100 text-sm md:text-base">@lang('Send money to banks worldwide through SWIFT network')</p>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    @if (@$setting->instruction)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 mb-6">
                        <div class="prose prose-sm dark:prose-invert text-blue-800 dark:text-blue-200">
                            @php echo @$setting->instruction; @endphp
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('user.transfer.wire.request') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Amount Section -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 md:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="las la-dollar-sign text-purple-500 mr-2"></i>
                                @lang('Transfer Amount')
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        @lang('Amount') <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               name="amount"
                                               step="0.01"
                                               placeholder="0.00"
                                               class="w-full px-4 py-3 pr-16 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-base"
                                               required>
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">{{ gs()->cur_text }}</span>
                                    </div>
                                    <p class="mt-2 text-sm @if(auth()->user()->balance > @$setting->minimum_limit) text-blue-600 dark:text-blue-400 @else text-red-600 dark:text-red-400 @endif">
                                        @lang('Current Balance'): {{ showAmount(auth()->user()->balance) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Wire Transfer Fields -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 md:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="las la-university text-purple-500 mr-2"></i>
                                @lang('Wire Transfer Details')
                            </h3>
                            
                            <div class="space-y-4">
                                <x-viser-form identifier="act" identifierValue="wire_transfer" />
                            </div>
                        </div>

                        <!-- OTP Section -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 md:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="las la-shield-alt text-purple-500 mr-2"></i>
                                @lang('Security Verification')
                            </h3>
                            
                            @include($activeTemplate . 'partials.otp_field')
                        </div>

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
                            <button type="button" 
                                    onclick="history.back()"
                                    class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                                @lang('Cancel')
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                                <i class="las la-paper-plane"></i>
                                <span>@lang('Submit Wire Transfer')</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
'use strict';
(function($) {
    // Form validation and UX improvements
    const form = $('form');
    const amountInput = $('input[name="amount"]');
    const submitButton = $('button[type="submit"]');
    
    // Real-time amount validation
    amountInput.on('input', function() {
        const amount = parseFloat($(this).val()) || 0;
        const minAmount = {{ @$setting->minimum_limit ?? 0 }};
        const maxAmount = {{ @$setting->maximum_limit ?? 999999999 }};
        const balance = {{ auth()->user()->balance ?? 0 }};
        
        let isValid = true;
        let message = '';
        
        if (amount < minAmount) {
            isValid = false;
            message = '@lang("Amount must be at least")' + ' {{ showAmount(@$setting->minimum_limit) }}';
        } else if (amount > maxAmount) {
            isValid = false;
            message = '@lang("Amount cannot exceed")' + ' {{ showAmount(@$setting->maximum_limit) }}';
        } else if (amount > balance) {
            isValid = false;
            message = '@lang("Insufficient balance")';
        }
        
        // Update input styling
        if (amount > 0) {
            if (isValid) {
                $(this).removeClass('border-red-500 dark:border-red-500').addClass('border-green-500 dark:border-green-500');
            } else {
                $(this).removeClass('border-green-500 dark:border-green-500').addClass('border-red-500 dark:border-red-500');
            }
        } else {
            $(this).removeClass('border-red-500 dark:border-red-500 border-green-500 dark:border-green-500');
        }
        
        // Show/hide validation message
        let errorMsg = $(this).parent().next('.error-message');
        if (message && amount > 0) {
            if (errorMsg.length === 0) {
                $(this).parent().after('<p class="error-message mt-1 text-sm text-red-600 dark:text-red-400">' + message + '</p>');
            } else {
                errorMsg.text(message);
            }
        } else {
            errorMsg.remove();
        }
    });
    
    // Form submission
    form.on('submit', function(e) {
        const amount = parseFloat(amountInput.val()) || 0;
        const minAmount = {{ @$setting->minimum_limit ?? 0 }};
        const maxAmount = {{ @$setting->maximum_limit ?? 999999999 }};
        const balance = {{ auth()->user()->balance ?? 0 }};
        
        if (amount < minAmount || amount > maxAmount || amount > balance) {
            e.preventDefault();
            notify('error', '@lang("Please enter a valid amount")');
            return false;
        }
        
        // Show loading state
        submitButton.prop('disabled', true).html('<i class="las la-spinner la-spin mr-2"></i>@lang("Processing...")');
    });
    
})(jQuery);
</script>
@endpush