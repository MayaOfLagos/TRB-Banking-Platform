@extends($activeTemplate . 'user.fdr.layout')
@section('fdr-content')

<!-- FDR Plans Section -->
<div class="space-y-8">
    
    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden fdr-card fade-in group">
            
            <!-- Plan Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold">{{ __($plan->name) }}</h3>
                    <div class="bg-white bg-opacity-20 px-3 py-1 rounded-full">
                        <span class="text-sm font-semibold">@lang('Plan')</span>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-bold">{{ getAmount($plan->interest_rate) }}%</div>
                    <div class="text-blue-100 text-sm">@lang('Interest Rate')</div>
                </div>
            </div>

            <!-- Plan Details -->
            <div class="p-6 space-y-4">
                <!-- Lock-in Period -->
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <i class="las la-clock text-blue-600 text-xl mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">@lang('Lock-in Period')</span>
                    </div>
                    <span class="text-gray-900 dark:text-white font-bold">{{ $plan->locked_days }} {{__(Str::plural('Day', $plan->locked_days))}}</span>
                </div>

                <!-- Minimum Amount -->
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <i class="las la-dollar-sign text-green-600 text-xl mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">@lang('Minimum Amount')</span>
                    </div>
                    <span class="text-gray-900 dark:text-white font-bold">{{ showUserAmount($plan->minimum_amount, auth()->user()) }}</span>
                </div>

                <!-- Maximum Amount -->
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <i class="las la-coins text-yellow-600 text-xl mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">@lang('Maximum Amount')</span>
                    </div>
                    <span class="text-gray-900 dark:text-white font-bold">
                        @if($plan->maximum_amount == 0)
                            @lang('Unlimited')
                        @else
                            {{ showUserAmount($plan->maximum_amount, auth()->user()) }}
                        @endif
                    </span>
                </div>

                <!-- Installment Interval -->
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center">
                        <i class="las la-calendar-alt text-purple-600 text-xl mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">@lang('Get Profit Every')</span>
                    </div>
                    <span class="text-gray-900 dark:text-white font-bold">{{ $plan->installment_interval }} {{__(Str::plural('Day', $plan->installment_interval))}}</span>
                </div>
            </div>

            <!-- Action Button -->
            <div class="p-6 pt-0">
                <button type="button" 
                        data-id="{{ $plan->id }}" 
                        data-minimum="{{ showUserAmount($plan->minimum_amount, auth()->user()) }}" 
                        data-maximum="{{ showUserAmount($plan->maximum_amount, auth()->user()) }}"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg fdrBtn">
                    <i class="las la-plus-circle mr-2"></i>
                    @lang('Apply for FDR')
                </button>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="col-span-full">
            <div class="text-center py-16">
                <div class="mx-auto h-32 w-32 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                    <i class="las la-chart-line text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('No FDR Plans Available')</h3>
                <p class="text-gray-600 dark:text-gray-400">@lang('There are currently no FDR plans available. Please check back later.')</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@endsection

@push('script')
<script>
"use strict";
(function($) {
    $('.fdrBtn').on('click', (e) => {
        let data = e.currentTarget.dataset;
        let modal = document.getElementById('fdrModal');
        let form = modal.querySelector('form');
        
        form.action = `{{ route('user.fdr.apply', '') }}/${data.id}`;
        
        modal.querySelector('.min-limit').textContent = `Min Amount ${data.minimum}`;
        modal.querySelector('.max-limit').textContent = `Max Amount ${data.maximum}`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    });
    
    function closeModal() {
        let modal = document.getElementById('fdrModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
        
        let form = modal.querySelector('form');
        form.reset();
        resetSubmitButton();
    }
    
    function setLoadingState() {
        let submitBtn = document.getElementById('fdrSubmitBtn');
        let submitText = submitBtn.querySelector('.submit-text');
        let loadingText = submitBtn.querySelector('.loading-text');
        
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        loadingText.classList.remove('hidden');
    }
    
    function resetSubmitButton() {
        let submitBtn = document.getElementById('fdrSubmitBtn');
        let submitText = submitBtn.querySelector('.submit-text');
        let loadingText = submitBtn.querySelector('.loading-text');
        
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        loadingText.classList.add('hidden');
    }
    
    document.getElementById('fdrModal').addEventListener('submit', function(e) {
        let form = e.target;
        if (form.tagName === 'FORM') {
            setLoadingState();
            
            setTimeout(() => {
                if (document.getElementById('fdrSubmitBtn')) {
                    resetSubmitButton();
                }
            }, 5000);
        }
    });
    
    document.getElementById('fdrModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', closeModal);
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            let modal = document.getElementById('fdrModal');
            if (!modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });
})(jQuery);
</script>
@endpush

@push('modal')
<div id="fdrModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
    <div class="relative w-full max-w-md mx-4 bg-white dark:bg-gray-800 rounded-xl shadow-2xl transform transition-all duration-300 scale-100">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                @lang('Apply to Open an FDR')
            </h3>
            <button type="button" data-modal-close class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                <i class="las la-times text-2xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form action="" method="post" class="space-y-6">
                @csrf
                @auth
                    <!-- Amount Input -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('Amount')
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   step="any" 
                                   name="amount" 
                                   class="w-full px-4 py-3 pr-20 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors duration-200" 
                                   placeholder="@lang('Enter An Amount')" 
                                   required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ gs()->cur_text }}</span>
                            </div>
                        </div>
                        
                        <!-- Limit Messages -->
                        <div class="flex justify-between">
                            <p class="text-xs text-red-600 dark:text-red-400 min-limit"></p>
                            <p class="text-xs text-red-600 dark:text-red-400 max-limit"></p>
                        </div>
                    </div>

                    <!-- OTP Field -->
                    @include($activeTemplate . 'partials.otp_field')

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="fdrSubmitBtn"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 disabled:hover:scale-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <span class="submit-text flex items-center justify-center">
                            <i class="las la-paper-plane mr-2"></i>
                            @lang('Submit Application')
                        </span>
                        <span class="loading-text hidden flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            @lang('Processing...')
                        </span>
                    </button>
                @else
                    <!-- Not Logged In State -->
                    <div class="text-center py-8">
                        <div class="mx-auto h-16 w-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mb-4">
                            <i class="las la-times-circle text-3xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            @lang('You are not logged in!')
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            @lang('Please log in to apply for an FDR.')
                        </p>
                        <div class="flex space-x-3">
                            <a href="{{ route('user.login') }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                                @lang('Login')
                            </a>
                            <button type="button" 
                                    data-modal-close
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                                @lang('Close')
                            </button>
                        </div>
                    </div>
                @endauth
            </form>
        </div>
    </div>
</div>
@endpush