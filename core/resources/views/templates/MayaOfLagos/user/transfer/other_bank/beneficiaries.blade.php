@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')
<div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-6">
    <!-- Mobile Header -->
    <div class="lg:hidden mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Other Bank Transfers')</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Transfer to external banks')</p>
            </div>
            @if (gs()->modules->other_bank)
            <a href="{{ route('user.beneficiary.other') }}" 
               class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <i class="las la-users text-blue-600 dark:text-blue-400"></i>
            </a>
            @endif
        </div>
        
        <!-- Mobile Stats -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-3 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="las la-users text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $beneficiaries->count() }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">@lang('Beneficiaries')</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-3 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="las la-wallet text-green-600 dark:text-green-400 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ showAmount(auth()->user()->balance) }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">@lang('Balance')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Header -->
    <div class="hidden lg:flex lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Other Bank Transfers')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Send money to accounts in other banks')</p>
        </div>
        @if (gs()->modules->other_bank)
        <a href="{{ route('user.beneficiary.other') }}" 
           class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2">
            <i class="las la-users text-lg"></i>
            <span>@lang('Manage Beneficiaries')</span>
        </a>
        @endif
    </div>

    <!-- Desktop Stats -->
    <div class="hidden lg:grid lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-users text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $beneficiaries->count() }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Beneficiaries')</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-university text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $otherBanks->count() ?? 0 }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Partner Banks')</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-exchange-alt text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $transfers_count ?? 0 }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('This Month')</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-wallet text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount(auth()->user()->balance) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Available')</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Search -->
    <div class="lg:hidden mb-4">
        <div class="relative">
            <i class="las la-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" 
                   placeholder="@lang('Search beneficiaries...')"
                   class="w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
        </div>
    </div>

    <!-- Beneficiaries Content -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if (gs()->modules->other_bank)
        <div class="hidden lg:flex lg:items-center lg:justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h6 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Beneficiaries')</h6>
            <a href="{{ route('user.beneficiary.other') }}" 
               class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex items-center space-x-1 transition-colors">
                <i class="las la-users"></i>
                <span>@lang('Manage Beneficiaries')</span>
            </a>
        </div>
        @endif

        <div class="p-3 md:p-6 lg:p-8">
            @if($beneficiaries->count() > 0)
                <!-- Desktop Search -->
                <div class="hidden lg:block mb-6">
                    <div class="relative max-w-md">
                        <i class="las la-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               placeholder="@lang('Search beneficiaries...')"
                               class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Name')</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Account Name')</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Account Number')</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Bank')</th>
                                <th class="px-6 py-4 text-right text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($beneficiaries as $beneficiary)
                            @php
                                $bank = $beneficiary->beneficiaryOf;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $beneficiary->short_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $beneficiary->account_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-mono">{{ $beneficiary->account_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $bank->name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="showDetails({{ $beneficiary->id }})" 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors"
                                                title="@lang('Details')">
                                            <i class="las la-eye text-sm"></i>
                                        </button>
                                        <button onclick="openTransferModal({{ $beneficiary->id }}, '{{ $beneficiary->short_name }}', '{{ $beneficiary->account_number }}', '{{ $bank->name }}', {{ json_encode([
                                            'minimum_amount' => showAmount($bank->minimum_limit),
                                            'maximum_amount' => showAmount($bank->maximum_limit), 
                                            'daily_limit' => showAmount($bank->daily_maximum_limit),
                                            'monthly_limit' => showAmount($bank->monthly_maximum_limit),
                                            'daily_count' => $bank->daily_total_transaction,
                                            'monthly_count' => $bank->monthly_total_transaction,
                                            'processing_time' => $bank->processing_time,
                                            'transfer_charge' => $bank->charge_text ?? null
                                        ]) }})" 
                                                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 flex items-center space-x-1">
                                            <i class="las la-paper-plane text-sm"></i>
                                            <span>@lang('Transfer')</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden space-y-3">
                    @foreach ($beneficiaries as $beneficiary)
                    @php
                        $bank = $beneficiary->beneficiaryOf;
                    @endphp
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200">
                        <!-- Mobile Card Header -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-sm">{{ substr($beneficiary->short_name, 0, 1) }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $beneficiary->short_name }}</h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $bank->name }}</p>
                                </div>
                            </div>
                            
                            <button onclick="showDetails({{ $beneficiary->id }})" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                <i class="las la-eye text-sm"></i>
                            </button>
                        </div>
                        
                        <!-- Mobile Card Details -->
                        <div class="space-y-2 mb-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600 dark:text-gray-400">@lang('Account Name'):</span>
                                <span class="text-gray-900 dark:text-white font-medium text-right truncate ml-2">{{ $beneficiary->account_name }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600 dark:text-gray-400">@lang('Account Number'):</span>
                                <span class="text-gray-900 dark:text-white font-mono">{{ $beneficiary->account_number }}</span>
                            </div>
                        </div>
                        
                        <!-- Mobile Transfer Button -->
                        <button onclick="openTransferModal({{ $beneficiary->id }}, '{{ $beneficiary->short_name }}', '{{ $beneficiary->account_number }}', '{{ $bank->name }}', {{ json_encode([
                            'minimum_amount' => showAmount($bank->minimum_limit),
                            'maximum_amount' => showAmount($bank->maximum_limit), 
                            'daily_limit' => showAmount($bank->daily_maximum_limit),
                            'monthly_limit' => showAmount($bank->monthly_maximum_limit),
                            'daily_count' => $bank->daily_total_transaction,
                            'monthly_count' => $bank->monthly_total_transaction,
                            'processing_time' => $bank->processing_time,
                            'transfer_charge' => $bank->charge_text ?? null
                        ]) }})" 
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-2.5 rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                            <i class="las la-paper-plane text-base"></i>
                            <span>@lang('Transfer')</span>
                        </button>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12 md:py-16">
                    <div class="w-16 h-16 md:w-24 md:h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <i class="las la-university text-gray-400 dark:text-gray-500 text-2xl md:text-4xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('No Beneficiaries Found')</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 md:mb-8 max-w-md mx-auto px-4 text-sm md:text-base">@lang($emptyMessage ?? 'Add your first beneficiary to start making transfers to other banks.')</p>
                    @if (gs()->modules->other_bank)
                    <a href="{{ route('user.beneficiary.other') }}" 
                       class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 md:px-8 py-2.5 md:py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl text-sm md:text-base">
                        @lang('Add Your First Beneficiary')
                    </a>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Pagination -->
        @if ($beneficiaries->hasPages())
        <div class="px-3 md:px-6 lg:px-8 py-3 md:py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            {{ paginateLinks($beneficiaries) }}
        </div>
        @endif
    </div>
