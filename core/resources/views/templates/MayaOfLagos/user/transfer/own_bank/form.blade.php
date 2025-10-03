@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')

<!-- Own Bank Transfer Form -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Transfer Form -->
    <div class="lg:col-span-8">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 px-8 py-6 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-transparent via-white/20 to-transparent transform rotate-12"></div>
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/10 rounded-full"></div>
                </div>
                
                <div class="relative">
                    <h2 class="text-2xl font-bold text-white mb-2">@lang('Own Bank Transfer')</h2>
                    <p class="text-green-100">@lang('Transfer money to other accounts within our bank')</p>
                </div>
            </div>
            
            <!-- Form -->
            <div class="p-8">
                <form action="{{ route('user.transfer.own.bank.confirm') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Beneficiary Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Account Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Recipient Account Number') <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="account_number"
                                       id="accountNumber"
                                       placeholder="@lang('Enter account number')"
                                       value="{{ old('account_number', $beneficiary->account_number ?? '') }}"
                                       class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                                       required>
                                <button type="button" 
                                        onclick="openBeneficiaryModal()"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                                    <i class="las la-address-book text-green-600 dark:text-green-400"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">@lang('Click the address book icon to select from saved beneficiaries')</p>
                        </div>
                        
                        <!-- Beneficiary Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Recipient Name') <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="beneficiary_name"
                                   id="beneficiaryName"
                                   placeholder="@lang('Enter recipient name')"
                                   value="{{ old('beneficiary_name', $beneficiary->beneficiary_name ?? '') }}"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                                   required>
                        </div>
                    </div>
                    
                    <!-- Transfer Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            @lang('Transfer Amount') <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">
                                {{ getUserCurrency(auth()->user())['symbol'] }}
                            </div>
                            <input type="number" 
                                   name="amount"
                                   id="transferAmount"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="1"
                                   max="{{ auth()->user()->balance }}"
                                   value="{{ old('amount') }}"
                                   class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-lg font-semibold"
                                   required>
                        </div>
                        <div class="flex justify-between mt-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Available Balance'): {{ showUserAmount(auth()->user()->balance, auth()->user()) }}</p>
                            <div class="flex space-x-2">
                                <button type="button" onclick="setAmount(1000)" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">1K</button>
                                <button type="button" onclick="setAmount(5000)" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">5K</button>
                                <button type="button" onclick="setAmount(10000)" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">10K</button>
                                <button type="button" onclick="setMaxAmount()" class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">@lang('Max')</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transfer Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Transfer Purpose -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Transfer Purpose')
                            </label>
                            <select name="transfer_purpose" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white">
                                <option value="">@lang('Select Purpose')</option>
                                <option value="family_support">@lang('Family Support')</option>
                                <option value="business">@lang('Business Payment')</option>
                                <option value="personal">@lang('Personal Transfer')</option>
                                <option value="gift">@lang('Gift')</option>
                                <option value="education">@lang('Education')</option>
                                <option value="medical">@lang('Medical Expenses')</option>
                                <option value="other">@lang('Other')</option>
                            </select>
                        </div>
                        
                        <!-- Transfer Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Transfer Type')
                            </label>
                            <select name="transfer_type" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white">
                                <option value="instant">@lang('Instant Transfer') - @lang('Free')</option>
                                <option value="scheduled">@lang('Scheduled Transfer') - @lang('Free')</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Transfer Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            @lang('Transfer Description')
                        </label>
                        <textarea name="description" 
                                  rows="3"
                                  placeholder="@lang('Enter a description for this transfer (optional)')"
                                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white resize-none">{{ old('description') }}</textarea>
                    </div>
                    
                    <!-- Save Beneficiary Option -->
                    @if(!isset($beneficiary))
                    <div class="flex items-center space-x-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
                        <input type="checkbox" 
                               name="save_beneficiary" 
                               id="saveBeneficiary"
                               class="w-4 h-4 text-green-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500 focus:ring-2">
                        <label for="saveBeneficiary" class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                            @lang('Save this recipient as a beneficiary for future transfers')
                        </label>
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <a href="{{ route('user.transfer.own.bank.beneficiaries') }}" 
                           class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 text-center font-medium">
                            @lang('Back to Beneficiaries')
                        </a>
                        <button type="submit" 
                                id="transferSubmitBtn"
                                class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <i class="las la-paper-plane" id="transferSubmitIcon"></i>
                            <span id="transferSubmitText">@lang('Review Transfer')</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Transfer Information -->
    <div class="lg:col-span-4">
        <div class="space-y-6">
            <!-- Transfer Fees -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="las la-info-circle text-green-500 mr-2"></i>
                    @lang('Transfer Information')
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Transfer Fee'):</span>
                        <span class="text-green-600 dark:text-green-400 font-semibold">@lang('FREE')</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Processing Time'):</span>
                        <span class="text-gray-900 dark:text-white font-medium">@lang('Instant')</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Daily Limit'):</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ getUserCurrency(auth()->user())['symbol'] }}100,000</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Available 24/7'):</span>
                        <i class="las la-check-circle text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Security Features -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-700">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-4 flex items-center">
                    <i class="las la-shield-alt text-blue-500 mr-2"></i>
                    @lang('Security Features')
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <i class="las la-check-circle text-blue-500"></i>
                        <span class="text-blue-800 dark:text-blue-200 text-sm">@lang('256-bit SSL Encryption')</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="las la-check-circle text-blue-500"></i>
                        <span class="text-blue-800 dark:text-blue-200 text-sm">@lang('Two-Factor Authentication')</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="las la-check-circle text-blue-500"></i>
                        <span class="text-blue-800 dark:text-blue-200 text-sm">@lang('Real-time Fraud Detection')</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="las la-check-circle text-blue-500"></i>
                        <span class="text-blue-800 dark:text-blue-200 text-sm">@lang('Transaction Monitoring')</span>
                    </div>
                </div>
            </div>
            
            <!-- Recent Transfers -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="las la-history text-gray-500 mr-2"></i>
                    @lang('Recent Transfers')
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                <i class="las la-arrow-up text-green-600 dark:text-green-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">John Doe</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ getUserCurrency(auth()->user())['symbol'] }}500</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                <i class="las la-arrow-up text-green-600 dark:text-green-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Jane Smith</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">1 day ago</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ getUserCurrency(auth()->user())['symbol'] }}1,200</span>
                    </div>
                </div>
                
                <a href="{{ route('user.transfer.history') }}" class="block text-center text-green-600 dark:text-green-400 text-sm font-medium mt-4 hover:text-green-700 dark:hover:text-green-300 transition-colors">
                    @lang('View All Transfers')
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
function setAmount(amount) {
    document.getElementById('transferAmount').value = amount;
}

function setMaxAmount() {
    const maxAmount = {{ auth()->user()->balance }};
    document.getElementById('transferAmount').value = maxAmount;
}

function openBeneficiaryModal() {
    alert('@lang("Beneficiary selection modal would open here")');
}

function setTransferLoading(loading) {
    const submitBtn = document.getElementById('transferSubmitBtn');
    const submitIcon = document.getElementById('transferSubmitIcon');
    const submitText = document.getElementById('transferSubmitText');
    
    if (loading) {
        submitBtn.disabled = true;
        submitIcon.className = 'las la-spinner la-spin';
        submitText.textContent = '@lang("Processing...")';
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = false;
        submitIcon.className = 'las la-paper-plane';
        submitText.textContent = '@lang("Review Transfer")';
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
}

// Add form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const transferForm = document.querySelector('form[action*="confirm"]');
    if (transferForm) {
        transferForm.addEventListener('submit', function(e) {
            setTransferLoading(true);
        });
    }
});
</script>
@endpush