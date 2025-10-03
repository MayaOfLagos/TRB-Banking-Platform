@extends($activeTemplate . 'user.fdr.layout')
@section('fdr-content')

{{-- FDR Preview Section - All variables correctly defined --}}
<!-- FDR Preview Section -->
<div class="space-y-6">
    
    <!-- Preview Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                    <i class="las la-eye text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold">@lang('FDR Application Preview')</h2>
                <p class="text-blue-100 mt-2">@lang('Please review your FDR application details before confirmation')</p>
            </div>
        </div>
    </div>

    <!-- Application Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- FDR Plan Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="las la-chart-line mr-2 text-blue-600"></i>
                    @lang('Plan Information')
                </h3>
            </div>
            <div class="p-6 space-y-4">
                
                <!-- Plan Name -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Plan Name')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ __($plan->name) }}</span>
                </div>
                
                <!-- Interest Rate -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Interest Rate')</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">{{ $plan->interest_rate }}% @lang('per annum')</span>
                </div>
                
                <!-- Lock-in Period -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Lock-in Period')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $plan->locked_days }} @lang('Days')</span>
                </div>
                
                <!-- Installment Interval -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Installment Interval')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $plan->installment_interval }} @lang('Days')</span>
                </div>
                
                <!-- Total Installments -->
                @php
                    $totalInstallments = max(1, intval($plan->locked_days / max(1, $plan->installment_interval)));
                @endphp
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Total Installments')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $totalInstallments }}</span>
                </div>
            </div>
        </div>
        
        <!-- Investment Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="las la-coins mr-2 text-green-600"></i>
                    @lang('Investment Details')
                </h3>
            </div>
            <div class="p-6 space-y-4">
                
                @php
                    $investmentAmount = (float) $amount;
                    $profitPerInterval = ($investmentAmount * $plan->interest_rate) / 100;
                    $totalInstallments = max(1, intval($plan->locked_days / max(1, $plan->installment_interval)));
                    $totalProfit = $profitPerInterval * $totalInstallments;
                    $maturityValue = $investmentAmount + $totalProfit;
                    $maturityDate = now()->addDays($plan->locked_days);
                @endphp
                
                <!-- Investment Amount -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Investment Amount')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ showUserAmount($investmentAmount, auth()->user()) }}</span>
                </div>
                
                <!-- Expected Total Profit -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Expected Total Profit')</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">+{{ showUserAmount($totalProfit, auth()->user()) }}</span>
                </div>
                
                <!-- Profit per Installment -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Profit per Installment')</span>
                    <span class="font-semibold text-blue-600 dark:text-blue-400">+{{ showUserAmount($profitPerInterval, auth()->user()) }}</span>
                </div>
                
                <!-- Maturity Value -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Maturity Value')</span>
                    <span class="font-semibold text-purple-600 dark:text-purple-400">{{ showUserAmount($maturityValue, auth()->user()) }}</span>
                </div>
                
                <!-- Maturity Date -->
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-600 dark:text-gray-400">@lang('Maturity Date')</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $maturityDate->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculation Breakdown -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="las la-calculator mr-2 text-purple-600"></i>
                @lang('Calculation Breakdown')
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Profit per Interval -->
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ showUserAmount($profitPerInterval, auth()->user()) }}
                    </div>
                    <div class="text-sm text-blue-700 dark:text-blue-300">@lang('Profit per') {{ $plan->installment_interval }} @lang('Days')</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ showUserAmount($investmentAmount, auth()->user()) }} × {{ $plan->interest_rate }}%
                    </div>
                </div>
                
                <!-- Total Installments -->
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $totalInstallments }}
                    </div>
                    <div class="text-sm text-green-700 dark:text-green-300">@lang('Total Installments')</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $plan->locked_days }} ÷ {{ $plan->installment_interval }}
                    </div>
                </div>
                
                <!-- Total Days -->
                <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ $plan->locked_days }}
                    </div>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300">@lang('Total Days')</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">@lang('Lock-in Period')</div>
                </div>
                
                <!-- Total Profit -->
                <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ showUserAmount($totalProfit, auth()->user()) }}
                    </div>
                    <div class="text-sm text-purple-700 dark:text-purple-300">@lang('Total Profit')</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ showUserAmount($profitPerInterval, auth()->user()) }} × {{ $totalInstallments }}
                    </div>
                </div>
            </div>
            
            <!-- Formula Display -->
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">@lang('Interest Calculation Formula')</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <div class="flex items-center justify-between">
                        <span>@lang('Principal Amount'):</span>
                        <span class="font-mono">{{ showUserAmount($investmentAmount, auth()->user()) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>@lang('Interest Rate'):</span>
                        <span class="font-mono">{{ $plan->interest_rate }}% @lang('per interval')</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>@lang('Interval Period'):</span>
                        <span class="font-mono">{{ $plan->installment_interval }} @lang('days')</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>@lang('Total Installments'):</span>
                        <span class="font-mono">{{ $totalInstallments }}</span>
                    </div>
                    <hr class="border-gray-300 dark:border-gray-600">
                    <div class="flex items-center justify-between font-semibold">
                        <span>@lang('Total Interest'):</span>
                        <span class="font-mono text-green-600 dark:text-green-400">{{ showUserAmount($totalProfit, auth()->user()) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Terms & Conditions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="las la-exclamation-triangle mr-2 text-yellow-600"></i>
                @lang('Important Terms & Conditions')
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-start">
                    <i class="las la-check-circle text-green-500 mr-3 mt-0.5"></i>
                    <span>@lang('Your investment will be locked for the entire duration of {{days}} days.', ['days' => $plan->locked_days])</span>
                </div>
                
                <div class="flex items-start">
                    <i class="las la-check-circle text-green-500 mr-3 mt-0.5"></i>
                    <span>@lang('Profit will be credited to your account every {{interval}} days.', ['interval' => $plan->installment_interval])</span>
                </div>
                
                <div class="flex items-start">
                    <i class="las la-check-circle text-green-500 mr-3 mt-0.5"></i>
                    <span>@lang('Early withdrawal is not permitted before the maturity date.')</span>
                </div>
                
                <div class="flex items-start">
                    <i class="las la-check-circle text-green-500 mr-3 mt-0.5"></i>
                    <span>@lang('Interest rates are fixed and will not change during the FDR period.')</span>
                </div>
                
                <div class="flex items-start">
                    <i class="las la-check-circle text-green-500 mr-3 mt-0.5"></i>
                    <span>@lang('Upon maturity, both principal and final profit will be credited to your account.')</span>
                </div>
                
                <div class="flex items-start">
                    <i class="las la-exclamation-circle text-yellow-500 mr-3 mt-0.5"></i>
                    <span>@lang('Please ensure you have sufficient balance before confirming this application.')</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Balance Check -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">@lang('Account Balance Verification')</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">@lang('Ensure sufficient balance for this investment')</p>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ showUserAmount(auth()->user()->balance, auth()->user()) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">@lang('Available Balance')</div>
                </div>
            </div>
            
            @if(auth()->user()->balance >= $investmentAmount)
                <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex items-center">
                        <i class="las la-check-circle text-green-500 mr-2"></i>
                        <span class="text-sm text-green-700 dark:text-green-300">@lang('Sufficient balance available for this investment')</span>
                    </div>
                </div>
            @else
                <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-center">
                        <i class="las la-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-sm text-red-700 dark:text-red-300">
                            @lang('Insufficient balance. You need {{amount}} {{currency}} more.', [
                                'amount' => showAmount($investmentAmount - auth()->user()->balance),
                                'currency' => gs()->cur_text
                            ])
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Confirmation Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Ready to Invest?')</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Please review all details carefully before confirming your FDR application.')</p>
            </div>
            
            <div class="flex space-x-3">
                <a href="{{ route('user.fdr.plans') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <i class="las la-arrow-left mr-2"></i>
                    @lang('Back to Plans')
                </a>
                
                @if(auth()->user()->balance >= $investmentAmount)
                    <form method="POST" action="{{ route('user.fdr.apply.confirm', $verificationId) }}" style="display: inline;" class="confirm-fdr-form">
                        @csrf
                        <button type="submit" 
                                class="confirm-fdr-btn inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 disabled:hover:scale-100">
                            <span class="submit-text flex items-center">
                                <i class="las la-check-circle mr-2"></i>
                                @lang('Confirm FDR Application')
                            </span>
                            <span class="loading-text hidden flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @lang('Processing...')
                            </span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('user.deposit') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <i class="las la-plus-circle mr-2"></i>
                        @lang('Add Funds')
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
// Add subtle animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on scroll
    const cards = document.querySelectorAll('.bg-white, .bg-gray-800');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});

// Form submission with loading state
document.querySelector('.confirm-fdr-form')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('.confirm-fdr-btn');
    const submitText = submitBtn.querySelector('.submit-text');
    const loadingText = submitBtn.querySelector('.loading-text');
    
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    loadingText.classList.remove('hidden');
    
    // Reset if form validation fails after 10 seconds
    setTimeout(() => {
        if (submitBtn.disabled) {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            loadingText.classList.add('hidden');
        }
    }, 10000);
});

// Copy FDR details function
function copyDetails() {
    const details = `
FDR Application Preview
Plan: {{ $plan->name }}
Investment: {{ showAmount($investmentAmount) }} {{ gs()->cur_text }}
Interest Rate: {{ $plan->interest_rate }}%
Lock-in Period: {{ $plan->locked_days }} days
Expected Profit: {{ showAmount($totalProfit) }} {{ gs()->cur_text }}
Maturity Value: {{ showAmount($maturityValue) }} {{ gs()->cur_text }}
Maturity Date: {{ $maturityDate->format('d M Y') }}
`;
    
    navigator.clipboard.writeText(details.trim()).then(() => {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg z-50';
        toast.textContent = '@lang("Details copied to clipboard!")';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    });
}

// Print preview
function printPreview() {
    window.print();
}
</script>

<style>
@media print {
    .bg-gradient-to-r {
        background: #3b82f6 !important;
        color: white !important;
    }
    
    .shadow-lg {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
    
    .dark\:bg-gray-800 {
        background: white !important;
    }
    
    .dark\:text-white {
        color: black !important;
    }
    
    .dark\:text-gray-400 {
        color: #6b7280 !important;
    }
}
</style>
@endpush