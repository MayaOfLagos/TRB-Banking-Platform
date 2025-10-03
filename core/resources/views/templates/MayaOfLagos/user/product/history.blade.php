@extends($activeTemplate.'layouts.master')
@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-4 sm:py-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Upload History')</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">@lang('Track your product receipt submissions and rebate status')</p>
        </div>

        {{-- Summary Stats --}}
        @php
            $totalUploads = $uploads->total();
            $pendingCount = $uploads->where('status', 'pending')->count();
            $approvedCount = $uploads->where('status', 'approved')->count();
            $rejectedCount = $uploads->where('status', 'rejected')->count();
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-colors duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUploads }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">@lang('Total Uploads')</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <i class="las la-upload text-lg sm:text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-colors duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xl sm:text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">@lang('Pending Review')</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <i class="las la-clock text-lg sm:text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-colors duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400">{{ $approvedCount }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">@lang('Approved')</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <i class="las la-check-circle text-lg sm:text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-colors duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-400">{{ $rejectedCount }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm">@lang('Rejected')</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-red-100 dark:bg-red-900 rounded-full">
                        <i class="las la-times-circle text-lg sm:text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 mb-6 transition-colors duration-300">
            <div class="p-4 sm:p-6">
                <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:gap-4">
                    <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:gap-4">
                        <!-- Status Filter -->
                        <select onchange="filterByStatus(this.value)" class="w-full sm:w-auto px-3 sm:px-4 py-2 text-sm sm:text-base rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200">
                            <option value="">@lang('All Status')</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>@lang('Approved')</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>@lang('Rejected')</option>
                        </select>

                        <!-- Program Filter -->
                        <select onchange="filterByProgram(this.value)" class="w-full sm:w-auto px-3 sm:px-4 py-2 text-sm sm:text-base rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200">
                            <option value="">@lang('All Programs')</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ __($program->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-center sm:justify-start lg:justify-end">
                        <a href="{{ route('user.product.upload') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-4 sm:px-6 py-2 text-sm sm:text-base bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="las la-plus mr-2"></i>
                            @lang('New Upload')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload History -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Recent Uploads')</h2>
            </div>
            
            @if($uploads->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">@lang('Receipt')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">@lang('Program')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">@lang('Purchase')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">@lang('Rebate')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">@lang('Status')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">@lang('Date')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($uploads as $upload)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($upload->receipt_image)
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-lg object-cover cursor-pointer" 
                                                         src="{{ getImage(getFilePath('productUploads') . '/' . $upload->receipt_image) }}" 
                                                         alt="@lang('Receipt')"
                                                         onclick="showImageModal(this.src)">
                                                </div>
                                            @else
                                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                    <i class="las la-image text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($upload->store_name, 20) }}</div>
                                                @if($upload->description)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($upload->description, 30) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ __($upload->rebateProgram->name) }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ showAmount($upload->rebateProgram->default_rate) }}% @lang('rate')</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ showAmount($upload->purchase_amount) }} {{ __($general->cur_text) }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ showDateTime($upload->purchase_date, 'd M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($upload->rebateTransaction)
                                            <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                                {{ showAmount($upload->rebateTransaction->final_amount) }} {{ __($general->cur_text) }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                @lang('Base'): {{ showAmount($upload->rebateTransaction->rebate_amount) }}
                                                @if($upload->rebateTransaction->tier_multiplier > 1)
                                                    <span class="text-blue-600 dark:text-blue-400">(×{{ $upload->rebateTransaction->tier_multiplier }})</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500 dark:text-gray-400">@lang('Calculating...')</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($upload->status == 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                <i class="las la-clock mr-1"></i>
                                                @lang('Pending')
                                            </span>
                                        @elseif($upload->status == 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                <i class="las la-check-circle mr-1"></i>
                                                @lang('Approved')
                                            </span>
                                        @elseif($upload->status == 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                <i class="las la-times-circle mr-1"></i>
                                                @lang('Rejected')
                                            </span>
                                        @elseif($upload->status == 'flagged')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                <i class="las la-flag mr-1"></i>
                                                @lang('Flagged')
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ showDateTime($upload->created_at, 'd M Y') }}
                                        <div class="text-xs">{{ showDateTime($upload->created_at, 'H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            @if($upload->receipt_image)
                                                <button onclick="showImageModal('{{ getImage(getFilePath('productUploads') . '/' . $upload->receipt_image) }}')" 
                                                        class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300" 
                                                        title="@lang('View Receipt')">
                                                    <i class="las la-eye"></i>
                                                </button>
                                            @endif
                                            @if($upload->status == 'rejected' && $upload->rejection_reason)
                                                <button onclick="showRejectionReason('{{ $upload->rejection_reason }}')" 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" 
                                                        title="@lang('View Rejection Reason')">
                                                    <i class="las la-info-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($uploads as $upload)
                            <div class="p-4 sm:p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <!-- Receipt and Store Info -->
                                <div class="flex items-start space-x-4 mb-4">
                                    @if($upload->receipt_image)
                                        <div class="flex-shrink-0">
                                            <img class="h-16 w-16 rounded-lg object-cover cursor-pointer" 
                                                 src="{{ getImage(getFilePath('productUploads') . '/' . $upload->receipt_image) }}" 
                                                 alt="@lang('Receipt')"
                                                 onclick="showImageModal(this.src)">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                            <i class="las la-image text-2xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $upload->store_name }}</h3>
                                            <!-- Status Badge -->
                                            @if($upload->status == 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                    <i class="las la-clock mr-1"></i>
                                                    @lang('Pending')
                                                </span>
                                            @elseif($upload->status == 'approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    <i class="las la-check-circle mr-1"></i>
                                                    @lang('Approved')
                                                </span>
                                            @elseif($upload->status == 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    <i class="las la-times-circle mr-1"></i>
                                                    @lang('Rejected')
                                                </span>
                                            @elseif($upload->status == 'flagged')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                    <i class="las la-flag mr-1"></i>
                                                    @lang('Flagged')
                                                </span>
                                            @endif
                                        </div>
                                        @if($upload->description)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $upload->description }}</p>
                                        @endif
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ showDateTime($upload->created_at, 'd M Y, H:i') }}</p>
                                    </div>
                                </div>

                                <!-- Details Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <!-- Program -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">@lang('Program')</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ __($upload->rebateProgram->name) }}</dd>
                                        <dd class="text-xs text-gray-500 dark:text-gray-400">{{ showAmount($upload->rebateProgram->default_rate) }}% @lang('rate')</dd>
                                    </div>

                                    <!-- Purchase -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">@lang('Purchase')</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ showAmount($upload->purchase_amount) }} {{ __($general->cur_text) }}</dd>
                                        <dd class="text-xs text-gray-500 dark:text-gray-400">{{ showDateTime($upload->purchase_date, 'd M Y') }}</dd>
                                    </div>

                                    <!-- Rebate -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">@lang('Rebate')</dt>
                                        @if($upload->rebateTransaction)
                                            <dd class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                {{ showAmount($upload->rebateTransaction->final_amount) }} {{ __($general->cur_text) }}
                                            </dd>
                                            <dd class="text-xs text-gray-500 dark:text-gray-400">
                                                @lang('Base'): {{ showAmount($upload->rebateTransaction->rebate_amount) }}
                                                @if($upload->rebateTransaction->tier_multiplier > 1)
                                                    <span class="text-blue-600 dark:text-blue-400">(×{{ $upload->rebateTransaction->tier_multiplier }})</span>
                                                @endif
                                            </dd>
                                        @else
                                            <dd class="text-sm text-gray-500 dark:text-gray-400">@lang('Calculating...')</dd>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">@lang('Actions')</dt>
                                        <dd class="flex items-center gap-3">
                                            @if($upload->receipt_image)
                                                <button onclick="showImageModal('{{ getImage(getFilePath('productUploads') . '/' . $upload->receipt_image) }}')" 
                                                        class="inline-flex items-center text-sm text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 font-medium">
                                                    <i class="las la-eye mr-1"></i>
                                                    @lang('View')
                                                </button>
                                            @endif
                                            @if($upload->status == 'rejected' && $upload->rejection_reason)
                                                <button onclick="showRejectionReason('{{ $upload->rejection_reason }}')" 
                                                        class="inline-flex items-center text-sm text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                    <i class="las la-info-circle mr-1"></i>
                                                    @lang('Reason')
                                                </button>
                                            @endif
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if($uploads->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ paginateLinks($uploads) }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="mb-4">
                            <i class="las la-inbox text-6xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No uploads found')</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">@lang('You haven\'t uploaded any product receipts yet. Start earning rebates by uploading your first receipt!')</p>
                        <a href="{{ route('user.product.upload') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="las la-plus mr-2"></i>
                            @lang('Upload Receipt')
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <i class="las la-times text-2xl"></i>
        </button>
        <img id="modalImage" src="" alt="@lang('Receipt')" class="max-w-full max-h-full rounded-lg">
    </div>
</div>

<!-- Rejection Reason Modal -->
<div id="rejectionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Rejection Reason')</h3>
                <button onclick="closeRejectionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="las la-times text-xl"></i>
                </button>
            </div>
            <div id="rejectionReason" class="text-gray-700 dark:text-gray-300"></div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeRejectionModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg transition-colors duration-200">
                    @lang('Close')
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
}

function filterByProgram(programId) {
    const url = new URL(window.location);
    if (programId) {
        url.searchParams.set('program_id', programId);
    } else {
        url.searchParams.delete('program_id');
    }
    window.location.href = url.toString();
}

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

function showRejectionReason(reason) {
    document.getElementById('rejectionReason').textContent = reason;
    document.getElementById('rejectionModal').classList.remove('hidden');
    document.getElementById('rejectionModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
    document.getElementById('rejectionModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modals when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

document.getElementById('rejectionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectionModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
        closeRejectionModal();
    }
});
</script>

@endsection