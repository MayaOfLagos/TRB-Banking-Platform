@extends($activeTemplate . 'layouts.app')
@section('app')
    @php
        $loginBg = getContent('login_bg.content', true);
        use App\Constants\Status;
    @endphp
    
    <!-- Auth Preloader Component -->
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Signing you in...',
        'showPattern' => false
    ])
    
    <!-- Dark/Light Mode Toggle - Fixed Top -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-white dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
            <i class="las la-sun text-xl hidden dark:block"></i>
            <i class="las la-moon text-xl block dark:hidden"></i>
        </button>
    </div>

    <!-- Main Container with Dynamic Background -->
    <div class="min-h-screen relative overflow-hidden">
        <!-- Dynamic Background Videos/Images -->
        <div id="background-container" class="absolute inset-0 z-0">
            <!-- Background will be populated by JavaScript -->
        </div>
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/40 via-blue-900/30 to-black/60 z-10"></div>
        
        <!-- Mobile Logo - Centered Top -->
        <div class="lg:hidden fixed top-8 left-1/2 transform -translate-x-1/2 z-40">
            <!-- Dark colored logo → shown in light mode -->
            <img src="{{ siteLogo('dark') }}" alt="@lang('logo')" 
                 class="h-12 block dark:hidden" id="logo-for-white-bg">
            <!-- White colored logo → shown in dark mode -->
            <img src="{{ siteLogo() }}" alt="@lang('logo')" 
                 class="h-12 hidden dark:block" id="logo-for-dark-bg">
        </div>
        
        <!-- Main Content Container -->
        <div class="relative z-20 min-h-screen flex items-center justify-center p-4 pt-20 lg:pt-4">
            <div class="w-full max-w-6xl mx-auto">
                <!-- Desktop Logo - Above Container -->
                <div class="hidden lg:block text-center mb-8">
                    <!-- Dark colored logo → shown in light mode -->
                    <img src="{{ siteLogo('dark') }}" alt="@lang('logo')" 
                         class="h-16 mx-auto mb-4 block dark:hidden" id="logo-for-white-bg">
                    <!-- White colored logo → shown in dark mode -->
                    <img src="{{ siteLogo() }}" alt="@lang('logo')" 
                         class="h-16 mx-auto mb-4 hidden dark:block" id="logo-for-dark-bg">
                </div>
                
                <!-- Login Container - Two Columns -->
                <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg rounded-3xl shadow-2xl overflow-hidden border border-white/20 dark:border-gray-700/20">
                    <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[600px]">
                        
                        <!-- Left Column - Business Quotes & Image -->
                        <div class="hidden lg:flex flex-col justify-center p-12 bg-gradient-to-br from-blue-600 via-purple-600 to-teal-600 relative overflow-hidden">
                            <!-- Background Business Image -->
                            <div id="business-bg-container" class="absolute inset-0 z-0">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 via-purple-900/70 to-teal-900/80 z-10"></div>
                            
                            <!-- Content -->
                            <div class="relative z-20 text-white">
                                <div class="mb-8">
                                    <i class="las la-chart-line text-6xl mb-6 opacity-80"></i>
                                    <h2 class="text-4xl font-bold mb-4">Business Excellence</h2>
                                    <p class="text-xl text-white/90 mb-8">Success in business requires discipline, focus, and strategic thinking.</p>
                                </div>
                                
                                <!-- Sliding Quotes -->
                                <div class="quote-container">
                                    <div id="quote-text" class="text-lg italic mb-4 h-16 flex items-center transition-all duration-500">
                                        "Success is not the key to happiness. Happiness is the key to success."
                                    </div>
                                    <div id="quote-author" class="text-sm font-semibold opacity-80">
                                        - Business Leader
                                    </div>
                                </div>
                                
                                <!-- Progress Dots -->
                                <div class="flex space-x-2 mt-8">
                                    <div class="quote-dot w-3 h-3 bg-white rounded-full opacity-100 transition-opacity duration-300"></div>
                                    <div class="quote-dot w-3 h-3 bg-white/50 rounded-full opacity-50 transition-opacity duration-300"></div>
                                    <div class="quote-dot w-3 h-3 bg-white/50 rounded-full opacity-50 transition-opacity duration-300"></div>
                                    <div class="quote-dot w-3 h-3 bg-white/50 rounded-full opacity-50 transition-opacity duration-300"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Login Form -->
                        <div class="flex flex-col justify-center p-8 lg:p-12">
                            <div class="w-full max-w-md mx-auto">
                                
                                <!-- Mobile Header -->
                                <div class="lg:hidden text-center mb-8">
                                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome Back</h2>
                                    <p class="text-gray-600 dark:text-gray-400">Sign in to your account</p>
                                </div>
                                
                                <!-- Desktop Header -->
                                <div class="hidden lg:block mb-8">
                                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Welcome Back</h2>
                                    <p class="text-gray-600 dark:text-gray-400 text-lg">Please sign in to your account</p>
                                </div>

                                <!-- Social Login -->
                                @if (@gs('socialite_credentials')->google->status == Status::ENABLE || @gs('socialite_credentials')->facebook->status == Status::ENABLE || @gs('socialite_credentials')->linkedin->status == Status::ENABLE)
                                    <div class="mb-6">
                                        @include($activeTemplate . 'partials.social_login')
                                        
                                        <div class="relative my-6">
                                            <div class="absolute inset-0 flex items-center">
                                                <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                                            </div>
                                            <div class="relative flex justify-center text-sm">
                                                <span class="px-4 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400">Or continue with email</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Login Form -->
                                <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha space-y-6" id="login-form">
                                    @csrf
                                    
                                    <!-- Username/Email Field -->
                                    <div class="form-group">
                                        <div class="relative">
                                            <input type="text" 
                                                   name="username" 
                                                   id="username" 
                                                   value="{{ old('username') }}" 
                                                   required
                                                   placeholder=" "
                                                   class="peer w-full px-4 pt-6 pb-2 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-lg placeholder-transparent">
                                            <label for="username" class="absolute left-4 top-4 text-gray-500 dark:text-gray-400 text-base font-medium transition-all duration-300 transform origin-top-left pointer-events-none peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-focus:top-2 peer-focus:text-xs peer-focus:text-blue-600 dark:peer-focus:text-blue-400 peer-focus:font-semibold peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-blue-600 dark:peer-[:not(:placeholder-shown)]:text-blue-400 peer-[:not(:placeholder-shown)]:font-semibold" style="line-height: 2rem;">
                                                @lang('Username or Email')
                                            </label>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                                <i class="las la-user text-gray-400 text-xl transition-colors duration-300"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Field -->
                                    <div class="form-group">
                                        <div class="relative">
                                            <input type="password" 
                                                   name="password" 
                                                   id="password" 
                                                   required
                                                   placeholder=" "
                                                   class="peer w-full px-4 pt-6 pb-2 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-lg placeholder-transparent pr-20">
                                            <label for="password" class="absolute left-4 top-4 text-gray-500 dark:text-gray-400 text-base font-medium transition-all duration-300 transform origin-top-left pointer-events-none peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-focus:top-2 peer-focus:text-xs peer-focus:text-blue-600 dark:peer-focus:text-blue-400 peer-focus:font-semibold peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-blue-600 dark:peer-[:not(:placeholder-shown)]:text-blue-400 peer-[:not(:placeholder-shown)]:font-semibold" style="line-height: 2rem;">
                                                @lang('Password')
                                            </label>
                                            <button type="button" 
                                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300 focus:outline-none focus:text-blue-600 dark:focus:text-blue-400"
                                                    onclick="togglePassword()">
                                                <svg class="w-5 h-5 transition-all duration-300" id="password-toggle-icon" fill="currentColor" viewBox="0 0 20 20">
                                                    <path id="eye-open" d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path id="eye-open-outline" fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                    <path id="eye-closed" class="hidden" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"/>
                                                    <path id="eye-closed-outline" class="hidden" d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Captcha -->
                                    <x-captcha />

                                    <!-- Remember Me & Forgot Password -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   name="remember" 
                                                   id="remember" 
                                                   {{ old('remember') ? 'checked' : '' }}
                                                   class="h-5 w-5 text-blue-600 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 dark:bg-gray-800">
                                            <label for="remember" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                @lang('Remember me')
                                            </label>
                                        </div>
                                        <a href="{{ route('user.password.request') }}" 
                                           class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-semibold transition-colors">
                                            @lang('Forgot Password?')
                                        </a>
                                    </div>

                                    <!-- Sign In Button -->
                                    <button type="submit" 
                                            id="submit-btn" 
                                            disabled
                                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-500/50 transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none text-lg shadow-lg">
                                        <span id="btn-text">@lang('Sign In')</span>
                                        <span id="btn-loading" class="hidden">
                                            <i class="las la-spinner la-spin mr-2"></i>
                                            Signing in...
                                        </span>
                                    </button>

                                    <!-- Register Link -->
                                    @if (gs('registration'))
                                        <div class="text-center">
                                            <p class="text-gray-600 dark:text-gray-400">
                                                @lang('Don\'t have an account?')
                                                <a href="{{ route('user.register') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-semibold transition-colors">
                                                    @lang('Create an Account')
                                                </a>
                                            </p>
                                        </div>
                                    @endif
                                </form>

                                <!-- Additional Links -->
                                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                                    <a href="{{ route('home') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium transition-colors">
                                        <i class="las la-arrow-left mr-1"></i>
                                        @lang('Back to Homepage')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    // Background Images and Videos
    const backgroundMedia = [
        // Banking Videos
        'https://cdn.pixabay.com/video/2024/05/18/212404_large.mp4',
        'https://cdn.pixabay.com/video/2018/11/29/19627-304735769_tiny.mp4',
        'https://cdn.pixabay.com/video/2024/06/01/214888_tiny.mp4',
        'https://cdn.pixabay.com/video/2024/07/04/219339_tiny.mp4',
        'https://cdn.pixabay.com/video/2023/12/01/191518-890528350_tiny.mp4',
        'https://cdn.pixabay.com/video/2024/06/01/214822_tiny.mp4',
        'https://cdn.pixabay.com/video/2025/08/12/296958_large.mp4',
        // Banking Images
        'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=1920&h=1080&fit=crop',
        'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1920&h=1080&fit=crop',
        'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=1920&h=1080&fit=crop',
        'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=1920&h=1080&fit=crop',
        'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1920&h=1080&fit=crop'
    ];

    // Business Background Images
    const businessImages = [
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1553484771-371a605b060b?w=800&h=600&fit=crop'
    ];

    // Business Quotes
    const businessQuotes = [
        {
            text: "Success in business requires discipline, determination, and the willingness to take calculated risks.",
            author: "Business Leader"
        },
        {
            text: "The way to get started is to quit talking and begin doing. Every moment counts in business.",
            author: "Entrepreneur"
        },
        {
            text: "Innovation distinguishes between a leader and a follower in today's competitive market.",
            author: "CEO"
        },
        {
            text: "Your most unhappy customers are your greatest source of learning and improvement.",
            author: "Business Strategist"
        },
        {
            text: "The best time to plant a tree was 20 years ago. The second best time is now.",
            author: "Investment Advisor"
        }
    ];

    let currentQuoteIndex = 0;
    let backgroundChangeInterval;
    let quoteChangeInterval;

    // Initialize Page
    document.addEventListener('DOMContentLoaded', function() {
        initializeBackground();
        initializeBusinessBackground();
        initializeQuotes();
        initializeForm();
        initializeThemeToggle();
    });

    // Background Management
    function initializeBackground() {
        setRandomBackground();
        backgroundChangeInterval = setInterval(setRandomBackground, 15000); // Change every 15 seconds
    }

    function setRandomBackground() {
        const container = document.getElementById('background-container');
        const randomMedia = backgroundMedia[Math.floor(Math.random() * backgroundMedia.length)];
        
        if (randomMedia.includes('.mp4')) {
            container.innerHTML = `
                <video autoplay muted loop class="w-full h-full object-cover">
                    <source src="${randomMedia}" type="video/mp4">
                </video>
            `;
        } else {
            container.innerHTML = `
                <div class="w-full h-full bg-cover bg-center bg-no-repeat" 
                     style="background-image: url('${randomMedia}')"></div>
            `;
        }
    }

    function initializeBusinessBackground() {
        const container = document.getElementById('business-bg-container');
        const randomImage = businessImages[Math.floor(Math.random() * businessImages.length)];
        container.innerHTML = `
            <div class="w-full h-full bg-cover bg-center bg-no-repeat opacity-30" 
                 style="background-image: url('${randomImage}')"></div>
        `;
    }

    // Quote Management
    function initializeQuotes() {
        displayQuote(currentQuoteIndex);
        quoteChangeInterval = setInterval(nextQuote, 5000); // Change every 5 seconds
    }

    function displayQuote(index) {
        const quote = businessQuotes[index];
        document.getElementById('quote-text').textContent = `"${quote.text}"`;
        document.getElementById('quote-author').textContent = `- ${quote.author}`;
        
        // Update dots
        document.querySelectorAll('.quote-dot').forEach((dot, i) => {
            dot.style.opacity = i === index ? '1' : '0.5';
        });
    }

    function nextQuote() {
        currentQuoteIndex = (currentQuoteIndex + 1) % businessQuotes.length;
        displayQuote(currentQuoteIndex);
    }

    // Form Management
    function initializeForm() {
        const form = document.getElementById('login-form');
        const submitBtn = document.getElementById('submit-btn');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');

        // Enhanced floating label functionality
        function handleFloatingLabels() {
            const inputs = [usernameInput, passwordInput];
            
            inputs.forEach(input => {
                const label = input.nextElementSibling;
                
                // Check if input has value and add class
                function checkValue() {
                    if (input.value.trim() !== '') {
                        input.classList.add('has-value');
                    } else {
                        input.classList.remove('has-value');
                    }
                }
                
                // Initial check
                checkValue();
                
                // Add event listeners
                input.addEventListener('input', checkValue);
                input.addEventListener('focus', () => {
                    input.classList.add('focused');
                });
                input.addEventListener('blur', () => {
                    input.classList.remove('focused');
                    checkValue();
                });
            });
        }

        // Initialize floating labels
        handleFloatingLabels();

        // Enable/disable submit button based on form validity
        function checkFormValidity() {
            const isValid = usernameInput.value.trim() !== '' && passwordInput.value.trim() !== '';
            submitBtn.disabled = !isValid;
        }

        usernameInput.addEventListener('input', checkFormValidity);
        passwordInput.addEventListener('input', checkFormValidity);

        // Form submission
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            document.getElementById('btn-text').classList.add('hidden');
            document.getElementById('btn-loading').classList.remove('hidden');
        });
    }

    // Theme Toggle
    function initializeThemeToggle() {
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        themeToggle.addEventListener('click', function() {
            html.classList.toggle('dark');
            
            // Save preference
            const isDark = html.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        }
    }

    // Password Toggle
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeOpenOutline = document.getElementById('eye-open-outline');
        const eyeClosed = document.getElementById('eye-closed');
        const eyeClosedOutline = document.getElementById('eye-closed-outline');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            // Show closed eye
            eyeOpen.classList.add('hidden');
            eyeOpenOutline.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
            eyeClosedOutline.classList.remove('hidden');
        } else {
            passwordField.type = 'password';
            // Show open eye
            eyeOpen.classList.remove('hidden');
            eyeOpenOutline.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
            eyeClosedOutline.classList.add('hidden');
        }
    }

    // Cleanup intervals when page unloads
    window.addEventListener('beforeunload', function() {
        if (backgroundChangeInterval) clearInterval(backgroundChangeInterval);
        if (quoteChangeInterval) clearInterval(quoteChangeInterval);
    });
