@if (checkIsOtpEnable())
    <div class="space-y-4 mb-4">
        <label for="verification" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            @lang('Authorization Mode')
        </label>
        <select name="auth_mode" 
                id="verification" 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                required>
            <option disabled selected value="">@lang('Select One')</option>
            @if (auth()->user()->ts)
                <option value="2fa">@lang('Google Authenticator')</option>
            @endif
            @if (gs()->modules->otp_email)
                <option value="email">@lang('Email')</option>
            @endif
            @if (gs()->modules->otp_sms)
                <option value="sms">@lang('SMS')</option>
            @endif
        </select>
    </div>
@endif