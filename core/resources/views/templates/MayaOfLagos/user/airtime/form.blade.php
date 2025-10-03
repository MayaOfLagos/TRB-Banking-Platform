@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- Airtime Header Section -->
<div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-2xl p-8 mb-8 text-white">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="lg:w-2/3">
            <h1 class="text-3xl font-bold mb-4 flex items-center">
                <i class="las la-mobile-alt mr-3 text-4xl"></i>
                @lang('Mobile Top Up')
            </h1>
            <p class="text-green-100 text-lg leading-relaxed">
                @lang('Recharge mobile phones instantly worldwide. Send airtime, data bundles, and more to any mobile number with our secure and fast service.')
            </p>
            
            <!-- Key Features -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                        <i class="las la-bolt text-white"></i>
                    </div>
                    <span class="text-sm">@lang('Instant Delivery')</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                        <i class="las la-shield-alt text-white"></i>
                    </div>
                    <span class="text-sm">@lang('Secure Transactions')</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                        <i class="las la-globe text-white"></i>
                    </div>
                    <span class="text-sm">@lang('Global Coverage')</span>
                </div>
            </div>
        </div>
        
        <div class="lg:w-1/3 mt-6 lg:mt-0 text-center">
            <div class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 rounded-xl">
                <i class="las la-wallet text-2xl mr-3"></i>
                <div class="text-left">
                    <div class="text-sm opacity-90">@lang('Current Balance')</div>
                    <div class="font-bold text-xl">{{ showAmount(auth()->user()->balance) }} {{ gs()->cur_text }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        <!-- Form Header -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="las la-credit-card mr-2 text-blue-600"></i>
                @lang('Top Up Details')
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">@lang('Fill in the details below to send mobile top up')</p>
        </div>
        
        <!-- Form Content -->
        <div class="p-8">
            <form action="{{ route('user.airtime.apply') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Country Selection -->
                <div class="form-group">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="las la-flag mr-1"></i>
                        @lang('Country')
                    </label>
                    <select name="country_id" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                            required>
                        <option value="" selected disabled>@lang('Select Country')</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" 
                                    data-calling_codes="{{ json_encode($country->calling_codes) }}" 
                                    @selected(old('country_id') == $country->id)>
                                {{ __($country->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Operator Selection -->
                <div class="form-group operatorDiv hidden">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="las la-tower-cell mr-1"></i>
                        @lang('Operator') <span class="text-red-500">*</span>
                    </label>
                    <div class="operator-wrapper"></div>
                </div>

                <!-- Mobile Number -->
                <div class="form-group">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="las la-phone mr-1"></i>
                        @lang('Mobile Number')
                    </label>
                    <div class="flex">
                        <select name="calling_code" 
                                class="px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 min-w-0 flex-shrink-0">
                        </select>
                        <input type="tel" 
                               class="flex-1 px-4 py-3 border-t border-r border-b border-gray-300 dark:border-gray-600 rounded-r-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 mobileNumber" 
                               name="mobile_number" 
                               value="{{ old('mobile_number') }}" 
                               placeholder="@lang('Enter mobile number')"
                               required>
                    </div>
                </div>

                <!-- Variable Amount Input -->
                <div class="form-group amount-wrapper hidden">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="las la-dollar-sign mr-1"></i>
                        @lang('Amount') 
                        <span class="topupLimit text-blue-600 dark:text-blue-400 text-xs hidden"></span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               step="any" 
                               class="w-full px-4 py-3 pr-20 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 amount" 
                               name="amount" 
                               value="{{ old('amount') }}" 
                               placeholder="@lang('Enter amount')"
                               required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">{{ gs()->cur_text }}</span>
                        </div>
                    </div>
                </div>

                <!-- Fixed Amounts -->
                <div class="form-group fixed-amounts-wrapper hidden">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="las la-list mr-1"></i>
                        @lang('Select Amount')
                    </label>
                    <div class="fixed-amount-input-wrapper"></div>
                </div>

                <!-- OTP Field -->
                @include($activeTemplate . 'partials.otp_field')

                <!-- Suggested Amounts -->
                <div class="form-group suggested-amounts-wrapper hidden">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="las la-lightbulb mr-1"></i>
                        @lang('Suggested Amounts')
                    </label>
                    <div class="suggested-amounts"></div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-600">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <i class="las la-paper-plane mr-2"></i>
                        @lang('Send Top Up')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@include('Template::partials.operator_modal')
@include('Template::partials.top_up')