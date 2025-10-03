<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Basic Loan Information -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="las la-info-circle text-blue-500 mr-2"></i>
            @lang('Loan Information')
        </h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Plan')</span>
                <span class="text-gray-900 dark:text-white font-semibold">{{ $loan->plan->name }}</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Interest Rate')</span>
                <span class="text-gray-900 dark:text-white font-semibold">{{ getAmount($loan->interestRate()) }}%</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Installment Interval')</span>
                <span class="text-gray-900 dark:text-white font-semibold">
                    {{ $loan->plan->installment_interval }} {{ __(Str::plural('Day', $loan->plan->installment_interval)) }}
                </span>
            </div>
            
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Applied At')</span>
                <span class="text-gray-900 dark:text-white font-semibold">{{ showDateTime($loan->created_at) }}</span>
            </div>
            
            @if($loan->approved_at)
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Approved At')</span>
                <span class="text-green-600 dark:text-green-400 font-semibold">{{ showDateTime($loan->approved_at) }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Financial Details -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="las la-dollar-sign text-green-500 mr-2"></i>
            @lang('Financial Details')
        </h3>
        <div class="space-y-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex justify-between items-center">
                    <span class="text-blue-800 dark:text-blue-300 font-medium">@lang('Loan Amount')</span>
                    <span class="text-blue-900 dark:text-blue-100 font-bold text-lg">{{ showAmount($loan->amount) }}</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Per Installment')</span>
                <span class="text-gray-900 dark:text-white font-bold">{{ showAmount($loan->per_installment) }}</span>
            </div>
            
            @if($loan->paid_amount)
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                <div class="flex justify-between items-center">
                    <span class="text-green-800 dark:text-green-300 font-medium">@lang('Total Paid')</span>
                    <span class="text-green-900 dark:text-green-100 font-bold">{{ showAmount($loan->paid_amount) }}</span>
                </div>
            </div>
            @endif
            
            <div class="bg-{{ $loan->total_installment == $loan->given_installment ? 'green' : 'red' }}-50 dark:bg-{{ $loan->total_installment == $loan->given_installment ? 'green' : 'red' }}-900/20 p-4 rounded-lg border border-{{ $loan->total_installment == $loan->given_installment ? 'green' : 'red' }}-200 dark:border-{{ $loan->total_installment == $loan->given_installment ? 'green' : 'red' }}-800">
                <div class="flex justify-between items-center">
                    <span class="text-{{ $loan->total_installment == $loan->given_installment ? 'green' : 'red' }}-800 dark:text-{{ $loan->total_installment == $loan->given_installment ? 'green' : 'red' }}-300 font-medium">@lang('Total Payable')</span>
                    <span class="text-{{ $loan->total_installment == $loan->given_installment ? 'green' : 'red' }}-900 dark:text-{{ $loan->total_installment == $loan->given_installment ? 'green' : 'red' }}-100 font-bold text-lg">{{ showAmount($loan->payable_amount) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Installment Progress -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="las la-chart-line text-purple-500 mr-2"></i>
            @lang('Installment Progress')
        </h3>
        <div class="space-y-4">
            @php
                // Calculate progress before using it in the template
                $totalInstallments = max($loan->total_installment, 1);
                $givenInstallments = max($loan->given_installment, 0);
                
                // Calculate progress percentage (0-100)
                $progress = min(($givenInstallments / $totalInstallments) * 100, 100);
            @endphp
            
            <!-- Progress Circle -->
            <div class="flex justify-center items-center mb-6">
                <div class="relative w-32 h-32 progress-circle-container {{ $progress >= 100 ? 'progress-complete-glow' : '' }}">
                @php
                    // SVG circle calculations for proper progress display
                    $radius = 45;
                    $circumference = 2 * pi() * $radius; // More precise than 3.14159
                    $dashOffset = $circumference - ($progress / 100) * $circumference;
                    
                    // Determine progress color based on completion
                    $progressColorClass = $progress >= 100 ? 'progress-complete' : 
                                         ($progress >= 75 ? 'progress-high' : 
                                         ($progress >= 50 ? 'progress-medium' : 
                                         ($progress > 0 ? 'progress-low' : 'progress-starting')));
                @endphp
                <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 100 100" style="overflow: visible;">
                    <!-- Background circle -->
                    <circle cx="50" cy="50" r="{{ $radius }}" 
                            stroke="currentColor" 
                            stroke-width="6" 
                            fill="none" 
                            class="text-gray-200 dark:text-gray-600"
                            style="transition: all 0.3s ease;"/>
                    
                    <!-- Progress circle -->
                    <circle cx="50" cy="50" r="{{ $radius }}" 
                            stroke="currentColor" 
                            stroke-width="6" 
                            fill="none" 
                            class="{{ $progressColorClass }}" 
                            style="stroke-dasharray: {{ number_format($circumference, 2) }}; 
                                   stroke-dashoffset: {{ number_format($dashOffset, 2) }}; 
                                   transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.3s ease;
                                   --final-offset: {{ number_format($dashOffset, 2) }};"
                            stroke-linecap="round"/>
                    
                    <!-- Optional: Add a subtle glow effect for completed loans -->
                    @if($progress >= 100)
                    <circle cx="50" cy="50" r="{{ $radius + 2 }}" 
                            stroke="currentColor" 
                            stroke-width="2" 
                            fill="none" 
                            class="text-green-300 dark:text-green-400 opacity-30"
                            style="stroke-dasharray: {{ number_format($circumference * 1.1, 2) }}; 
                                   stroke-dashoffset: 0; 
                                   filter: blur(2px);"
                            stroke-linecap="round"/>
                    @endif
                </svg>
                
                <!-- Progress text overlay -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center progress-text">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">
                            {{ number_format($progress, 1) }}%
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            @if($progress >= 100)
                                @lang('Completed')
                            @elseif($progress >= 75)
                                @lang('Almost Done')
                            @elseif($progress >= 50)
                                @lang('Halfway')
                            @elseif($progress > 0)
                                @lang('In Progress')
                            @else
                                @lang('Starting')
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Optional: Progress indicator dots around the circle for loans with 12 or fewer installments -->
                @if($loan->total_installment <= 12 && $loan->total_installment > 0)
                    @for($i = 1; $i <= $loan->total_installment; $i++)
                        @php
                            $angle = (($i - 1) / $loan->total_installment) * 360 - 90; // Start from top
                            $x = 50 + 42 * cos(deg2rad($angle)); // Slightly outside the circle
                            $y = 50 + 42 * sin(deg2rad($angle));
                            $isPaid = $i <= $loan->given_installment;
                        @endphp
                        <div class="absolute w-2 h-2 rounded-full transition-all duration-500 {{ $isPaid ? 'bg-green-500 progress-dot-paid' : 'bg-gray-300 dark:bg-gray-600' }}"
                             style="left: {{ $x }}%; top: {{ $y }}%; transform: translate(-50%, -50%); --dot-index: {{ $i }};">
                        </div>
                    @endfor
                @endif
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                    <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Total Installments')</span>
                    <span class="text-gray-900 dark:text-white font-bold">{{ $loan->total_installment }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                    <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Paid Installments')</span>
                    <span class="text-green-600 dark:text-green-400 font-bold">{{ $loan->given_installment }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600 dark:text-gray-400 text-sm">@lang('Remaining')</span>
                    <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $loan->total_installment - $loan->given_installment }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Next Installment Alert -->
@if($loan->nextInstallment)
<div class="mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-6">
    <div class="flex items-start">
        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
            <i class="las la-calendar-check text-amber-600 dark:text-amber-400 text-xl"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-lg font-semibold text-amber-800 dark:text-amber-300 mb-2">@lang('Next Installment Due')</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-amber-700 dark:text-amber-400 text-sm mb-1">@lang('Due Date')</p>
                    <p class="text-amber-900 dark:text-amber-200 font-bold">{{ showDateTime($loan->nextInstallment->installment_date, 'd M, Y') }}</p>
                </div>
                <div>
                    <p class="text-amber-700 dark:text-amber-400 text-sm mb-1">@lang('Amount')</p>
                    <p class="text-amber-900 dark:text-amber-200 font-bold">{{ showAmount($loan->per_installment) }}</p>
                </div>
            </div>
            @php
                $daysUntilDue = today()->diffInDays($loan->nextInstallment->installment_date, false);
            @endphp
            @if($daysUntilDue < 0)
                <div class="mt-3 p-3 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-red-800 dark:text-red-300 text-sm font-medium">
                        <i class="las la-exclamation-triangle mr-1"></i>
                        @lang('This installment is') {{ abs($daysUntilDue) }} @lang('day' . (abs($daysUntilDue) > 1 ? 's' : '')) @lang('overdue')
                    </p>
                </div>
            @elseif($daysUntilDue == 0)
                <div class="mt-3 p-3 bg-amber-100 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg">
                    <p class="text-amber-800 dark:text-amber-300 text-sm font-medium">
                        <i class="las la-clock mr-1"></i>
                        @lang('This installment is due today')
                    </p>
                </div>
            @else
                <div class="mt-3 p-3 bg-blue-100 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-blue-800 dark:text-blue-300 text-sm font-medium">
                        <i class="las la-info-circle mr-1"></i>
                        @lang('Due in') {{ $daysUntilDue }} @lang('day' . ($daysUntilDue > 1 ? 's' : ''))
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endif