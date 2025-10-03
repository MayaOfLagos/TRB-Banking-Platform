@push('modal')
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 hidden" id="operatorsModal" role="dialog">
        <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-h-[90vh] overflow-hidden" role="document">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                <h5 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="las la-tower-cell mr-2 text-blue-600"></i>
                    @lang('Select Operators')
                </h5>
                <button class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200" 
                        onclick="closeOperatorsModal()" type="button" aria-label="Close">
                    <i class="las la-times text-xl text-gray-500 dark:text-gray-400"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="relative">
                <!-- Loading Spinner -->
                <div class="modal-preloader hidden absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-80 z-50 flex items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>

                <!-- Tabs Navigation -->
                <div class="px-6 pt-6">
                    <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-600" role="tablist">
                        <button class="operator-tab px-4 py-2 font-medium text-sm rounded-t-lg border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors duration-200 active all bg-blue-50 dark:bg-blue-900 text-blue-600 border-blue-600" 
                                data-target="all" 
                                role="tab" 
                                aria-controls="all" 
                                aria-selected="true">
                            @lang('All')
                        </button>
                        <button class="operator-tab px-4 py-2 font-medium text-sm rounded-t-lg border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors duration-200 recharge text-gray-700 dark:text-gray-300" 
                                data-target="recharge" 
                                role="tab" 
                                aria-controls="recharge" 
                                aria-selected="false">
                            @lang('Recharge')
                        </button>
                        <button class="operator-tab px-4 py-2 font-medium text-sm rounded-t-lg border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors duration-200 bundle text-gray-700 dark:text-gray-300" 
                                data-target="bundle" 
                                role="tab" 
                                aria-controls="bundle" 
                                aria-selected="false">
                            @lang('Bundle')
                        </button>
                        <button class="operator-tab px-4 py-2 font-medium text-sm rounded-t-lg border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors duration-200 data text-gray-700 dark:text-gray-300" 
                                data-target="data" 
                                role="tab" 
                                aria-controls="data" 
                                aria-selected="false">
                            @lang('Data')
                        </button>
                        <button class="operator-tab px-4 py-2 font-medium text-sm rounded-t-lg border-b-2 border-transparent hover:text-blue-600 hover:border-blue-600 transition-colors duration-200 pin text-gray-700 dark:text-gray-300" 
                                data-target="pin" 
                                role="tab" 
                                aria-controls="pin" 
                                aria-selected="false">
                            @lang('Pin')
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="p-6 max-h-96 overflow-y-auto">
                    <div class="tab-content-wrapper">
                        <div class="tab-pane active all" id="all" role="tabpanel">
                            <div class="operator-wrapper all"></div>
                        </div>
                        <div class="tab-pane hidden recharge" id="recharge" role="tabpanel">
                            <div class="operator-wrapper recharge"></div>
                        </div>
                        <div class="tab-pane hidden bundle" id="bundle" role="tabpanel">
                            <div class="operator-wrapper bundle"></div>
                        </div>
                        <div class="tab-pane hidden data" id="data" role="tabpanel">
                            <div class="operator-wrapper data"></div>
                        </div>
                        <div class="tab-pane hidden pin" id="pin" role="tabpanel">
                            <div class="operator-wrapper pin"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-center p-6 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                <button class="confirmOperatorBtn bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800" 
                        type="button">
                    <i class="las la-check mr-2"></i>
                    @lang('Confirm')
                </button>
            </div>
        </div>
    </div>
@endpush

@push('style')
    <style>
        .modal-preloader {
            backdrop-filter: blur(2px);
        }
        
        .operator-tab.active {
            background-color: rgb(239 246 255);
            color: rgb(37 99 235);
            border-bottom-color: rgb(37 99 235);
        }
        
        .dark .operator-tab.active {
            background-color: rgb(30 58 138);
            color: rgb(147 197 253);
            border-bottom-color: rgb(147 197 253);
        }
        
        .tab-pane.active {
            display: block;
        }
        
        .tab-pane {
            display: none;
        }
        
        /* Scrollbar Styling */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-track {
            background: rgb(243 244 246);
            border-radius: 3px;
        }
        
        .dark .overflow-y-auto::-webkit-scrollbar-track {
            background: rgb(55 65 81);
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgb(156 163 175);
            border-radius: 3px;
        }
        
        .dark .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgb(107 114 128);
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgb(107 114 128);
        }
        
        .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgb(156 163 175);
        }
    </style>
@endpush

@push('script')
    <script>
        function closeOperatorsModal() {
            document.getElementById('operatorsModal').classList.add('hidden');
        }
        
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.operator-tab');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    
                    // Remove active class from all tabs and panes
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-blue-50', 'dark:bg-blue-900', 'text-blue-600', 'border-blue-600');
                        btn.classList.add('text-gray-700', 'dark:text-gray-300');
                        btn.setAttribute('aria-selected', 'false');
                    });
                    
                    tabPanes.forEach(pane => {
                        pane.classList.remove('active');
                        pane.classList.add('hidden');
                    });
                    
                    // Add active class to clicked tab
                    this.classList.add('active', 'bg-blue-50', 'dark:bg-blue-900', 'text-blue-600', 'border-blue-600');
                    this.classList.remove('text-gray-700', 'dark:text-gray-300');
                    this.setAttribute('aria-selected', 'true');
                    
                    // Show corresponding pane
                    const targetPane = document.getElementById(target);
                    if (targetPane) {
                        targetPane.classList.add('active');
                        targetPane.classList.remove('hidden');
                    }
                });
            });
        });
    </script>
@endpush