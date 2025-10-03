@php
    $contact = getContent('contact_us.content', true);
    $socialLinks = getContent('social_link.element', orderById: true);
@endphp
{{-- Header Area --}}
<header class="nav-header header-style7">
    <div class="sticky-wrapper">
        <div class="main-wrapper">
            {{-- Main Menu Area --}}
            <div class="menu-area">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto logo">
                        <div class="header-logo">
                            <a href="{{ route('home') }}">
                                <img alt="{{ gs('site_name') }}" src="{{ siteLogo() }}">
                                <img src="{{ siteLogo('dark') }}" alt="@lang('image')" /></a>
                            </a>
                        </div>
                    </div>
                    <div class="col-auto nav-menu">
                        <nav class="main-menu d-none d-lg-inline-block lh-1 lh-1">
                            <ul class="navigation">
                                <li class="{{ menuActive('home') }}">
                                    <a href="{{ route('home') }}">@lang('Home')</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">@lang('Banking')</a>
                                    <ul class="sub-menu">
                                        <li><a href="#">@lang('Accounts')</a></li>
                                        <li><a href="#">@lang('Cards')</a></li>
                                        <li><a href="#">@lang('Loans')</a></li>
                                        <li><a href="#">@lang('Transfers')</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">@lang('Deposits')</a></li>
                                <li><a href="#">@lang('Investments')</a></li>
                                <li><a href="#">@lang('Wire Transfer')</a></li>
                                <li class="menu-item-has-children">
                                    <a href="#">@lang('Rebate')</a>
                                    <ul class="sub-menu">
                                        <li><a href="#">@lang('Cashback Offers')</a></li>
                                        <li><a href="#">@lang('Reward Programs')</a></li>
                                    </ul>
                                </li>

                                @foreach ($pages as $k => $data)
                                    <li class="{{ @$pageTitle == $data->name ? 'active' : '' }}">
                                        <a href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a>
                                    </li>
                                @endforeach

                                <li class="{{ menuActive('contact') }}">
                                    <a href="{{ route('contact') }}">@lang('Contact Us')</a>
                                </li>
                            </ul>
                        </nav>
                        <div class="navbar-right d-inline-flex d-lg-none">
                            <button class="menu-toggle sidebar-btn" type="button">
                                <span class="line"></span>
                                <span class="line"></span>
                                <span class="line"></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-auto header-right-wrapper">
                        <div class="header-right">
                            <button class="search-btn">
                                <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                            </button>
                            @guest
                                <a href="{{ route('user.login') }}" class="theme-btn bg-theme">
                                    <span class="link-effect">
                                        <span class="effect-1">@lang('Get Started')</span>
                                        <span class="effect-1">@lang('Get Started')</span>
                                    </span><i class="fa-regular fa-arrow-right-long"></i>
                                </a>
                            @endguest
                            @auth
                                <a href="{{ route('user.home') }}" class="theme-btn bg-theme">
                                    <span class="link-effect">
                                        <span class="effect-1">@lang('Dashboard')</span>
                                        <span class="effect-1">@lang('Dashboard')</span>
                                    </span><i class="fa-regular fa-arrow-right-long"></i>
                                </a>
                            @endauth
                            <div class="sidebar-icon">
                                <button class="sidebar-tab open">
                                    <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/hm6-dot_icon.png') }}"
                                        alt="sidebar icon">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Mobile Menu --}}
<div class="mobile-menu-wrapper">
    <div class="mobile-menu-area">
        <button class="menu-toggle"><i class="fas fa-times"></i></button>
        <div class="mobile-logo">
            <a href="{{ route('home') }}"><img alt="{{ gs('site_name') }}" src="{{ siteLogo() }}" style="max-width: 150px;"></a>
        </div>
        <div class="mobile-menu">
            <ul class="navigation clearfix">
                {{-- Keep This Empty / Menu will come through Javascript --}}
            </ul>
        </div>
        <div class="sidebar-wrap">
            <h6>{{ gs('contact_address') }}</h6>
        </div>
        <div class="sidebar-wrap">
            <h6><a href="tel:{{ gs('contact_phone') }}">{{ gs('contact_phone') }}</a></h6>
            <h6><a href="mailto:{{ gs('contact_email') }}">{{ gs('contact_email') }}</a></h6>
        </div>
        @if(gs('social_facebook') || gs('social_twitter') || gs('social_instagram') || gs('social_linkedin'))
            <div class="social-btn style3">
                @if(gs('social_facebook'))
                    <a href="{{ gs('social_facebook') }}" target="_blank">
                        <span class="link-effect">
                            <span class="effect-1"><i class="fab fa-facebook"></i></span>
                            <span class="effect-1"><i class="fab fa-facebook"></i></span>
                        </span>
                    </a>
                @endif
                @if(gs('social_instagram'))
                    <a href="{{ gs('social_instagram') }}" target="_blank">
                        <span class="link-effect">
                            <span class="effect-1"><i class="fab fa-instagram"></i></span>
                            <span class="effect-1"><i class="fab fa-instagram"></i></span>
                        </span>
                    </a>
                @endif
                @if(gs('social_twitter'))
                    <a href="{{ gs('social_twitter') }}" target="_blank">
                        <span class="link-effect">
                            <span class="effect-1"><i class="fab fa-twitter"></i></span>
                            <span class="effect-1"><i class="fab fa-twitter"></i></span>
                        </span>
                    </a>
                @endif
                @if(gs('social_linkedin'))
                    <a href="{{ gs('social_linkedin') }}" target="_blank">
                        <span class="link-effect">
                            <span class="effect-1"><i class="fab fa-linkedin"></i></span>
                            <span class="effect-1"><i class="fab fa-linkedin"></i></span>
                        </span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- Sticky Header --}}
