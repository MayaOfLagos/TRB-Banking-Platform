@php
    $banner = getContent('banner.content', true);
@endphp

<!-- Hero Banner Section -->
<section class="hero relative min-h-screen flex items-center justify-center overflow-hidden" id="home">
    <!-- Background Image -->
    @if(@$banner->data_values->background_image)
        <div class="absolute inset-0 z-0">
            <img src="{{ frontendImage('banner', @$banner->data_values->background_image, '1920x1080') }}" 
                 alt="@lang('Hero Background')" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-teal-900/80 via-teal-800/70 to-slate-900/80"></div>
        </div>
    @endif

    <!-- Background Pattern -->
    <div class="absolute inset-0 lagos-pattern opacity-20 z-1"></div>

    <!-- Content -->
    <div class="relative z-10 w-full">
        <div class="container-custom">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center min-h-screen py-20">
                <!-- Left Content -->
                <div class="text-white animate-fade-in-up">
                    <!-- Main Heading -->
                    <h1 class="hero-title mb-6">
                        {{ __(@$banner->data_values->heading) }}
                    </h1>

                    <!-- Subheading -->
                    @if(@$banner->data_values->subheading)
                        <div class="hero-subtitle mb-8">
                            {{ __(@$banner->data_values->subheading) }}
                        </div>
                    @endif

                    <!-- Description -->
                    @if(@$banner->data_values->description)
                        <p class="text-xl text-white/90 mb-10 leading-relaxed max-w-lg">
                            {{ __(@$banner->data_values->description) }}
                        </p>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        @if(@$banner->data_values->primary_button_text)
                            <a href="{{ @$banner->data_values->primary_button_link ?: '#' }}" 
                               class="btn-primary btn-lg group">
                                {{ __(@$banner->data_values->primary_button_text) }}
                                <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                            </a>
                        @endif

                        @if(@$banner->data_values->secondary_button_text)
                            <a href="{{ @$banner->data_values->secondary_button_link ?: '#' }}" 
                               class="btn-outline btn-lg group">
                                {{ __(@$banner->data_values->secondary_button_text) }}
                                <i class="fas fa-play ml-2 transition-transform group-hover:scale-110"></i>
                            </a>
                        @endif

                        @if(@$banner->data_values->video_link)
                            <button class="btn-outline btn-lg group" data-modal="video-modal">
                                <i class="fas fa-play mr-2"></i>
                                @lang('Watch Demo')
                            </button>
                        @endif
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-3 gap-6">
                        @if(@$banner->data_values->stats_users)
                            <div class="text-center">
                                <div class="text-3xl font-bold text-orange-400 counter" data-count="{{ @$banner->data_values->stats_users }}">0</div>
                                <div class="text-sm text-white/80 mt-1">@lang('Happy Customers')</div>
                            </div>
                        @endif

                        @if(@$banner->data_values->stats_experience)
                            <div class="text-center">
                                <div class="text-3xl font-bold text-teal-400 counter" data-count="{{ @$banner->data_values->stats_experience }}">0</div>
                                <div class="text-sm text-white/80 mt-1">@lang('Years Experience')</div>
                            </div>
                        @endif

                        @if(@$banner->data_values->stats_transactions)
                            <div class="text-center">
                                <div class="text-3xl font-bold text-orange-400 counter" data-count="{{ @$banner->data_values->stats_transactions }}">0</div>
                                <div class="text-sm text-white/80 mt-1">@lang('Transactions')</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Content -->
                <div class="relative animate-fade-in-up" style="animation-delay: 0.3s">
                    @if(@$banner->data_values->hero_image)
                        <div class="relative">
                            <!-- Main Hero Image -->
                            <div class="relative z-10 animate-float">
                                <img src="{{ frontendImage('banner', @$banner->data_values->hero_image, '600x700') }}" 
                                     alt="@lang('Hero Image')" 
                                     class="w-full max-w-lg mx-auto rounded-2xl shadow-2xl">
                            </div>

                            <!-- Floating Elements -->
                            <div class="absolute -top-6 -left-6 w-24 h-24 bg-orange-500 rounded-2xl opacity-80 animate-float hidden lg:block" style="animation-delay: 1s"></div>
                            <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-teal-600 rounded-full opacity-60 animate-float hidden lg:block" style="animation-delay: 2s"></div>

                            <!-- User Avatars -->
                            @if(@$banner->data_values->user_avatars)
                                <div class="absolute bottom-6 left-6 bg-white rounded-2xl p-4 shadow-xl hidden lg:block">
                                    <div class="flex -space-x-2 mb-2">
                                        <img src="{{ frontendImage('banner', @$banner->data_values->user_avatars, '60x60') }}" alt="User" class="w-8 h-8 rounded-full border-2 border-white">
                                        <img src="{{ frontendImage('banner', @$banner->data_values->user_avatars, '60x60') }}" alt="User" class="w-8 h-8 rounded-full border-2 border-white">
                                        <img src="{{ frontendImage('banner', @$banner->data_values->user_avatars, '60x60') }}" alt="User" class="w-8 h-8 rounded-full border-2 border-white">
                                        <div class="w-8 h-8 bg-teal-600 rounded-full border-2 border-white flex items-center justify-center text-xs text-white font-semibold">+</div>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800">{{ formatNumber(@$banner->data_values->stats_users) }}+ @lang('Users')</div>
                                    <div class="text-xs text-gray-600">@lang('Trust our platform')</div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-20 left-10 w-6 h-6 border-2 border-white/30 rotate-45 animate-pulse hidden lg:block"></div>
    <div class="absolute top-40 right-20 w-4 h-4 bg-orange-500/40 rounded-full animate-bounce hidden lg:block" style="animation-delay: 0.5s"></div>
    <div class="absolute bottom-32 left-1/4 w-8 h-8 border border-teal-400/30 rounded-full animate-pulse hidden lg:block" style="animation-delay: 1.5s"></div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
        <div class="flex flex-col items-center">
            <span class="text-sm mb-2">@lang('Scroll Down')</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
</section>

<!-- Video Modal -->
@if(@$banner->data_values->video_link)
    <div class="modal fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center opacity-0 invisible transition-all duration-300" id="video-modal">
        <div class="relative max-w-4xl w-full mx-4">
            <button class="modal-close absolute -top-12 right-0 text-white text-2xl hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
            <div class="relative aspect-video bg-black rounded-lg overflow-hidden">
                <iframe src="{{ @$banner->data_values->video_link }}" 
                        class="w-full h-full" 
                        allowfullscreen></iframe>
            </div>
        </div>
    </div>
@endif

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.hero-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    line-height: 1.1;
    font-weight: 800;
}

.hero-subtitle {
    font-size: clamp(1.2rem, 2.5vw, 1.5rem);
    font-weight: 500;
    opacity: 0.95;
}

@media (max-width: 768px) {
    .hero {
        min-height: 80vh;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
}
</style>