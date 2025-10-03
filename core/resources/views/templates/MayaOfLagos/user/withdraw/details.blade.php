@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-2xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-receipt text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('Withdrawal Details')</h1>
                    <p class="text-gray-600 dark:text-gray-400">@lang('Transaction ID'): #{{ $withdraw->trx }}</p>
                </div>
            </div>
            <div class="text-right">
                @php echo $withdraw->statusBadge @endphp
            </div>
        </div>
    </div>

    <!-- Withdrawal Information -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Withdrawal Information')</h3>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Transaction Details -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('TRX Number')</span>
                        <span class="font-mono text-gray-900 dark:text-white font-medium">#{{ $withdraw->trx }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Requested Amount')</span>
                        <span class="font-bold text-lg text-gray-900 dark:text-white">{{ showUserAmount($withdraw->amount, auth()->user()) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Processing Charge')</span>
                        <span class="font-medium text-red-600 dark:text-red-400">{{ showUserAmount($withdraw->charge, auth()->user()) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Amount After Charge')</span>
                        <span class="font-bold text-lg text-green-600 dark:text-green-400">{{ showUserAmount($withdraw->after_charge, auth()->user()) }}</span>
                    </div>
                </div>

                <!-- Gateway & Conversion Details -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Conversion Rate')</span>
                        <span class="font-medium text-blue-600 dark:text-blue-400">
                            1 {{ getUserCurrency(auth()->user())['text'] }} = {{ showAmount($withdraw->rate, currencyFormat: false) }} {{ __($withdraw->currency) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Final Amount')</span>
                        <span class="font-bold text-lg text-blue-600 dark:text-blue-400">
                            {{ showAmount($withdraw->final_amount, currencyFormat: false) }} {{ __($withdraw->currency) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">@lang('Created At')</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ showDateTime($withdraw->created_at) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600 dark:text-gray-400">
                            @if ($withdraw->branch)
                                @lang('Branch')
                            @else
                                @lang('Withdrawal Method')
                            @endif
                        </span>
                        <span class="font-medium text-purple-600 dark:text-purple-400">
                            @if ($withdraw->branch)
                                {{ __(@$withdraw->branch->name) }}
                            @else
                                {{ __(@$withdraw->method->name) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason (if applicable) -->
    @if ($withdraw->status == App\Constants\Status::PAYMENT_REJECT)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-red-200 dark:border-red-700 mb-6">
            <div class="p-6 border-b border-red-200 dark:border-red-700">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="las la-times-circle text-red-600 dark:text-red-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-red-900 dark:text-red-100">@lang('Reason for Rejection')</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4">
                    <p class="text-red-800 dark:text-red-200">{{ $withdraw->admin_feedback }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Submitted Data -->
    @php
        $details = $withdraw->withdraw_information ? $withdraw->withdraw_information : null;
    @endphp

    @if ($details)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Submitted Information')</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach ($details as $detail)
                        <div class="flex flex-col sm:flex-row sm:justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                            <span class="text-gray-600 dark:text-gray-400 font-medium mb-2 sm:mb-0">{{ $detail->name }}</span>
                            <div class="text-right">
                                @if ($detail->type == 'checkbox')
                                    <span class="text-gray-900 dark:text-white">{{ implode(', ', $detail->value) }}</span>
                                @elseif($detail->type == 'file')
                                    @if ($detail->value)
                                        <a href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $detail->value)) }}" 
                                           class="inline-flex items-center space-x-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200">
                                            <i class="fa-regular fa-file"></i>
                                            <span>@lang('Download Attachment')</span>
                                        </a>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">@lang('No File')</span>
                                    @endif
                                @else
                                    <span class="text-gray-900 dark:text-white break-words">{{ $detail->value }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('user.withdraw.history') }}" 
           class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-4 px-6 rounded-xl transition-all duration-200 text-center">
            <i class="las la-list mr-2"></i>
            @lang('Withdrawal History')
        </a>
        
        <a href="{{ route('user.withdraw') }}" 
           class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg text-center">
            <i class="las la-plus mr-2"></i>
            @lang('New Withdrawal')
        </a>
    </div>

    <!-- Transaction Timeline (Visual Enhancement) -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Transaction Timeline')</h3>
        </div>
        <div class="p-6">
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                
                <div class="relative flex items-center space-x-4 pb-6">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center z-10">
                        <i class="las la-plus text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">@lang('Withdrawal Requested')</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ showDateTime($withdraw->created_at) }}</p>
                    </div>
                </div>
                
                @if($withdraw->status == App\Constants\Status::PAYMENT_SUCCESS)
                    <div class="relative flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center z-10">
                            <i class="las la-check text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">@lang('Withdrawal Completed')</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ showDateTime($withdraw->updated_at) }}</p>
                        </div>
                    </div>
                @elseif($withdraw->status == App\Constants\Status::PAYMENT_REJECT)
                    <div class="relative flex items-center space-x-4">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center z-10">
                            <i class="las la-times text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">@lang('Withdrawal Rejected')</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ showDateTime($withdraw->updated_at) }}</p>
                        </div>
                    </div>
                @else
                    <div class="relative flex items-center space-x-4">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center z-10">
                            <i class="las la-clock text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">@lang('Under Review')</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('Your withdrawal is being processed')</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection