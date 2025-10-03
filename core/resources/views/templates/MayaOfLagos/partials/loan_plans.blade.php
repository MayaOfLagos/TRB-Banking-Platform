<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
    @forelse($plans as $plan)
        <div class="loan-plan-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 overflow-hidden">
            <!-- Plan Header -->
            <div class="relative bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 lg:p-8">
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                <div class="absolute bottom-0 left-0 w-16 h-16 bg-white/5 rounded-full -ml-8 -mb-8"></div>
                
                <div class="relative">
                    <h3 class="text-xl lg:text-2xl font-bold mb-2">{{ __($plan->name) }}</h3>
                    <div class="flex items-baseline">
                        <span class="text-3xl lg:text-4xl font-bold">{{ getAmount($plan->per_installment) }}%</span>
                        <span class="text-sm opacity-80 ml-2">/{{ $plan->installment_interval }} {{__(Str::plural('Day', $plan->installment_interval))}}</span>
                    </div>
                    <p class="text-sm opacity-90 mt-2">@lang('Per Installment Rate')</p>
                </div>
            </div>

            <!-- Plan Features -->
            <div class="p-6 lg:p-8">
                <!-- Essential Information (Always Visible) -->
                <ul class="space-y-4">
                    <li class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="las la-arrow-down text-green-500 mr-2"></i>
                            @lang('Amount Range')
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white text-sm">
                            {{ showAmount($plan->minimum_amount) }} - {{ showAmount($plan->maximum_amount) }}
                        </span>
                    </li>

                    <li class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="las la-clock text-purple-500 mr-2"></i>
                            @lang('Duration')
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $plan->total_installment }} @lang('installments')
                        </span>
                    </li>

                    <li class="flex items-center justify-between py-2">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="las la-percentage text-blue-500 mr-2"></i>
                            @lang('Total Interest')
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ getAmount($plan->per_installment * $plan->total_installment - 100) }}%
                        </span>
                    </li>
                </ul>

                <!-- Expand/Collapse Toggle -->
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button @click="openDetailsModal($el.dataset)" 
                            data-id="{{ $plan->id }}"
                            data-name="{{ $plan->name }}"
                            data-minimum="{{ showAmount($plan->minimum_amount) }}"
                            data-maximum="{{ showAmount($plan->maximum_amount) }}"
                            data-rate="{{ getAmount($plan->per_installment) }}"
                            data-interval="{{ $plan->installment_interval }}"
                            data-total="{{ $plan->total_installment }}"
                            data-delay-charge="{{ $plan->delay_value && getAmount($plan->delay_charge) ? showAmount($plan->delay_charge) : '' }}"
                            data-delay-value="{{ $plan->delay_value ?: '' }}"
                            data-instruction="{{ $plan->instruction ?: '' }}"
                            class="w-full flex items-center justify-center space-x-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">
                        <i class="las la-info-circle text-lg"></i>
                        <span>@lang('View Details')</span>
                    </button>
                </div>
            </div>

            <!-- Plan Action -->
            <div class="px-6 lg:px-8 pb-6 lg:pb-8">
                <button type="button" 
                        data-id="{{ $plan->id }}" 
                        data-minimum="{{ showAmount($plan->minimum_amount) }}" 
                        data-maximum="{{ showAmount($plan->maximum_amount) }}" 
                        data-name="{{ $plan->name }}"
                        class="loanBtn w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                    <i class="las la-hand-holding-usd text-lg"></i>
                    <span>@lang('Apply Now')</span>
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-12">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="las la-hand-holding-usd text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No Loan Plans Available')</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-md">@lang('Currently there are no loan plans available. Please check back later or contact support for assistance.')</p>
        </div>
    @endforelse
</div>

