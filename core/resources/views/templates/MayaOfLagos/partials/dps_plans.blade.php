<!-- DPS Plans Section -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
    @foreach ($plans as $plan)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transform hover:scale-105 transition-all duration-300">
            
            <!-- Plan Header -->
            <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white bg-opacity-10 rounded-full -translate-y-16 translate-x-16"></div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-2">{{ __($plan->name) }}</h3>
                    <div class="flex items-baseline">
                        <span class="text-3xl font-bold">{{ showAmount($plan->per_installment) }}</span>
                        <span class="text-lg ml-2 opacity-90">{{ gs()->cur_text }}</span>
                    </div>
                    <p class="text-sm opacity-90 mt-1">@lang('Per') {{ $plan->installment_interval }} {{ __(Str::plural('Day', $plan->installment_interval)) }}</p>
                </div>
            </div>
            
            <!-- Plan Features -->
            <div class="p-6">
                <ul class="space-y-4">
                    
                    <!-- Interest Rate -->
                    <li class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                <i class="las la-percentage text-green-600 dark:text-green-400"></i>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300">@lang('Interest Rate')</span>
                        </div>
                        <span class="font-semibold text-green-600 dark:text-green-400">{{ getAmount($plan->interest_rate) }}%</span>
                    </li>
                    
                    <!-- Installment Interval -->
                    <li class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                <i class="las la-calendar text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300">@lang('Payment Interval')</span>
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $plan->installment_interval }} {{ __(Str::plural('Day', $plan->installment_interval)) }}</span>
                    </li>
                    
                    <!-- Total Installments -->
                    <li class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mr-3">
                                <i class="las la-list-ol text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300">@lang('Total Installments')</span>
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $plan->total_installment }}</span>
                    </li>
                    
                    <!-- Total Deposit -->
                    <li class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3">
                                <i class="las la-coins text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300">@lang('Total Deposit')</span>
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ showAmount($plan->total_installment * $plan->per_installment) }} {{ gs()->cur_text }}</span>
                    </li>
                    
                    <!-- Maturity Amount -->
                    <li class="flex items-center justify-between py-3">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900 rounded-full flex items-center justify-center mr-3">
                                <i class="las la-trophy text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300">@lang('You Will Get')</span>
                        </div>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 text-lg">{{ showAmount($plan->final_amount) }} {{ gs()->cur_text }}</span>
                    </li>
                </ul>
                
                <!-- Profit Calculation -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Total Profit')</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">
                            +{{ showAmount($plan->final_amount - ($plan->total_installment * $plan->per_installment)) }} {{ gs()->cur_text }}
                        </span>
                    </div>
                </div>
                
                <!-- Apply Button -->
                <button type="button" 
                        data-id="{{ $plan->id }}" 
                        class="dpsBtn w-full mt-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <i class="las la-rocket mr-2"></i>
                    @lang('Apply Now')
                </button>
            </div>
        </div>
    @endforeach
</div>

@push('script')
<script>
"use strict";
(function($) {
    $('.dpsBtn').on('click', (e) => {
        let modal = $('#dpsModal');
        let data = e.currentTarget.dataset;
        let form = modal.find('form')[0];
        form.action = `{{ route('user.dps.apply', '') }}/${data.id}`;
        modal.modal('show');
    });
})(jQuery);
</script>
@endpush

@push('modal')
<!-- DPS Application Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" id="dpsModal">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95">
        <form action="" method="post">
            @auth
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="las la-piggy-bank mr-2 text-blue-600"></i>
                            @lang('Apply to Open a DPS')
                        </h3>
                        <button type="button" 
                                class="modal-close text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                                onclick="closeModal('dpsModal')">
                            <i class="las la-times text-2xl"></i>
                        </button>
                    </div>
                </div>

                @csrf
                
                <!-- Modal Body -->
                <div class="p-6">
                    @if (checkIsOtpEnable())
                        @include($activeTemplate . 'partials.otp_field')
                        <button type="submit" 
                                class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200">
                            @lang('Submit')
                        </button>
                    @else
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="las la-question-circle text-2xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 mb-6">@lang('Are you sure to apply for this plan?')</p>
                        </div>
                    @endif
                </div>
                
                @if (!checkIsOtpEnable())
                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="closeModal('dpsModal')"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg transition-colors duration-200">
                        @lang('No')
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        @lang('Yes')
                    </button>
                </div>
                @endif
            @else
                <!-- Not Logged In -->
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="las la-times-circle text-3xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">@lang('You are not logged in!')</h3>
                    <button type="button" 
                            onclick="closeModal('dpsModal')"
                            class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        @lang('Close')
                    </button>
                </div>
            @endauth
        </form>
    </div>
</div>

<script>
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.getElementById(modalId).classList.remove('flex');
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.getElementById(modalId).classList.add('flex');
}

// Update the dpsBtn click handler to use new modal functions
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dpsBtn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            let data = e.currentTarget.dataset;
            let form = document.querySelector('#dpsModal form');
            form.action = `{{ route('user.dps.apply', '') }}/${data.id}`;
            openModal('dpsModal');
        });
    });
});
</script>
@endpush