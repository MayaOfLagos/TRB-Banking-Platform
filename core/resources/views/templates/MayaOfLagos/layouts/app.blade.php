<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')

    {{-- Global CSS --}}
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
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
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d'
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link href="{{ asset($activeTemplateTrue . 'css/style.css') }}" rel="stylesheet" />

    @stack('style-lib')
    @stack('style')

    <link href="{{ asset($activeTemplateTrue . 'css/color.php?color=' . gs()->base_color . '&secondColor=' . gs()->secondary_color) }}" rel="stylesheet">
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: {{ gs()->base_color }};
            --secondary-color: {{ gs()->secondary_color }};
        }
    </style>
</head>
@php echo loadExtension('google-analytics') @endphp

<body class="font-inter">
    {{-- Preloader --}}
    <div id="preloader" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="flex space-x-2">
            <div class="w-3 h-3 bg-teal-600 rounded-full animate-bounce"></div>
            <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
            <div class="w-3 h-3 bg-teal-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
        </div>
    </div>

    {{-- Scroll to Top Button --}}
    <button class="back-to-top fixed bottom-6 right-6 w-12 h-12 bg-teal-600 text-white rounded-full shadow-lg hover:bg-teal-700 transition-all duration-300 opacity-0 invisible z-40" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    {{-- Main Content --}}
    @yield('app')

    {{-- Modals --}}
    @stack('modal')

    {{-- Scripts --}}
    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery.validate.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')
    @php echo loadExtension('tawk-chat') @endphp
    @include('partials.notify')
    @if (gs('pn'))
        @include('partials.push_script')
    @endif
    @stack('script')

    @include('partials.user_activity')

    <script>
        "use strict";
        (function($) {
            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            $('.language_switcher > .language_switcher__caption').on('click', function() {
                $(this).parent().toggleClass('open');
            });

            $(document).on('keyup', function(evt) {
                if ((evt.keyCode || evt.which) === 27) {
                    $('.language_switcher').removeClass('open');
                }
            });

            $(document).on('click', function(evt) {
                if ($(evt.target).closest(".language_switcher > .language_switcher__caption").length === 0) {
                    $('.language_switcher').removeClass('open');
                }
            });

            // Cookie Policy
            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            $('.policy').on('click', function() {
                $.get(`{{ route('cookie.accept') }}`, function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            $('form').on('submit', function() {
                if ($(this).valid()) {
                    $(':submit', this).attr('disabled', 'disabled');
                }
            });

            $(window).on('load', function() {
                $('#preloader').fadeOut(500);
            });

            if (typeof MayaOfLagos !== 'undefined') {
                console.log('MayaOfLagos template loaded successfully');
            }

        })(jQuery);
    </script>
</body>

</html>