<!-- Loan Application Modal -->
<div x-data="loanModal()" x-show="open" x-cloak 
     @keydown.escape.window="close()"
     @open-loan-modal.window="openModal($event.detail)"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         @click="close()" 
         class="fixed inset-0 bg-black bg-opacity-75 transition-opacity">
    </div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all">
            
            <form :action="formAction" method="post" @submit="handleSubmit" class="loan-application-form">
                @auth
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-t-2xl px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold flex items-center">
                                <i class="las la-hand-holding-usd mr-2"></i>
                                @lang('Apply for Loan')
                            </h3>
                            <button type="button" 
                                    @click="close()"
                                    class="text-white hover:text-gray-200 transition-colors">
                                <i class="las la-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    @csrf
                    <!-- Modal Body -->
                    <div class="p-6">
                        <!-- Plan Info -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <h6 class="font-semibold text-blue-900 dark:text-blue-300 mb-2" x-text="planName">@lang('Selected Plan')</h6>
                            <p class="text-sm text-blue-700 dark:text-blue-400">@lang('Please enter the amount you wish to borrow within the allowed limits.')</p>
                        </div>

                        <!-- Amount Input -->
                        <div class="mb-6">
                            <label for="loanAmount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Loan Amount') <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       step="any" 
                                       name="amount" 
                                       x-model="amount"
                                       id="loanAmount"
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white pr-20" 
                                       :class="{ 'border-red-500': !isValidAmount && amount, 'border-green-500': isValidAmount && amount }"
                                       placeholder="@lang('Enter loan amount')" 
                                       required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 dark:text-gray-400 font-medium">{{ gs()->cur_text }}</span>
                                </div>
                            </div>
                            
                            <!-- Limit Display -->
                            <div class="flex justify-between mt-2 text-xs">
                                <span class="text-green-600 dark:text-green-400 flex items-center">
                                    <i class="las la-arrow-down mr-1"></i>
                                    @lang('Min:') <span class="font-medium ml-1" x-text="minAmount"></span>
                                </span>
                                <span class="text-red-600 dark:text-red-400 flex items-center">
                                    <i class="las la-arrow-up mr-1"></i>
                                    @lang('Max:') <span class="font-medium ml-1" x-text="maxAmount"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 rounded-b-2xl border-t border-gray-200 dark:border-gray-700">
                        <div class="flex space-x-3">
                            <button type="button" 
                                    @click="close()"
                                    class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors">
                                @lang('Cancel')
                            </button>
                            <button type="submit" 
                                    :disabled="!isValidAmount || loading"
                                    :class="{ 'opacity-50 cursor-not-allowed': !isValidAmount || loading }"
                                    class="flex-1 px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                                <template x-if="!loading">
                                    <i class="las la-check text-lg"></i>
                                </template>
                                <template x-if="loading">
                                    <i class="las la-spinner la-spin text-lg"></i>
                                </template>
                                <span x-text="loading ? '@lang('Processing...')' : '@lang('Continue')'"></span>
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Not Authenticated -->
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="las la-times-circle text-3xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('Authentication Required')</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('You need to be logged in to apply for a loan.')</p>
                        
                        <div class="flex space-x-3 justify-center mb-4">
                            <a href="{{ route('user.login') }}" 
                               class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                @lang('Login')
                            </a>
                            <a href="{{ route('user.register') }}" 
                               class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                                @lang('Register')
                            </a>
                        </div>

                        <button type="button" 
                                @click="close()"
                                class="w-full px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors">
                            @lang('Close')
                        </button>
                    </div>
                @endauth
            </form>
        </div>
    </div>
</div>

<!-- Loan Details Modal -->
<div x-data="detailsModal()" x-show="open" x-cloak 
     @keydown.escape.window="close()"
     @open-details-modal.window="openModal($event.detail)"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         @click="close()" 
         class="fixed inset-0 bg-black bg-opacity-75 transition-opacity">
    </div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-2xl transform rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-t-2xl px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold flex items-center">
                        <i class="las la-info-circle mr-2"></i>
                        <span x-text="planName"></span> @lang('Details')
                    </h3>
                    <button type="button" 
                            @click="close()"
                            class="text-white hover:text-gray-200 transition-colors">
                        <i class="las la-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6">
                <!-- Plan Overview -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h6 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <i class="las la-chart-bar text-blue-500 mr-2"></i>
                        @lang('Plan Overview')
                    </h6>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Amount Range')</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="minAmount + ' - ' + maxAmount"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Per Installment Rate')</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="rate + '%'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Installment Interval')</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="interval + ' days'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Total Installments')</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="total"></span>
                        </div>
                    </div>
                </div>

                <!-- Detailed Breakdown -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h6 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <i class="las la-calculator text-blue-500 mr-2"></i>
                        @lang('Loan Breakdown')
                    </h6>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Installment Interval')</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="interval + ' days'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Per Installment Rate')</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="rate + '%'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Total Installments')</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="total"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Total Interest Rate')</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="(parseFloat(rate) * parseInt(total) - 100) + '%'"></span>
                        </div>
                    </div>
                </div>

                <!-- Example Calculation -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h6 class="font-semibold text-blue-900 dark:text-blue-300 mb-3 flex items-center">
                        <i class="las la-lightbulb text-yellow-500 mr-2"></i>
                        @lang('Example Calculation')
                    </h6>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-400">@lang('If you borrow')</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-300" x-text="minAmount"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-400">@lang('Each installment')</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-300" x-text="installmentAmount"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-400">@lang('Total interest')</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-300" x-text="totalInterest"></span>
                        </div>
                        <hr class="border-blue-200 dark:border-blue-700">
                        <div class="flex justify-between">
                            <span class="font-semibold text-blue-800 dark:text-blue-200">@lang('Total payable')</span>
                            <span class="font-bold text-blue-900 dark:text-blue-100" x-text="totalPayable"></span>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-3">
                    <!-- Delay Charge -->
                    <div x-show="delayCharge" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
                        <div class="flex items-start">
                            <i class="las la-exclamation-triangle text-amber-500 mr-2 mt-0.5"></i>
                            <div>
                                <p class="text-xs font-medium text-amber-800 dark:text-amber-300">@lang('Delay Charge Policy')</p>
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1" x-text="delayChargeText"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div x-show="instruction" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                        <div class="flex items-start">
                            <i class="las la-info-circle text-green-500 mr-2 mt-0.5"></i>
                            <div>
                                <p class="text-xs font-medium text-green-800 dark:text-green-300">@lang('Important Instructions')</p>
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1" x-text="instruction"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Schedule Preview -->
                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-3">
                        <div class="flex items-start">
                            <i class="las la-calendar-alt text-purple-500 mr-2 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-purple-800 dark:text-purple-300 mb-2">@lang('Repayment Schedule')</p>
                                <div class="text-xs text-purple-600 dark:text-purple-400 space-y-1">
                                    <div class="flex justify-between">
                                        <span>@lang('Payment frequency')</span>
                                        <span class="font-medium" x-text="'Every ' + interval + ' days'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>@lang('Total duration')</span>
                                        <span class="font-medium" x-text="(parseInt(interval) * parseInt(total)) + ' days'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Effective Rate Information -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-3">
                        <div class="flex items-start">
                            <i class="las la-chart-line text-indigo-500 mr-2 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-xs font-medium text-indigo-800 dark:text-indigo-300 mb-2">@lang('Rate Information')</p>
                                <div class="text-xs text-indigo-600 dark:text-indigo-400 space-y-1">
                                    <div class="flex justify-between">
                                        <span>@lang('Periodic rate')</span>
                                        <span class="font-medium" x-text="rate + '%'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>@lang('Estimated APR')</span>
                                        <span class="font-medium" x-text="estimatedApr + '%'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 rounded-b-2xl border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-end">
                    <button type="button" 
                            @click="close()"
                            class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors font-medium">
                        @lang('Close')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
