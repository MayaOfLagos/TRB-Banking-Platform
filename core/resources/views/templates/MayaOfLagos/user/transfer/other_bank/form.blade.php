@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')

<!-- Other Bank Transfer Form -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Transfer Form -->
    <div class="lg:col-span-8">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 px-8 py-6 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-transparent via-white/20 to-transparent transform rotate-12"></div>
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/10 rounded-full"></div>
                </div>
                
                <div class="relative">
                    <h2 class="text-2xl font-bold text-white mb-2">@lang('Other Bank Transfer')</h2>
                    <p class="text-blue-100">@lang('Transfer money to accounts in other banks')</p>
                </div>
            </div>
            
            <!-- Form -->
            <div class="p-8">
                <form action="{{ route('user.transfer.other.bank.confirm') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Bank and Account Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bank Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Recipient Bank') <span class="text-red-500">*</span>
                            </label>
                            <select name="bank_name" 
                                    id="bankName"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                                    required>
                                <option value="">@lang('Select Bank')</option>
                                <option value="national_bank">@lang('National Bank')</option>
                                <option value="city_bank">@lang('City Bank')</option>
                                <option value="commercial_bank">@lang('Commercial Bank')</option>
                                <option value="trust_bank">@lang('Trust Bank')</option>
                                <option value="first_bank">@lang('First Bank')</option>
                                <option value="unity_bank">@lang('Unity Bank')</option>
                                <option value="federal_bank">@lang('Federal Bank')</option>
                                <option value="central_bank">@lang('Central Bank')</option>
                            </select>
                        </div>
                        
                        <!-- Account Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Account Number') <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="account_number"
                                       id="accountNumber"
                                       placeholder="@lang('Enter account number')"
                                       value="{{ old('account_number', $beneficiary->account_number ?? '') }}"
                                       class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                                       required>
                                <button type="button" 
                                        onclick="openBeneficiaryModal()"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                    <i class="las la-address-book text-blue-600 dark:text-blue-400"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Routing and Beneficiary Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Routing Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Routing Number') <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="routing_number"
                                   id="routingNumber"
                                   placeholder="@lang('Enter routing number')"
                                   value="{{ old('routing_number', $beneficiary->routing_number ?? '') }}"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                                   required>
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
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
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
                                   class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-lg font-semibold"
                                   required>
                        </div>
                        <div class="flex justify-between mt-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Available Balance'): {{ showUserAmount(auth()->user()->balance, auth()->user()) }}</p>
                            <div class="flex space-x-2">
                                <button type="button" onclick="setAmount(1000)" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">1K</button>
                                <button type="button" onclick="setAmount(5000)" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">5K</button>
                                <button type="button" onclick="setAmount(10000)" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">10K</button>
                                <button type="button" onclick="setMaxAmount()" class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">@lang('Max')</button>
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
                            <select name="transfer_purpose" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white">
                                <option value="">@lang('Select Purpose')</option>
                                <option value="family_support">@lang('Family Support')</option>
                                <option value="business">@lang('Business Payment')</option>
                                <option value="personal">@lang('Personal Transfer')</option>
                                <option value="gift">@lang('Gift')</option>
                                <option value="education">@lang('Education')</option>
                                <option value="medical">@lang('Medical Expenses')</option>
                                <option value="property">@lang('Property Purchase')</option>
                                <option value="investment">@lang('Investment')</option>
                                <option value="other">@lang('Other')</option>
                            </select>
                        </div>
                        
                        <!-- Account Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Recipient Account Type')
                            </label>
                            <select name="account_type" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white">
                                <option value="savings">@lang('Savings Account')</option>
                                <option value="current">@lang('Current Account')</option>
                                <option value="business">@lang('Business Account')</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Transfer Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            @lang('Transfer Method')
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative">
                                <input type="radio" 
                                       name="transfer_method" 
                                       value="rtgs" 
                                       id="rtgs"
                                       checked
                                       class="sr-only peer">
                                <label for="rtgs" class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:border-blue-500 transition-all duration-200">
                                    <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 dark:border-gray-500 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 mr-3">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">@lang('RTGS Transfer')</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Same day processing') • {{ getUserCurrency(auth()->user())['symbol'] }}25 @lang('fee')</p>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="relative">
                                <input type="radio" 
                                       name="transfer_method" 
                                       value="neft" 
                                       id="neft"
                                       class="sr-only peer">
                                <label for="neft" class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:border-blue-500 transition-all duration-200">
                                    <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 dark:border-gray-500 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 mr-3">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">@lang('NEFT Transfer')</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Next business day') • {{ getUserCurrency(auth()->user())['symbol'] }}15 @lang('fee')</p>
                                    </div>
                                </label>
                            </div>
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
                                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white resize-none">{{ old('description') }}</textarea>
                    </div>
                    
                    <!-- Save Beneficiary Option -->
                    @if(!isset($beneficiary))
                    <div class="flex items-center space-x-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <input type="checkbox" 
                               name="save_beneficiary" 
                               id="saveBeneficiary"
                               class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="saveBeneficiary" class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                            @lang('Save this recipient as a beneficiary for future transfers')
                        </label>
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <a href="{{ route('user.transfer.other.bank.beneficiaries') }}" 
                           class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 text-center font-medium">
                            @lang('Back to Beneficiaries')
                        </a>
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <i class="las la-paper-plane"></i>
                            <span>@lang('Review Transfer')</span>
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
                    <i class="las la-info-circle text-blue-500 mr-2"></i>
                    @lang('Transfer Fees & Limits')
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('RTGS Fee'):</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ getUserCurrency(auth()->user())['symbol'] }}25</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('NEFT Fee'):</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ getUserCurrency(auth()->user())['symbol'] }}15</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Daily Limit'):</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ getUserCurrency(auth()->user())['symbol'] }}50,000</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Monthly Limit'):</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ getUserCurrency(auth()->user())['symbol'] }}500,000</span>
                    </div>
                </div>
            </div>
            
            <!-- Supported Banks -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="las la-university text-blue-500 mr-2"></i>
                    @lang('Supported Banks')
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-university text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">National Bank</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-university text-green-600 dark:text-green-400 text-sm"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">City Bank</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-university text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Trust Bank</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                            <i class="las la-university text-orange-600 dark:text-orange-400 text-sm"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">+20 More</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">@lang('All major banks are supported through our secure interbank network')</p>
            </div>
            
            <!-- Security Features -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-2xl p-6 border border-green-200 dark:border-green-700">
                <h3 class="text-lg font-semibold text-green-900 dark:text-green-200 mb-4 flex items-center">
                    <i class="las la-shield-alt text-green-500 mr-2"></i>
                    @lang('Security Features')
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <i class="las la-check-circle text-green-500"></i>
                        <span class="text-green-800 dark:text-green-200 text-sm">@lang('End-to-End Encryption')</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="las la-check-circle text-green-500"></i>
                        <span class="text-green-800 dark:text-green-200 text-sm">@lang('Multi-Factor Authentication')</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="las la-check-circle text-green-500"></i>
                        <span class="text-green-800 dark:text-green-200 text-sm">@lang('Fraud Detection')</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="las la-check-circle text-green-500"></i>
                        <span class="text-green-800 dark:text-green-200 text-sm">@lang('Regulatory Compliance')</span>
                    </div>
                </div>
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
    // Implementation for beneficiary selection modal
    alert('@lang("Beneficiary selection modal would open here")');
}
</script>
@endpush