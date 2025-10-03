@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')

<!-- Own Bank Beneficiaries Page -->
<div class="space-y-4 md:space-y-6">
    
    <!-- Mobile Header -->
    <div class="lg:hidden bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold mb-1">@lang('Beneficiaries')</h1>
                <p class="text-green-100 text-sm">{{ $beneficiaries->total() ?? 0 }} @lang('saved contacts')</p>
            </div>
            <button onclick="openAddBeneficiaryModal()" 
                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                <i class="las la-plus mr-1"></i>
                @lang('Add')
            </button>
        </div>
    </div>
    
    <!-- Desktop Header & Search -->
    <div class="hidden lg:block">
        <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Desktop Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 md:p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
                <div class="mb-4 sm:mb-0">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">@lang('Own Bank Beneficiaries')</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm md:text-base mt-1">@lang('Manage your saved beneficiaries for quick transfers')</p>
                </div>
                <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 md:px-6 py-2 md:py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 text-sm md:text-base"
                        onclick="openAddBeneficiaryModal()">
                    <i class="las la-plus text-lg"></i>
                    <span>@lang('Add Beneficiary')</span>
                </button>
            </div>
            
            <!-- Search and Filter -->
            <div class="p-4 md:p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
                    <!-- Search -->
                    <div class="relative">
                        <i class="las la-search absolute left-3 md:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 text-lg"></i>
                        <input type="text" 
                               placeholder="@lang('Search beneficiaries...')"
                               class="w-full pl-10 md:pl-12 pr-3 md:pr-4 py-2.5 md:py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm md:text-base">
                    </div>
                    
                    <!-- Account Type Filter -->
                    <select class="w-full px-3 md:px-4 py-2.5 md:py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-sm md:text-base">
                        <option value="">@lang('All Account Types')</option>
                        <option value="savings">@lang('Savings Account')</option>
                        <option value="current">@lang('Current Account')</option>
                        <option value="business">@lang('Business Account')</option>
                    </select>
                    
                    <!-- Sort -->
                    <select class="w-full px-3 md:px-4 py-2.5 md:py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-sm md:text-base">
                        <option value="name">@lang('Sort by Name')</option>
                        <option value="recent">@lang('Recently Added')</option>
                        <option value="frequent">@lang('Most Used')</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Search -->
    <div class="lg:hidden">
        <div class="relative">
            <i class="las la-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
            <input type="text" 
                   placeholder="@lang('Search beneficiaries...')"
                   class="w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
        </div>
    </div>
    
    <!-- Beneficiaries Content -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-3 md:p-6 lg:p-8">
            @if($beneficiaries->count() > 0)
                <!-- Mobile Cards View -->
                <div class="lg:hidden space-y-3">
                    @foreach($beneficiaries as $beneficiary)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-600 transition-all duration-200">
                        <!-- Mobile Card Header -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-sm">{{ substr($beneficiary->short_name, 0, 1) }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $beneficiary->short_name }}</h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $beneficiary->account_number }}</p>
                                </div>
                            </div>
                            
                            <!-- Mobile Actions -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <i class="las la-ellipsis-v text-gray-500 dark:text-gray-400"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-10 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-20">
                                    <button onclick="editBeneficiary({{ $beneficiary->id }}, '{{ $beneficiary->short_name }}', '{{ $beneficiary->account_number }}', '{{ $beneficiary->account_name }}')" class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left">
                                        <i class="las la-edit mr-2"></i>
                                        @lang('Edit')
                                    </button>
                                    <button onclick="deleteBeneficiary({{ $beneficiary->id }}, '{{ $beneficiary->short_name }}')" class="w-full flex items-center px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left">
                                        <i class="las la-trash mr-2"></i>
                                        @lang('Delete')
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile Card Details -->
                        <div class="space-y-2 mb-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600 dark:text-gray-400">@lang('Account Name'):</span>
                                <span class="text-gray-900 dark:text-white font-medium text-right truncate ml-2">{{ $beneficiary->account_name }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600 dark:text-gray-400">@lang('Bank'):</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ gs()->site_name }}</span>
                            </div>
                        </div>
                        
                        <!-- Mobile Transfer Button -->
                        <button onclick="openTransferModal({{ $beneficiary->id }}, '{{ $beneficiary->short_name }}', '{{ $beneficiary->account_number }}')" 
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-2.5 rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                            <i class="las la-paper-plane text-base"></i>
                            <span>@lang('Transfer')</span>
                        </button>
                    </div>
                    @endforeach
                </div>
                
                <!-- Desktop Grid View -->
                <div class="hidden lg:grid lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($beneficiaries as $beneficiary)
                    <div class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 hover:border-green-300 dark:hover:border-green-600 hover:shadow-lg transition-all duration-200">
                        <!-- Beneficiary Info -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ substr($beneficiary->short_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $beneficiary->short_name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $beneficiary->account_number }}</p>
                                </div>
                            </div>
                            
                            <!-- Actions Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="las la-ellipsis-v text-gray-500 dark:text-gray-400"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-10 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-10">
                                    <button onclick="editBeneficiary({{ $beneficiary->id }}, '{{ $beneficiary->short_name }}', '{{ $beneficiary->account_number }}', '{{ $beneficiary->account_name }}')" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left">
                                        <i class="las la-edit mr-2"></i>
                                        @lang('Edit')
                                    </button>
                                    <button onclick="deleteBeneficiary({{ $beneficiary->id }}, '{{ $beneficiary->short_name }}')" class="w-full flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left">
                                        <i class="las la-trash mr-2"></i>
                                        @lang('Delete')
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">@lang('Account Name'):</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $beneficiary->account_name }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">@lang('Bank'):</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ gs()->site_name }}</span>
                            </div>
                        </div>
                        
                        <!-- Transfer Button -->
                        <button onclick="openTransferModal({{ $beneficiary->id }}, '{{ $beneficiary->short_name }}', '{{ $beneficiary->account_number }}')" 
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-3 rounded-xl font-medium text-center transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                            <i class="las la-paper-plane"></i>
                            <span>@lang('Transfer Money')</span>
                        </button>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12 md:py-16">
                    <div class="w-16 h-16 md:w-24 md:h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <i class="las la-users text-gray-400 dark:text-gray-500 text-2xl md:text-4xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('No Beneficiaries Found')</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 md:mb-8 max-w-md mx-auto px-4 text-sm md:text-base">@lang('Add your first beneficiary to start making quick transfers to other accounts within our bank.')</p>
                    <button onclick="openAddBeneficiaryModal()" 
                            class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 md:px-8 py-2.5 md:py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl text-sm md:text-base">
                        @lang('Add Your First Beneficiary')
                    </button>
                </div>
            @endif
        </div>
        
        <!-- Pagination -->
        @if($beneficiaries->hasPages())
        <div class="px-3 md:px-6 lg:px-8 py-3 md:py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            {{ paginateLinks($beneficiaries) }}
        </div>
        @endif
    </div>
