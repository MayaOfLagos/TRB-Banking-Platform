@extends($activeTemplate.'layouts.master')
@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-1 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Upload Details')</h1>
                    <p class="text-gray-600 dark:text-gray-400">@lang('View your product receipt submission details')</p>
                </div>
                <a href="{{ route('user.product.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="las la-arrow-left mr-2"></i>
                    @lang('Back to History')
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Upload Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Upload Information')</h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Status')</label>
                        <div>
                            @if($upload->status == 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                    <i class="las la-clock mr-1"></i>
                                    @lang('Pending Review')
                                </span>
                            @elseif($upload->status == 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    <i class="las la-check-circle mr-1"></i>
                                    @lang('Approved')
                                </span>
                            @elseif($upload->status == 'rejected')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                    <i class="las la-times-circle mr-1"></i>
                                    @lang('Rejected')
                                </span>
                            @elseif($upload->status == 'flagged')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                    <i class="las la-flag mr-1"></i>
                                    @lang('Flagged for Review')
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Program -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Rebate Program')</label>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ __($upload->rebateProgram->name) }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __($upload->rebateProgram->description) }}</p>
                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>@lang('Rate'): <strong>{{ showAmount($upload->rebateProgram->default_rate) }}%</strong></span>
                                @if($upload->rebateProgram->maximum_rebate)
                                    <span>@lang('Max'): <strong>{{ showAmount($upload->rebateProgram->maximum_rebate) }} {{ __($general->cur_text) }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Store Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Store Name')</label>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $upload->store_name }}</p>
                    </div>

                    <!-- Purchase Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Purchase Amount')</label>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($upload->purchase_amount) }} {{ __($general->cur_text) }}</p>
                    </div>

                    <!-- Purchase Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Purchase Date')</label>
                        <p class="text-gray-900 dark:text-white">{{ showDateTime($upload->purchase_date, 'd M Y') }}</p>
                    </div>

                    <!-- Description -->
                    @if($upload->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Description')</label>
                            <p class="text-gray-900 dark:text-white">{{ $upload->description }}</p>
                        </div>
                    @endif

                    <!-- Submission Details -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">@lang('Submission Details')</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">@lang('Submitted')</span>
                                <p class="text-gray-900 dark:text-white font-medium">{{ showDateTime($upload->created_at) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">@lang('Updated')</span>
                                <p class="text-gray-900 dark:text-white font-medium">{{ showDateTime($upload->updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt and Rebate Information -->
            <div class="space-y-8">
                <!-- Receipt Image -->
                @if($upload->receipt_image)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Receipt Image')</h2>
                        </div>
                        <div class="p-6">
                            <div class="text-center">
                                <img src="{{ getImage(getFilePath('productUploads') . '/' . $upload->receipt_image) }}" 
                                     alt="@lang('Receipt')" 
                                     class="max-w-full h-auto rounded-lg shadow-md cursor-pointer hover:shadow-lg transition-shadow duration-300"
                                     onclick="showImageModal(this.src)">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">@lang('Click to view full size')</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Rebate Information -->
                @if($upload->rebateTransaction)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Rebate Details')</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Base Rebate -->
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">@lang('Base Rebate')</span>
                                <span class="text-gray-900 dark:text-white font-semibold">{{ showAmount($upload->rebateTransaction->rebate_amount) }} {{ __($general->cur_text) }}</span>
                            </div>

                            <!-- Tier Multiplier -->
                            @if($upload->rebateTransaction->tier_multiplier > 1)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">@lang('Tier Multiplier')</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-semibold">×{{ $upload->rebateTransaction->tier_multiplier }}</span>
                                </div>
                            @endif

                            <!-- Final Amount -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Final Rebate')</span>
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ showAmount($upload->rebateTransaction->final_amount) }} {{ __($general->cur_text) }}</span>
                            </div>

                            <!-- Rebate Status -->
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">@lang('Rebate Status')</span>
                                <div>
                                    @if($upload->rebateTransaction->status == 'pending')
                                        <span class="text-yellow-600 dark:text-yellow-400 font-medium">@lang('Processing')</span>
                                    @elseif($upload->rebateTransaction->status == 'approved')
                                        <span class="text-green-600 dark:text-green-400 font-medium">@lang('Credited')</span>
                                    @elseif($upload->rebateTransaction->status == 'rejected')
                                        <span class="text-red-600 dark:text-red-400 font-medium">@lang('Denied')</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Rejection Reason -->
                @if($upload->status == 'rejected' && $upload->rejection_reason)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="las la-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">@lang('Rejection Reason')</h3>
                                <p class="mt-2 text-sm text-red-700 dark:text-red-300">{{ $upload->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4">
            @if($upload->status == 'rejected')
                <a href="{{ route('user.product.upload') }}" class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="las la-plus mr-2"></i>
                    @lang('Submit New Receipt')
                </a>
            @endif
            
            <a href="{{ route('user.product.history') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i class="las la-list mr-2"></i>
                @lang('View All Uploads')
            </a>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-5xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2">
            <i class="las la-times text-xl"></i>
        </button>
        <img id="modalImage" src="" alt="@lang('Receipt')" class="max-w-full max-h-full rounded-lg shadow-2xl">
    </div>
</div>

<script>
function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>

@endsection