// Alpine.js component for details modal
function detailsModal() {
    return {
        open: false,
        planId: null,
        planName: '',
        minAmount: '',
        maxAmount: '',
        rate: '',
        interval: '',
        total: '',
        delayCharge: '',
        delayValue: '',
        instruction: '',
        
        get delayChargeText() {
            if (!this.delayCharge || !this.delayValue) return '';
            return this.delayCharge + ' per day after ' + this.delayValue + ' days delay';
        },
        
        get installmentAmount() {
            if (!this.minAmount || !this.rate || !this.total) return '';
            const amount = parseFloat(this.minAmount.replace(/[^0-9.-]+/g, ''));
            const totalPayable = amount * (1 + (parseFloat(this.rate) * parseInt(this.total) / 100));
            return this.formatCurrency(totalPayable / parseInt(this.total));
        },
        
        get totalInterest() {
            if (!this.minAmount || !this.rate || !this.total) return '';
            const amount = parseFloat(this.minAmount.replace(/[^0-9.-]+/g, ''));
            const totalPayable = amount * (1 + (parseFloat(this.rate) * parseInt(this.total) / 100));
            return this.formatCurrency(totalPayable - amount);
        },
        
        get totalPayable() {
            if (!this.minAmount || !this.rate || !this.total) return '';
            const amount = parseFloat(this.minAmount.replace(/[^0-9.-]+/g, ''));
            const totalPayable = amount * (1 + (parseFloat(this.rate) * parseInt(this.total) / 100));
            return this.formatCurrency(totalPayable);
        },
        
        get estimatedApr() {
            if (!this.rate || !this.total || !this.interval) return '';
            const apr = (parseFloat(this.rate) * parseInt(this.total) * 365) / (parseInt(this.interval) * parseInt(this.total));
            return apr.toFixed(1);
        },
        
        formatCurrency(amount) {
            return '{{ gs()->cur_text }}' + amount.toLocaleString();
        },
        
        openModal(planData) {
            this.planId = planData.id;
            this.planName = planData.name;
            this.minAmount = planData.minimum;
            this.maxAmount = planData.maximum;
            this.rate = planData.rate;
            this.interval = planData.interval;
            this.total = planData.total;
            this.delayCharge = planData.delayCharge;
            this.delayValue = planData.delayValue;
            this.instruction = planData.instruction;
            this.open = true;
        },
        
        close() {
            this.open = false;
            this.planId = null;
            this.planName = '';
            this.minAmount = '';
            this.maxAmount = '';
            this.rate = '';
            this.interval = '';
            this.total = '';
            this.delayCharge = '';
            this.delayValue = '';
            this.instruction = '';
        }
    }
}