</div>

<!-- Transfer Money Modal -->
<div id="transferModal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeTransferModal()"></div>
    
    <!-- Mobile: Full screen bottom sheet -->
    <div class="sm:hidden fixed inset-x-0 bottom-0 top-0 flex flex-col bg-white dark:bg-gray-900">
        <!-- Mobile Header with close button -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 sticky top-0 z-10">
            <div class="flex items-center space-x-3">
                <button onclick="closeTransferModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <i class="las la-arrow-left text-gray-600 dark:text-gray-400 text-xl"></i>
                </button>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">@lang('Transfer Money')</h3>
            </div>
        </div>
        
        <!-- Mobile Body - Scrollable -->
        <div class="flex-1 overflow-y-auto p-4 pb-safe">
            <!-- Beneficiary Info -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-base" id="mobileBeneficiaryInitial">J</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-900 dark:text-white text-base truncate" id="mobileBeneficiaryName">John Doe</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate" id="mobileBeneficiaryAccount">1234567890</p>
                    </div>
                </div>
            </div>
            
            <form id="mobileTransferForm" action="" method="post">
                @csrf
                
                <!-- Amount -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">@lang('Amount')</label>
                    <div class="relative">
                        <input type="number" 
                               name="amount"
                               step="0.01"
                               placeholder="0.00"
                               class="w-full px-4 py-4 pr-20 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-lg font-medium"
                               required>
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">{{ gs()->cur_text }}</span>
                    </div>
                </div>

                @include($activeTemplate . 'partials.otp_field')

                <!-- Transfer Limits -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 mb-6">
                    <h4 class="font-medium text-blue-900 dark:text-blue-300 mb-3">@lang('Transfer Limits')</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-400">@lang('Minimum')</span>
                            <span class="font-medium text-blue-900 dark:text-blue-300">{{ showAmount(gs()->minimum_transfer_limit) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-400">@lang('Daily Limit')</span>
                            <span class="font-medium text-blue-900 dark:text-blue-300">{{ showAmount(gs()->daily_transfer_limit) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-400">@lang('Monthly Limit')</span>
                            <span class="font-medium text-blue-900 dark:text-blue-300">{{ showAmount(gs()->monthly_transfer_limit) }}</span>
                        </div>
                        @php $transferCharge = gs()->transferCharge(); @endphp
                        @if ($transferCharge)
                        <div class="flex justify-between pt-2 border-t border-blue-200 dark:border-blue-800">
                            <span class="text-blue-700 dark:text-blue-400">@lang('Charge')</span>
                            <span class="font-medium text-red-600 dark:text-red-400">{{ $transferCharge }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Mobile Fixed Actions -->
        <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 pb-safe">
            <div class="flex space-x-3">
                <button type="button" 
                        onclick="closeTransferModal()"
                        class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-medium">
                    @lang('Cancel')
                </button>
                <button type="submit" 
                        form="mobileTransferForm"
                        id="mobileTransferSubmitBtn"
                        class="flex-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="las la-paper-plane" id="mobileTransferIcon"></i>
                    <span id="mobileTransferText">@lang('Transfer Money')</span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Desktop: Centered modal -->
    <div class="hidden sm:flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 max-w-md w-full rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700">
            <!-- Desktop Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Transfer Money')</h3>
                <button onclick="closeTransferModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="las la-times text-gray-500 dark:text-gray-400 text-xl"></i>
                </button>
            </div>
            
            <!-- Desktop Body -->
            <div class="p-6">
                <!-- Beneficiary Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-base" id="desktopBeneficiaryInitial">J</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 dark:text-white text-base truncate" id="desktopBeneficiaryName">John Doe</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate" id="desktopBeneficiaryAccount">1234567890</p>
                        </div>
                    </div>
                </div>
                
                <form id="desktopTransferForm" action="" method="post">
                    @csrf
                    
                    <!-- Amount -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Amount')</label>
                        <div class="relative">
                            <input type="number" 
                                   name="amount"
                                   step="0.01"
                                   placeholder="0.00"
                                   class="w-full px-4 py-3 pr-20 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-base"
                                   required>
                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">{{ gs()->cur_text }}</span>
                        </div>
                    </div>

                    @include($activeTemplate . 'partials.otp_field')

                    <!-- Transfer Limits -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 mb-6">
                        <h4 class="font-medium text-blue-900 dark:text-blue-300 mb-3">@lang('Transfer Limits')</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-400">@lang('Minimum')</span>
                                <span class="font-medium text-blue-900 dark:text-blue-300">{{ showAmount(gs()->minimum_transfer_limit) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-400">@lang('Daily Limit')</span>
                                <span class="font-medium text-blue-900 dark:text-blue-300">{{ showAmount(gs()->daily_transfer_limit) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-400">@lang('Monthly Limit')</span>
                                <span class="font-medium text-blue-900 dark:text-blue-300">{{ showAmount(gs()->monthly_transfer_limit) }}</span>
                            </div>
                            @php $transferCharge = gs()->transferCharge(); @endphp
                            @if ($transferCharge)
                            <div class="flex justify-between pt-2 border-t border-blue-200 dark:border-blue-800">
                                <span class="text-blue-700 dark:text-blue-400">@lang('Charge')</span>
                                <span class="font-medium text-red-600 dark:text-red-400">{{ $transferCharge }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Desktop Actions -->
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <button type="button" 
                                onclick="closeTransferModal()"
                                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                            @lang('Cancel')
                        </button>
                        <button type="submit" 
                                id="desktopTransferSubmitBtn"
                                class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200 flex items-center justify-center space-x-2">
                            <i class="las la-paper-plane" id="desktopTransferIcon"></i>
                            <span id="desktopTransferText">@lang('Transfer Money')</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Beneficiary Modal -->
<div id="editBeneficiaryModal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto border border-gray-200 dark:border-gray-700">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Edit Beneficiary')</h3>
                <button onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="las la-times text-gray-500 dark:text-gray-400"></i>
                </button>
            </div>
            
            <!-- Form -->
            <form id="editBeneficiaryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Short Name')</label>
                        <input type="text" name="short_name" id="editShortName" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Account Number')</label>
                        <input type="text" name="account_number" id="editAccountNumber" required readonly
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Account Name')</label>
                        <input type="text" name="account_name" id="editAccountName" required readonly
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 p-6 pt-0">
                    <button type="button" onclick="closeEditModal()"
                            class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        @lang('Cancel')
                    </button>
                    <button type="submit" id="editSubmitBtn"
                            class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="las la-save" id="editIcon"></i>
                        <span id="editText">@lang('Update')</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteBeneficiaryModal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto border border-gray-200 dark:border-gray-700">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">@lang('Delete Beneficiary')</h3>
                <button onclick="closeDeleteModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="las la-times text-gray-500 dark:text-gray-400"></i>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                        <i class="las la-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">@lang('Are you sure?')</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">@lang('This action cannot be undone.')</p>
                    </div>
                </div>
                
                <p class="text-gray-700 dark:text-gray-300 mb-6">
                    @lang('You are about to delete') "<span id="deleteBeneficiaryName" class="font-semibold"></span>". @lang('This beneficiary will be permanently removed from your list.')
                </p>
                
                <!-- Actions -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        @lang('Cancel')
                    </button>
                    <button type="button" onclick="confirmDelete()" id="deleteSubmitBtn"
                            class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="las la-trash" id="deleteIcon"></i>
                        <span id="deleteText">@lang('Delete')</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
function openTransferModal(beneficiaryId, beneficiaryName, accountNumber) {
    // Update mobile form
    document.getElementById('mobileBeneficiaryName').textContent = beneficiaryName;
    document.getElementById('mobileBeneficiaryAccount').textContent = accountNumber;
    document.getElementById('mobileBeneficiaryInitial').textContent = beneficiaryName.charAt(0).toUpperCase();
    
    // Update desktop form
    document.getElementById('desktopBeneficiaryName').textContent = beneficiaryName;
    document.getElementById('desktopBeneficiaryAccount').textContent = accountNumber;
    document.getElementById('desktopBeneficiaryInitial').textContent = beneficiaryName.charAt(0).toUpperCase();
    
    // Set form actions
    const route = `{{ route('user.transfer.own.bank.request', ':id') }}`;
    const actionUrl = route.replace(':id', beneficiaryId);
    
    document.getElementById('mobileTransferForm').action = actionUrl;
    document.getElementById('desktopTransferForm').action = actionUrl;
    
    document.getElementById('transferModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeTransferModal() {
    document.getElementById('transferModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    
    // Reset both forms
    document.getElementById('mobileTransferForm').reset();
    document.getElementById('desktopTransferForm').reset();
    setMobileTransferLoading(false);
    setDesktopTransferLoading(false);
}

function openAddBeneficiaryModal() {
    window.location.href = "{{ route('user.beneficiary.own') }}";
}

function setMobileTransferLoading(loading) {
    const submitBtn = document.getElementById('mobileTransferSubmitBtn');
    const submitIcon = document.getElementById('mobileTransferIcon');
    const submitText = document.getElementById('mobileTransferText');
    
    if (loading) {
        submitBtn.disabled = true;
        submitIcon.className = 'las la-spinner la-spin';
        submitText.textContent = '@lang("Processing...")';
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = false;
        submitIcon.className = 'las la-paper-plane';
        submitText.textContent = '@lang("Transfer Money")';
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
}

function setDesktopTransferLoading(loading) {
    const submitBtn = document.getElementById('desktopTransferSubmitBtn');
    const submitIcon = document.getElementById('desktopTransferIcon');
    const submitText = document.getElementById('desktopTransferText');
    
    if (loading) {
        submitBtn.disabled = true;
        submitIcon.className = 'las la-spinner la-spin';
        submitText.textContent = '@lang("Processing...")';
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = false;
        submitIcon.className = 'las la-paper-plane';
        submitText.textContent = '@lang("Transfer Money")';
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
}

// Legacy function for backward compatibility
function setModalTransferLoading(loading) {
    setMobileTransferLoading(loading);
    setDesktopTransferLoading(loading);
}

// Edit Beneficiary Functions
function editBeneficiary(id, shortName, accountNumber, accountName) {
    document.getElementById('editShortName').value = shortName;
    document.getElementById('editAccountNumber').value = accountNumber;
    document.getElementById('editAccountName').value = accountName;
    
    const form = document.getElementById('editBeneficiaryForm');
    const route = `{{ route('user.beneficiary.own.update', ':id') }}`;
    form.action = route.replace(':id', id);
    
    document.getElementById('editBeneficiaryModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeEditModal() {
    document.getElementById('editBeneficiaryModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    
    document.getElementById('editBeneficiaryForm').reset();
    setEditLoading(false);
}

function setEditLoading(loading) {
    const submitBtn = document.getElementById('editSubmitBtn');
    const submitIcon = document.getElementById('editIcon');
    const submitText = document.getElementById('editText');
    
    if (loading) {
        submitBtn.disabled = true;
        submitIcon.className = 'las la-spinner la-spin';
        submitText.textContent = '@lang("Updating...")';
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = false;
        submitIcon.className = 'las la-save';
        submitText.textContent = '@lang("Update")';
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
}

// Delete Beneficiary Functions
let deleteBeneficiaryId = null;

function deleteBeneficiary(id, shortName) {
    deleteBeneficiaryId = id;
    document.getElementById('deleteBeneficiaryName').textContent = shortName;
    
    document.getElementById('deleteBeneficiaryModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteBeneficiaryModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    
    deleteBeneficiaryId = null;
    setDeleteLoading(false);
}

function confirmDelete() {
    if (!deleteBeneficiaryId) return;
    
    setDeleteLoading(true);
    
    // Create and submit delete form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ route('user.beneficiary.delete', ':id') }}`.replace(':id', deleteBeneficiaryId);
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    
    form.submit();
}

function setDeleteLoading(loading) {
    const submitBtn = document.getElementById('deleteSubmitBtn');
    const submitIcon = document.getElementById('deleteIcon');
    const submitText = document.getElementById('deleteText');
    
    if (loading) {
        submitBtn.disabled = true;
        submitIcon.className = 'las la-spinner la-spin';
        submitText.textContent = '@lang("Deleting...")';
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = false;
        submitIcon.className = 'las la-trash';
        submitText.textContent = '@lang("Delete Beneficiary")';
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
}

// Add form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // Mobile form submission handler
    const mobileForm = document.getElementById('mobileTransferForm');
    if (mobileForm) {
        mobileForm.addEventListener('submit', function(e) {
            setMobileTransferLoading(true);
        });
    }
    
    // Desktop form submission handler
    const desktopForm = document.getElementById('desktopTransferForm');
    if (desktopForm) {
        desktopForm.addEventListener('submit', function(e) {
            setDesktopTransferLoading(true);
        });
    }
    
    // Edit form submission handler
    const editForm = document.getElementById('editBeneficiaryForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            setEditLoading(true);
        });
    }
});
</script>
@endpush