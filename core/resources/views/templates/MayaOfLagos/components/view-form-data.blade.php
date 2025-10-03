@props(['data' => [], 'beneficiary' => null])

<!-- Basic Beneficiary Information -->
@if($beneficiary)
<div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/30 rounded-xl p-4 mb-6">
    <div class="flex items-center space-x-3 mb-4">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
            <span class="text-white font-bold">{{ substr($beneficiary->short_name, 0, 1) }}</span>
        </div>
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $beneficiary->short_name }}</h4>
            <p class="text-sm text-blue-600 dark:text-blue-400">{{ $beneficiary->beneficiaryOf->name ?? 'Unknown Bank' }}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">@lang('Account Name')</label>
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $beneficiary->account_name }}</p>
        </div>
        <div>
            <label class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">@lang('Account Number')</label>
            <p class="text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $beneficiary->account_number }}</p>
        </div>
    </div>
</div>
@endif

<!-- Additional Details -->
@if(!empty($data))
<div class="space-y-4">
    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('Additional Details')</h5>
    @foreach ($data as $val)
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0 last:pb-0">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __(@$val->name) }}
            </label>
            
            @if ($val->type == 'checkbox')
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', implode(',', $val->value)) as $item)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            {{ trim($item) }}
                        </span>
                    @endforeach
                </div>
            @elseif(@$val->type == 'file')
                @if ($val->value)
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="las la-file text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <a href="{{ route('admin.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}" 
                           class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm transition-colors">
                            @lang('Download Attachment')
                        </a>
                    </div>
                @else
                    <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="las la-file text-gray-400"></i>
                        </div>
                        <span class="text-sm">@lang('No File')</span>
                    </div>
                @endif
            @else
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                    <p class="text-gray-900 dark:text-white text-sm leading-relaxed">
                        {{ __(@$val->value) }}
                    </p>
                </div>
            @endif
        </div>
    @endforeach
</div>
@else
    <!-- No Additional Details -->
    @if($beneficiary)
    <div class="text-center py-6">
        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="las la-info-circle text-gray-400 dark:text-gray-500 text-xl"></i>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">@lang('No additional details available for this beneficiary.')</p>
    </div>
    @endif
@endif