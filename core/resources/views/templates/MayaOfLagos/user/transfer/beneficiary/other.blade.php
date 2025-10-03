@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')
<div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 md:mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Other Bank Beneficiaries')</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm md:text-base">@lang('Manage beneficiaries for transfers to other banks')</p>
        </div>
        <button onclick="showAddForm()" 
                class="mt-4 sm:mt-0 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 md:px-6 py-2.5 md:py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2 text-sm md:text-base">
            <i class="las la-plus text-lg"></i>
            <span>@lang('Add Beneficiary')</span>
        </button>
    </div>

    <!-- Add/Edit Form -->
    <div id="addForm" class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-6 @if (!old('account_number') || !old('id')) hidden @endif">
        <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white" id="formTitle">@lang('Add Beneficiary to Other Banks')</h3>
            <button onclick="hideAddForm()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i class="las la-times text-gray-500 dark:text-gray-400 text-xl"></i>
            </button>
        </div>

        <div class="p-4 md:p-6 lg:p-8">
            <form action="{{ route('user.beneficiary.other.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id">
                
                <!-- Bank Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Select Bank')</label>
                    <select class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white" 
                            name="bank" required>
                        <option value="" disabled selected>@lang('Select One')</option>
                        @foreach ($otherBanks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Short Name -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Short Name')</label>
                    <input type="text" 
                           name="short_name"
                           placeholder="@lang('Enter a short name for easy identification')"
                           class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                           required>
                </div>

                <!-- Dynamic Bank Fields -->
                <div id="user-fields" class="space-y-6 mb-4">
                    <!-- Bank-specific fields will be loaded here -->
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <button type="button" 
                            onclick="hideAddForm()"
                            class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        @lang('Cancel')
                    </button>
                    <button type="submit" 
                            id="submitBtn"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200">
                        <span class="submit-text">@lang('Submit')</span>
                        <span class="loading-text hidden">
                            <i class="las la-spinner animate-spin mr-1"></i>
                            @lang('Processing...')
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Beneficiaries List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($beneficiaries->count() > 0)
            <!-- Mobile Search -->
            <div class="lg:hidden p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="relative">
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
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Bank')</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Account No.')</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Account Name')</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Short Name')</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($beneficiaries as $beneficiary)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $beneficiary->beneficiaryOf->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-mono">{{ $beneficiary->account_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $beneficiary->account_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $beneficiary->short_name }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="showDetails({{ $beneficiary->id }})" 
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors"
                                            title="@lang('View Details')">
                                        <i class="las la-eye text-sm"></i>
                                    </button>
                                    <button onclick="editBeneficiary({{ json_encode($beneficiary) }})" 
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors"
                                            title="@lang('Edit')">
                                        <i class="las la-edit text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden p-4 space-y-4">
                @foreach ($beneficiaries as $beneficiary)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($beneficiary->short_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $beneficiary->short_name }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $beneficiary->beneficiaryOf->name }}</p>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button onclick="showDetails({{ $beneficiary->id }})" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                <i class="las la-eye text-sm"></i>
                            </button>
                            <button onclick="editBeneficiary({{ json_encode($beneficiary) }})" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                                <i class="las la-edit text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Account Name'):</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $beneficiary->account_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">@lang('Account Number'):</span>
                            <span class="text-gray-900 dark:text-white font-mono">{{ $beneficiary->account_number }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12 md:py-16">
                <div class="w-16 h-16 md:w-24 md:h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <i class="las la-university text-gray-400 dark:text-gray-500 text-2xl md:text-4xl"></i>
                </div>
                <h3 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('No Other Bank Beneficiaries')</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 md:mb-8 max-w-md mx-auto px-4 text-sm md:text-base">@lang('Add beneficiaries from other banks to start transferring money to external accounts.')</p>
                <button onclick="showAddForm()" 
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 md:px-8 py-2.5 md:py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl text-sm md:text-base">
                    @lang('Add Your First Beneficiary')
                </button>
            </div>
        @endif

        <!-- Pagination -->
        @if ($beneficiaries->hasPages())
        <div class="px-4 md:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            {{ paginateLinks($beneficiaries) }}
        </div>
        @endif
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 z-[999] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="hideDetailsModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Beneficiary Details')</h3>
                <button onclick="hideDetailsModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
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
    const addForm = $('#addForm');

    // Loading state management
    function setSubmitLoading(button, loading = true) {
        const $button = $(button);
        const submitText = $button.find('.submit-text');
        const loadingText = $button.find('.loading-text');
        
        if (loading) {
            $button.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
            submitText.addClass('hidden');
            loadingText.removeClass('hidden');
        } else {
            $button.prop('disabled', false).removeClass('opacity-75 cursor-not-allowed');
            submitText.removeClass('hidden');
            loadingText.addClass('hidden');
        }
    }

    // Handle form submission
    addForm.find('form').on('submit', function(e) {
        const submitBtn = $('#submitBtn');
        setSubmitLoading(submitBtn, true);
        
        // Re-enable button after 10 seconds as fallback
        setTimeout(() => {
            setSubmitLoading(submitBtn, false);
        }, 10000);
    });

    // Show add form
    window.showAddForm = function() {
        addForm.find('#formTitle').text('@lang("Add Beneficiary to Other Banks")');
        addForm.find('form').trigger("reset");
        addForm.find('input[name="id"]').val('');
        addForm.removeClass('hidden').hide().fadeIn(500);
        $('#user-fields').empty();
        
        // Reset submit button state
        setSubmitLoading($('#submitBtn'), false);
    }

    // Hide add form
    window.hideAddForm = function() {
        addForm.addClass('hidden');
        // Reset submit button state
        setSubmitLoading($('#submitBtn'), false);
    }

    // Handle bank selection
    addForm.find('select[name=bank]').on('change', function() {
        let bankId = $(this).val();
        if (bankId) {
            bankFormProcess(bankId);
        }
    });

    // Load bank form fields
    function bankFormProcess(bankId) {
        let action = `{{ route('user.beneficiary.other.bank.form.data', ':id') }}`;
        $.ajax({
            url: action.replace(':id', bankId),
            type: "GET",
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                $('#user-fields').html('<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div></div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#user-fields').html(response.html).hide().fadeIn(500);
                } else {
                    notify('error', response.message || '@lang("Something went wrong")');
                }
            },
            error: function(e) {
                notify('error', '@lang("Something went wrong")');
            }
        });
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
                    hideDetailsModal();
                }
            },
            error: function(e) {
                notify('error', '@lang("Something went wrong")');
                hideDetailsModal();
            }
        });
    }

    // Hide details modal
    window.hideDetailsModal = function() {
        $('#detailsModal').addClass('hidden');
    }

    // Edit beneficiary
    window.editBeneficiary = function(beneficiary) {
        let bankId = beneficiary.beneficiary_of.id;
        
        // Reset submit button state
        setSubmitLoading($('#submitBtn'), false);
        
        // Load bank form first
        bankFormProcess(bankId);
        
        // Wait for form to load, then populate
        setTimeout(() => {
            addForm.find('#formTitle').text('@lang("Update Beneficiary to Other Banks")');
            addForm.find('input[name="id"]').val(beneficiary.id);
            addForm.find('select[name="bank"]').val(bankId);
            addForm.find('input[name="short_name"]').val(beneficiary.short_name);
            addForm.find('input[name="account_number"]').val(beneficiary.account_number);
            addForm.find('input[name="account_name"]').val(beneficiary.account_name);

            // Populate dynamic fields
            if (beneficiary.details && beneficiary.details.length > 0) {
                $.each(beneficiary.details, function(index, field) {
                    var fieldName = field.name.replace(/\s+/g, '_').toLowerCase();
                    
                    if (field.type == 'radio') {
                        addForm.find('input:radio[name="' + fieldName + '"]').filter('[value="' + field.value + '"]').prop('checked', true);
                    } else if (field.type == 'textarea') {
                        addForm.find('textarea[name="' + fieldName + '"]').val(field.value);
                    } else if (field.type == 'select') {
                        addForm.find('select[name="' + fieldName + '"]').val(field.value);
                    } else if (field.type == 'file') {
                        // File fields cannot be pre-populated
                        addForm.find('input[name="' + fieldName + '"]').val('');
                    } else {
                        addForm.find('input[name="' + fieldName + '"]').val(field.value);
                    }
                });
            }
            
            addForm.removeClass('hidden');
        }, 800);
    }

})(jQuery);
</script>
@endpush