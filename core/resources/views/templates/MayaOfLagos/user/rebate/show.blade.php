@extends($activeTemplate.'layouts.master')
@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Rebate Details')</h1>
                    <p class="text-gray-600 dark:text-gray-400">@lang('Complete information about your rebate transaction')</p>
                </div>
            </div>
        </div>

        {{-- Transaction Status Banner --}}
        @php
            $statusConfig = match($rebate->status) {
                'approved' => [
                    'bg' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
                    'icon' => 'las la-check-circle text-green-600 dark:text-green-400',
                    'text' => 'text-green-800 dark:text-green-300',
                    'badge' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                ],
                'pending' => [
                    'bg' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800',
                    'icon' => 'las la-clock text-yellow-600 dark:text-yellow-400',
                    'text' => 'text-yellow-800 dark:text-yellow-300',
                    'badge' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                ],
                'rejected' => [
                    'bg' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
                    'icon' => 'las la-times-circle text-red-600 dark:text-red-400',
                    'text' => 'text-red-800 dark:text-red-300',
                    'badge' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                ],
                default => [
                    'bg' => 'bg-gray-50 dark:bg-gray-900/20 border-gray-200 dark:border-gray-800',
                    'icon' => 'las la-question-circle text-gray-600 dark:text-gray-400',
                    'text' => 'text-gray-800 dark:text-gray-300',
                    'badge' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
                ]
            };
        @endphp

        <div class="rounded-xl border-2 {{ $statusConfig['bg'] }} p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="{{ $statusConfig['icon'] }} text-3xl mr-4"></i>
                    <div>
                        <h3 class="text-xl font-semibold {{ $statusConfig['text'] }} mb-1">
                            @lang('Transaction') #{{ $rebate->id }}
                        </h3>
                        <p class="text-sm {{ $statusConfig['text'] }} opacity-80">
                            @lang('Submitted on') {{ showDateTime($rebate->created_at, 'd M Y \a\t h:i A') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusConfig['badge'] }}">
                        {{ __(ucfirst($rebate->status)) }}
                    </span>
                    @if($rebate->requires_review)
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 flex items-center justify-end mt-2">
                            <i class="las la-flag mr-1"></i> @lang('Under Review')
                        </p>
                    @endif
                </div>
            </div>

            @if($rebate->admin_feedback)
                <div class="mt-4 pt-4 border-t {{ str_contains($statusConfig['bg'], 'border-') ? '' : 'border-gray-200 dark:border-gray-700' }}">
                    <h4 class="font-medium {{ $statusConfig['text'] }} mb-2">@lang('Admin Feedback'):</h4>
                    <p class="text-sm {{ $statusConfig['text'] }} opacity-90">{{ $rebate->admin_feedback }}</p>
                </div>
            @endif
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Rebate Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <i class="las la-money-bill-wave text-2xl text-purple-600 dark:text-purple-400 mr-3"></i>
                    @lang('Rebate Information')
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Program'):</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $rebate->rebateProgram?->name ?? 'Unknown Program' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Category'):</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $rebate->rebateCategory?->name ?? 'Uncategorized' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Type'):</span>
                        @php
                            $typeClass = match($rebate->transaction_type) {
                                'product_upload' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'referral' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                default => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $typeClass }}">
                            {{ __(ucwords(str_replace('_', ' ', $rebate->transaction_type))) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Base Amount'):</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ showUserAmount($rebate->original_amount, auth()->user()) }}
                        </span>
                    </div>
                    
                    @if($rebate->tier_multiplier > 1)
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Tier Multiplier'):</span>
                        <span class="text-sm font-semibold text-green-600 dark:text-green-400 flex items-center">
                            <i class="las la-star mr-1"></i>
                            {{ $rebate->tier_multiplier }}x @lang('bonus')
                        </span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center py-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg px-4">
                        <span class="text-lg font-semibold text-purple-700 dark:text-purple-300">@lang('Final Amount'):</span>
                        <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ showUserAmount($rebate->rebate_amount, auth()->user()) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Timeline & Product Details --}}
            <div class="space-y-6">
                {{-- Product Upload Details (if applicable) --}}
                @if($rebate->product_upload)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="las la-upload text-2xl text-blue-600 dark:text-blue-400 mr-3"></i>
                        @lang('Product Upload Details')
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Product Name'):</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $rebate->product_upload->product_name ?: ($rebate->product_upload->description ?: ($rebate->product_upload->store_name ? 'Purchase from ' . $rebate->product_upload->store_name : 'Product Purchase')) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Purchase Amount'):</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ showUserAmount($rebate->product_upload->purchase_amount, auth()->user()) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang('Purchase Date'):</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ showDateTime($rebate->product_upload->purchase_date, 'd M Y') }}
                            </span>
                        </div>
                        
                        @if($rebate->product_upload->receipt_image)
                        <div class="py-3">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block mb-2">@lang('Receipt'):</span>
                            <a href="{{ getImage(getFilePath('productUploads') . '/' . $rebate->product_upload->receipt_image) }}" 
                               target="_blank" class="inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-lg text-sm transition-colors duration-200">
                                <i class="las la-external-link-alt mr-2"></i>
                                @lang('View Receipt')
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Transaction Timeline --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="las la-history text-2xl text-green-600 dark:text-green-400 mr-3"></i>
                        @lang('Transaction Timeline')
                    </h2>
                    
                    <div class="relative">
                        {{-- Timeline line --}}
                        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-600"></div>
                        
                        <div class="space-y-6">
                            {{-- Submitted --}}
                            <div class="relative flex items-start">
                                <div class="absolute left-4 w-4 h-4 bg-blue-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                <div class="ml-12">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">@lang('Transaction Submitted')</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ showDateTime($rebate->created_at, 'd M Y \a\t h:i A') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">@lang('Your rebate claim has been received and is being processed.')</p>
                                </div>
                            </div>
                            
                            {{-- Processing --}}
                            @if($rebate->status !== 'pending')
                            <div class="relative flex items-start">
                                <div class="absolute left-4 w-4 h-4 {{ $rebate->status === 'approved' ? 'bg-green-500' : 'bg-red-500' }} rounded-full border-2 border-white dark:border-gray-800"></div>
                                <div class="ml-12">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        @lang('Transaction') {{ __(ucfirst($rebate->status)) }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $rebate->approved_at ? showDateTime($rebate->approved_at, 'd M Y \a\t h:i A') : showDateTime($rebate->updated_at, 'd M Y \a\t h:i A') }}
                                    </p>
                                    @if($rebate->admin_feedback)
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $rebate->admin_feedback }}</p>
                                    @else
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                            @if($rebate->status === 'approved')
                                                @lang('Your rebate has been approved and processed.')
                                            @else
                                                @lang('Your rebate claim has been reviewed and rejected.')
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="relative flex items-start">
                                <div class="absolute left-4 w-4 h-4 bg-yellow-500 rounded-full border-2 border-white dark:border-gray-800 animate-pulse"></div>
                                <div class="ml-12">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">@lang('Under Review')</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Processing...')</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">@lang('Our team is reviewing your rebate claim. You will be notified once processed.')</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('user.rebate.history') }}" class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="las la-list mr-2"></i>
                @lang('View All Rebates')
            </a>
            
            @if($rebate->status === 'approved')
            <button class="inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-200" onclick="window.print()">
                <i class="las la-print mr-2"></i>
                @lang('Print Receipt')
            </button>
            @endif
            
            <a href="{{ route('user.rebate.programs') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-lg transition-colors duration-200">
                <i class="las la-plus mr-2"></i>
                @lang('Submit New Rebate')
            </a>
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>
@endpush