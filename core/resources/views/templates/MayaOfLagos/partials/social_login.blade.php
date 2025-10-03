@php
    $text = isset($register) ? 'Register' : 'Login';
@endphp

<div class="space-y-3">
    @if (@gs('socialite_credentials')->google->status == Status::ENABLE)
        <a href="{{ route('user.social.login', 'google') }}" 
           class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-300 font-medium">
            <img src="{{ asset('assets/images/google.svg') }}" alt="Google" class="w-5 h-5 mr-3">
            @lang("$text with Google")
        </a>
    @endif
    
    @if (@gs('socialite_credentials')->facebook->status == Status::ENABLE)
        <a href="{{ route('user.social.login', 'facebook') }}" 
           class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-300 font-medium">
            <img src="{{ asset('assets/images/facebook.svg') }}" alt="Facebook" class="w-5 h-5 mr-3">
            @lang("$text with Facebook")
        </a>
    @endif
    
    @if (@gs('socialite_credentials')->linkedin->status == Status::ENABLE)
        <a href="{{ route('user.social.login', 'linkedin') }}" 
           class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-300 font-medium">
            <img src="{{ asset('assets/images/linkedin.svg') }}" alt="LinkedIn" class="w-5 h-5 mr-3">
            @lang("$text with LinkedIn")
        </a>
    @endif
</div>