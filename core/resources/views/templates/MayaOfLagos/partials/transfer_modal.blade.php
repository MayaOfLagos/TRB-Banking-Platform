<div x-data="transferModal()" 
     x-show="showModal" 
     x-on:open-transfer-modal.window="openModal()" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     class="fixed inset-0 z-[999] items-center justify-center hidden lg:flex" 
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" 
         @click="closeModal()"></div>
    
    <!-- Modal Container -->
    <div class="relative bg-white dark:bg-gray-800 max-w-md w-full rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 max-h-[80vh] overflow-hidden">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Quick Transfer')</h3>
                <button @click="closeModal()" 
                        class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    <i class="las la-times text-sm"></i>
                </button>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="p-6">
            <div class="space-y-3">
                
                @if (gs()->modules->own_bank ?? false)
                <a href="{{ route('user.transfer.own.bank.beneficiaries') }}" 
                   class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 hover:border-green-200 dark:hover:border-green-700 border border-gray-200 dark:border-gray-600 transition-all duration-200 group"
                   @click="closeModal()">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                        <i class="las la-university text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white group-hover:text-green-700 dark:group-hover:text-green-300">@lang('Own Bank Transfer')</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('Instant & Free')</p>
                    </div>
                    <i class="las la-chevron-right text-gray-400 dark:text-gray-500 group-hover:text-green-500 transition-colors"></i>
                </a>
                @endif
                
                @if (gs()->modules->other_bank ?? false)
                <a href="{{ route('user.transfer.other.bank.beneficiaries') }}" 
                   class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-200 dark:hover:border-blue-700 border border-gray-200 dark:border-gray-600 transition-all duration-200 group"
                   @click="closeModal()">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                        <i class="las la-building text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-300">@lang('Other Bank Transfer')</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('All Banks Supported')</p>
                    </div>
                    <i class="las la-chevron-right text-gray-400 dark:text-gray-500 group-hover:text-blue-500 transition-colors"></i>
                </a>
                @endif
                
                @if (gs()->modules->wire_transfer ?? false)
                <a href="{{ route('user.transfer.wire.index') }}" 
                   class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:border-purple-200 dark:hover:border-purple-700 border border-gray-200 dark:border-gray-600 transition-all duration-200 group"
                   @click="closeModal()">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors">
                        <i class="las la-globe text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white group-hover:text-purple-700 dark:group-hover:text-purple-300">@lang('Wire Transfer')</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('International')</p>
                    </div>
                    <i class="las la-chevron-right text-gray-400 dark:text-gray-500 group-hover:text-purple-500 transition-colors"></i>
                </a>
                @endif
                
                <!-- Transfer History -->
                <a href="{{ route('user.transfer.history') }}" 
                   class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 transition-all duration-200 group"
                   @click="closeModal()">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-600 rounded-xl flex items-center justify-center group-hover:bg-gray-200 dark:group-hover:bg-gray-500 transition-colors">
                        <i class="las la-history text-gray-600 dark:text-gray-400 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">@lang('Transfer History')</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('View Past Transfers')</p>
                    </div>
                    <i class="las la-chevron-right text-gray-400 dark:text-gray-500 transition-colors"></i>
                </a>
                
            </div>
        </div>
        
    </div>
</div>

<div x-data="mobileFab()" 
     x-on:open-transfer-modal.window="toggleFab()" 
     class="fixed bottom-24 right-6 z-50 lg:hidden">
    
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 transform translate-y-4 scale-95"
         class="flex flex-col items-end space-y-3 mb-4">
        
        @if (gs()->modules->own_bank ?? false)
        <!-- Own Bank Transfer -->
        <div class="flex items-center space-x-3">
            <div class="bg-white dark:bg-gray-800 rounded-full px-4 py-2 shadow-lg border border-gray-200 dark:border-gray-700">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">@lang('Own Bank')</span>
            </div>
            <a href="{{ route('user.transfer.own.bank.beneficiaries') }}" 
               class="w-12 h-12 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center shadow-lg transition-colors"
               @click="closeFab()">
                <i class="las la-university text-white text-xl"></i>
            </a>
        </div>
        @endif
        
        @if (gs()->modules->other_bank ?? false)
        <!-- Other Bank Transfer -->
        <div class="flex items-center space-x-3">
            <div class="bg-white dark:bg-gray-800 rounded-full px-4 py-2 shadow-lg border border-gray-200 dark:border-gray-700">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">@lang('Other Bank')</span>
            </div>
            <a href="{{ route('user.transfer.other.bank.beneficiaries') }}" 
               class="w-12 h-12 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center shadow-lg transition-colors"
               @click="closeFab()">
                <i class="las la-building text-white text-xl"></i>
            </a>
        </div>
        @endif
        
        @if (gs()->modules->wire_transfer ?? false)
        <!-- Wire Transfer -->
        <div class="flex items-center space-x-3">
            <div class="bg-white dark:bg-gray-800 rounded-full px-4 py-2 shadow-lg border border-gray-200 dark:border-gray-700">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">@lang('Wire Transfer')</span>
            </div>
            <a href="{{ route('user.transfer.wire.index') }}" 
               class="w-12 h-12 bg-purple-500 hover:bg-purple-600 rounded-full flex items-center justify-center shadow-lg transition-colors"
               @click="closeFab()">
                <i class="las la-globe text-white text-xl"></i>
            </a>
        </div>
        @endif
        
        <div class="flex items-center space-x-3">
            <div class="bg-white dark:bg-gray-800 rounded-full px-4 py-2 shadow-lg border border-gray-200 dark:border-gray-700">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">@lang('History')</span>
            </div>
            <a href="{{ route('user.transfer.history') }}" 
               class="w-12 h-12 bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-500 rounded-full flex items-center justify-center shadow-lg transition-colors"
               @click="closeFab()">
                <i class="las la-history text-white text-xl"></i>
            </a>
        </div>
        
    </div>
    
    <!-- Backdrop with blur effect for mobile actions -->
    <div x-show="isOpen" 
         @click="closeFab()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/30 backdrop-blur-sm z-[-1]">
    </div>
    
</div>

<script>
function transferModal() {
    return {
        showModal: false,
        
        openModal() {
            this.showModal = true;
            document.body.classList.add('overflow-hidden');
        },
        
        closeModal() {
            this.showModal = false;
            document.body.classList.remove('overflow-hidden');
        }
    }
}

function transferMobilModal() {
    return {
        showMobileModal: false,
        
        openMobileModal() {
            this.showMobileModal = true;
            document.body.classList.add('overflow-hidden');
        },
        
        closeMobileModal() {
            this.showMobileModal = false;
            document.body.classList.remove('overflow-hidden');
        }
    }
}

function mobileFab() {
    return {
        isOpen: false,
        
        toggleFab() {
            this.isOpen = !this.isOpen;
        },
        
        closeFab() {
            this.isOpen = false;
        }
    }
}
</script>