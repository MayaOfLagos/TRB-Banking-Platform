@php
    $finishTime = \Carbon\Carbon::parse($verification->expired_at);
    $currentTime = now();
    $totalDuration = $currentTime > $finishTime ? 0 : floor($currentTime->diffInSeconds($finishTime));
@endphp

<div class="mb-6 p-6 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-xl border border-red-200 dark:border-red-800">
    <div class="text-center">
        @if ($verification->send_via == 'email')
            <div class="flex items-center justify-center mb-3">
                <i class="las la-envelope text-red-600 dark:text-red-400 text-2xl mr-2"></i>
                <span class="text-red-800 dark:text-red-200 font-medium">@lang('Email Verification')</span>
            </div>
            <p class="text-red-700 dark:text-red-300 text-sm mb-3">
                @lang('We sent a verification code to your email address')
            </p>
        @else
            <div class="flex items-center justify-center mb-3">
                <i class="las la-sms text-red-600 dark:text-red-400 text-2xl mr-2"></i>
                <span class="text-red-800 dark:text-red-200 font-medium">@lang('SMS Verification')</span>
            </div>
            <p class="text-red-700 dark:text-red-300 text-sm mb-3">
                @lang('We sent a verification code to your mobile number')
            </p>
        @endif

        @if ($totalDuration)
            <p class="text-amber-700 dark:text-amber-300 text-xs mb-4 otp-warning">
                @lang('Code expires in')
            </p>
        @endif

        <div class="flex justify-center mb-4">
            <div class="expired-time-circle @if (!$totalDuration) border-red-500 @else border-green-500 @endif border-4 rounded-full w-20 h-20 flex flex-col items-center justify-center bg-white dark:bg-gray-800 shadow-lg relative overflow-hidden">
                <div class="exp-time text-2xl font-bold text-gray-900 dark:text-white">{{ $totalDuration }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400">@lang('sec')</div>
                <div class="animation-circle absolute inset-0 rounded-full border-4 border-transparent" 
                     style="border-top-color: rgb(34, 197, 94); animation: spin {{ $totalDuration }}s linear;">
                </div>
            </div>
        </div>

        <div class="try-btn-wrapper @if($totalDuration) hidden @endif">
            <div class="text-center">
                <p class="text-red-600 dark:text-red-400 text-sm mb-3">
                    <i class="las la-exclamation-triangle mr-1"></i>
                    @lang('Your verification code has expired')
                </p>
                <form method="POST" action="{{ route('user.otp.resend', $verification->id) }}" class="inline-block">
                    @csrf
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="las la-redo-alt mr-1"></i>
                        @lang('Resend Code')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animation-circle {
        transition: all 0.3s ease;
    }
    
    .expired-time-circle {
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('script')
<script>
    'use strict';
    (function($) {
        let secondsLeft = {{ $totalDuration }};
        
        const timer = setInterval(function() {
            if (secondsLeft > 0) {
                secondsLeft--;
                $('.exp-time').text(secondsLeft);
            }

            if (secondsLeft === 0) {
                $('.try-btn-wrapper').removeClass('hidden');
                $('.otp-warning').addClass('hidden');
                $('.expired-time-circle').removeClass('border-green-500').addClass('border-red-500');
                $('.animation-circle').hide();
                clearInterval(timer);
            }
        }, 1000);
    })(jQuery);
</script>
@endpush