// Global function to open details modal
window.openDetailsModal = function(planData) {
    // Dispatch custom event that Alpine.js can listen to
    window.dispatchEvent(new CustomEvent('open-details-modal', {
        detail: planData
    }));
};

// Alpine.js component for loan modal
function loanModal() {
    return {
        open: false,
        loading: false,
        planId: null,
        planName: '',
        minAmount: '',
        maxAmount: '',
        amount: '',
        formAction: '',
        
        get isValidAmount() {
            if (!this.amount) return false;
            const numAmount = parseFloat(this.amount);
            const numMin = parseFloat(this.minAmount.replace(/[^0-9.-]+/g, ''));
            const numMax = parseFloat(this.maxAmount.replace(/[^0-9.-]+/g, ''));
            return numAmount >= numMin && numAmount <= numMax;
        },
        
        openModal(planData) {
            this.planId = planData.id;
            this.planName = planData.name;
            this.minAmount = planData.minimum;
            this.maxAmount = planData.maximum;
            this.formAction = `{{ route('user.loan.apply', '') }}/${planData.id}`;
            this.amount = '';
            this.loading = false;
            this.open = true;
            
            // Focus on amount input after modal opens
            this.$nextTick(() => {
                const input = document.getElementById('loanAmount');
                if (input) input.focus();
            });
        },
        
        close() {
            this.open = false;
            this.planId = null;
            this.planName = '';
            this.minAmount = '';
            this.maxAmount = '';
            this.amount = '';
            this.formAction = '';
            this.loading = false;
        },
        
        handleSubmit(event) {
            if (!this.isValidAmount) {
                event.preventDefault();
                this.showNotification('error', '@lang('Please enter a valid amount within the allowed limits')');
                return false;
            }
            
            this.loading = true;
        },
        
        showNotification(type, message) {
            // Use existing notification system if available
            if (typeof notify === 'function') {
                notify(type, message);
            } else {
                alert(message);
            }
        }
    }
}

// Global function to open modal (accessible from anywhere)
window.openLoanModal = function(planData) {
    // Dispatch custom event that Alpine.js can listen to
    window.dispatchEvent(new CustomEvent('open-loan-modal', {
        detail: planData
    }));
};

(function($) {
    "use strict";
    
    // Details modal button handler
    $(document).on('click', 'button[data-id]', function(e) {
        if ($(this).hasClass('loanBtn')) {
            // Handle loan application modal
            e.preventDefault();
            
            const data = e.currentTarget.dataset;
            const planData = {
                id: data.id,
                name: data.name,
                minimum: data.minimum,
                maximum: data.maximum
            };
            
            // Use global function to open modal
            window.openLoanModal(planData);
        }
    });
    
    // Details button handler (separate from loan application)
    $(document).on('click', 'button', function(e) {
        if ($(this).text().includes('View Details') && !$(this).hasClass('loanBtn')) {
            e.preventDefault();
            
            const data = e.currentTarget.dataset;
            const planData = {
                id: data.id,
                name: data.name,
                minimum: data.minimum,
                maximum: data.maximum,
                rate: data.rate,
                interval: data.interval,
                total: data.total,
                delayCharge: data.delayCharge,
                delayValue: data.delayValue,
                instruction: data.instruction
            };
            
            // Use global function to open details modal
            window.openDetailsModal(planData);
        }
    });
    
})(jQuery);
</script>
@endpush

@push('style')
<style>
/* Loan Plan Cards */
.loan-plan-card {
    position: relative;
    transition: all 0.3s ease;
}

.loan-plan-card:hover {
    transform: translateY(-8px);
}

/* Alpine.js Modal Enhancements */
[x-cloak] {
    display: none !important;
}

/* Input Focus States */
input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Animation for plan cards */
.loan-plan-card {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Staggered animation for multiple cards */
.loan-plan-card:nth-child(1) { animation-delay: 0.1s; }
.loan-plan-card:nth-child(2) { animation-delay: 0.2s; }
.loan-plan-card:nth-child(3) { animation-delay: 0.3s; }
.loan-plan-card:nth-child(4) { animation-delay: 0.4s; }
.loan-plan-card:nth-child(5) { animation-delay: 0.5s; }
.loan-plan-card:nth-child(6) { animation-delay: 0.6s; }

/* Modal backdrop blur effect */
.modal-backdrop {
    backdrop-filter: blur(4px);
}

/* Smooth transitions for modal elements */
.modal-content {
    will-change: transform, opacity;
}

/* Loading button state */
.loading {
    pointer-events: none;
}

/* Enhanced expand/collapse animations */
.loan-plan-card [x-show] {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth hover effect for detail toggle */
.loan-plan-card button:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

/* Enhanced card height transition */
.loan-plan-card {
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
@endpush