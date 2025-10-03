@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="min-h-screen flex items-center justify-center py-12 px-0 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="las la-shield-alt text-red-600 dark:text-red-400 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        @lang('Security Verification')
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        @if ($verification->send_via != '2fa')
                            @if ($verification->send_via == 'email')
                                @lang('Enter the 6-digit code sent to your email')
                            @else
                                @lang('Enter the 6-digit code sent to your phone')
                            @endif
                        @else
                            @lang('Enter the code from your Google Authenticator app')
                        @endif
                    </p>
                </div>

                @if ($verification->send_via != '2fa')
                    @include($activeTemplate . 'user.otp.email_sms')
                @endif

                <form action="{{ route('user.otp.submit', $verification->id) }}" method="post" class="submit-form">
                    @csrf
                    <div class="mb-6">
                        <label for="verification-code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            @lang('Verification Code')
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="otp" 
                                   id="verification-code" 
                                   class="w-full px-4 py-4 text-center text-2xl font-mono tracking-widest border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200" 
                                   placeholder="------" 
                                   maxlength="6"
                                   required 
                                   autocomplete="off">
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="flex space-x-2 text-gray-300 dark:text-gray-600">
                                    <span class="verification-box w-8 h-8 border-b-2 border-gray-300 dark:border-gray-600"></span>
                                    <span class="verification-box w-8 h-8 border-b-2 border-gray-300 dark:border-gray-600"></span>
                                    <span class="verification-box w-8 h-8 border-b-2 border-gray-300 dark:border-gray-600"></span>
                                    <span class="verification-box w-8 h-8 border-b-2 border-gray-300 dark:border-gray-600"></span>
                                    <span class="verification-box w-8 h-8 border-b-2 border-gray-300 dark:border-gray-600"></span>
                                    <span class="verification-box w-8 h-8 border-b-2 border-gray-300 dark:border-gray-600"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <span class="btn-text">
                            <i class="las la-check-circle mr-2"></i>
                            @lang('Verify Code')
                        </span>
                        <span class="loading-text hidden">
                            <i class="las la-spinner animate-spin mr-2"></i>
                            @lang('Verifying...')
                        </span>
                    </button>
                </form>

                <!-- Back to previous page -->
                <div class="text-center mt-6">
                    <a href="javascript:history.back()" 
                       class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 text-sm transition-colors duration-200">
                        <i class="las la-arrow-left mr-1"></i>
                        @lang('Go Back')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .verification-box {
        transition: all 0.2s ease;
    }
    
    .verification-box.filled {
        border-color: rgb(220, 38, 38);
        background-color: rgba(220, 38, 38, 0.1);
    }
    
    #verification-code {
        background: transparent;
        letter-spacing: 1rem;
        text-indent: 0.5rem;
    }
    
    #verification-code:focus {
        background: transparent;
    }
    
    .dark .verification-box.filled {
        border-color: rgb(248, 113, 113);
        background-color: rgba(248, 113, 113, 0.1);
    }
</style>
@endpush

@push('script')
<script>
    'use strict';
    (function($) {
        $('#verification-code').on('input', function() {
            const value = $(this).val();
            const boxes = $('.verification-box');
            
            boxes.removeClass('filled');
            for (let i = 0; i < value.length && i < 6; i++) {
                boxes.eq(i).addClass('filled');
            }
            
            if (value.length >= 6) {
                const $submitBtn = $('.submit-form button[type=submit]');
                const $btnText = $submitBtn.find('.btn-text');
                const $loadingText = $submitBtn.find('.loading-text');
                
                $btnText.addClass('hidden');
                $loadingText.removeClass('hidden');
                $submitBtn.prop('disabled', true);
                
                setTimeout(() => {
                    $('.submit-form').submit();
                }, 500);
            }
            
            if (value.length > 6) {
                $(this).val(value.substring(0, 6));
            }
        });

        $('.submit-form').on('submit', function() {
            const $submitBtn = $(this).find('button[type=submit]');
            const $btnText = $submitBtn.find('.btn-text');
            const $loadingText = $submitBtn.find('.loading-text');
            
            $btnText.addClass('hidden');
            $loadingText.removeClass('hidden');
            $submitBtn.prop('disabled', true);
        });

        setTimeout(() => {
            $('#verification-code').focus();
        }, 500);
    })(jQuery);
</script>
@endpush