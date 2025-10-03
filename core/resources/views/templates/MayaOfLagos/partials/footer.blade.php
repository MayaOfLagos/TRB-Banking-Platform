{{-- Footer Section Start --}}
        <footer class="footer-section bg-dark">
            <div class="footer-top space">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-6 footer-brand">
                            <div class="brand-header">
                                <a href="{{ route('home') }}" class="footer-logo d-block mb-20"><img alt="{{ gs('site_name') }}" src="{{ siteLogo() }}" style="max-width: 150px;"></a>
                                <p class="text">@lang('Trump Rebate Banking offers secure, reliable banking services with exclusive rebate programs designed to maximize your financial benefits.')</p>
                            </div>
                            @if(gs('social_facebook') || gs('social_twitter') || gs('social_instagram') || gs('social_linkedin'))
                            <div class="footer-social">
                                @if(gs('social_facebook'))
                                <a href="{{ gs('social_facebook') }}" class="social-link">FB.</a>
                                @endif
                                @if(gs('social_twitter'))
                                <a href="{{ gs('social_twitter') }}" class="social-link">TW.</a>
                                @endif
                                @if(gs('social_linkedin'))
                                <a href="{{ gs('social_linkedin') }}" class="social-link">LN.</a>
                                @endif
                                @if(gs('social_instagram'))
                                <a href="{{ gs('social_instagram') }}" class="social-link">IG</a>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 p-0 sm-pl-15">
                                    <div class="footer-widget">
                                        <h4 class="title">@lang('Banking')</h4>
                                        <ul class="list-unstyled">
                                            <li><a href="{{ route('pages', 'about') }}">@lang('About Us')</a></li>
                                            <li><a href="{{ route('pages', 'services') }}">@lang('Our Services')</a></li>
                                            <li><a href="{{ route('pages', 'team') }}">@lang('Our Team')</a></li>
                                            <li><a href="{{ route('pages', 'blog') }}">@lang('Banking News')</a></li>
                                            <li><a href="{{ route('contact') }}">@lang('Contact Us')</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 p-0 sm-pl-15">
                                    <div class="footer-widget">
                                        <h4 class="title">@lang('Services')</h4>
                                        <ul class="list-unstyled">
                                            <li><a href="{{ route('pages', 'account-opening') }}">@lang('Account Opening')</a></li>
                                            <li><a href="{{ route('pages', 'money-transfer') }}">@lang('Money Transfer')</a></li>
                                            <li><a href="{{ route('pages', 'investment-services') }}">@lang('Investment Services')</a></li>
                                            <li><a href="{{ route('pages', 'loan-services') }}">@lang('Loan Services')</a></li>
                                            <li><a href="{{ route('pages', 'rebate-program') }}">@lang('Rebate Program')</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 md-d-none"></div>
                        <div class="col-lg-3 col-md-4">
                            <div class="footer-widget ml-0 mb-0">
                                <h4 class="title">@lang('Newsletter')</h4>
                                <p class="text">@lang('Stay updated with banking news and rebate offers')</p>
                                <form class="newsletter-form" action="{{ route('subscribe') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" name="email" class="email" value="" placeholder="@lang('Email Address')" autocomplete="on" required="">
                                        <button type="submit">
                                            <i class="far fa-paper-plane"></i>
                                            <span class="btn-title"></span>
                                        </button>
                                    </div>
                                </form>
                                <div class="notify"><div class="icon"><i class="fa-regular fa-bell"></i></div> @lang('Sign up for banking updates and exclusive offers')</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0">&copy;{{ date('Y') }} - @lang('All Rights Reserved by') <a href="{{ url('/') }}">Trump Rebate Banking</a></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="footer-policy">
                                <a href="{{ route('policy.pages', 'terms-and-conditions') }}">@lang('Terms & Conditions')</a>
                                <a href="{{ route('policy.pages', 'privacy-policy') }}">@lang('Privacy Policy')</a>
                                <a href="{{ route('policy.pages', 'legal') }}">@lang('Legal')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
{{-- Footer Section End --}}