</div>

<!-- Transfer Modal -->
<div id="transferModal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeTransferModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <!-- Centered modal for all screen sizes -->
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-3xl shadow-2xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">@lang('Transfer Money')</h3>
                <button onclick="closeTransferModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="las la-times text-gray-500 dark:text-gray-400 text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-4 md:p-6 max-h-[80vh] overflow-y-auto">
                <!-- Beneficiary Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 md:p-4 mb-4 md:mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm md:text-base" id="beneficiaryInitial">B</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 dark:text-white text-sm md:text-base truncate" id="beneficiaryName">Beneficiary Name</p>
                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 truncate" id="beneficiaryAccount">1234567890</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 truncate" id="beneficiaryBank">Bank Name</p>
                        </div>
                    </div>
                </div>
                
                <form id="transferForm" action="" method="post">
                    @csrf
                    
                    <!-- Amount -->
                    <div class="mb-4 md:mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Amount')</label>
                        <div class="relative">
                            <input type="number" 
                                   name="amount"
                                   step="0.01"
                                   placeholder="0.00"
                                   class="w-full px-4 py-3 pr-16 md:pr-20 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white text-base"
                                   required>
                            <span class="absolute right-3 md:right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium text-sm md:text-base">{{ gs()->cur_text }}</span>
                        </div>
                    </div>

                    @include($activeTemplate . 'partials.otp_field')

                    <!-- Transfer Limits -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 md:p-4 mb-4 md:mb-6">
                        <h4 class="font-medium text-blue-900 dark:text-blue-300 mb-3 text-sm md:text-base">@lang('Transfer Information')</h4>
                        <div class="space-y-2 text-xs md:text-sm" id="transferLimits">
                            <!-- Limits will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <button type="button" 
                                onclick="closeTransferModal()"
                                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm md:text-base font-medium">
                            @lang('Cancel')
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200 text-sm md:text-base">
                            @lang('Transfer Money')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDetailsModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Beneficiary Details')</h3>
                <button onclick="closeDetailsModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="las la-times text-gray-500 dark:text-gray-400"></i>
                </button>
            </div>
            <div class="p-6" id="detailsContent">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
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
    // Open transfer modal
    window.openTransferModal = function(beneficiaryId, beneficiaryName, accountNumber, bankName, limits) {
        // Update modal content
        document.getElementById('beneficiaryName').textContent = beneficiaryName;
        document.getElementById('beneficiaryAccount').textContent = accountNumber;
        document.getElementById('beneficiaryBank').textContent = bankName;
        document.getElementById('beneficiaryInitial').textContent = beneficiaryName.charAt(0).toUpperCase();
        
        // Update limits
        const limitsHtml = `
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-400">@lang('Minimum')</span>
                <span class="font-medium text-blue-900 dark:text-blue-300">${limits.minimum_amount}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-400">@lang('Maximum')</span>
                <span class="font-medium text-blue-900 dark:text-blue-300">${limits.maximum_amount}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-400">@lang('Daily Limit')</span>
                <span class="font-medium text-blue-900 dark:text-blue-300">${limits.daily_limit}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-400">@lang('Monthly Limit')</span>
                <span class="font-medium text-blue-900 dark:text-blue-300">${limits.monthly_limit}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-blue-700 dark:text-blue-400">@lang('Processing Time')</span>
                <span class="font-medium text-blue-900 dark:text-blue-300">${limits.processing_time}</span>
            </div>
            ${limits.transfer_charge ? `
            <div class="flex justify-between pt-2 border-t border-blue-200 dark:border-blue-800">
                <span class="text-blue-700 dark:text-blue-400">@lang('Charge')</span>
                <span class="font-medium text-red-600 dark:text-red-400">${limits.transfer_charge}</span>
            </div>
            ` : ''}
        `;
        document.getElementById('transferLimits').innerHTML = limitsHtml;
        
        // Update form action
        const form = document.getElementById('transferForm');
        const route = `{{ route('user.transfer.other.bank.request', ':id') }}`;
        form.action = route.replace(':id', beneficiaryId);
        
        // Show modal
        document.getElementById('transferModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // Close transfer modal
    window.closeTransferModal = function() {
        document.getElementById('transferModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Reset form
        document.getElementById('transferForm').reset();
    }

    // Show beneficiary details
    window.showDetails = function(beneficiaryId) {
        let modal = $('#detailsModal');
        let action = `{{ route('user.beneficiary.details', ':id') }}`;
        
        modal.removeClass('hidden');
        $('#detailsContent').html('<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div></div>');
        
        $.ajax({
            url: action.replace(':id', beneficiaryId),
            type: "GET",
            dataType: 'json',
            cache: false,
            success: function(response) {
                if (response.success) {
                    $('#detailsContent').html(response.html);
                } else {
                    notify('error', response.message || '@lang("Something went wrong")');
                    closeDetailsModal();
                }
            },
            error: function(e) {
                notify('error', '@lang("Something went wrong")');
                closeDetailsModal();
            }
        });
    }

    // Close details modal
    window.closeDetailsModal = function() {
        $('#detailsModal').addClass('hidden');
    }

})(jQuery);
</script>
@endpush