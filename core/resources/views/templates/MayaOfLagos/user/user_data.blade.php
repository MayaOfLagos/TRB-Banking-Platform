@extends($activeTemplate . 'layouts.app')

@push('style')
<style>
    .profile-image-preview {
        margin-top: 15px;
    }
    .profile-image-preview img {
        width: 200px;
        height: 160px;
        object-fit: cover;
        border-radius: 1rem;
    }
    .auth-container {
        background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 50%, #f0fdfa 100%);
        min-height: 100vh;
    }
    .dark .auth-container {
        background: linear-gradient(135deg, #1f2937 0%, #111827 50%, #1f2937 100%);
    }
</style>
@endpush

@section('app')
    <!-- Auth Preloader Component -->
    @include($activeTemplate . 'partials.auth_preloader', [
        'text' => 'Setting up your profile...',
        'showPattern' => true
    ])

    <!-- Dark/Light Mode Toggle - Fixed Top -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" class="w-12 h-12 flex items-center justify-center bg-white/20 dark:bg-gray-800/20 backdrop-blur-sm rounded-full border border-white/30 dark:border-gray-600/30 text-gray-700 dark:text-gray-300 hover:bg-white/30 dark:hover:bg-gray-700/30 transition-all duration-300 hover:scale-110">
            <i class="las la-sun text-xl hidden dark:block"></i>
            <i class="las la-moon text-xl block dark:hidden"></i>
        </button>
    </div>

    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-emerald-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center p-4 transition-all duration-300 auth-container">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-400/20 dark:bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-300/20 dark:bg-emerald-600/10 rounded-full blur-3xl"></div>
            <div class="absolute top-20 left-20 w-60 h-60 bg-emerald-200/20 dark:bg-emerald-400/5 rounded-full blur-2xl"></div>
        </div>

        <div class="relative w-full max-w-4xl mx-auto">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ siteLogo() }}" alt="{{ __(gs('site_name')) }}" class="h-16 w-auto mx-auto">
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mt-4 mb-2">@lang('Complete Your Profile')</h1>
                <p class="text-gray-600 dark:text-gray-400">@lang('Just a few more details to get started')</p>
            </div>

            <!-- Profile Completion Form -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                
                <!-- Warning Alert -->
                <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-6 mx-8 mt-8 rounded-r-2xl">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="las la-info-circle text-amber-500 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-amber-700 dark:text-amber-300 font-medium">
                                @lang('You need to complete your profile to get access to your dashboard')
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <form method="POST" action="{{ route('user.data.submit') }}" enctype="multipart/form-data" class="p-8" id="profileForm">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Username') <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="username" 
                                   id="username" 
                                   value="{{ old('username') }}" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 checkUser @error('username') border-red-500 @enderror">
                            <small class="text-red-500 usernameExist"></small>
                            @error('username')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country & Mobile -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Country') <span class="text-red-500">*</span>
                                </label>
                                <select name="country" 
                                        id="country"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('country') border-red-500 @enderror">
                                    @foreach($countries as $key => $country)
                                        <option data-mobile_code="{{ $country->dial_code }}" 
                                                value="{{ $country->country }}" 
                                                data-code="{{ $key }}" 
                                                {{ old('country') == $country->country ? 'selected' : '' }}>
                                            {{ __($country->country) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Mobile Number') <span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-4 rounded-l-2xl border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-sm mobile-code font-medium">
                                    </span>
                                    <input type="text" 
                                           name="mobile" 
                                           id="mobile" 
                                           value="{{ old('mobile') }}" 
                                           required
                                           placeholder="@lang('Phone number')"
                                           class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-r-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 checkUser @error('mobile') border-red-500 @enderror">
                                    <input type="hidden" name="mobile_code">
                                    <input type="hidden" name="country_code">
                                </div>
                                <small class="text-red-500 mobileExist"></small>
                                @error('mobile')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Profile Image') <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       name="image" 
                                       id="imageUpload" 
                                       required
                                       accept=".png, .jpg, .jpeg"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('image') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <i class="las la-camera text-gray-400 text-xl"></i>
                                </div>
                            </div>
                            <small class="text-gray-500 dark:text-gray-400">
                                @lang('Please upload an image with a 3.5:3 aspect ratio, which will be resized to') {{ getFileSize('userProfile') }} @lang('pixels')
                            </small>
                            <div class="profile-image-preview hidden mt-4">
                                <img src="" alt="profile-image" class="border border-gray-200 dark:border-gray-600">
                            </div>
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Address')
                                </label>
                                <input type="text" 
                                       name="address" 
                                       id="address" 
                                       value="{{ old('address') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('address') border-red-500 @enderror">
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('State')
                                </label>
                                <input type="text" 
                                       name="state" 
                                       id="state" 
                                       value="{{ old('state') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('state') border-red-500 @enderror">
                                @error('state')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="zip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Zip Code')
                                </label>
                                <input type="text" 
                                       name="zip" 
                                       id="zip" 
                                       value="{{ old('zip') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('zip') border-red-500 @enderror">
                                @error('zip')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('City')
                                </label>
                                <input type="text" 
                                       name="city" 
                                       id="city" 
                                       value="{{ old('city') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 @error('city') border-red-500 @enderror">
                                @error('city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8">
                        <button type="submit" 
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 inline-flex items-center justify-center"
                                id="submit-btn">
                            <span class="default-text inline-flex items-center">
                                <i class="las la-check-circle text-xl mr-2"></i>@lang('Complete Profile')
                            </span>
                            <span class="loading-text hidden inline-flex items-center">
                                <svg class="animate-spin h-5 w-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @lang('Setting up...')
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    'use strict';
    
    // Theme toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.classList.toggle('dark', currentTheme === 'dark');
        
        themeToggle.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            // Add animation effect
            themeToggle.style.transform = 'scale(0.9)';
            setTimeout(() => {
                themeToggle.style.transform = 'scale(1)';
            }, 150);
        });
    }

    // Image preview functionality
    $("#imageUpload").on('change', function() {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('.profile-image-preview').removeClass('hidden');
                $('.profile-image-preview img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Set mobile code based on saved preference
    @if ($mobileCode)
        $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
    @endif

    // Country selection change handler
    $('select[name=country]').on('change', function() {
        $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
        $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
        $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
        var value = $('[name=mobile]').val();
        var name = 'mobile';
        checkUser(value, name);
    });

    // Initialize mobile code on page load
    $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
    $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
    $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

    // User existence check
    $('.checkUser').on('focusout', function(e) {
        var value = $(this).val();
        var name = $(this).attr('name')
        checkUser(value, name);
    });

    function checkUser(value, name) {
        var url = '{{ route('user.checkUser') }}';
        var token = '{{ csrf_token() }}';

        if (name == 'mobile') {
            var mobile = `${value}`;
            var data = {
                mobile: mobile,
                mobile_code: $('.mobile-code').text().substr(1),
                _token: token
            }
        }
        if (name == 'username') {
            var data = {
                username: value,
                _token: token
            }
        }
        
        $.post(url, data, function(response) {
            if (response.data != false) {
                $(`.${response.type}Exist`).text(`${response.field} already exist`);
            } else {
                $(`.${response.type}Exist`).text('');
            }
        });
    }

    // Form submission with loading state
    $('#profileForm').on('submit', function() {
        const submitBtn = document.getElementById('submit-btn');
        const defaultText = submitBtn.querySelector('.default-text');
        const loadingText = submitBtn.querySelector('.loading-text');
        
        defaultText.classList.add('hidden');
        loadingText.classList.remove('hidden');
        submitBtn.disabled = true;
    });
</script>
@endpush