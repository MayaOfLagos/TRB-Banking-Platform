{{--
    Auth Preloader Component
    
    A reusable preloader component for authentication pages with customizable options.
    
    Usage Examples:
    
    1. Basic preloader:
    @include($activeTemplate . 'partials.auth_preloader')
    
    2. With custom text:
    @include($activeTemplate . 'partials.auth_preloader', ['text' => 'Signing you in...'])
    
    3. With pattern:
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Creating your account...',
        'showPattern' => true
    ])
    
    Available Parameters:
    - text (string, optional): Custom loading text to display
    - showPattern (boolean, default: false): Show background pattern
    
    JavaScript Functions:
    - hideAuthPreloader(): Manually hide the preloader
    - showAuthPreloader(text): Programmatically show preloader with optional text
    
    Features:
    - Automatic fade out on page load
    - Dark mode support
    - Mobile responsive
    - Backdrop blur effect
    - Clean DOM removal after fade
    - Fallback for environments without jQuery
    - Uses site favicon as center icon
--}}

<!-- Auth Preloader Component -->
<div class="preloader fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center transition-colors duration-300">
    <div class="relative">
        <!-- Main Spinner -->
        <div class="w-16 h-16 border-4 border-primary-200 dark:border-primary-800 border-t-primary-600 dark:border-t-primary-400 rounded-full animate-spin"></div>
        
        <!-- Center Favicon -->
        <div class="absolute inset-0 flex items-center justify-center">
            <img src="{{ siteFavicon() }}" alt="@lang('favicon')" class="w-6 h-6 rounded-sm">
        </div>
        
        <!-- Loading Text (Optional) -->
        @if(isset($text) && $text)
            <div class="absolute top-20 left-1/2 transform -translate-x-1/2 text-center min-w-max">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium whitespace-nowrap">{{ $text }}</p>
            </div>
        @endif
    </div>
    
    <!-- Background Pattern (Optional) -->
    @if(isset($showPattern) && $showPattern)
        <div class="absolute inset-0 opacity-5">
            <div class="w-full h-full bg-gradient-to-br from-primary-500 via-transparent to-secondary-500"></div>
        </div>
    @endif
</div>

@push('script')
<script>
    // Auth Preloader Management
    (function() {
        // Pure JavaScript version (fallback)
        window.addEventListener('load', function() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.transition = 'opacity 0.5s ease';
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                    preloader.remove(); // Clean up DOM
                }, 500);
            }
        });

        // jQuery version (primary) - if jQuery is available
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                $(window).on('load', function() {
                    $('.preloader').fadeOut(500, function() {
                        $(this).remove(); // Clean up DOM
                    });
                });
            });
        }

        // Manual hide function for programmatic control
        window.hideAuthPreloader = function() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                if (typeof $ !== 'undefined') {
                    $(preloader).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    preloader.style.transition = 'opacity 0.3s ease';
                    preloader.style.opacity = '0';
                    setTimeout(() => {
                        preloader.remove();
                    }, 300);
                }
            }
        };

        // Show preloader function for dynamic loading
        window.showAuthPreloader = function(text = null) {
            // Remove existing preloader
            const existing = document.querySelector('.preloader');
            if (existing) existing.remove();

            // Create new preloader
            const preloader = document.createElement('div');
            preloader.className = 'preloader fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center transition-colors duration-300';
            
            let textHtml = text ? `<div class="absolute top-20 left-1/2 transform -translate-x-1/2 text-center min-w-max">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium whitespace-nowrap">${text}</p>
            </div>` : '';
            
            preloader.innerHTML = `
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-primary-200 dark:border-primary-800 border-t-primary-600 dark:border-t-primary-400 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img src="{{ siteFavicon() }}" alt="favicon" class="w-6 h-6 rounded-sm">
                    </div>
                    ${textHtml}
                </div>
            `;
            
            document.body.appendChild(preloader);
        };
    })();
</script>
@endpush

@push('style')
<style>
    /* Auth Preloader Styles */
    .preloader {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    
    /* Enhanced spinner animation */
    .preloader .animate-spin {
        animation: auth-spin 1s linear infinite;
    }
    
    @keyframes auth-spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Smooth fade transitions */
    .preloader {
        transition: opacity 0.5s ease-in-out;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .preloader .w-16 {
            width: 3rem;
            height: 3rem;
        }
        
        .preloader .text-xl {
            font-size: 1rem;
        }
    }
</style>
@endpush