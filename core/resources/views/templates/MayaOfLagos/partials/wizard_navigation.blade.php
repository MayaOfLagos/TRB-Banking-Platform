{{--
    Reusable Wizard Navigation Component
    
    Usage:
    @include($activeTemplate . 'partials.wizard_navigation', [
        'currentStep' => 2,
        'totalSteps' => 3,
        'prevText' => 'Back', // Optional
        'nextText' => 'Continue', // Optional
        'submitText' => 'Complete Registration', // Optional
        'showPrev' => true, // Optional: defaults to true for steps > 1
        'showNext' => true, // Optional: defaults to true for steps < totalSteps
        'formId' => 'myForm', // Optional: for submit button
        'theme' => 'emerald', // Optional: emerald, blue, purple, etc.
        'alignment' => 'between', // Optional: between, end, start
        'loadingText' => 'Processing...' // Optional
    ])
--}}

@php
    $currentStep = $currentStep ?? 1;
    $totalSteps = $totalSteps ?? 3;
    $prevText = $prevText ?? 'Back';
    $nextText = $nextText ?? 'Continue';
    $submitText = $submitText ?? 'Submit';
    $loadingText = $loadingText ?? 'Processing...';
    $showPrev = $showPrev ?? ($currentStep > 1);
    $showNext = $showNext ?? ($currentStep < $totalSteps);
    $formId = $formId ?? 'wizardForm';
    $theme = $theme ?? 'emerald';
    $alignment = $alignment ?? 'between';
    
    // Theme colors
    $themeColors = [
        'emerald' => [
            'primary' => 'bg-emerald-600 hover:bg-emerald-700',
            'secondary' => 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600'
        ],
        'blue' => [
            'primary' => 'bg-blue-600 hover:bg-blue-700',
            'secondary' => 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600'
        ],
        'purple' => [
            'primary' => 'bg-purple-600 hover:bg-purple-700',
            'secondary' => 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600'
        ]
    ];
    
    $colors = $themeColors[$theme] ?? $themeColors['emerald'];
    
    // Alignment classes
    $alignmentClasses = [
        'between' => 'justify-between',
        'end' => 'justify-end',
        'start' => 'justify-start',
        'center' => 'justify-center'
    ];
    
    $alignClass = $alignmentClasses[$alignment] ?? 'justify-between';
    
    $isLastStep = $currentStep >= $totalSteps;
@endphp

<!-- Wizard Navigation -->
<div class="flex {{ $alignClass }} mt-6 sm:mt-8 gap-3 sm:gap-4">
    @if($showPrev)
        <button type="button" 
                class="btn-prev {{ $colors['secondary'] }} text-gray-700 dark:text-gray-200 font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center text-sm sm:text-base">
            <i class="las la-arrow-left mr-2 text-base sm:text-xl"></i>
            @lang($prevText)
        </button>
    @endif
    
    @if($isLastStep)
        <!-- Submit Button -->
        <button type="submit" 
                class="{{ $colors['primary'] }} text-white font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center text-sm sm:text-base"
                id="submit-btn"
                form="{{ $formId }}">
            <span class="default-text inline-flex items-center">
                <i class="las la-check-circle text-base sm:text-xl mr-2"></i>
                @lang($submitText)
            </span>
            <span class="loading-text hidden inline-flex items-center">
                <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                @lang($loadingText)
            </span>
        </button>
    @elseif($showNext)
        <!-- Next Button -->
        <button type="button" 
                class="btn-next {{ $colors['primary'] }} text-white font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center text-sm sm:text-base">
            @lang($nextText)
            <i class="las la-arrow-right ml-2 text-base sm:text-xl"></i>
        </button>
    @endif
</div>