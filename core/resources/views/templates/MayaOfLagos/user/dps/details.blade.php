@extends($activeTemplate . 'user.dps.layout')
@section('dps-content')

<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- DPS Details Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        
        <!-- Header with Status -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang('DPS Details')</h2>
                    <p class="text-gray-600 dark:text-gray-400">@lang('Complete information about your Deposit Pension Scheme')</p>
                </div>
                <div class="text-right">
                    @if($dps->status == 1)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                            <i class="las la-play-circle mr-2"></i>
                            @lang('Running')
                        </span>
                    @elseif($dps->status == 2)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            <i class="las la-check-circle mr-2"></i>
                            @lang('Matured')
                        </span>
                    @elseif($dps->status == 3)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                            <i class="las la-times-circle mr-2"></i>
                            @lang('Closed')
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                            <i class="las la-clock mr-2"></i>
                            @lang('Pending')
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- DPS Information -->
        <div class="p-6">
            @include($activeTemplate . 'partials.dps_details')
            
            <!-- Download Button -->
            <div class="flex justify-end mt-6">
                <a href="{{ route('user.dps.details', $dps->dps_number) }}?download" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    <i class="las la-file-download mr-2"></i>
                    @lang('Download Certificate')
                </a>
            </div>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="las la-chart-bar mr-2 text-blue-600"></i>
            @lang('Progress Overview')
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Installment Progress -->
            <div class="text-center">
                <div class="relative w-24 h-24 mx-auto mb-4">
                    @php
                        $progressPercentage = $dps->total_installment > 0 ? ($dps->given_installment / $dps->total_installment) * 100 : 0;
                    @endphp
                    <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-blue-600 dark:text-blue-400" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="{{ $progressPercentage }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($progressPercentage, 0) }}%</span>
                    </div>
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white">@lang('Completion')</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $dps->given_installment }}/{{ $dps->total_installment }} @lang('Installments')</p>
            </div>
            
            <!-- Amount Progress -->
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                    {{ showAmount($dps->per_installment * $dps->given_installment) }}
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white">@lang('Deposited')</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ gs()->cur_text }}</p>
            </div>
            
            <!-- Expected Maturity -->
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                    {{ showAmount($dps->plan->final_amount) }}
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white">@lang('Maturity Amount')</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ gs()->cur_text }}</p>
            </div>
        </div>
    </div>

    <!-- Withdrawal Section -->
    @if ($dps->status == 2)
        <div class="bg-gradient-to-r from-green-100 to-blue-100 dark:from-green-900/30 dark:to-blue-900/30 rounded-xl border border-green-200 dark:border-green-700 p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                        <i class="las la-check-circle text-white text-2xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('DPS Matured - Ready for Withdrawal')</h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        @lang('Congratulations! Your Deposit Pension Scheme (DPS) has matured. You can now withdraw the amount. Upon withdrawal, the maturity amount will be added to your main balance.')
                    </p>
                    <button type="button" 
                            class="confirmationBtn inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200"
                            data-action="{{ route('user.dps.withdraw', $dps->id) }}" 
                            data-question="@lang('Are you sure to withdraw this DPS?')">
                        <i class="las la-money-check mr-2"></i>
                        @lang('Withdraw Now')
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-xl border border-blue-200 dark:border-blue-700 p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="las la-info-circle text-white text-2xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('Withdrawal Information')</h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        @lang('You will have the option to withdraw this DPS after all required installments have been completed.')
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="las la-bolt mr-2 text-yellow-500"></i>
            @lang('Quick Actions')
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('user.dps.instalment.logs', $dps->dps_number) }}" 
               class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                    <i class="las la-list text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 dark:text-white">@lang('Installments')</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('View payment history')</div>
                </div>
            </a>
            
            <a href="{{ route('user.dps.list') }}" 
               class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                    <i class="las la-arrow-left text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 dark:text-white">@lang('Back to List')</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('View all DPS')</div>
                </div>
            </a>
            
            <a href="{{ route('user.dps.plans') }}" 
               class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                    <i class="las la-plus text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 dark:text-white">@lang('New DPS')</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">@lang('Open another DPS')</div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Confirmation Modal Component -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="las la-question-circle text-2xl text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('Confirm Action')</h3>
            <p id="confirmationQuestion" class="text-gray-600 dark:text-gray-400 mb-6"></p>
            
            <div class="flex items-center justify-center space-x-3">
                <button type="button" 
                        onclick="closeConfirmationModal()"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg transition-colors duration-200">
                    @lang('Cancel')
                </button>
                <button type="button" 
                        id="confirmButton"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    @lang('Confirm')
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
(function($) {
    "use strict";
    
    let confirmationAction = '';
    
    $('.confirmationBtn').on('click', function() {
        let action = $(this).data('action');
        let question = $(this).data('question');
        
        confirmationAction = action;
        $('#confirmationQuestion').text(question);
        
        document.getElementById('confirmationModal').classList.remove('hidden');
        document.getElementById('confirmationModal').classList.add('flex');
    });
    
    $('#confirmButton').on('click', function() {
        if (confirmationAction) {
            // Create and submit form
            let form = $('<form>', {
                'method': 'POST',
                'action': confirmationAction
            });
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': '{{ csrf_token() }}'
            }));
            
            $('body').append(form);
            form.submit();
        }
    });
    
    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
        document.getElementById('confirmationModal').classList.remove('flex');
        confirmationAction = '';
    }
    
    // Make function global
    window.closeConfirmationModal = closeConfirmationModal;
    
})(jQuery);
</script>
@endpush