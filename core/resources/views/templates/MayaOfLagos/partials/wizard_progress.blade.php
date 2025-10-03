{{--
    Reusable Wizard Progress Component
    
    Usage:
    @include($activeTemplate . 'partials.wizard_progress', [
        'steps' => [
            ['id' => 1, 'label' => 'Personal Info', 'icon' => 'user'],
            ['id' => 2, 'label' => 'Security', 'icon' => 'shield-alt'],
            ['id' => 3, 'label' => 'Complete', 'icon' => 'check-circle']
        ],
        'currentStep' => 1, // Optional: defaults to 1
        'theme' => 'emerald' // Optional: emerald, blue, purple, etc.
    ])
--}}

@php
    $steps = $steps ?? [];
    $currentStep = $currentStep ?? 1;
    $theme = $theme ?? 'emerald';
    $totalSteps = count($steps);
    
    // Theme colors
    $themeColors = [
        'emerald' => [
            'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
            'active' => 'bg-emerald-600 text-white',
            'active_text' => 'text-emerald-700 dark:text-emerald-300',
            'progress' => 'bg-emerald-600',
            'border' => 'border-gray-200/50 dark:border-gray-700/50'
        ],
        'blue' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/20',
            'active' => 'bg-blue-600 text-white',
            'active_text' => 'text-blue-700 dark:text-blue-300',
            'progress' => 'bg-blue-600',
            'border' => 'border-gray-200/50 dark:border-gray-700/50'
        ],
        'purple' => [
            'bg' => 'bg-purple-50 dark:bg-purple-900/20',
            'active' => 'bg-purple-600 text-white',
            'active_text' => 'text-purple-700 dark:text-purple-300',
            'progress' => 'bg-purple-600',
            'border' => 'border-gray-200/50 dark:border-gray-700/50'
        ]
    ];
    
    $colors = $themeColors[$theme] ?? $themeColors['emerald'];
@endphp

@if(count($steps) > 0)
<!-- Wizard Progress Steps -->
<div class="{{ $colors['bg'] }} px-4 sm:px-8 rounded-3xl py-6 border-b {{ $colors['border'] }}">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 sm:space-x-4 w-full">
            @foreach($steps as $index => $step)
                @php
                    $stepNumber = $step['id'] ?? ($index + 1);
                    $isActive = $stepNumber == $currentStep;
                    $isCompleted = $stepNumber < $currentStep;
                    $isLast = $index == count($steps) - 1;
                @endphp
                
                <!-- Step {{ $stepNumber }} -->
                <div class="flex items-center step-indicator {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}" data-step="{{ $stepNumber }}">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-semibold text-sm sm:text-base step-circle transition-all duration-300
                        @if($isCompleted || $isActive)
                            {{ $colors['active'] }}
                        @else
                            bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400
                        @endif
                    ">
                        <span class="step-number {{ $isCompleted ? 'hidden' : '' }}">{{ $stepNumber }}</span>
                        @if($isCompleted)
                            <i class="las la-check text-sm sm:text-lg step-check"></i>
                        @else
                            <i class="las la-check text-sm sm:text-lg step-check hidden"></i>
                        @endif
                    </div>
                    @if(isset($step['label']))
                        <span class="ml-2 sm:ml-3 text-xs sm:text-sm font-medium transition-all duration-300 hidden sm:block
                            @if($isActive || $isCompleted)
                                {{ $colors['active_text'] }}
                            @else
                                text-gray-500 dark:text-gray-400
                            @endif
                        ">
                            @lang($step['label'])
                        </span>
                    @endif
                </div>
                
                @if(!$isLast)
                    <!-- Progress Line {{ $index + 1 }} -->
                    <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full mx-2 sm:mx-4 progress-line" data-line="{{ $stepNumber }}">
                        <div class="h-full {{ $colors['progress'] }} rounded-full transition-all duration-500 progress-fill" 
                             style="width: {{ $isCompleted ? '100%' : '0%' }}"></div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif