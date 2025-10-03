@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')

<div class="grid grid-cols-1 gap-6">
    <div class="w-full mb-4">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-3xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">@lang('Manage Beneficiaries')</h1>
                    <p class="text-green-100">@lang('Add and manage your beneficiaries for quick transfers within') {{ gs()->site_name }}</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="las la-users text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden" id="beneficiaryFormCard">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="formTitle">@lang('Add New Beneficiary')</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1" id="formDescription">@lang('Enter account details to add a new beneficiary')</p>
                    </div>
                    
                    <div class="p-6">
                        <form action="{{ route('user.beneficiary.own.add') }}" method="post" id="addBeneficiaryForm">
                            @csrf
                            <input type="hidden" name="id" id="beneficiaryId">
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Account Number')</label>
                                <div class="relative">
                                    <input type="text" 
                                           name="account_number"
                                           id="account_number"
                                           placeholder="@lang('Enter account number')"
                                           class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                                           required>
                                    <button type="button" 
                                            id="verify-button"
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 w-6 h-6 flex items-center justify-center text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                            title="@lang('Verify Account Number')">
                                        <i class="las la-search text-lg" id="verify-icon"></i>
                                    </button>
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <div id="verification-status" class="hidden text-sm">
                                        <span class="text-green-600 dark:text-green-400">
                                            <i class="las la-check-circle"></i> @lang('Verified')
                                        </span>
                                    </div>
                                    <div id="verification-error" class="hidden text-sm">
                                        <span class="text-red-600 dark:text-red-400">
                                            <i class="las la-exclamation-circle"></i> <span id="error-text"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Account Name')</label>
                                <input type="text" 
                                       name="account_name"
                                       id="account_name"
                                       placeholder="@lang('Will be auto-filled after verification')"
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                                       readonly>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Nickname')</label>
                                <input type="text" 
                                       name="short_name"
                                       id="short_name"
                                       placeholder="@lang('Enter a nickname for this beneficiary')"
                                       class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                                       required>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('A friendly name to identify this beneficiary')
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <button type="submit" 
                                        id="submitButton"
                                        class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                                    <i class="las la-plus" id="submitIcon"></i>
                                    <span id="submitText">@lang('Add Beneficiary')</span>
                                </button>
                                <button type="button" 
                                        id="cancelEdit"
                                        class="hidden px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center space-x-2">
                                    <i class="las la-times"></i>
                                    <span>@lang('Cancel')</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Your Beneficiaries')</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">@lang('Manage your saved beneficiaries')</p>
                        </div>
                        <a href="{{ route('user.transfer.own.bank.beneficiaries') }}" 
                           class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium text-sm flex items-center space-x-1">
                            <span>@lang('Transfer Money')</span>
                            <i class="las la-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="p-6">
                        @if($beneficiaries->count() > 0)
                            <div class="space-y-4">
                                @foreach($beneficiaries as $beneficiary)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-600 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ substr($beneficiary->short_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $beneficiary->short_name }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $beneficiary->account_number }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editBeneficiary({{ json_encode($beneficiary) }})" 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                                title="@lang('Edit Beneficiary')">
                                            <i class="las la-edit"></i>
                                        </button>
                                        <button onclick="deleteBeneficiary({{ $beneficiary->id }})" 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                                title="@lang('Delete Beneficiary')">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            @if($beneficiaries->hasPages())
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                {{ paginateLinks($beneficiaries) }}
                            </div>
                            @endif
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="las la-users text-gray-400 dark:text-gray-500 text-2xl"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('No Beneficiaries Yet')</h4>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">@lang('Add your first beneficiary to start making transfers')</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="las la-exclamation-triangle text-red-600 dark:text-red-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">@lang('Delete')</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                @lang('Are you sure you want to delete this beneficiary? This action cannot be undone.')
            </p>
            <div class="flex items-center space-x-3">
                <button type="button" 
                        id="cancelDeleteBtn"
                        onclick="closeDeleteModal()"
                        class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl font-medium transition-all duration-200">
                    @lang('Cancel')
                </button>
                <button type="button" 
                        id="confirmDeleteBtn"
                        onclick="confirmDelete()"
                        class="flex-1 px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                    <i class="las la-trash" id="deleteIcon"></i>
                    <span id="deleteText">@lang('Delete')</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
class AccountVerifier {
    constructor() {
        this.accountInput = document.getElementById('account_number');
        this.accountNameField = document.getElementById('account_name');
        this.verifyButton = document.getElementById('verify-button');
        this.verifyIcon = document.getElementById('verify-icon');
        this.verificationStatus = document.getElementById('verification-status');
        this.verificationError = document.getElementById('verification-error');
        this.errorText = document.getElementById('error-text');
        this.isVerifying = false;
        this.autoVerifyTimeout = null;
        this.init();
    }
    
