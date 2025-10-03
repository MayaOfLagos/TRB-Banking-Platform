@extends($activeTemplate . 'layouts.master')
@section('content')
@php use App\Constants\Status; @endphp

<!-- KYC Info Page -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
                    <i class="las la-user-shield text-3xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('KYC Verification Status')</h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">@lang('Track your identity verification progress and manage your documents.')</p>
            </div>
        </div>

        <!-- Status Overview Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
            @if($user->kv == Status::KYC_UNVERIFIED)
                <!-- Unverified Status -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 lg:px-8 py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="las la-exclamation-triangle text-3xl text-white"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-semibold text-white">@lang('Verification Required')</h2>
                            <p class="text-amber-100 text-sm mt-1">@lang('Complete your KYC verification to access all features')</p>
                        </div>
                        <div class="hidden sm:block">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                                @lang('Action Required')
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 lg:p-8">
                    <div class="text-center">
                        <i class="las la-file-upload text-6xl text-amber-500 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('Start Your Verification')</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">@lang('Begin the KYC process to unlock all platform features and secure your account.')</p>
                        
                        <a href="{{ route('user.kyc.form') }}" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="las la-plus mr-2"></i>
                            @lang('Submit KYC Application')
                        </a>
                    </div>
                </div>

            @elseif($user->kv == Status::KYC_PENDING)
                <!-- Pending Status -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 lg:px-8 py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="las la-clock text-3xl text-white"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-semibold text-white">@lang('Under Review')</h2>
                            <p class="text-blue-100 text-sm mt-1">@lang('Your KYC application is being reviewed by our team')</p>
                        </div>
                        <div class="hidden sm:block">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                                @lang('In Progress')
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 lg:p-8">
                    <div class="text-center mb-8">
                        <div class="inline-block relative">
                            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-4 mx-auto">
                                <i class="las la-hourglass-half text-3xl text-blue-600 dark:text-blue-400 animate-pulse"></i>
                            </div>
                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="las la-check text-xs text-white"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('Review in Progress')</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">@lang('Thank you for submitting your KYC application. Our team is reviewing your documents and will get back to you soon.')</p>
                        
                        <!-- Timeline -->
                        <div class="max-w-md mx-auto">
                            <div class="relative">
                                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-600"></div>
                                
                                <div class="relative flex items-start pb-4">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <i class="las la-check text-white text-sm"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">@lang('Application Submitted')</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->kyc_data ? showDateTime($user->created_at) : 'N/A' }}</p>
                                    </div>
                                </div>
                                
                                <div class="relative flex items-start pb-4">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center animate-pulse">
                                        <i class="las la-search text-white text-sm"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">@lang('Under Review')</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Currently processing...')</p>
                                    </div>
                                </div>
                                
                                <div class="relative flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                        <i class="las la-flag text-white text-sm"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Verification Complete')</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">@lang('Estimated: 1-3 business days')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($user->kv == Status::KYC_VERIFIED)
                <!-- Verified Status -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 lg:px-8 py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="las la-check-circle text-3xl text-white"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-semibold text-white">@lang('Verification Complete')</h2>
                            <p class="text-green-100 text-sm mt-1">@lang('Your identity has been successfully verified')</p>
                        </div>
                        <div class="hidden sm:block">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                                @lang('Verified')
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 lg:p-8">
                    <div class="text-center">
                        <div class="inline-block relative mb-4">
                            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                <i class="las la-shield-alt text-3xl text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-600 rounded-full flex items-center justify-center">
                                <i class="las la-check text-xs text-white"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('Account Verified')</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">@lang('Congratulations! Your KYC verification is complete. You now have access to all platform features.')</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-lg mx-auto">
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                <i class="las la-wallet text-2xl text-green-600 dark:text-green-400 mb-2"></i>
                                <p class="text-sm font-medium text-green-900 dark:text-green-300">@lang('Full Access')</p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                <i class="las la-exchange-alt text-2xl text-green-600 dark:text-green-400 mb-2"></i>
                                <p class="text-sm font-medium text-green-900 dark:text-green-300">@lang('All Transactions')</p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                <i class="las la-shield-alt text-2xl text-green-600 dark:text-green-400 mb-2"></i>
                                <p class="text-sm font-medium text-green-900 dark:text-green-300">@lang('Enhanced Security')</p>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- Rejected Status -->
                <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 lg:px-8 py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="las la-times-circle text-3xl text-white"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h2 class="text-xl font-semibold text-white">@lang('Verification Rejected')</h2>
                            <p class="text-red-100 text-sm mt-1">@lang('Your KYC application requires attention')</p>
                        </div>
                        <div class="hidden sm:block">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                                @lang('Action Required')
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 lg:p-8">
                    @if($user->kyc_rejection_reason)
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 mb-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="las la-exclamation-circle text-2xl text-red-600 dark:text-red-400"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-red-900 dark:text-red-300 mb-2">@lang('Rejection Reason')</h4>
                                    <p class="text-red-800 dark:text-red-200">{{ $user->kyc_rejection_reason }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="text-center">
                        <i class="las la-redo-alt text-6xl text-red-500 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('Resubmission Required')</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">@lang('Please review the rejection reason and submit your KYC application again with the required corrections.')</p>
                        
                        <a href="{{ route('user.kyc.form') }}" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="las la-redo-alt mr-2"></i>
                            @lang('Resubmit Application')
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- KYC Data Display -->
        @if($user->kyc_data)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            <!-- Section Header -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 lg:px-8 py-4 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="las la-file-alt text-xl text-gray-600 dark:text-gray-400 mr-3"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Submitted Information')</h3>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        @lang('Submitted on') {{ $user->kyc_data ? showDateTime($user->created_at) : 'N/A' }}
                    </span>
                </div>
            </div>

            <!-- KYC Data Content -->
            <div class="p-6 lg:p-8" x-data="{ showData: false }">
                
                <!-- Toggle Button -->
                <div class="text-center mb-6">
                    <button @click="showData = !showData" 
                            class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all duration-200">
                        <i class="las la-eye mr-2" :class="{ 'la-eye': !showData, 'la-eye-slash': showData }"></i>
                        <span x-text="showData ? '@lang('Hide Information')' : '@lang('View Submitted Information')'"></span>
                    </button>
                </div>

                <!-- Data Display -->
                <div x-show="showData" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                    
                    @if($user->kyc_data)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($user->kyc_data as $val)
                                @continue(!$val->value)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                            {{ __($val->name) }}
                                        </label>
                                        
                                        @if($val->type == 'checkbox')
                                            <!-- Checkbox field -->
                                            <div class="space-y-1">
                                                @foreach(explode(',', $val->value) as $item)
                                                    <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs rounded-full">
                                                        {{ trim($item) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @elseif($val->type == 'file')
                                            <!-- File field -->
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-1">
                                                    <span class="text-sm text-gray-900 dark:text-white font-mono bg-white dark:bg-gray-800 px-3 py-2 rounded border">
                                                        {{ $val->value }}
                                                    </span>
                                                </div>
                                                <a href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}" 
                                                   class="inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-400 text-sm font-medium rounded-lg transition-colors">
                                                    <i class="las la-download mr-1"></i>
                                                    @lang('Download')
                                                </a>
                                            </div>
                                        @else
                                            <!-- Text data -->
                                            <span class="text-gray-900 dark:text-white font-medium">
                                                {{ __($val->value) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="las la-inbox text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">@lang('No data available')</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Help Section -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full mb-4">
                    <i class="las la-question-circle text-2xl text-purple-600 dark:text-purple-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('Need Help?')</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">@lang('If you have questions about the KYC process or need assistance, our support team is here to help.')</p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('ticket.open') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                        <i class="las la-ticket-alt mr-2"></i>
                        @lang('Open Support Ticket')
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                        <i class="las la-envelope mr-2"></i>
                        @lang('Contact Us')
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
/* KYC Info Page Enhancements */
.status-animation {
    animation: statusPulse 2s ease-in-out infinite;
}

@keyframes statusPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Timeline enhancements */
.timeline-item {
    transition: all 0.3s ease;
}

.timeline-item:hover {
    transform: translateX(4px);
}

/* File download button animations */
.download-btn {
    transition: all 0.3s ease;
}

.download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Data grid enhancements */
.data-grid-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.data-grid-item:hover {
    border-color: rgba(59, 130, 246, 0.3);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Status badge animations */
.status-badge {
    animation: fadeInScale 0.5s ease-out;
}

@keyframes fadeInScale {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Mobile responsive enhancements */
@media (max-width: 768px) {
    .kyc-header {
        padding: 1rem;
    }
    
    .status-card {
        margin: 0 1rem;
    }
    
    .timeline {
        padding-left: 1rem;
    }
    
    .data-grid {
        grid-template-columns: 1fr;
    }
}

/* Dark mode file preview */
@media (prefers-color-scheme: dark) {
    .file-preview {
        background-color: rgba(31, 41, 55, 0.5);
        border-color: rgba(75, 85, 99, 0.3);
    }
}

/* Loading states */
.loading-shimmer {
    background: linear-gradient(90deg, 
        rgba(255, 255, 255, 0) 0%, 
        rgba(255, 255, 255, 0.4) 50%, 
        rgba(255, 255, 255, 0) 100%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}
</style>
@endpush