<div class="sticky-header">
    <div class="container">
        {{-- Main Menu Area --}}
        <div class="menu-area">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto logo">
                    <div class="header-logo">
                        <a href="{{ route('home') }}">
                            <img alt="{{ gs('site_name') }}" src="{{ siteLogo() }}">
                            <img src="{{ siteLogo('dark') }}" alt="@lang('image')" /></a>
                        </a>
                    </div>
                </div>
                <div class="col-auto nav-menu">
                    <nav class="main-menu d-none d-lg-inline-block">
                        <ul class="navigation clearfix">
                            {{-- Keep This Empty / Menu will come through Javascript --}}
                        </ul>
                    </nav>
                    <div class="navbar-right d-inline-flex d-lg-none">
                        <button class="menu-toggle sidebar-btn" type="button">
                            <span class="line"></span>
                            <span class="line"></span>
                            <span class="line"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- End Header Area --}}

{{-- Header Search --}}
<div class="search-popup">
    <button class="close-search"><i class="fa-solid fa-xmark"></i></button>
    <form method="post" action="#">
        <div class="form-group">
            <input id="search" type="search" name="search" placeholder="@lang('Search...')" required="">
            <button type="submit"><i class="fa fa-search"></i></button>
        </div>
    </form>
</div>
{{-- End Header Search --}}


{{-- Start Sidebar Area --}}
<div id="sidebar-area" class="sidebar">
    <button class="sidebar-close-btn">
        <svg class="icon-close" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
            y="0px" width="16px" height="12.7px" viewBox="0 0 16 12.7" style="enable-background:new 0 0 16 12.7"
            xml:space="preserve">
            <g>
                <rect x="0" y="5.4" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -2.1569 7.5208)" width="16"
                    height="2"></rect>
                <rect x="0" y="5.4" transform="matrix(0.7071 0.7071 -0.7071 0.7071 6.8431 -3.7929)" width="16"
                    height="2"></rect>
            </g>
        </svg>
    </button>
    <div class="sidebar-content">
        <div class="sidebar-logo">
            <a class="dark-logo" href="{{ route('home') }}"><img src="{{ siteLogo('dark') }}"
                    alt="@lang('image')" /></a>
        </div>
        <div class="sidebar-menu-wrap"></div>
        <div class="sidebar-about">
            <div class="sidebar-header">
                <h3>@lang('About Us')</h3>
            </div>
            <p>Trump Rebate Banking offers innovative financial services with a focus on rebates, loans, and secure
                transfers, empowering users with smart banking solutions.</p>
            <a href="{{ route('contact') }}" class="theme-btn">
                <span class="link-effect">
                    <span class="effect-1">@lang('Contact Us')</span>
                    <span class="effect-1">@lang('Contact Us')</span>
                </span>
            </a>
        </div>
        <div class="sidebar-contact">
            <div class="sidebar-header">
                <h3>@lang('Contact Us')</h3>
            </div>
            <ul class="contact-info">
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    <p>{{ gs('contact_address') }}</p>
                </li>
                <li>
                    <i class="fas fa-phone"></i>
                    <a href="tel:{{ gs('contact_phone') }}">{{ gs('contact_phone') }}</a>
                </li>
                <li>
                    <i class="fas fa-envelope-open-text"></i>
                    <a href="mailto:{{ gs('contact_email') }}">{{ gs('contact_email') }}</a>
                </li>
            </ul>
        </div>
        @if (gs('social_facebook') || gs('social_twitter') || gs('social_instagram') || gs('social_linkedin'))
            <ul class="sidebar-social">
                @if (gs('social_facebook'))
                    <li class="facebook"><a href="{{ gs('social_facebook') }}" target="_blank"><i
                                class="fab fa-facebook-f"></i></a></li>
                @endif
                @if (gs('social_instagram'))
                    <li class="instagram"><a href="{{ gs('social_instagram') }}" target="_blank"><i
                                class="fab fa-instagram"></i></a></li>
                @endif
                @if (gs('social_twitter'))
                    <li class="twitter"><a href="{{ gs('social_twitter') }}" target="_blank"><i
                                class="fab fa-twitter"></i></a></li>
                @endif
                @if (gs('social_linkedin'))
                    <li class="linkedin"><a href="{{ gs('social_linkedin') }}" target="_blank"><i
                                class="fab fa-linkedin"></i></a></li>
                @endif
            </ul>
        @endif
    </div>
</div>
{{-- / Sidebar Area ======== --}}
