@php
    $breadcrumbContent = getContent('breadcrumb.content', true);
@endphp

<!-- Breadcrumb Section -->
<section class="breadcrumb-section relative py-16 lg:py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 gradient-bg"></div>
    <div class="absolute inset-0 lagos-pattern opacity-20"></div>
    
    <!-- Content -->
    <div class="relative z-10">
        <div class="container-custom">
            <div class="text-center text-white">
                <!-- Page Title -->
                <h1 class="text-4xl lg:text-5xl font-bold mb-4 african-text-shadow">
                    {{ __($pageTitle) }}
                </h1>
                
                <!-- Breadcrumb Navigation -->
                <nav class="flex items-center justify-center space-x-2 text-sm lg:text-base" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" 
                       class="flex items-center space-x-1 text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-home"></i>
                        <span>@lang('Home')</span>
                    </a>
                    
                    <div class="flex items-center space-x-2 text-white/60">
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="font-medium text-white">{{ __($pageTitle) }}</span>
                    </div>
                </nav>

                <!-- Optional Subtitle -->
                @if(isset($pageSubtitle) && $pageSubtitle)
                    <p class="mt-4 text-lg text-white/90 max-w-2xl mx-auto">
                        {{ __($pageSubtitle) }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 border border-white/20 rounded-full animate-float hidden lg:block"></div>
    <div class="absolute bottom-10 right-10 w-16 h-16 border border-orange-500/30 rounded-full animate-float hidden lg:block" style="animation-delay: 1s"></div>
    <div class="absolute top-1/2 left-1/4 w-2 h-2 bg-white/40 rounded-full animate-pulse hidden lg:block"></div>
    <div class="absolute top-1/3 right-1/3 w-3 h-3 bg-orange-500/40 rounded-full animate-pulse hidden lg:block" style="animation-delay: 0.5s"></div>
</section>

<style>
.breadcrumb-section {
    margin-top: 80px; /* Account for fixed header */
}

@media (max-width: 768px) {
    .breadcrumb-section {
        margin-top: 70px;
    }
}
</style>