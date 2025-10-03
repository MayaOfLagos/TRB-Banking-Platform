{{--
    Reusable Wizard Container Component
    
    Usage:
    @include($activeTemplate . 'partials.wizard_container', [
        'title' => 'Create Account',
        'subtitle' => 'Join thousands of satisfied customers',
        'showLogo' => true, // Optional: defaults to true
        'logoLink' => route('home'), // Optional: defaults to home
        'maxWidth' => '2xl', // Optional: sm, md, lg, xl, 2xl, 3xl, 4xl
        'theme' => 'emerald', // Optional: emerald, blue, purple, etc.
        'showThemeToggle' => true, // Optional: defaults to true
        'showBackgroundElements' => true, // Optional: defaults to true
        'contentClass' => 'p-8', // Optional: additional classes for content area
        'containerClass' => '', // Optional: additional classes for container
    ])
    
    Content goes here using $slot
    
    @endinclude
--}}

@php
    $title = $title ?? 'Wizard Form';
    $subtitle = $subtitle ?? '';
    $showLogo = $showLogo ?? true;
    $logoLink = $logoLink ?? route('home');
    $maxWidth = $maxWidth ?? '2xl';
    $theme = $theme ?? 'emerald';
    $showThemeToggle = $showThemeToggle ?? true;
    $showBackgroundElements = $showBackgroundElements ?? true;
    $contentClass = $contentClass ?? 'p-4 sm:p-8';
    $containerClass = $containerClass ?? '';
    
    // Max width classes
    $maxWidthClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        'full' => 'max-w-full'
    ];
    
    $maxWidthClass = $maxWidthClasses[$maxWidth] ?? 'max-w-2xl';
    
    // Theme background colors
    $themeBgs = [
        'emerald' => 'from-emerald-50 via-white to-emerald-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900',
        'blue' => 'from-blue-50 via-white to-blue-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900',
        'purple' => 'from-purple-50 via-white to-purple-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900'
    ];
    
    $themeBg = $themeBgs[$theme] ?? $themeBgs['emerald'];
    
    // Theme background elements
    $themeElements = [
        'emerald' => [
            'bg-emerald-400/20 dark:bg-emerald-500/10',
            'bg-emerald-300/20 dark:bg-emerald-600/10',
            'bg-emerald-200/20 dark:bg-emerald-400/5'
        ],
        'blue' => [
            'bg-blue-400/20 dark:bg-blue-500/10',
            'bg-blue-300/20 dark:bg-blue-600/10',
            'bg-blue-200/20 dark:bg-blue-400/5'
        ],
        'purple' => [
            'bg-purple-400/20 dark:bg-purple-500/10',
            'bg-purple-300/20 dark:bg-purple-600/10',
            'bg-purple-200/20 dark:bg-purple-400/5'
        ]
    ];
    
    $elements = $themeElements[$theme] ?? $themeElements['emerald'];
@endphp

<div class="min-h-screen bg-gradient-to-br {{ $themeBg }} flex items-center justify-center p-4 transition-all duration-300 {{ $containerClass }}">
    
    @if($showBackgroundElements)
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 {{ $elements[0] }} rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 {{ $elements[1] }} rounded-full blur-3xl"></div>
            <div class="absolute top-20 left-20 w-60 h-60 {{ $elements[2] }} rounded-full blur-2xl"></div>
        </div>
    @endif

    @if($showThemeToggle)
        <!-- Dark/Light Mode Toggle - Fixed Top -->
        <div class="fixed top-4 right-4 z-50">
            <button id="theme-toggle" class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
                <i class="las la-sun text-xl hidden dark:block"></i>
                <i class="las la-moon text-xl block dark:hidden"></i>
            </button>
        </div>
    @endif

    <div class="relative w-full {{ $maxWidthClass }} mx-auto">
        
        @if($showLogo || $title)
            <!-- Header Section -->
            <div class="text-center mb-6 sm:mb-8">
                @if($showLogo)
                    <a href="{{ $logoLink }}" class="inline-block">
                        <!-- Dark logo -->
                        <img src="{{ siteLogo('dark') }}" alt="{{ __(gs('site_name')) }}" 
                             class="h-12 sm:h-16 w-auto mx-auto block dark:hidden" id="logo-for-white-bg">
                        <!-- White logo -->
                        <img src="{{ siteLogo() }}" alt="{{ __(gs('site_name')) }}" 
                             class="h-12 sm:h-16 w-auto mx-auto hidden dark:block" id="logo-for-dark-bg">
                    </a>
                @endif
                
                @if($title)
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-4 mb-2">@lang($title)</h1>
                @endif
                
                @if($subtitle)
                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">@lang($subtitle)</p>
                @endif
            </div>
        @endif

        <!-- Main Content Container -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
            <div class="{{ $contentClass }}">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

@if($showThemeToggle)
    @push('script')
    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                // Check for saved theme preference or default to 'light'
                const currentTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.classList.toggle('dark', currentTheme === 'dark');
                
                themeToggle.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    
                    // Add animation effect
                    themeToggle.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        themeToggle.style.transform = 'scale(1)';
                    }, 150);
                });
            }
        });
    </script>
    @endpush
@endif