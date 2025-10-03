@extends($activeTemplate . 'layouts.main')
@section('app')
    @include($activeTemplate . 'partials.header')

    @yield('content')

    @include($activeTemplate . 'partials.footer')
@endsection