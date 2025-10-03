<!-- Header Area Start -->
<header class="header-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-2 col-md-3 col-sm-4 col-5">
                <div class="logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="logo">
                    </a>
                </div>
            </div>
            <div class="col-lg-10 col-md-9 col-sm-8 col-7">
                <div class="header-right-area">
                    <div class="main-menu">
                        <nav class="navbar navbar-expand-lg">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('home') }}">@lang('Home')</a>
                                    </li>

                                    @foreach ($pages as $k => $data)
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a>
                                    </li>
                                    @endforeach

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('contact') }}">@lang('Contact')</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                    <div class="header-right">
                        <a href="{{ route('user.login') }}" class="theme-btn">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header Area End -->