</script>
@endpush

@push('style')
<style>
    /* Custom Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Floating Label Animations */
    @keyframes labelFloat {
        from {
            transform: translateY(0);
            font-size: 1rem;
        }
        to {
            transform: translateY(-8px);
            font-size: 0.75rem;
        }
    }

    @keyframes labelSink {
        from {
            transform: translateY(-8px);
            font-size: 0.75rem;
        }
        to {
            transform: translateY(0);
            font-size: 1rem;
        }
    }

    .form-group {
        animation: slideIn 0.6s ease-out forwards;
    }

    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }

    /* Enhanced Input Focus Effects */
    input:focus {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Floating Label Styles - Enhanced */
    .floating-label {
        position: absolute;
        left: 1rem;
        top: 1rem;
        color: #6b7280;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: top left;
        pointer-events: none;
        z-index: 10;
    }

    /* Label states */
    .peer:focus ~ label,
    .peer:not(:placeholder-shown) ~ label,
    .peer.has-value ~ label {
        top: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #2563eb;
        transform: translateY(-2px);
    }

    .dark .peer:focus ~ label,
    .dark .peer:not(:placeholder-shown) ~ label,
    .dark .peer.has-value ~ label {
        color: #60a5fa;
    }

    /* Alternative floating approach using transform */
    .peer:placeholder-shown ~ label {
        top: 1rem;
        font-size: 1rem;
        color: #6b7280;
        transform: translateY(0);
    }

    .peer:not(:placeholder-shown) ~ label,
    .peer:focus ~ label {
        top: 0.5rem;
        font-size: 0.75rem;
        color: #2563eb;
        font-weight: 600;
        transform: translateY(-2px);
    }

    /* Dark mode colors */
    .dark .peer:placeholder-shown ~ label {
        color: #9ca3af;
    }

    .dark .peer:not(:placeholder-shown) ~ label,
    .dark .peer:focus ~ label {
        color: #60a5fa;
    }

    /* Enhanced Password Toggle */
    #password-toggle-icon {
        transition: all 0.3s ease;
    }

    #password-toggle-icon:hover {
        transform: scale(1.1);
    }

    /* Input Animation Enhancement */
    input.peer {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    input.peer:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15), 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Label smooth transitions */
    label {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: left top;
    }

    /* Quote Text Animation */
    #quote-text {
        transition: all 0.5s ease-in-out;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.1);
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.3);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(0,0,0,0.5);
    }

    /* Backdrop Blur Support */
    .backdrop-blur-lg {
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
    }

    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }

    /* Enhanced form field focus states */
    .form-group:focus-within {
        transform: translateY(-2px);
    }

    /* Floating input enhanced states */
    .peer.focused,
    .peer:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .dark .peer.focused,
    .dark .peer:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.1);
    }

    /* Label animation keyframes */
    @keyframes labelUp {
        from {
            top: 1rem;
            font-size: 1rem;
            color: #6b7280;
        }
        to {
            top: 0.5rem;
            font-size: 0.75rem;
            color: #2563eb;
        }
    }

    @keyframes labelDown {
        from {
            top: 0.5rem;
            font-size: 0.75rem;
            color: #2563eb;
        }
        to {
            top: 1rem;
            font-size: 1rem;
            color: #6b7280;
        }
    }

    /* Icon animations */
    .form-group .las {
        transition: all 0.3s ease;
    }

    .form-group:focus-within .las {
        color: rgb(37, 99, 235);
        transform: scale(1.1);
    }

    .dark .form-group:focus-within .las {
        color: rgb(96, 165, 250);
    }

    /* Ensure proper spacing for floating labels */
    .peer {
        padding-top: 1.5rem;
        padding-bottom: 0.5rem;
    }

    /* Smooth label transitions */
    label {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush