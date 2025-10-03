<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')

    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a'
                        },
                        secondary: {
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309'
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { 
            display: none !important; 
        }
        
        @media (max-width: 1023px) {
            aside[x-cloak] {
                transform: translateX(-100%) !important;
                opacity: 0 !important;
            }
            
            body:not([x-data-initialized]) aside {
                transform: translateX(-100%) !important;
                visibility: hidden !important;
            }
            
            body[x-data-initialized] aside {
                visibility: visible !important;
                opacity: 1 !important;
                transition: all 0.3s ease-in-out !important;
            }
        }
        
        @media (min-width: 1024px) {
            aside {
                transform: translateX(0) !important;
                visibility: visible !important;
                opacity: 1 !important;
                height: 100vh !important;
                height: 100dvh !important;
            }
        }
        
        @media (max-width: 1023px) {
            aside {
                height: calc(100vh - 5rem) !important;
                height: calc(100dvh - 5rem) !important;
                max-height: calc(100vh - 5rem) !important;
                max-height: calc(100dvh - 5rem) !important;
            }
            
            aside nav {
                max-height: calc(100vh - 16rem) !important;
                max-height: calc(100dvh - 16rem) !important;
                overflow-y: auto !important;
                padding-bottom: 1.5rem !important;
            }
        }
        }
        
        @media (min-width: 1024px) {
            main.content-card {
                background: white !important;
                border-radius: 1.5rem !important;
                margin: 1.5rem !important;
                margin-left: 1.5rem !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
                border: 1px solid rgba(0, 0, 0, 0.05) !important;
                min-height: calc(100vh - 8rem) !important;
                overflow: hidden !important;
            }
            
            .dark main.content-card {
                background: rgb(31, 41, 55) !important; /* gray-800 */
                border-color: rgba(75, 85, 99, 0.3) !important; /* gray-600 with opacity */
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important;
            }
            
            aside {
                background: rgb(249, 250, 251) !important; /* gray-50 */
                border: none !important;
                border-right: none !important;
                border-left: none !important;
                border-top: none !important;
                border-bottom: none !important;
                box-shadow: none !important;
            }
            
            .dark aside {
                background: rgb(17, 24, 39) !important; /* gray-900 */
                border: none !important;
                border-right: none !important;
                border-left: none !important;
                border-top: none !important;
                border-bottom: none !important;
            }
            
            header {
                background: rgb(249, 250, 251) !important; /* gray-50 */
                border: none !important;
                border-bottom: none !important;
                border-top: none !important;
                border-left: none !important;
                border-right: none !important;
                box-shadow: none !important;
            }
            
            .dark header {
                background: rgb(17, 24, 39) !important; /* gray-900 */
                border: none !important;
                border-bottom: none !important;
                border-top: none !important;
                border-left: none !important;
                border-right: none !important;
                box-shadow: none !important;
            }
            
            .main-content-wrapper {
                background: rgb(249, 250, 251) !important;
                min-height: 100vh !important;
            }
            
            .dark .main-content-wrapper {
                background: rgb(17, 24, 39) !important;
            }
            
            aside.border-r,
            aside.border-gray-200,
            aside.dark\:border-gray-700 {
                border-right: none !important;
            }
            
            header.border-b,
            header.border-gray-200,
            header.dark\:border-gray-700 {
                border-bottom: none !important;
            }
        }
        
        @media (max-width: 1023px) {
            main.content-card {
                border-radius: 0;
                margin: 0;
                box-shadow: none;
                border: none;
            }
        }
        
        div[x-show][x-cloak] {
            display: none !important;
        }
        
        body:not([x-data-initialized]) {
            overflow: hidden;
        }
        
        body[x-data-initialized] {
            overflow: visible;
            transition: all 0.3s ease;
        }
        
        .sidebar-loading-fix {
            transform: translateX(-100%);
            visibility: hidden;
        }
        
        @media (min-width: 1024px) {
            .sidebar-loading-fix {
                transform: translateX(0);
                visibility: visible;
            }
        }
    </style>
    
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('style-lib')
    @stack('style')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans transition-colors duration-300" 
      x-data="{ ...themeManager(), sidebarOpen: false, alpineReady: false }" 
      x-init="init(); $nextTick(() => { alpineReady = true; $el.setAttribute('x-data-initialized', true); })" 
      :class="{ 'dark': isDark }">
    <script>
        function themeManager() {
            return {
                isDark: false,
                theme: 'system', // 'light', 'dark', 'system'
                
                init() {
                    this.theme = localStorage.getItem('theme') || 'system';
                    this.updateTheme();
                    
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                        if (this.theme === 'system') {
                            this.updateTheme();
                        }
                    });
                },
                
                updateTheme() {
                    if (this.theme === 'system') {
                        this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    } else {
                        this.isDark = this.theme === 'dark';
                    }
                    
                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },
                
                toggleDarkMode() {
                    if (this.theme === 'light') {
                        this.setTheme('dark');
                    } else if (this.theme === 'dark') {
                        this.setTheme('system');
                    } else {
                        this.setTheme('light');
                    }
                },
                
                setTheme(newTheme) {
                    this.theme = newTheme;
                    localStorage.setItem('theme', newTheme);
                    this.updateTheme();
                }
            }
        }
        
        (function() {
            const theme = localStorage.getItem('theme') || 'system';
            let isDark = false;
            
            if (theme === 'system') {
                isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            } else {
                isDark = theme === 'dark';
            }
            
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <div class="preloader fixed inset-0 bg-white dark:bg-gray-900 z-[9999] flex items-center justify-center transition-colors duration-300">
        <div class="text-center">
            <div class="relative mb-4">
                <div class="w-16 h-16 border-4 border-primary-200 dark:border-primary-800 border-t-primary-600 dark:border-t-primary-400 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <img src="{{ siteFavicon() }}" alt="{{ gs()->siteName('Favicon') }}" class="w-8 h-8 object-contain">
                </div>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium animate-pulse">Loading...</p>
        </div>
    </div>

    <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition></div>

    <div class="min-h-screen flex bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        @include($activeTemplate . 'partials.sidenav')

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col lg:ml-64 main-content-wrapper lg:bg-white dark:lg:bg-gray-800">
            @include($activeTemplate . 'partials.dashboard_header')

            {{-- Content --}}
            <main class="flex-1 p-2 lg:p-6 pb-40 lg:pb-6 content-card lg:mx-6 lg:my-6 lg:mb-6 lg:p-8 lg:bg-gray-50 dark:lg:bg-gray-900" style="border-radius: 0; border-radius: clamp(0px, 6.5rem, 6.5rem);">
                <style>
                    @media (min-width: 1024px) {
                        main.content-card {
                            border-radius: 1.5rem !important;
                        }
                    }
                    @media (max-width: 1023px) {
                        main.content-card {
                            border-radius: 0 !important;
                        }
                    }
                </style>
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('modal')
    
    {{-- Transfer Modal --}}
    @include('templates.MayaOfLagos.partials.transfer_modal')

    {{-- Scripts --}}
    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery.validate.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')
    @php echo loadExtension('tawk-chat') @endphp
    @include('partials.notify')

    @stack('script')

    <script>
        $(window).on('load', function() {
            $('.preloader').fadeOut(500);
        });

        function toggleSidebar() {
            $('.sidebar-overlay').toggleClass('hidden');
            $('#sidebar').toggleClass('-translate-x-full');
        }

        function toggleBodyOverlay() {
            $('.body-overlay').toggleClass('hidden');
        }

        $('.sidebar-overlay').on('click', function() {
            toggleSidebar();
        });

        $(window).resize(function() {
            if ($(window).width() >= 1024) {
                $('.sidebar-overlay').addClass('hidden');
                $('#sidebar').removeClass('-translate-x-full');
            }
        });
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized');
        });
        
        document.addEventListener('alpine:initialized', () => {
            console.log('Alpine.js fully initialized');
            document.body.setAttribute('x-data-initialized', 'true');
            document.body.classList.remove('sidebar-loading-fix');
            
            setTimeout(() => {
                const sidebar = document.querySelector('aside[x-cloak]');
                if (sidebar) {
                    sidebar.style.visibility = 'visible';
                    sidebar.style.opacity = '1';
                }
            }, 100);
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                if (!document.body.hasAttribute('x-data-initialized')) {
                    document.body.setAttribute('x-data-initialized', 'true');
                    console.log('Alpine.js fallback initialization');
                }
            }, 1000);
            
            function updateLogos() {
                const isDark = document.documentElement.classList.contains('dark');
                
                const logosForWhiteBg = document.querySelectorAll('#logo-for-white-bg, #light-logo');
                const logosForDarkBg = document.querySelectorAll('#logo-for-dark-bg, #dark-logo');
                
                logosForWhiteBg.forEach(logo => {
                    if (isDark) {
                        logo.style.display = 'none';
                    } else {
                        logo.style.display = 'block';
                    }
                });
                
                logosForDarkBg.forEach(logo => {
                    if (isDark) {
                        logo.style.display = 'block';
                    } else {
                        logo.style.display = 'none';
                    }
                });
            }
            
            updateLogos();
            
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        updateLogos();
                    }
                });
            });
            
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    </script>

    @include($activeTemplate . 'partials.mobile_bottom_nav')

</body>

</html>