    init() {
        this.verifyButton.addEventListener('click', () => this.verifyAccount());
        this.accountInput.addEventListener('input', (e) => this.handleInput(e));
        document.getElementById('addBeneficiaryForm').addEventListener('submit', (e) => this.validateForm(e));
    }
    
    async verifyAccount() {
        const accountNumber = this.accountInput.value.trim();
        if (!accountNumber) {
            this.showError('@lang("Please enter an account number")');
            return;
        }
        if (this.isVerifying) return;
        
        this.setLoadingState(true);
        this.clearStatus();
        
        try {
            const response = await fetch(`{{ route("user.beneficiary.check.account") }}?${new URLSearchParams({
                account_number: accountNumber
            })}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            const text = await response.text();
            let data;
            
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid server response');
            }
            
            if (data.error === false && data.data) {
                this.handleSuccess(data.data);
            } else {
                this.handleError(data.message || '@lang("Account verification failed")');
            }
            
        } catch (error) {
            this.handleError('@lang("Network error. Please try again.")');
        } finally {
            this.setLoadingState(false);
        }
    }
    
    handleSuccess(data) {
        const accountName = data.account_name || data.account_number || '';
        if (accountName) {
            this.accountNameField.value = accountName;
            this.setFieldState('success');
            this.showSuccess();
            notify('success', '@lang("Account verified successfully")');
        } else {
            this.handleError('@lang("Account name not found")');
        }
    }
    
    handleError(message) {
        this.accountNameField.value = '';
        this.setFieldState('error');
        this.showError(message);
        notify('error', message);
    }
    
    setLoadingState(loading) {
        this.isVerifying = loading;
        if (loading) {
            this.verifyIcon.className = 'las la-spinner la-spin text-lg';
            this.verifyButton.classList.add('text-blue-500');
            this.verifyButton.disabled = true;
        } else {
            this.verifyIcon.className = 'las la-search text-lg';
            this.verifyButton.classList.remove('text-blue-500');
            this.verifyButton.disabled = false;
        }
    }
    
    setFieldState(state) {
        const baseClasses = 'w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-gray-700 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white';
        this.accountInput.className = baseClasses;
        this.accountNameField.classList.remove('border-green-500', 'border-red-500', 'border-gray-300', 'dark:border-gray-600');
        
        switch (state) {
            case 'success':
                this.accountInput.classList.add('border-green-500');
                this.accountNameField.classList.add('border-green-500');
                break;
            case 'error':
                this.accountInput.classList.add('border-red-500');
                this.accountNameField.classList.add('border-red-500');
                break;
            default:
                this.accountInput.classList.add('border-gray-300', 'dark:border-gray-600');
                this.accountNameField.classList.add('border-gray-300', 'dark:border-gray-600');
        }
    }
    
    showSuccess() {
        this.verificationStatus.classList.remove('hidden');
        this.verificationError.classList.add('hidden');
    }
    
    showError(message) {
        this.errorText.textContent = message;
        this.verificationError.classList.remove('hidden');
        this.verificationStatus.classList.add('hidden');
    }
    
    clearStatus() {
        this.verificationStatus.classList.add('hidden');
        this.verificationError.classList.add('hidden');
    }
    
    handleInput(e) {
        const accountNumber = e.target.value.trim();
        if (this.accountNameField.value) {
            this.accountNameField.value = '';
            this.setFieldState('default');
            this.clearStatus();
        }
        if (this.autoVerifyTimeout) {
            clearTimeout(this.autoVerifyTimeout);
        }
        if (accountNumber.length >= 10 && /^\d+$/.test(accountNumber)) {
            this.autoVerifyTimeout = setTimeout(() => {
                if (this.accountInput.value.trim() === accountNumber && !this.isVerifying) {
                    this.verifyAccount();
                }
            }, 1500);
        }
    }
    
    validateForm(e) {
        if (!this.accountNameField.value.trim()) {
            e.preventDefault();
            notify('error', '@lang("Please verify the account number first")');
            this.accountInput.focus();
            return false;
        }
        
        // Set loading state when form is valid and submitting
        window.beneficiaryManager.setSubmitLoading(true);
        return true;
    }
}

class BeneficiaryManager {
    constructor() {
        this.accountVerifier = new AccountVerifier();
        this.deleteId = null;
        this.isEditMode = false;
        this.isSubmitting = false;
        this.isDeleting = false;
    }
    
    setSubmitLoading(loading) {
        const submitButton = document.getElementById('submitButton');
        const submitIcon = document.getElementById('submitIcon');
        const submitText = document.getElementById('submitText');
        
        this.isSubmitting = loading;
        
        if (loading) {
            submitButton.disabled = true;
            submitIcon.className = 'las la-spinner la-spin';
            submitText.textContent = this.isEditMode ? '@lang("Updating...")' : '@lang("Adding...")';
            submitButton.classList.add('opacity-75', 'cursor-not-allowed');
        } else {
            submitButton.disabled = false;
            submitIcon.className = this.isEditMode ? 'las la-edit' : 'las la-plus';
            submitText.textContent = this.isEditMode ? '@lang("Update")' : '@lang("Add Beneficiary")';
            submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    }
    
    setDeleteLoading(loading) {
        const deleteButton = document.getElementById('confirmDeleteBtn');
        const deleteIcon = document.getElementById('deleteIcon');
        const deleteText = document.getElementById('deleteText');
        
        this.isDeleting = loading;
        
        if (loading) {
            deleteButton.disabled = true;
            deleteIcon.className = 'las la-spinner la-spin';
            deleteText.textContent = '@lang("Deleting...")';
            deleteButton.classList.add('opacity-75', 'cursor-not-allowed');
        } else {
            deleteButton.disabled = false;
            deleteIcon.className = 'las la-trash';
            deleteText.textContent = '@lang("Delete")';
            deleteButton.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    }
    
    editBeneficiary(beneficiary) {
        this.isEditMode = true;
        document.getElementById('formTitle').textContent = '@lang("Edit Beneficiary")';
        document.getElementById('formDescription').textContent = '@lang("Update beneficiary details")';
        document.getElementById('submitText').textContent = '@lang("Update")';
        document.getElementById('submitIcon').className = 'las la-edit';
        document.getElementById('cancelEdit').classList.remove('hidden');
        document.getElementById('beneficiaryId').value = beneficiary.id;
        document.getElementById('account_number').value = beneficiary.account_number;
        document.getElementById('account_name').value = beneficiary.account_name;
        document.getElementById('short_name').value = beneficiary.short_name;
        this.accountVerifier.setFieldState('success');
        this.accountVerifier.showSuccess();
        document.getElementById('beneficiaryFormCard').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
        notify('info', '@lang("Editing beneficiary: ")' + beneficiary.short_name);
    }
    
    cancelEdit() {
        this.isEditMode = false;
        document.getElementById('formTitle').textContent = '@lang("Add New Beneficiary")';
        document.getElementById('formDescription').textContent = '@lang("Enter account details to add a new beneficiary")';
        document.getElementById('submitText').textContent = '@lang("Add Beneficiary")';
        document.getElementById('submitIcon').className = 'las la-plus';
        document.getElementById('cancelEdit').classList.add('hidden');
        document.getElementById('addBeneficiaryForm').reset();
        document.getElementById('beneficiaryId').value = '';
        this.accountVerifier.setFieldState('default');
        this.accountVerifier.clearStatus();
        this.setSubmitLoading(false);
    }
    
    deleteBeneficiary(id) {
        this.deleteId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    
    closeDeleteModal() {
        this.deleteId = null;
        this.setDeleteLoading(false);
        document.getElementById('deleteModal').classList.add('hidden');
    }
    
    confirmDelete() {
        if (!this.deleteId) return;
        
        this.setDeleteLoading(true);
        
        // Create a form and submit it to the delete route
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('user.beneficiary.delete', '') }}/${this.deleteId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add method spoofing for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.beneficiaryManager = new BeneficiaryManager();
    document.getElementById('cancelEdit').addEventListener('click', function() {
        window.beneficiaryManager.cancelEdit();
    });
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            window.beneficiaryManager.closeDeleteModal();
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.beneficiaryManager.closeDeleteModal();
        }
    });
    
    // Add direct event listeners to modal buttons as backup
    document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
        window.beneficiaryManager.closeDeleteModal();
    });
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        window.beneficiaryManager.confirmDelete();
    });
});

function editBeneficiary(beneficiary) {
    window.beneficiaryManager.editBeneficiary(beneficiary);
}

function deleteBeneficiary(id) {
    window.beneficiaryManager.deleteBeneficiary(id);
}

function closeDeleteModal() {
    window.beneficiaryManager.closeDeleteModal();
}

function confirmDelete() {
    window.beneficiaryManager.confirmDelete();
}
</script>
@endpush