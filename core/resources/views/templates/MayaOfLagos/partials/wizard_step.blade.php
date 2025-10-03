{{--
    Reusable Wizard Step Component
    
    Usage:
    @include($activeTemplate . 'partials.wizard_step', [
        'stepId' => 1,
        'title' => 'Personal Information',
        'subtitle' => 'Let\'s start with your basic information',
        'icon' => 'user', // Optional: las la- icon name
        'iconBg' => 'emerald', // Optional: emerald, blue, purple, etc.
        'isActive' => true, // Optional: defaults to false
        'contentClass' => 'space-y-6', // Optional: additional classes for content area
        'theme' => 'emerald' // Optional: theme for styling
    ])
    
    Step content goes here using $slot
    
    @endinclude
--}}

@php
    $stepId = $stepId ?? 1;
    $title = $title ?? '';
    $subtitle = $subtitle ?? '';
    $icon = $icon ?? 'circle';
    $iconBg = $iconBg ?? 'emerald';
    $isActive = $isActive ?? false;
    $contentClass = $contentClass ?? 'space-y-6';
    $theme = $theme ?? 'emerald';
    
    // Icon background colors
    $iconBgColors = [
        'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
        'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        'amber' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
        'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
        'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'
    ];
    
    $iconBgClass = $iconBgColors[$iconBg] ?? $iconBgColors['emerald'];
@endphp

<!-- Wizard Step {{ $stepId }} -->
<div class="form-step {{ $isActive ? 'active' : '' }}" id="step-{{ $stepId }}">
    <div class="p-4 sm:p-8">
        
        @if($title || $subtitle || $icon)
            <!-- Step Header -->
            <div class="text-center mb-6 sm:mb-8">
                @if($icon)
                    <div class="w-12 h-12 sm:w-16 sm:h-16 {{ $iconBgClass }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="las la-{{ $icon }} text-xl sm:text-2xl"></i>
                    </div>
                @endif
                
                @if($title)
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang($title)</h2>
                @endif
                
                @if($subtitle)
                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">@lang($subtitle)</p>
                @endif
            </div>
        @endif

        <!-- Step Content -->
        <div class="{{ $contentClass }}">
            {{ $slot }}
        </div>
    </div>
</div>