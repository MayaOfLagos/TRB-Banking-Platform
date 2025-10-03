<!DOCTYPE html>
<html class="no-js" lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <title>{{ gs('site_name') }} - {{ __($pageTitle ?? 'Home') }}</title>
    <meta content="{{ gs('meta_description') ?? 'Business Consulting and Financial Services' }}" name="description">
    <meta content="{{ gs('meta_keywords') ?? 'business, consulting, finance, banking' }}" name="keywords">
    <meta content="INDEX,FOLLOW" name="robots">
    @include('partials.seo')

    {{-- Mobile Specific Metas --}}
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">

    {{-- Favicons --}}
    <link href="{{ siteFavicon() }}" rel="icon" sizes="32x32" type="image/png">
    <meta content="#ffffff" name="msapplication-TileColor">
    <meta content="#ffffff" name="theme-color">

    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    {{-- All CSS File --}}
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/flaticon.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/fontawesome/css/fontawesome.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/fancybox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/odometer.css') }}" rel="stylesheet">
    {{-- Theme Custom CSS --}}
    <link href="{{ asset('assets/templates/MayaOfLagos/assets/css/style.css') }}" rel="stylesheet">

    @stack('style-lib')
    @stack('style')

    <link
        href="{{ asset($activeTemplateTrue . 'css/color.php?color=' . gs()->base_color . '&secondColor=' . gs()->secondary_color) }}"
        rel="stylesheet">
</head>
@php echo loadExtension('google-analytics') @endphp

<body id="body" class="bg-theme3">

    <div class="page-wrapper bg-theme3 overflow-visible">
        {{-- Preloader Start --}}
        @include($activeTemplate . 'partials.preloader')
        {{-- Preloader End --}}
        @yield('app')
    </div>

    {{-- Jquery --}}
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/vendor/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/bootstrap.min.js') }}"></script>

    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/marquee.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/jquery.fancybox.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/jquery.appear.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/jquery.odometer.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/lenis.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/splite-type.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/vanilla-tilt.min.js') }}"></script>
    <script src="{{ asset('assets/templates/MayaOfLagos/assets/js/main.js') }}"></script>

    @stack('script-lib')
    @php echo loadExtension('tawk-chat') @endphp
    @include('partials.notify')
    @if (gs('pn'))
        @include('partials.push_script')
    @endif
    @stack('script')

    {{-- Back to top button --}}
    @include($activeTemplate . 'partials.back_to_top')
</body>

</html>
