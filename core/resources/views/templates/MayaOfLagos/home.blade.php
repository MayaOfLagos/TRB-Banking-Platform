@extends($activeTemplate . 'layouts.frontend')

@section('content')
    {{-- Hero Section Style six --}}
    <section class="hero-section style-6 mx-30 nhb-br-0  lg-mx-0 mt-30 lg-mt-0">
        <div class="bg image"><img src="{{ asset('assets/templates/MayaOfLagos/assets/images/banner/hm6-bg01.jpg') }}"
                alt=""></div>
        <div class="hero-scroll smooth">
            <a href="#about-section" id="scrollLink">
                <div class="scroll-me">@lang('Scroll Down')</div>
                <div class="hero-social_arrow">
                    <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/arrow-down-long.png') }}"
                        alt="">
                </div>
            </a>
        </div>
        <div class="p-top-right wow slideInDown" data-wow-delay="500ms" data-wow-duration="1000ms"><img
                src="{{ asset('assets/templates/MayaOfLagos/assets/images/banner/home4-shape01.png') }}" alt="shape">
        </div>
        <div class="hero-slider-6 swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="container">
                        <div class="row align-items-center">
                            {{-- Left Side (Text Content) --}}
                            <div class="col-lg-7">
                                <div class="hero-content md-mb-50">
                                    <h1 class="title">@lang('Unlock Premium Rebates &') <span>@lang('Maximize Your Returns with')</span><br>@lang('Intelligent Banking')</h1>
                                    <div class="text">
                                        <div class="icon spin"><img
                                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/shapes/star3.png') }}"
                                                alt=""></div>
                                        <p>@lang('Experience innovative banking with competitive rebates, secure transfers, and personalized financial services designed for your success.')
                                        </p>
                                    </div>
                                    <a href="{{ route('user.login') }}" class="theme-btn bg-color10">
                                        <span class="link-effect">
                                            <span class="effect-1">@lang('Open Account')</span>
                                            <span class="effect-1">@lang('Open Account')</span>
                                        </span><i class="fa-regular fa-arrow-right-long"></i>
                                    </a>
                                </div>
                            </div>
                            {{-- Right Side (Image) --}}
                            <div class="col-lg-5">
                                <div class="hero-right">
                                    <div class="image-box">
                                        <img src="{{ asset('assets/images/uploaded/trumps-1_17594744737628.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="container">
                        <div class="row align-items-center">
                            {{-- Left Side (Text Content) --}}
                            <div class="col-lg-7">
                                <div class="hero-content md-mb-50">
                                    <h1 class="title">@lang('Earn More with Every')
                                        <span>@lang('Transaction')</span><br>@lang('Premium Rebate Banking')</h1>
                                    <div class="text">
                                        <div class="icon spin"><img
                                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/shapes/star3.png') }}"
                                                alt=""></div>
                                        <p>@lang('Get rewarded for your banking activities with our comprehensive rebate program, featuring loans, investments, and secure money transfers.')
                                        </p>
                                    </div>
                                    <a href="{{ route('user.login') }}" class="theme-btn bg-color10">
                                        <span class="link-effect">
                                            <span class="effect-1">@lang('Start Earning')</span>
                                            <span class="effect-1">@lang('Start Earning')</span>
                                        </span><i class="fa-regular fa-arrow-right-long"></i>
                                    </a>
                                </div>
                            </div>
                            {{-- Right Side (Image) --}}
                            <div class="col-lg-5">
                                <div class="hero-right">
                                    <div class="image-box">
                                        <img src="{{ asset('assets/images/uploaded/3-1_17594730649409.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <div class="info-box style-2 bg-theme3 z-1 shape-mockup-wrap">
            <div class="inner-box">
                <div class="bg image"><img
                        src="{{ asset('assets/templates/MayaOfLagos/assets/images/banner/hm5-info-bg.png') }}"
                        alt=""></div>
                <div class="content">
                    <div class="awards"><span class="count-number odometer" data-count="16"></span><span
                            class="plus">+</span></div>
                    <p>Years of Banking <br> Excellence</p>
                </div>
                <div class="image p-bottom-right shape-mockup
                    " data-right="15px"><img
                        src="{{ asset('assets/images/uploaded/trump_17594717958014.png') }}"
                        alt=""></div>
            </div>
        </div>
    </section>
    {{-- Hero Section --}}

    {{-- about Section Style six --}}
    <section class="about-section style-6 space overflow-hidden bg-theme3" id="about-section">
        <div class="container">
            <div class="row gy-50">
                <div class="col-lg-6">
                    <div class="about-thumb-area mr-60 xl-mr-0">
                        <div class="about-slider swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="about-slide_thumb">
                                        <img src="{{ asset('assets/images/uploaded/3_17594713403941.png') }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="about-slide_thumb">
                                        <img src="{{ asset('assets/images/uploaded/1_17594713403379.png') }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="about-slide_thumb">
                                        <img src="{{ asset('assets/images/uploaded/4_17594713406766.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="array-button">
                                <button class="array-prev"><i class="fa-light fa-arrow-left-long"></i></button>
                                <button class="array-next active"><i class="fa-light fa-arrow-right-long"></i></button>
                            </div>
                        </div>
                        <div class="customar-box">
                            <div class="box-top">
                                <div class="awards"><span class="count-number odometer" data-count="1"></span><span
                                        class="plus">M+</span></div>
                                <div class="icon"><i class="fa-solid fa-circle-check"></i></div>
                            </div>
                            <div class="box-bottom">
                                <h6>@lang('Satisfied Banking
                                                                    Customers')</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content-wrapper">
                        <div class="title-area two">
                            <div class="sub-title"><span><i class="asterisk"></i></span>@lang('About Trump Rebate Banking')</div>
                            <h2 class="sec-title mb-25">@lang('Revolutionizing Banking with') <br> @lang('Smart Rebates &') <span
                                    class="bold">@lang('Financial Innovation')</span></h2>
                            <p class="sec-text text-gray">@lang('Trump Rebate Banking offers cutting-edge financial services with competitive rebates,') <br>
                                @lang('secure money transfers, and comprehensive banking solutions for modern customers.')
                            </p>
                        </div>
                        <div class="feature-list">
                            <div class="feature-item">
                                <div class="icon"><i class="flaticon-service"></i></div>
                                <p>@lang('High-Yield Rebate Programs')</p>
                            </div>
                            <div class="feature-item">
                                <div class="icon"><i class="flaticon-people"></i></div>
                                <p>@lang('Secure Banking Solutions')</p>
                            </div>
                        </div>
                        <div class="pt-35 pb-25">
                            <div class="border"><span class="bar"></span></div>
                        </div>
                        <ul class="features-list">
                            <li>@lang('Competitive interest rates on savings')</li>
                            <li>@lang('Instant money transfers worldwide')</li>
                        </ul>
                        <a href="{{ route('user.login') }}" class="theme-btn bg-dark mt-35">
                            <span class="link-effect">
                                <span class="effect-1">@lang('Learn More About Us')</span>
                                <span class="effect-1">@lang('Learn More About Us')</span>
                            </span><i class="fa-regular fa-arrow-right-long"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-top-right wow slideInRight"><img
                src="{{ asset('assets/templates/MayaOfLagos/assets/images/choose/shape01.png') }}" alt="about shape">
        </div>
        <div class="p-bottom-right wow img-anim-right"><img
                src="{{ asset('assets/templates/MayaOfLagos/assets/images/about/hm6-about-line.png') }}"
                alt="about shape"></div>
    </section>

    {{-- Feature Section five --}}
    <section class="feature-section space bg-dark style-5 mx-30 lg-mx-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="feature-title-area d-flex justify-content-between sm-flex-column sm-mb-30">
                        <div class="title-area white three">
                            <div class="sub-title"><span><i class="asterisk"></i></span>@lang('OUR BANKING SOLUTIONS')</div>
                            <h2 class="sec-title mb-0">@lang('Comprehensive Banking Services with') <span
                                    class="bold text-theme2">@lang('Maximum Rebates')</span><br> @lang('for Your Financial Success')</h2>
                        </div>
                        <div class="service-btn sm-justify-content-start">
                            <a href="{{ route('user.login') }}" class="theme-btn bg-color10">
                                <span class="link-effect">
                                    <span class="effect-1">@lang('View All Service')</span>
                                    <span class="effect-1">@lang('View All Service')</span>
                                </span><i class="fa-regular fa-arrow-right-long"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row gy-30">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-box-four dark">
                        <div class="inner">
                            <div class="image-box">
                                <div class="thumb"><img
                                        src="https://picsum.photos/424/300?random={{ rand(200, 299) }}"
                                        alt="Service 01" style="width: 424px; height: 300px; object-fit: cover;"></div>
                                <div class="service-icon">
                                    <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/target01.png') }}"
                                        alt="Icon">
                                </div>
                            </div>
                            <div class="content">
                                <h4 class="title"><a href="#">@lang('Rebate Banking') <br> @lang('Programs')</a></h4>
                                <p class="text">@lang('Earn competitive rebates on all your banking transactions with our innovative reward system.')</p>
                                <a href="#" class="service-btn">@lang('Learn More') <i
                                        class="fa fa-arrow-right-long"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-box-four dark current">
                        <div class="inner">
                            <div class="image-box">
                                <div class="thumb"><img
                                        src="https://picsum.photos/424/300?random={{ rand(300, 399) }}"
                                        alt="Service 02" style="width: 424px; height: 300px; object-fit: cover;"></div>
                                <div class="service-icon">
                                    <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/growth-chart3.png') }}"
                                        alt="Icon">
                                </div>
                            </div>
                            <div class="content">
                                <h4 class="title"><a href="#">@lang('Secure Money') <br> @lang('Transfers')</a></h4>
                                <p class="text">@lang('Fast, secure, and reliable money transfer services with competitive rates and instant processing worldwide.')</p>
                                <a href="#" class="service-btn">@lang('Learn More') <i
                                        class="fa fa-arrow-right-long"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-box-four dark">
                        <div class="inner">
                            <div class="image-box">
                                <div class="thumb"><img
                                        src="https://picsum.photos/424/300?random={{ rand(400, 499) }}"
                                        alt="Service 03" style="width: 424px; height: 300px; object-fit: cover;"></div>
                                <div class="service-icon">
                                    <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/rocket01.png') }}"
                                        alt="Icon">
                                </div>
                            </div>
                            <div class="content">
                                <h4 class="title"><a href="#">@lang('Investment &') <br> @lang('Loan Services')</a></h4>
                                <p class="text">@lang('Smart investment opportunities and flexible loan options with competitive rates and personalized financial planning.')</p>
                                <a href="#" class="service-btn">@lang('Learn More') <i
                                        class="fa fa-arrow-right-long"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- choose Section five --}}
    <section class="choose-section style-6 bg-theme3 space overflow-hidden">
        <div class="container-fluid px-150 xxl-px-80 lg-px-30 md-px-15">
            <div class="row gy-30">
                <div class="col-lg-4 col-xxl-4">
                    <div class="choose-content-wrapper">
                        <div class="title-area twoT">
                            <div class="sub-title"><span><i class="asterisk"></i></span>@lang('WHY CHOOSE TRUMP REBATE BANKING')</div>
                            <h2 class="sec-title">@lang('Leading the Future of') <br>@lang('Digital Banking with') <span
                                    class="bold">@lang('Rebate Innovation')</span> <br> @lang('Smart Financial Solutions')</h2>
                            <p class="sec-text text-gray">@lang('Experience the next generation of banking with our comprehensive rebate programs,') <br> @lang('secure transactions, and customer-centric financial services designed for your success.')
                            </p>
                        </div>
                        <ul class="features-list">
                            <li>@lang('Competitive Rebate Rates on All Transactions')</li>
                            <li>@lang('Advanced Security & Fraud Protection')</li>
                        </ul>
                        <a href="{{ route('user.login') }}" class="theme-btn bg-dark mt-35">
                            <span class="link-effect">
                                <span class="effect-1">@lang('Discover Our Advantages')</span>
                                <span class="effect-1">@lang('Discover Our Advantages')</span>
                            </span><i class="fa-regular fa-arrow-right-long"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-xxl-4 md-d-none">
                    <div class="choose-image-wrapper">
                        <div class="thumb-bg"><img
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/choose/hm6-bg01.png') }}"
                                alt=""></div>
                        <div class="thumb"><img
                                src="{{ asset('assets/images/uploaded/story-1-1-removebg-preview_17593049692148.png') }}"
                                alt=""></div>
                    </div>
                </div>
                <div class="col-lg-4  col-xxl-4">
                    <div class="choose-right-wrapper">
                        <div class="right-top">
                            <div class="icon"><i class="fa-solid fa-check"></i></div>
                            <h4 class="text">
                                @lang("We're Committed to Your") <br>
                                @lang('Financial Success')
                            </h4>
                        </div>
                        <div class="py-40">
                            <div class="border"></div>
                        </div>
                        <div class="featured-box mb-45">
                            <div class="icon"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/hm6-choose_icon01.png') }}"
                                    alt=""></div>
                            <div class="content">
                                <h4>@lang('Rebate Excellence')</h4>
                                <p>@lang('Maximize your earnings with our industry-leading rebate programs on all banking activities.')</p>
                            </div>
                        </div>
                        <div class="featured-box mb-45">
                            <div class="icon"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/hm6-choose_icon02.png') }}"
                                    alt=""></div>
                            <div class="content">
                                <h4>@lang('Secure Transactions')</h4>
                                <p>@lang('Bank with confidence using our advanced security systems and encrypted transfer protocols.')</p>
                            </div>
                        </div>
                        <div class="featured-box">
                            <div class="icon"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/hm6-choose_icon03.png') }}"
                                    alt=""></div>
                            <div class="content">
                                <h4>@lang('24/7 Support')</h4>
                                <p>@lang('Access round-the-clock customer support and personalized banking assistance whenever you need it.')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="wrapper-section br-20 lg-br-0 overflow-hidden mx-30 lg-mx-0">
        {{-- Marquee Section --}}
        <div class="marquee-section style-3">
            <div class="bg image"><img
                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/marquee/hm5-bg01.jpg') }}" alt="">
            </div>
            <div class="container-fluid p-0 overflow-hidden">
                <div class="slider__marquee clearfix marquee-wrap">
                    <ul class="marquee_mode marquee__group">
                        <li class="item m-item"><img class="icon"
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/marquee/hm5-star01.png') }}"
                                alt="">@lang('Rebate Banking')</li>
                        <li class="item m-item"><img class="icon"
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/marquee/hm5-star01.png') }}"
                                alt="">@lang('Money Transfers')</li>
                        <li class="item m-item"><img class="icon"
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/marquee/hm5-star01.png') }}"
                                alt="">@lang('Investment Services')</li>

                        <li class="item m-item"><img class="icon"
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/marquee/hm5-star01.png') }}"
                                alt="">@lang('Rebate Banking')</li>
                        <li class="item m-item"><img class="icon"
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/marquee/hm5-star01.png') }}"
                                alt="">@lang('Money Transfers')</li>
                        <li class="item m-item"><img class="icon"
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/marquee/hm5-star01.png') }}"
                                alt="">@lang('Investment Services')</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Video Section Three --}}
        <div class="video-section bg-theme3 style-3 position-relative z-1" id="video">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 p-0">
                        <div class="video-area position-relative">
                            <div class="video-box">
                                <a class="popup-video play-btn style-2" href="https://www.youtube.com/watch?v=E7bKpwXdquI"
                                    data-fancybox="video-gallery">
                                    <i class="fa-sharp fa-solid fa-play"></i>
                                </a>
                            </div>
                            <div class="thumb"><img class="mw-inherit"
                                    src="{{ asset('assets/images/uploaded/trb-banking-attachments_17593917157907.png') }}"
                                    alt=""></div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="stats-container">
                <div class="stat-box bg-theme br_tl-10 white">
                    <div class="count-box"><span class="count-number odometer" data-count="50"></span>k+</div>
                    <p class="text">@lang('Successful Transactions')</p>
                </div>
                <div class="stat-box bg-theme2 br_br-10 dark">
                    <div class="count-box"><span class="count-number odometer" data-count="2.5"></span>M+</div>
                    <p class="text">@lang('Rebates Paid Out')</p>
                </div>
            </div>
        </div>

        {{--  Service Section six --}}
        <section class="service-section style-6 bg-white space overflow-hidden">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="service-title-area d-flex justify-content-between sm-flex-column sm-mb-30">
                            <div class="title-area dark three">
                                <div class="sub-title"><span><i class="asterisk"></i></span>@lang('OUR BANKING SERVICES')</div>
                                <h2 class="sec-title mb-0">@lang('Comprehensive Banking Solutions with') <br> <span
                                        class="bold">@lang('Rebate Rewards')</span></h2>
                            </div>
                            <div class="service-btn sm-justify-content-start">
                                <a href="{{ route('user.login') }}" class="theme-btn bg-transparent">
                                    <span class="link-effect">
                                        <span class="effect-1">@lang('View All Service')</span>
                                        <span class="effect-1">@lang('View All Service')</span>
                                    </span><i class="fa-regular fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="service-wrapper">
                            <div class="service-single-item">
                                <div class="item-left">
                                    <div class="image"><img
                                            src="{{ asset('assets/images/uploaded/1_17594582739894.png') }}"
                                            alt=""></div>
                                    <div class="item-wrap">
                                        <div class="icon"><img
                                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/hm6-icon01.png') }}"
                                                alt=""></div>
                                        <h3 class="text"><a href="#">@lang('Rebate Banking Solutions')</a></h3>
                                    </div>
                                </div>
                                <div class="item-right">
                                    <div class="item-right-inner">
                                        <p>@lang('Maximize your savings with our comprehensive rebate banking programs designed to reward every transaction and build your financial future.')</p>
                                        <a href="#" class="icon"><i class="fa-solid fa-angle-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="service-single-item">
                                <div class="item-left">
                                    <div class="image"><img
                                            src="{{ asset('assets/images/uploaded/2_17594582737475.png') }}"
                                            alt=""></div>
                                    <div class="item-wrap">
                                        <div class="icon"><img
                                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/hm6-icon02.png') }}"
                                                alt=""></div>
                                        <h3 class="text"><a href="#">@lang('Secure Money Transfers')</a></h3>
                                    </div>
                                </div>
                                <div class="item-right">
                                    <div class="item-right-inner">
                                        <p>@lang('Experience fast, secure, and reliable money transfer services with competitive rates and instant processing for all your financial needs.')</p>
                                        <a href="#" class="icon"><i class="fa-solid fa-angle-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="service-single-item">
                                <div class="item-left">
                                    <div class="image"><img
                                            src="{{ asset('assets/images/uploaded/3_17594582731699.png') }}"
                                            alt=""></div>
                                    <div class="item-wrap">
                                        <div class="icon"><img
                                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/hm6-icon03.png') }}"
                                                alt=""></div>
                                        <h3 class="text"><a href="#">@lang('Investment Banking')</a></h3>
                                    </div>
                                </div>
                                <div class="item-right">
                                    <div class="item-right-inner">
                                        <p>@lang('Smart investment opportunities and portfolio management services with expert guidance to help you grow your wealth effectively.')</p>
                                        <a href="#" class="icon"><i class="fa-solid fa-angle-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="service-single-item">
                                <div class="item-left">
                                    <div class="image"><img
                                            src="{{ asset('assets/images/uploaded/4_17594582736420.png') }}"
                                            alt=""></div>
                                    <div class="item-wrap">
                                        <div class="icon"><img
                                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/icons/hm6-icon04.png') }}"
                                                alt=""></div>
                                        <h3 class="text"><a href="#">@lang('Loan & Credit Services')</a></h3>
                                    </div>
                                </div>
                                <div class="item-right">
                                    <div class="item-right-inner">
                                        <p>@lang('Flexible loan options and credit services with competitive rates and personalized terms to meet your financial goals.')</p>
                                        <a href="#" class="icon"><i class="fa-solid fa-angle-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>


    {{-- project section style-six --}}

    <section class="project-section style-6 space-top bg-theme3 mx-30 lg-mx-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="project-title-area">
                        <div class="title-area dark three">
                            <div class="sub-title text-dark"><span><i class="asterisk"></i></span>@lang('SUCCESS STORIES')</div>
                            <h2 class="sec-title mb-0">@lang('Real Results from Our') <span class="bold">@lang('Banking Solutions')</span>
                            </h2>
                        </div>
                        <div class="project-btn-wrapper">
                            <div class="array-button">
                                <button class="array-prev"><i class="fa fa-arrow-left-long"></i></button>
                                <button class="array-next active"><i class="fa fa-arrow-right-long"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="project-slider swiper z-1 position-relative">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="project-wrapper position-relative z-1">
                        <div class="project-thum"><img
                                src="{{ asset('assets/images/uploaded/1_17594595468736.png') }}"
                                alt=""></div>
                        <div class="project-content position-relative z-1">
                            <div class="project-box-title">
                                <p class="sub-title">@lang('SUCCESS STORY')</p>
                                <h2 class="title">@lang('Rebate Program Success')</h2>
                            </div>
                            <p>@lang('Our client achieved 35% savings through our comprehensive rebate banking program, maximizing returns on all transactions and building substantial wealth.')</p>
                            @php
                                $maleNames = ['Michael Johnson', 'David Williams', 'James Brown', 'Robert Davis', 'Christopher Miller', 'Daniel Wilson', 'Matthew Moore', 'Anthony Taylor', 'Donald Anderson', 'Mark Thomas', 'Steven Jackson', 'Paul White', 'Andrew Harris', 'Joshua Martin', 'Kenneth Thompson', 'Kevin Garcia', 'Brian Martinez', 'George Robinson', 'Edward Clark', 'Ronald Rodriguez', 'Timothy Lewis', 'Jason Lee', 'Jeffrey Walker', 'Ryan Hall', 'Jacob Allen', 'Gary Young', 'Nicholas King', 'Eric Wright', 'Stephen Lopez', 'Jonathan Hill', 'Larry Scott', 'Justin Green', 'Scott Adams', 'Brandon Baker', 'Benjamin Nelson', 'Samuel Carter', 'Frank Mitchell', 'Gregory Perez', 'Raymond Roberts', 'Patrick Turner', 'Alexander Phillips', 'Jack Campbell', 'Dennis Parker', 'Jerry Evans', 'Tyler Edwards', 'Aaron Collins', 'Jose Stewart', 'Henry Morris', 'Douglas Rogers', 'Peter Reed'];
                                $femaleNames = ['Emily Johnson', 'Sarah Williams', 'Jessica Brown', 'Ashley Davis', 'Jennifer Miller', 'Amanda Wilson', 'Melissa Moore', 'Michelle Taylor', 'Stephanie Anderson', 'Nicole Thomas', 'Elizabeth Jackson', 'Rebecca White', 'Laura Harris', 'Kimberly Martin', 'Lisa Thompson', 'Amy Garcia', 'Angela Martinez', 'Heather Robinson', 'Rachel Clark', 'Maria Rodriguez', 'Samantha Lewis', 'Karen Lee', 'Nancy Walker', 'Betty Hall', 'Helen Allen', 'Sandra Young', 'Donna King', 'Carol Wright', 'Ruth Lopez', 'Sharon Hill', 'Michelle Scott', 'Laura Green', 'Sarah Adams', 'Kimberly Baker', 'Deborah Nelson', 'Jessica Carter', 'Shirley Mitchell', 'Cynthia Perez', 'Angela Roberts', 'Melissa Turner', 'Brenda Phillips', 'Amy Campbell', 'Anna Parker', 'Rebecca Evans', 'Virginia Edwards', 'Kathleen Collins', 'Pamela Stewart', 'Martha Morris', 'Debra Rogers', 'Amanda Reed'];
                                $allNames = array_merge($maleNames, $femaleNames);
                                $durations = ['6 Months', '8 Months', '9 Months', '10 Months', '12 Months', '14 Months', '15 Months', '18 Months', '20 Months', '24 Months', '30 Months', '36 Months'];
                                $savings = ['$25K+ Savings', '$35K+ Savings', '$40K+ Savings', '$50K+ Savings', '$60K+ Savings', '$75K+ Savings', '$85K+ Savings', '$100K+ Savings', '$120K+ Savings', '$150K+ Savings'];
                                
                                $client1 = $allNames[array_rand($allNames)];
                                $duration1 = $durations[array_rand($durations)];
                                $result1 = $savings[array_rand($savings)];
                            @endphp
                            <ul class="project-list">
                                <li><i class="fa-solid fa-check"></i><span>@lang('Client') :</span>{{ $client1 }}</li>
                                <li><i class="fa-solid fa-check"></i><span>@lang('Duration') :</span>{{ $duration1 }}</li>
                                <li><i class="fa-solid fa-check"></i><span>@lang('Result') :</span>{{ $result1 }}</li>
                            </ul>
                            <div class="pt-35 pb-35">
                                <div class="border white"><span class="bar"></span></div>
                            </div>
                            <div class="project-btn">
                                <a href="{{ route('user.login') }}" class="theme-btn bg-theme2">
                                    <span class="link-effect">
                                        <span class="effect-1">@lang('View Details')</span>
                                        <span class="effect-1">@lang('View Details')</span>
                                    </span><i class="fa-regular fa-arrow-right-long"></i>
                                </a>
                            </div>
                            <div class="p-top-right wow slideInRight"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/project/hm6-project-shape.png') }}"
                                    alt="about shape"></div>
                            <div class="p-bottom-right wow img-anim-right"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/project/hm6-project-line.png') }}"
                                    alt="about shape"></div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="project-wrapper position-relative z-1">
                        <div class="project-thum"><img
                                src="{{ asset('assets/images/uploaded/2_17594595466897.png') }}"
                                alt=""></div>
                        <div class="project-content position-relative z-1">
                            <div class="project-box-title">
                                <p class="sub-title">@lang('SUCCESS STORY')</p>
                                <h2 class="title">@lang('Investment Growth')</h2>
                            </div>
                            <p>@lang('Through our expert investment banking services, this client grew their portfolio by 85% in just 18 months with strategic asset allocation.')</p>
                            @php
                                $growthRates = ['45% Growth', '55% Growth', '65% Growth', '75% Growth', '85% Growth', '95% Growth', '105% Growth', '120% Growth', '135% Growth', '150% Growth'];
                                
                                $client2 = $allNames[array_rand($allNames)];
                                $duration2 = $durations[array_rand($durations)];
                                $result2 = $growthRates[array_rand($growthRates)];
                            @endphp
                            <ul class="project-list">
                                <li><i class="fa-solid fa-check"></i><span>@lang('Client') :</span>{{ $client2 }}</li>
                                <li><i class="fa-solid fa-check"></i><span>@lang('Duration') :</span>{{ $duration2 }}</li>
                                <li><i class="fa-solid fa-check"></i><span>@lang('Result') :</span>{{ $result2 }}</li>
                            </ul>
                            <div class="pt-35 pb-35">
                                <div class="border white"><span class="bar"></span></div>
                            </div>
                            <div class="project-btn">
                                <a href="{{ route('user.login') }}" class="theme-btn bg-theme2">
                                    <span class="link-effect">
                                        <span class="effect-1">@lang('View Details')</span>
                                        <span class="effect-1">@lang('View Details')</span>
                                    </span><i class="fa-regular fa-arrow-right-long"></i>
                                </a>
                            </div>
                            <div class="p-top-right wow slideInRight"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/project/hm6-project-shape.png') }}"
                                    alt="about shape"></div>
                            <div class="p-bottom-right wow img-anim-right"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/project/hm6-project-line.png') }}"
                                    alt="about shape"></div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="project-wrapper position-relative z-1">
                        <div class="project-thum"><img
                                src="{{ asset('assets/images/uploaded/3_17594599231324.png') }}"
                                alt=""></div>
                        <div class="project-content position-relative z-1">
                            <div class="project-box-title">
                                <p class="sub-title">@lang('BANKING')</p>
                                <h2 class="title">@lang('Secure Money Transfer')</h2>
                            </div>
                            <p>@lang('Successfully processed over $2.5 million in secure international transfers with zero security incidents. Our advanced banking technology ensures fast, reliable, and protected transactions for our valued clients worldwide.')</p>
                            @php
                                $transferVolumes = ['$1.2M Transferred', '$1.5M Transferred', '$1.8M Transferred', '$2.0M Transferred', '$2.5M Transferred', '$3.0M Transferred', '$3.5M Transferred', '$4.0M Transferred', '$4.5M Transferred', '$5.0M Transferred'];
                                
                                $client3 = $allNames[array_rand($allNames)];
                                $duration3 = $durations[array_rand($durations)];
                                $result3 = $transferVolumes[array_rand($transferVolumes)];
                            @endphp
                            <ul class="project-list">
                                <li><i class="fa-solid fa-check"></i><span>@lang('Client') :</span>{{ $client3 }}
                                </li>
                                <li><i class="fa-solid fa-check"></i><span>@lang('Duration') :</span>{{ $duration3 }}
                                </li>
                                <li><i class="fa-solid fa-check"></i><span>@lang('Volume') :</span>{{ $result3 }}
                                </li>
                            </ul>
                            <div class="pt-35 pb-35">
                                <div class="border white"><span class="bar"></span></div>
                            </div>
                            <div class="project-btn">
                                <a href="{{ route('user.login') }}" class="theme-btn bg-theme2">
                                    <span class="link-effect">
                                        <span class="effect-1">@lang('View Details')</span>
                                        <span class="effect-1">@lang('View Details')</span>
                                    </span><i class="fa-regular fa-arrow-right-long"></i>
                                </a>
                            </div>
                            <div class="p-top-right wow slideInRight"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/project/hm6-project-shape.png') }}"
                                    alt="about shape"></div>
                            <div class="p-bottom-right wow img-anim-right"><img
                                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/project/hm6-project-line.png') }}"
                                    alt="about shape"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Process Section five --}}
    <section class="process-section space bg-theme3 overflow-hidden style-5">
        <div class="container">
            <div class="title-area three text-center">
                <div class="sub-title"><span><i class="asterisk"></i></span>@lang('BANKING PROCESS')</div>
                <h2 class="sec-title">@lang('Secure banking made') <span class="bold">@lang('simple')</span></h2>
            </div>
            <div class="row gy-30">
                <div class="col-lg-4 col-md-6 col-sm-6 wow fadeInLeft">
                    <div class="process-single-box br-10">
                        <div class="inner-box">
                            <div class="header">
                                <div class="icon"><i class="icon-comercial"></i></div>
                                <h4 class="title m-0">@lang('Account Setup') <span
                                        class="fw-normal">@lang('& Verification')</span></h4>
                            </div>
                            <p class="text">@lang('Quick and secure account opening with advanced KYC verification. Get started with your banking journey in minutes with our streamlined digital onboarding process.')</p>
                            <div class="box-footer">
                                <div class="box-count">
                                    <span>01</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 wow fadeInRight">
                    <div class="process-single-box br-10 current">
                        <div class="inner-box">
                            <div class="header">
                                <div class="icon"><i class="icon-infomsg"></i></div>
                                <h4 class="title m-0">@lang('Secure Transactions') <span
                                        class="fw-normal">@lang('& Transfers')</span></h4>
                            </div>
                            <p class="text">@lang('Execute secure money transfers and payments with bank-level encryption. Our advanced security protocols protect every transaction, ensuring your funds are safe and arrive quickly.')</p>
                            <div class="box-footer">
                                <div class="box-count">
                                    <span>02</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 wow fadeInLeft">
                    <div class="process-single-box br-10">
                        <div class="inner-box">
                            <div class="header">
                                <div class="icon"><i class="icon-finished"></i></div>
                                <h4 class="title m-0">@lang('Rebate Rewards') <span
                                        class="fw-normal">@lang('& Benefits')</span>
                                </h4>
                            </div>
                            <p class="text">@lang('Earn exclusive rebates on every transaction and enjoy premium banking benefits. Maximize your savings with our comprehensive rebate program designed for loyal customers.')</p>
                            <div class="box-footer">
                                <div class="box-count">
                                    <span>03</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="wrapper-section br-20 lg-br-0 overflow-hidden mx-30 lg-mx-0 mb-30 md-mb-0">

        {{-- Team Section four --}}
        <section class="team-section style-4 space bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="team-title-area mb-60 md-mb-40">
                            <div class="title-area mb-0">
                                <div class="sub-title"><span><i class="asterisk"></i></span>@lang('EXPERT TEAM')</div>
                                <h2 class="sec-title mb-0">@lang('Meet our') <span
                                        class="bold">@lang('experts')</span></h2>
                            </div>
                            <div class="team-btn d-flex align-items-center">
                                <a href="{{ route('user.login') }}" class="theme-btn bg-dark">
                                    <span class="link-effect">
                                        <span class="effect-1">@lang('View All Experts')</span>
                                        <span class="effect-1">@lang('View All Experts')</span>
                                    </span><i class="fa-regular fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
                // Team Members Data
                $maleTeamNames = [
                    'Michael Anderson', 'David Thompson', 'James Mitchell', 'Robert Harrison', 'Christopher Bennett',
                    'Daniel Patterson', 'Matthew Coleman', 'Anthony Richardson', 'Donald Martinez', 'Mark Stevens',
                    'Steven Phillips', 'Paul Henderson', 'Andrew Campbell', 'Joshua Parker', 'Kenneth Brooks',
                    'Kevin Henderson', 'Brian Foster', 'George Butler', 'Edward Barnes', 'Ronald Powell',
                    'Timothy Jenkins', 'Jason Perry', 'Jeffrey Russell', 'Ryan Griffin', 'Jacob Hayes',
                    'Gary Dixon', 'Nicholas Kelly', 'Eric Patterson', 'Stephen Murphy', 'Jonathan Rivera',
                    'Larry Cooper', 'Justin Richardson', 'Scott Reed', 'Brandon Bailey', 'Benjamin Foster',
                    'Samuel Collins', 'Frank Peterson', 'Gregory Sanders', 'Raymond Price', 'Patrick Bennett',
                    'Alexander Ross', 'Jack Henderson', 'Dennis Coleman', 'Jerry Hughes', 'Tyler Jenkins',
                    'Aaron Richardson', 'Jose Watson', 'Henry Brooks', 'Douglas Gray', 'Peter Howard'
                ];
                
                $femaleTeamNames = [
                    'Emily Richardson', 'Sarah Anderson', 'Jessica Thompson', 'Ashley Mitchell', 'Jennifer Bennett',
                    'Amanda Patterson', 'Melissa Coleman', 'Michelle Henderson', 'Stephanie Brooks', 'Nicole Foster',
                    'Elizabeth Phillips', 'Rebecca Butler', 'Laura Barnes', 'Kimberly Powell', 'Lisa Jenkins',
                    'Amy Perry', 'Angela Russell', 'Heather Griffin', 'Rachel Hayes', 'Maria Dixon',
                    'Samantha Kelly', 'Karen Murphy', 'Nancy Rivera', 'Betty Cooper', 'Helen Richardson',
                    'Sandra Reed', 'Donna Bailey', 'Carol Foster', 'Ruth Collins', 'Sharon Peterson',
                    'Michelle Sanders', 'Laura Price', 'Sarah Bennett', 'Kimberly Ross', 'Deborah Henderson',
                    'Jessica Coleman', 'Shirley Hughes', 'Cynthia Jenkins', 'Angela Richardson', 'Melissa Watson',
                    'Brenda Brooks', 'Amy Gray', 'Anna Howard', 'Rebecca Morgan', 'Virginia Bailey',
                    'Kathleen Foster', 'Pamela Collins', 'Martha Peterson', 'Debra Sanders', 'Amanda Price'
                ];
                
                $positions = [
                    'Chief Banking Officer', 'Investment Advisor', 'Risk Management Specialist', 'Customer Service Manager',
                    'Financial Analyst', 'Senior Banking Consultant', 'Portfolio Manager', 'Credit Risk Manager',
                    'Wealth Management Advisor', 'Operations Director', 'Compliance Officer', 'Treasury Manager',
                    'Loan Officer', 'Branch Manager', 'Digital Banking Manager', 'Mortgage Specialist',
                    'Private Banking Advisor', 'Financial Planning Director', 'Corporate Banking Manager', 'Asset Manager'
                ];
                
                // Generate 4 random team members
                $teamMembers = [];
                $usedNames = [];
                
                for ($i = 0; $i < 4; $i++) {
                    $gender = rand(0, 1) ? 'male' : 'female';
                    
                    if ($gender === 'male') {
                        do {
                            $name = $maleTeamNames[array_rand($maleTeamNames)];
                        } while (in_array($name, $usedNames));
                        $imageGender = 'men';
                    } else {
                        do {
                            $name = $femaleTeamNames[array_rand($femaleTeamNames)];
                        } while (in_array($name, $usedNames));
                        $imageGender = 'women';
                    }
                    
                    $usedNames[] = $name;
                    $position = $positions[array_rand($positions)];
                    $imageId = rand(1, 99); // Random ID for diverse images
                    
                    $teamMembers[] = [
                        'name' => $name,
                        'position' => $position,
                        'image' => "https://randomuser.me/api/portraits/{$imageGender}/{$imageId}.jpg",
                        'gender' => $gender
                    ];
                }
            @endphp
            
            <div class="container-fluid px-100 xxl-px-15 xl-px-15">
                <div class="row gy-25">
                    @foreach($teamMembers as $member)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="team-box-four">
                            <div class="inner-box">
                                <div class="image-box">
                                    <div class="image">
                                        <img src="{{ $member['image'] }}" 
                                             alt="{{ $member['name'] }}"
                                             style="width: 398px; height: 480px; object-fit: cover;">
                                    </div>
                                    <div class="share-btn-wrap">
                                        <ul class="social-link">
                                            <li><a href="#"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                            <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                                            <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="profile-info">
                                    <h4 class="name"><a href="{{ route('user.login') }}">{{ $member['name'] }}</a></h4>
                                    <p class="position">{{ __($member['position']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Call To Action Section Four --}}
        <section class="cta-section style-4 bg-theme2">
            <div class="bg image mbm-color-dodge"><img
                    src="{{ asset('assets/templates/MayaOfLagos/assets/images/cta/hm6-bg01.png') }}" alt="">
            </div>
            <div class="overlay"></div>
            <div class="container py-75">
                <div class="row gy-30">
                    <div class="col-lg-6">
                        <div class="social-proof">
                            <div class="icon"><i class="fa-solid fa-check"></i></div>
                            <p class="text">@lang('We make the most creative Digital') <br> @lang('Solutions for your Business')</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="cta-btn text-right md-text-left">
                            <a href="{{ route('user.login') }}" class="theme-btn bg-dark">
                                <span class="link-effect">
                                    <span class="effect-1">@lang('Contact Us Now')</span>
                                    <span class="effect-1">@lang('Contact Us Now')</span>
                                </span><i class="fa-regular fa-arrow-right-long"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    {{-- Testimonial Section six --}}
    <section class="testimonial-section style-6 space overflow-hidden bg-dark mx-30 lg-mx-0 br-20 lg-br-0 ">
        <div class="shape-mockup jump" data-bottom="70px" data-left="35%"><img
                src="{{ asset('assets/templates/MayaOfLagos/assets/images/testimonial/hm6-dotshape.png') }}"
                alt="shape"></div>
        <div class="container">
            <div class="row gy-30">
                <div class="col-lg-4 col-md-6">
                    <div class="testi-content-wrap">
                        <div class="title-area white two">
                            <div class="sub-title">
                                <span><i class="asterisk"></i></span>TESTIMONIAL
                            </div>
                            <h2 class="sec-title">What’s our satisfied <br> customers <span
                                    class="bold text-theme2">feedback</span> <br> about Trump Rebate Banking</h2>
                        </div>
                        <div class="testi-clutch mb-35">
                            <div class="review-card-three">
                                <span class="avarage-rating text-theme2">4.9</span>
                                <div class="rating-inner">
                                    <span class="rating-text text-white">Rating (30 Reviews)</span>
                                    <span class="stars">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star-half-stroke"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="testi-btn-wrapper">
                            <div class="array-button">
                                <button class="array-prev"><i class="fa fa-arrow-left-long"></i></button>
                                <button class="array-next active"><i class="fa fa-arrow-right-long"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-lg-8 col-md-6">
                    <div class="testi-slider-6 swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="testimonial-card-five style-6">
                                    <div class="inner-box">
                                        <div class="content">
                                            <h4 class="title">
                                                <i class="fa-solid fa-quote-left"></i>
                                                @lang('Excellent Banking Services!')
                                            </h4>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                                <span>4.5</span>
                                            </div>
                                            <p class="text">@lang("Trump Rebate Banking has completely transformed how I manage my finances. The rebate program is incredible - I've saved over $2,000 in just six months! Their customer service is outstanding and the mobile app makes banking so convenient.")</p>
                                        </div>
                                        <div class="border mt-35 mb-40"></div>
                                        <div class="user-info">
                                            <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="User Image"
                                                class="user-image">
                                            <div>
                                                <h5 class="user-name">Sarah Mitchell</h5>
                                                <p class="user-title">United States</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-card-five style-6">
                                    <div class="inner-box">
                                        <div class="content">
                                            <h4 class="title">
                                                <i class="fa-solid fa-quote-left"></i>
                                                @lang('Amazing Investment Growth!')
                                            </h4>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                                <span>5.0</span>
                                            </div>
                                            <p class="text">@lang("The investment opportunities at Trump Rebate Banking are outstanding. I've grown my portfolio by 150% in just two years thanks to their expert financial advisors and smart rebate incentives. Their customer support is always there when I need them.")</p>
                                        </div>
                                        <div class="border mt-35 mb-40"></div>
                                        <div class="user-info">
                                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User Image"
                                                class="user-image">
                                            <div>
                                                <h5 class="user-name">Michael Chen</h5>
                                                <p class="user-title">Singapore</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-card-five style-6">
                                    <div class="inner-box">
                                        <div class="content">
                                            <h4 class="title">
                                                <i class="fa-solid fa-quote-left"></i>
                                                @lang('Exceptional Customer Service!')
                                            </h4>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                                <span>4.5</span>
                                            </div>
                                            <p class="text">@lang("Trump Rebate Banking's customer service is absolutely exceptional. I've never experienced such personalized attention and quick resolution to my banking needs. The rebate program has saved me thousands, and I couldn't be happier with their professional team.")</p>
                                        </div>
                                        <div class="border mt-35 mb-40"></div>
                                        <div class="user-info">
                                            <img src="https://randomuser.me/api/portraits/women/28.jpg" alt="User Image"
                                                class="user-image">
                                            <div>
                                                <h5 class="user-name">Emily Rodriguez</h5>
                                                <p class="user-title">Canada</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-card-five style-6">
                                    <div class="inner-box">
                                        <div class="content">
                                            <h4 class="title">
                                                <i class="fa-solid fa-quote-left"></i>
                                                @lang('Secure & Reliable Banking!')
                                            </h4>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                                <span>5.0</span>
                                            </div>
                                            <p class="text">@lang("I've been with Trump Rebate Banking for three years now, and their security measures give me complete peace of mind. Fast international transfers, competitive rates, and the best rebate program in the industry. Absolutely recommend them to anyone seeking premium banking services.")</p>
                                        </div>
                                        <div class="border mt-35 mb-40"></div>
                                        <div class="user-info">
                                            <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="User Image"
                                                class="user-image">
                                            <div>
                                                <h5 class="user-name">David Thompson</h5>
                                                <p class="user-title">United Kingdom</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Banking Plans Section --}}
    @php
        // Fetch latest active plans from database
        $loanPlan = \App\Models\LoanPlan::where('status', 1)->latest()->first();
        $fdrPlan = \App\Models\FdrPlan::where('status', 1)->latest()->first();
        $dpsPlan = \App\Models\DpsPlan::where('status', 1)->latest()->first();
    @endphp
    
    <section class="pricing-section style-3 space bg-theme3">
        <div class="p-top-right  wow slideInRight"><img
                src="{{ asset('assets/templates/MayaOfLagos/assets/images/choose/shape01.png') }}" alt="pricing shape">
        </div>
        <div class="p-bottom-left wow img-anim-left"><img
                src="{{ asset('assets/templates/MayaOfLagos/assets/images/pricing/hm6-pricing-line.png') }}"
                alt="pricing"></div>
        <div class="container">
            <div class="row gy-30">
                <div class="col-lg-6">
                    <div class="pricing-content-wrapper">
                        <div class="title-area">
                            <div class="sub-title"><span><i class="asterisk"></i></span>@lang('BANKING PROGRAMS')</div>
                            <h2 class="sec-title">@lang('Explore our financial') <br><span class="bold">@lang('investment programs') </span>
                                <br>@lang('start growing today')</h2>
                            <p class="sec-text text-gray">@lang('Discover our comprehensive investment and savings programs designed to maximize your returns.') <br> @lang('From flexible loans to high-yield deposits and pension schemes, we offer the perfect solution')
                                <br>@lang('for your financial growth and security')</p>
                        </div>
                        <a href="{{ route('user.login') }}" class="theme-btn bg-dark">
                            <span class="link-effect">
                                <span class="effect-1">@lang('View All Programs')</span>
                                <span class="effect-1">@lang('View All Programs')</span>
                            </span><i class="fa-regular fa-arrow-right-long"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    @if($loanPlan)
                    <div class="pricing-single-box mb-20">
                        <div class="pricing-box-left">
                            <div class="pricing-title">
                                <p>{{ $loanPlan->name }}</p>
                                <h2><sup></sup>{{ showAmount($loanPlan->minimum_amount) }}</h2>
                            </div>
                            <a href="{{ route('user.login') }}" class="theme-btn bg-theme3">
                                <span class="link-effect">
                                    <span class="effect-1">@lang('Apply Now')</span>
                                    <span class="effect-1">@lang('Apply Now')</span>
                                </span><i class="fa-regular fa-arrow-right-long"></i>
                            </a>
                        </div>
                        <div class="pricing-box-right">
                            <ul class="pricing-list">
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Max Amount'): {{ showAmount($loanPlan->maximum_amount) }} {{ __(gs('cur_sym')) }}</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Total Installments'): {{ $loanPlan->total_installment }}</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Per Installment'): {{ showAmount($loanPlan->per_installment) }}%</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Installment Interval'): {{ $loanPlan->installment_interval }} @lang('Days')</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Delay Charge'): {{ showAmount($loanPlan->fixed_charge) }} + {{ showAmount($loanPlan->percent_charge) }}%</li>
                            </ul>
                        </div>
                    </div>
                    @endif
                    
                    @if($fdrPlan)
                    <div class="pricing-single-box current mb-20">
                        <div class="pricing-box-left">
                            <div class="pricing-title">
                                <p>{{ $fdrPlan->name }}</p>
                                <h2><sup></sup>{{ showAmount($fdrPlan->minimum_amount) }}</h2>
                            </div>
                            <a href="{{ route('user.login') }}" class="theme-btn bg-theme3">
                                <span class="link-effect">
                                    <span class="effect-1">@lang('Invest Now')</span>
                                    <span class="effect-1">@lang('Invest Now')</span>
                                </span><i class="fa-regular fa-arrow-right-long"></i>
                            </a>
                        </div>
                        <div class="pricing-box-right">
                            <ul class="pricing-list">
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Interest Rate'): {{ getAmount($fdrPlan->interest_rate) }}%</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Max Deposit'): {{ showAmount($fdrPlan->maximum_amount) }} {{ __(gs('cur_sym')) }}</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Locked Period'): {{ $fdrPlan->locked_days }} @lang('Days')</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Profit Every'): {{ $fdrPlan->installment_interval }} @lang('Days')</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Minimum Term'): {{ $fdrPlan->locked_days }} @lang('Days')</li>
                            </ul>
                        </div>
                    </div>
                    @endif
                    
                    @if($dpsPlan)
                    <div class="pricing-single-box">
                        <div class="pricing-box-left">
                            <div class="pricing-title">
                                <p>{{ $dpsPlan->name }}</p>
                                <h2><sup></sup>{{ showAmount($dpsPlan->per_installment) }}</h2>
                            </div>
                            <a href="{{ route('user.login') }}" class="theme-btn bg-theme3">
                                <span class="link-effect">
                                    <span class="effect-1">@lang('Start Saving')</span>
                                    <span class="effect-1">@lang('Start Saving')</span>
                                </span><i class="fa-regular fa-arrow-right-long"></i>
                            </a>
                        </div>
                        <div class="pricing-box-right">
                            <ul class="pricing-list">
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Interest Rate'): {{ getAmount($dpsPlan->interest_rate) }}%</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Total Installments'): {{ $dpsPlan->total_installment }}</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Maturity Amount'): {{ showAmount($dpsPlan->final_amount) }} {{ __(gs('cur_sym')) }}</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Installment Interval'): {{ $dpsPlan->installment_interval }} @lang('Days')</li>
                                <li><i class="fa-regular fa-circle-check"></i>@lang('Delay Charge'): {{ showAmount($dpsPlan->fixed_charge) }} + {{ showAmount($dpsPlan->percent_charge) }}%</li>
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="wrapper-section br-20 overflow-hidden mx-30 lg-mx-0">

        {{-- Contact Section Three --}}
        <section class="contact-section style-3 overflow-hidden space-top">
            <div class="bg image"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28628.366670740204!2d-98.26166332568359!3d26.243942699999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8665a6cd720c12ed%3A0xde58a10abb982ba8!2sTexas%20Regional%20Bank!5e0!3m2!1sen!2sng!4v1759468020681!5m2!1sen!2sng" width="600" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="overlay"></div>
            <div class="container">
                <div class="row align-items-end">
                    <div class=" col-xl-7 col-lg-6">
                        <div class="contact-content mb-120">
                            <div class="title-area white two">
                                <div class="sub-title">
                                    <span><i class="asterisk"></i></span>APPOINMENTS
                                </div>
                                <h2 class="sec-title">Get Free Banking Consultation<br> <span
                                        class="bold text-theme2">Client’s</span> Support</h2>
                                <div class="text">
                                    <div class="icon"><i class="fa-solid fa-check"></i></div>
                                    <p>We're always ready to help you with your banking needs. Contact us today</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6">
                        <div class="appointment-area">
                            <div class="header">
                                <h2 class="title">@lang('Get In Touch')</h2>
                                <span class="availability mb-25">@lang("We're always ready to help you with your banking needs. Contact us today")</span>
                            </div>
                            <form id="appointment_form" class="appointment-form" action="https://formspree.io/f/xgvnypdz"
                                method="POST">
                                @csrf
                                <div class="form-group">
                                    <span class="icon"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" id="fullName" name="name"
                                        placeholder="@lang('Your Name')" value="{{ old('name', auth()->user() ? auth()->user()->fullname : '') }}"
                                        @if(auth()->check() && auth()->user()->profile_complete) readonly @endif required autocomplete="on">
                                </div>
                                <div class="form-group">
                                    <span class="icon"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="email" id="userEmail" name="email"
                                        placeholder="@lang('Email Address')" value="{{ old('email', auth()->user() ? auth()->user()->email : '') }}"
                                        @if(auth()->check()) readonly @endif required autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <select class="custom-select" id="subject_select" name="subject_select" autocomplete="off" required>
                                        <option value="" disabled selected>@lang('Select Subject')</option>
                                        <option value="Account Opening" {{ old('subject_select') == 'Account Opening' ? 'selected' : '' }}>@lang('Account Opening')</option>
                                        <option value="Money Transfer" {{ old('subject_select') == 'Money Transfer' ? 'selected' : '' }}>@lang('Money Transfer')</option>
                                        <option value="Loan Services" {{ old('subject_select') == 'Loan Services' ? 'selected' : '' }}>@lang('Loan Services')</option>
                                        <option value="Investment Advisory" {{ old('subject_select') == 'Investment Advisory' ? 'selected' : '' }}>@lang('Investment Advisory')</option>
                                        <option value="Rebate Program" {{ old('subject_select') == 'Rebate Program' ? 'selected' : '' }}>@lang('Rebate Program')</option>
                                        <option value="FDR Program" {{ old('subject_select') == 'FDR Program' ? 'selected' : '' }}>@lang('FDR Program')</option>
                                        <option value="DPS Program" {{ old('subject_select') == 'DPS Program' ? 'selected' : '' }}>@lang('DPS Program')</option>
                                        <option value="Account Issues" {{ old('subject_select') == 'Account Issues' ? 'selected' : '' }}>@lang('Account Issues')</option>
                                        <option value="Technical Support" {{ old('subject_select') == 'Technical Support' ? 'selected' : '' }}>@lang('Technical Support')</option>
                                        <option value="Billing Inquiry" {{ old('subject_select') == 'Billing Inquiry' ? 'selected' : '' }}>@lang('Billing Inquiry')</option>
                                        <option value="General Inquiry" {{ old('subject_select') == 'General Inquiry' ? 'selected' : '' }}>@lang('General Inquiry')</option>
                                        <option value="Other" {{ old('subject_select') == 'Other' ? 'selected' : '' }}>@lang('Other')</option>
                                    </select>
                                </div>
                                <div class="form-group" id="other_subject_group" style="display: {{ old('subject_select') == 'Other' ? 'block' : 'none' }};">
                                    <span class="icon"><i class="fa-solid fa-pen"></i></span>
                                    <input type="text" id="other_subject" name="other_subject"
                                        placeholder="@lang('Please specify your subject')" value="{{ old('other_subject') }}">
                                </div>
                                <input type="hidden" name="subject" id="subject_hidden" value="{{ old('subject') }}">
                                <div class="form-group mb-15">
                                    <textarea id="msg" name="message" placeholder="@lang('Write Message')" required>{{ old('message') }}</textarea>
                                </div>
                                
                                <x-captcha />
                                
                                <button type="submit" class="theme-btn bg-theme"
                                    data-loading-text="@lang('Please wait...')">
                                    <span class="link-effect">
                                        <span class="btn-title">@lang('Send Message')</span>
                                    </span><i class="fa-regular fa-arrow-right-long"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Brands Section --}}
        <div class="brands-section space bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sponsors-outer">
                            <div class="trusted-partners mt--15"><span class="bg-white pr-10">@lang('Our Trusted Partners')</span>
                            </div>
                            <div class="brands-slider swiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="brand-item">
                                            <a class="image" href="#">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images//brands/01.png') }}"
                                                    alt="Brand 01">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images//brands/01.png') }}"
                                                    alt="Brand 01">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="brand-item">
                                            <a class="image" href="#">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/02.png') }}"
                                                    alt="Brand 02">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/02.png') }}"
                                                    alt="Brand 02">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="brand-item">
                                            <a class="image" href="#">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/03.png') }}"
                                                    alt="Brand  03">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/03.png') }}"
                                                    alt="Brand  03">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="brand-item">
                                            <a class="image" href="#">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/04.png') }}"
                                                    alt="Brand 04">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/04.png') }}"
                                                    alt="Brand 04">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="brand-item">
                                            <a class="image" href="#">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/05.png') }}"
                                                    alt="Brand 05">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/05.png') }}"
                                                    alt="Brand 05">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="brand-item">
                                            <a class="image" href="#">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/06.png') }}"
                                                    alt="Brand 06">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/06.png') }}"
                                                    alt="Brand 06">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="brand-item">
                                            <a class="image" href="#">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/06.png') }}"
                                                    alt="Brand 06">
                                                <img src="{{ asset('assets/templates/MayaOfLagos/assets/images/brands/06.png') }}"
                                                    alt="Brand 06">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="trusted-partners text-right mb--10"><span
                                    class="bg-white pl-10">@lang('Almost') <span
                                        class="text-theme">@lang('3k+ Partners')</span> @lang('we have')</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Blog Section --}}
    @php
        $blogAuthors = [
            ['name' => 'Sarah Mitchell', 'gender' => 'female'],
            ['name' => 'Michael Roberts', 'gender' => 'male'],
            ['name' => 'Jennifer Thompson', 'gender' => 'female'],
            ['name' => 'David Anderson', 'gender' => 'male'],
            ['name' => 'Emily Davis', 'gender' => 'female'],
            ['name' => 'James Wilson', 'gender' => 'male'],
            ['name' => 'Lisa Martinez', 'gender' => 'female'],
            ['name' => 'Robert Taylor', 'gender' => 'male'],
            ['name' => 'Amanda Garcia', 'gender' => 'female'],
            ['name' => 'Christopher Lee', 'gender' => 'male'],
            ['name' => 'Jessica White', 'gender' => 'female'],
            ['name' => 'Daniel Harris', 'gender' => 'male'],
            ['name' => 'Michelle Clark', 'gender' => 'female'],
            ['name' => 'Matthew Lewis', 'gender' => 'male'],
            ['name' => 'Rebecca Walker', 'gender' => 'female'],
        ];
        
        $blogCategories = [
            'Banking', 'Finance', 'Investment', 'Technology', 'Business', 
            'Economics', 'Markets', 'Cryptocurrency', 'Real Estate', 'Insurance',
            'Loans', 'Savings', 'Credit Cards', 'Mobile Banking', 'Fintech'
        ];
        
        $blogTitles = [
            'The Future of Digital Banking in 2025',
            'How to Maximize Your Savings Account Returns',
            'Understanding Investment Risks and Rewards',
            'Top 10 Banking Trends Transforming Finance',
            'Smart Strategies for Managing Your Loans',
            'The Rise of Mobile Banking Applications',
            'Cryptocurrency Integration in Traditional Banking',
            'Building Wealth Through Strategic Investments',
            'Essential Tips for First-Time Home Buyers',
            'Navigating the Complex World of Credit Scores',
            'Retirement Planning Made Simple and Effective',
            'The Impact of AI on Modern Banking Services',
            'Sustainable Investing: Profits with Purpose',
            'Breaking Down Complex Financial Jargon',
            'How Fintech is Revolutionizing Money Transfer',
            'Securing Your Financial Future with Smart Choices',
            'The Evolution of Contactless Payment Systems',
            'Understanding Interest Rates and Their Impact',
            'Debt Management Strategies That Actually Work',
            'Exploring Alternative Investment Opportunities',
            'The Power of Compound Interest in Savings',
            'Cybersecurity Best Practices for Online Banking',
            'Real Estate Investment Trusts Explained',
            'Mastering Personal Finance in Your 20s and 30s',
            'The Role of Central Banks in Economic Stability',
            'Tax-Advantaged Savings Accounts You Should Know',
            'Building an Emergency Fund: Step by Step Guide',
            'International Money Transfers Made Easy',
            'Credit Card Rewards Programs Worth Considering',
            'The Impact of Inflation on Your Savings',
        ];
        
        $randomBlogPosts = [];
        $usedIndices = [];
        
        for ($i = 0; $i < 3; $i++) {
            do {
                $titleIndex = array_rand($blogTitles);
            } while (in_array($titleIndex, $usedIndices));
            $usedIndices[] = $titleIndex;
            
            $author = $blogAuthors[array_rand($blogAuthors)];
            $authorGender = $author['gender'] === 'male' ? 'men' : 'women';
            $authorImageId = rand(1, 99);
            
            $coverImageId = rand(100, 999);
            
            $randomBlogPosts[] = [
                'title' => $blogTitles[$titleIndex],
                'category' => $blogCategories[array_rand($blogCategories)],
                'author' => $author['name'],
                'author_image' => "https://randomuser.me/api/portraits/{$authorGender}/{$authorImageId}.jpg",
                'cover_image' => "https://picsum.photos/384/280?random={$coverImageId}",
            ];
        }
    @endphp
    
    <section class="blog-section space bg-theme3">
        <div class="container">
            <div class="title-area three text-center">
                <div class="sub-title"><span><i class="asterisk"></i></span>@lang('LATEST BLOG')</div>
                <h2 class="sec-title">@lang('Read our latest') <span class="bold">@lang('blog posts')</span></h2>
            </div>
            <div class="row gy-30">
                @foreach($randomBlogPosts as $post)
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <article class="blog-single-box">
                        <div class="inner-box">
                            <div class="blog-image">
                                <img src="{{ $post['cover_image'] }}" 
                                     alt="{{ $post['title'] }}"
                                     style="width: 384px; height: 280px; object-fit: cover;">
                                <div class="category-tag">{{ __($post['category']) }}</div>
                            </div>
                            <div class="blog-content">
                                <div class="author">
                                    <img src="{{ $post['author_image'] }}" 
                                         alt="{{ $post['author'] }}"
                                         style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%;">
                                    <span class="name"><span>@lang('By')</span> {{ $post['author'] }}</span>
                                </div>
                                <div class="pt-25 pb-20">
                                    <div class="border dark"></div>
                                </div>
                                <h4 class="title"><a href="{{ route('user.login') }}">{{ __($post['title']) }}</a></h4>
                                <a href="{{ route('user.login') }}" class="continue-reading">@lang('Continue Reading')</a>
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======= Contact Wrapper ======= --}}
    <div class="contact-wrapper space-bottom bg-theme3">
        <div class="contact-option">
            <i class="fa-regular fa-envelope"></i>
            <span>@lang('Looking for help?')</span>
            <a href="#" class="contact-link">@lang('Contact us Today')</a>
        </div>
        @if (gs('social_facebook') || gs('social_twitter') || gs('social_instagram') || gs('social_linkedin'))
            <div class="social-option">
                <i class="fa-regular fa-thumbs-up"></i>
                <span>@lang('Keep in touch')</span>
                @if (gs('social_facebook'))
                    <a href="{{ gs('social_facebook') }}" class="social-link">@lang('Like us on Facebook')</a>
                @endif
            </div>
        @endif
    </div>

    {{-- Newsletter Section --}}
    <section class="newsletter-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="newsletter">
                        <div class="p-top-left wow slideInLeft"><img
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/newslatter/shape01.png') }}"
                                alt="Newsletter shape"></div>
                        <div class="p-top-right wow slideInRight"><img
                                src="{{ asset('assets/templates/MayaOfLagos/assets/images/newslatter/shape02.png') }}"
                                alt="Newsletter shape"></div>
                        <div class="text">
                            <h3>@lang('Need Banking Consultation?')</h3>
                        </div>
                        <div class="contact-info">
                            <div class="email-icon">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <div class="email-details">
                                <p>@lang('Send e-Mail')</p>
                                <a href="mailto:{{ gs('contact_email') }}">{{ gs('contact_email') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Handle subject select change
        $('#subject_select').on('change', function() {
            var selectedValue = $(this).val();
            
            if (selectedValue === 'Other') {
                $('#other_subject_group').slideDown(300);
                $('#other_subject').attr('required', true);
            } else {
                $('#other_subject_group').slideUp(300);
                $('#other_subject').attr('required', false);
                $('#other_subject').val('');
            }
        });
        
        // Handle form submission to set the final subject value
        $('#appointment_form').on('submit', function(e) {
            var selectedSubject = $('#subject_select').val();
            var finalSubject = '';
            
            if (selectedSubject === 'Other') {
                finalSubject = $('#other_subject').val();
                if (!finalSubject) {
                    e.preventDefault();
                    alert('@lang("Please specify your subject")');
                    return false;
                }
            } else {
                finalSubject = selectedSubject;
            }
            
            $('#subject_hidden').val(finalSubject);
        });
        
        // Trigger on page load if "Other" is selected (for validation errors)
        if ($('#subject_select').val() === 'Other') {
            $('#other_subject_group').show();
            $('#other_subject').attr('required', true);
        }
        
    })(jQuery);
</script>
@endpush
