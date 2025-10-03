@php
    $subscribe = getContent('subscribe.content', true);
@endphp

<!-- Newsletter Subscription Section -->
<section class="relative py-16 lg:py-20 overflow-hidden">
    <!-- Background -->
    @if(@$subscribe->data_values->background_image)
        <div class="absolute inset-0 z-0">
            <img src="{{ frontendImage('subscribe', @$subscribe->data_values->background_image, '1920x400') }}" 
                 alt="@lang('Newsletter Background')" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-teal-900/90 to-orange-600/80"></div>
        </div>
    @else
        <div class="absolute inset-0 gradient-bg"></div>
    @endif

    <!-- Pattern Overlay -->
    <div class="absolute inset-0 lagos-pattern opacity-20"></div>

    <!-- Content -->
    <div class="relative z-10">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto text-center text-white">
                <!-- Heading -->
                <div class="mb-8">
                    @if(@$subscribe->data_values->subheading)
                        <div class="text-orange-300 font-semibold text-lg mb-3">
                            {{ __(@$subscribe->data_values->subheading) }}
                        </div>
                    @endif
                    
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4 african-text-shadow">
                        {{ __(@$subscribe->data_values->heading ?: 'Stay Updated with Our Newsletter') }}
                    </h2>

                    @if(@$subscribe->data_values->description)
                        <p class="text-xl text-white/90 leading-relaxed max-w-2xl mx-auto">
                            {{ __(@$subscribe->data_values->description) }}
                        </p>
                    @endif
                </div>

                <!-- Newsletter Form -->
                <div class="max-w-lg mx-auto">
                    <form class="subscribe-form flex flex-col sm:flex-row gap-4" method="POST" action="{{ route('subscribe') }}">
                        @csrf
                        <div class="flex-1 relative">
                            <input type="email" 
                                   name="email" 
                                   placeholder="@lang('Enter your email address')" 
                                   class="w-full px-6 py-4 rounded-xl bg-white/95 backdrop-blur-sm text-gray-800 placeholder-gray-500 border-0 focus:ring-4 focus:ring-white/30 focus:outline-none transition-all duration-300"
                                   required>
                            <div class="absolute inset-y-0 right-4 flex items-center">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="btn-secondary px-8 py-4 rounded-xl font-semibold text-white bg-orange-500 hover:bg-orange-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                            {{ __(@$subscribe->data_values->button_text ?: 'Subscribe Now') }}
                            <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </form>

                    <!-- Privacy Note -->
                    <p class="text-sm text-white/70 mt-4">
                        <i class="fas fa-shield-alt mr-1"></i>
                        @lang('We respect your privacy. Unsubscribe at any time.')
                    </p>
                </div>

                <!-- Features/Benefits -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-12 max-w-3xl mx-auto">
                    <div class="flex items-center justify-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-bell text-xl"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-semibold">@lang('Latest Updates')</div>
                            <div class="text-sm text-white/80">@lang('Get notified first')</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-gift text-xl"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-semibold">@lang('Exclusive Offers')</div>
                            <div class="text-sm text-white/80">@lang('Special promotions')</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-semibold">@lang('Market Insights')</div>
                            <div class="text-sm text-white/80">@lang('Expert analysis')</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-16 h-16 border border-white/30 rounded-full animate-pulse hidden lg:block"></div>
    <div class="absolute bottom-10 right-10 w-20 h-20 border border-orange-400/30 rounded-full animate-float hidden lg:block"></div>
    <div class="absolute top-1/2 left-1/4 w-3 h-3 bg-white/50 rounded-full animate-bounce hidden lg:block"></div>
    <div class="absolute top-1/3 right-1/3 w-2 h-2 bg-orange-400/50 rounded-full animate-pulse hidden lg:block" style="animation-delay: 1s"></div>
</section>

<style>
.subscribe-form input:focus {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.subscribe-form button:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

@media (max-width: 640px) {
    .subscribe-form {
        flex-direction: column;
    }
    
    .subscribe-form button {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subscribeForm = document.querySelector('.subscribe-form');
    
    if (subscribeForm) {
        subscribeForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>@lang("Subscribing...")';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds (adjust based on your actual submission handling)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    }
});
</script>