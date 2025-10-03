@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
    <div class="mx-auto px-0 sm:px-0 lg:px-0">
        <!-- Navigation Pills -->
        <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
            <div class="flex gap-3">
                <a href="{{ route('user.loan.list') }}" 
                   class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ menuActive('user.loan.list') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                    <i class="las la-list mr-2"></i>@lang('My Loan List')
                </a>
                <a href="{{ route('user.loan.plans') }}" 
                   class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ menuActive('user.loan.plans') ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300' : '' }}">
                    <i class="las la-clipboard-list mr-2"></i>@lang('Loan Plans')
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mr-6">
                            <i class="las la-file-invoice-dollar text-3xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-2">@lang('Loan Details')</h1>
                            <p class="text-blue-100">@lang('Loan Number'): #{{ $loan->loan_number }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        @php
                            $statusClass = '';
                            $statusText = '';
                            switch($loan->status) {
                                case 0: // LOAN_PENDING
                                    $statusClass = 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                                    $statusText = 'Pending';
                                    break;
                                case 1: // LOAN_RUNNING
                                    $statusClass = 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700';
                                    $statusText = 'Running';
                                    break;
                                case 2: // LOAN_PAID
                                    $statusClass = 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700';
                                    $statusText = 'Paid';
                                    break;
                                case 3: // LOAN_REJECTED
                                    $statusClass = 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700';
                                    $statusText = 'Rejected';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';
                                    $statusText = 'Unknown';
                            }
                        @endphp
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border {{ $statusClass }}">
                            <i class="las la-circle text-xs mr-2"></i>
                            @lang($statusText)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Loan Information -->
            <div class="p-8">
                @include($activeTemplate . 'partials.loan_details_section')
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 gap-3">
                    <a href="{{ route('user.loan.details', $loan->loan_number) }}?download" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl">
                        <i class="las la-file-download mr-2"></i>
                        @lang('Download')
                    </a>
                    
                    @if($loan->nextInstallment)
                    <a href="{{ route('user.loan.instalment.logs', $loan->loan_number) }}" 
                       class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl">
                        <i class="las la-calendar-check mr-2"></i>
                        @lang('View Installments')
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('bottom-menu')
    <li><a href="{{ route('user.loan.plans') }}">@lang('Loan Plans')</a></li>
    <li><a href="{{ route('user.loan.list') }}" class="active">@lang('My Loan List')</a></li>
@endpush

@push('style')
<style>
/* Progress Circle Enhancements */
.progress-circle-container {
    position: relative;
    display: inline-block;
}

.progress-circle-container svg {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.progress-circle-container:hover svg {
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
    transform: scale(1.02);
}

/* Animate progress circle on load */
@keyframes drawCircle {
    from {
        stroke-dashoffset: 283; /* Full circumference for radius 45 */
    }
    to {
        stroke-dashoffset: var(--final-offset);
    }
}

/* Progress indicator dots pulse animation */
@keyframes dotPulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0.8;
    }
}

.progress-dot-paid {
    animation: dotPulse 2s ease-in-out infinite;
    animation-delay: calc(var(--dot-index) * 0.1s);
}

/* Progress text fade-in animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.progress-text {
    animation: fadeInUp 0.6s ease-out 0.3s both;
}

/* Gradient progress colors */
.progress-starting { @apply text-red-500; }
.progress-low { @apply text-amber-500; }
.progress-medium { @apply text-purple-500; }
.progress-high { @apply text-blue-500; }
.progress-complete { @apply text-green-500; }

/* Completion celebration effect */
@keyframes celebrate {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.05); }
    50% { transform: scale(1.1); }
    75% { transform: scale(1.05); }
}

.progress-complete-glow {
    animation: celebrate 0.6s ease-in-out;
}

/* Dark mode specific adjustments */
@media (prefers-color-scheme: dark) {
    .progress-circle-container svg {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
    }
    
    .progress-circle-container:hover svg {
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .progress-circle-container {
        transform: scale(0.9);
    }
}
</style>
@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress circle on page load
    const progressCircle = document.querySelector('.progress-circle-container svg circle[stroke-linecap="round"]');
    if (progressCircle) {
        // Get the final offset from the CSS custom property
        const finalOffset = getComputedStyle(progressCircle).getPropertyValue('--final-offset');
        
        // Start from full circle (no progress)
        progressCircle.style.strokeDashoffset = '283';
        
        // Animate to the actual progress after a short delay
        setTimeout(() => {
            progressCircle.style.strokeDashoffset = finalOffset;
        }, 300);
    }
    
    // Add hover effects for progress dots
    const progressDots = document.querySelectorAll('.progress-dot-paid');
    progressDots.forEach((dot, index) => {
        dot.addEventListener('mouseenter', function() {
            this.style.transform = 'translate(-50%, -50%) scale(1.5)';
            this.style.boxShadow = '0 0 10px rgba(34, 197, 94, 0.5)';
        });
        
        dot.addEventListener('mouseleave', function() {
            this.style.transform = 'translate(-50%, -50%) scale(1)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Add intersection observer for scroll-triggered animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, { threshold: 0.5 });
    
    const progressContainer = document.querySelector('.progress-circle-container');
    if (progressContainer) {
        observer.observe(progressContainer);
    }
});
</script>
@endpush