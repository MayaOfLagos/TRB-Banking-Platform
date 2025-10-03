@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mr-4">
                    <i class="las la-shield-alt text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('Transfer PIN Management')</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">@lang('Secure your wire transfers and billing code verification')</p>
                </div>
            </div>
            @if(!$user->hasTransferPin())
                {{-- Set New Transfer PIN --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 mb-8">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                            <i class="las la-info-circle"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Setup Required')</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                @lang('You haven\'t set a transfer PIN yet. A transfer PIN is required for secure wire transfers and billing code verification.')
                            </p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('user.transfer.pin.set') }}" method="post" class="space-y-6">
                    @csrf
                    
                    <!-- Current Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            @lang('Current Password') <span class="text-red-500">*</span>
                        </label>
                        <input 
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                            name="current_password" 
                            type="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="@lang('Enter your account password')">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">@lang('Enter your account password for verification')</p>
                    </div>
                    
                    <!-- Transfer PIN Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                @lang('Transfer PIN') <span class="text-red-500">*</span>
                            </label>
                            <input 
                                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-center text-lg font-mono tracking-wider focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                name="transfer_pin" 
                                type="password" 
                                maxlength="4" 
                                pattern="[0-9]{4}" 
                                required 
                                autocomplete="new-password"
                                placeholder="••••">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">@lang('Enter exactly 4 digits')</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                @lang('Confirm Transfer PIN') <span class="text-red-500">*</span>
                            </label>
                            <input 
                                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-center text-lg font-mono tracking-wider focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                name="transfer_pin_confirmation" 
                                type="password" 
                                maxlength="4" 
                                pattern="[0-9]{4}" 
                                required 
                                autocomplete="new-password"
                                placeholder="••••">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">@lang('Re-enter your 4-digit PIN')</p>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="las la-shield-alt mr-2"></i>
                        @lang('Set Transfer PIN')
                    </button>
                </form>
            @else
                {{-- Update Transfer PIN --}}
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 mb-8">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center mr-4 flex-shrink-0 mt-1">
                            <i class="las la-check-circle"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('PIN Active')</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                @lang('Transfer PIN is active and ready for secure transactions.')
                            </p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('user.transfer.pin.update') }}" method="post" class="space-y-6">
                    @csrf
                    
                    <!-- Current PIN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            @lang('Current Transfer PIN') <span class="text-red-500">*</span>
                        </label>
                        <input 
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-center text-lg font-mono tracking-wider focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                            name="current_pin" 
                            type="password" 
                            maxlength="4" 
                            pattern="[0-9]{4}" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">@lang('Enter your current 4-digit transfer PIN')</p>
                    </div>
                    
                    <!-- New PIN Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                @lang('New Transfer PIN') <span class="text-red-500">*</span>
                            </label>
                            <input 
                                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-center text-lg font-mono tracking-wider focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                name="transfer_pin" 
                                type="password" 
                                maxlength="4" 
                                pattern="[0-9]{4}" 
                                required 
                                autocomplete="new-password"
                                placeholder="••••">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">@lang('Enter new 4-digit PIN')</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                @lang('Confirm New PIN') <span class="text-red-500">*</span>
                            </label>
                            <input 
                                class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-center text-lg font-mono tracking-wider focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                name="transfer_pin_confirmation" 
                                type="password" 
                                maxlength="4" 
                                pattern="[0-9]{4}" 
                                required 
                                autocomplete="new-password"
                                placeholder="••••">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">@lang('Re-enter new PIN')</p>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="las la-sync mr-2"></i>
                        @lang('Update Transfer PIN')
                    </button>
                </form>
            @endif
        </div>
        
        <!-- Important Information -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mr-3">
                    <i class="las la-info-circle"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Important Information')</h3>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <i class="las la-check text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Wire Transfer Security')</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">@lang('Your transfer PIN is used for wire transfer verification')</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <i class="las la-check text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Billing Code Authentication')</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">@lang('Required for billing codes (IMF, TAX, COT) authentication')</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <i class="las la-hashtag text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('4-Digit Format')</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">@lang('PIN must be exactly 4 digits (0-9)')</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <i class="las la-shield-alt text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Encrypted Storage')</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">@lang('PIN is encrypted and stored securely')</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mr-3 flex-shrink-0 mt-0.5">
                        <i class="las la-exclamation-triangle text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('Keep Private')</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">@lang('Never share your transfer PIN with anyone')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    /* Custom PIN input styling */
    input[type="password"].font-mono {
        letter-spacing: 0.5em;
    }
    
    /* Focus ring enhancement */
    .focus\:ring-2:focus {
        --tw-ring-opacity: 0.3;
    }
    
    /* Gradient button hover effects */
    .bg-gradient-to-r:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all PIN input fields
        const pinInputs = document.querySelectorAll('input[name="transfer_pin"], input[name="transfer_pin_confirmation"], input[name="current_pin"]');
        
        pinInputs.forEach(function(input) {
            // Only allow numeric input for PIN fields
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 4) {
                    this.value = this.value.slice(0, 4);
                }
            });
            
            // Prevent non-numeric key presses
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(String.fromCharCode(e.which))) {
                    e.preventDefault();
                }
            });
        });
        
        // Auto-focus next field after 4 digits
        const transferPinInput = document.querySelector('input[name="transfer_pin"]');
        const confirmPinInput = document.querySelector('input[name="transfer_pin_confirmation"]');
        
        if (transferPinInput && confirmPinInput) {
            transferPinInput.addEventListener('input', function() {
                if (this.value.length === 4) {
                    confirmPinInput.focus();
                }
            });
        }
        
        // Add visual feedback on form submission
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="las la-spinner la-spin mr-2"></i>Processing...';
                    submitBtn.disabled = true;
                }
            });
        });
    });
</script>
@endpushhttps://localhost/admin/users/detail/1