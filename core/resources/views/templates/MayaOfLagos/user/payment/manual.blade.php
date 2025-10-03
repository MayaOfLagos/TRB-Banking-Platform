@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    /* Enhanced floating animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.3); }
        50% { box-shadow: 0 0 30px rgba(139, 92, 246, 0.6); }
    }
    
    @keyframes slide-in-left {
        from { 
            opacity: 0; 
            transform: translateX(-30px); 
        }
        to { 
            opacity: 1; 
            transform: translateX(0); 
        }
    }
    
    @keyframes slide-in-right {
        from { 
            opacity: 0; 
            transform: translateX(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateX(0); 
        }
    }
    
    @keyframes fade-in-up {
        from { 
            opacity: 0; 
            transform: translateY(20px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    .payment-container {
        background: linear-gradient(135deg, 
            rgba(79, 70, 229, 0.15) 0%, 
            rgba(147, 51, 234, 0.15) 50%,
            rgba(16, 185, 129, 0.1) 100%);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        animation: fade-in-up 0.8s ease-out;
    }
    
    .payment-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #8B5CF6, #3B82F6, #10B981);
        border-radius: 24px 24px 0 0;
    }
    
    .payment-summary {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 28px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        animation: slide-in-left 0.8s ease-out 0.2s both;
        transition: all 0.3s ease;
    }
    
    .payment-summary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-color: rgba(139, 92, 246, 0.3);
    }
    
    .dark .payment-summary:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .amount-display {
        background: linear-gradient(135deg, 
            rgba(16, 185, 129, 0.15) 0%, 
            rgba(5, 150, 105, 0.15) 100%);
        border: 2px solid rgba(16, 185, 129, 0.3);
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: pulse-glow 3s ease-in-out infinite;
    }
    
    .amount-display::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .gateway-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        object-fit: cover;
        margin-right: 16px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        animation: float 3s ease-in-out infinite;
    }
    
    .gateway-icon:hover {
        transform: scale(1.05) rotate(2deg);
        border-color: rgba(139, 92, 246, 0.5);
    }
    
    .instructions-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 28px;
        margin-bottom: 24px;
        animation: slide-in-right 0.8s ease-out 0.4s both;
        position: relative;
        overflow: hidden;
    }
    
    .instructions-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #3B82F6, #8B5CF6);
        border-radius: 20px 20px 0 0;
    }
    
    .form-container {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 28px;
        animation: slide-in-right 0.8s ease-out 0.6s both;
        position: relative;
        overflow: hidden;
    }
    
    .form-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #10B981, #059669);
        border-radius: 20px 20px 0 0;
    }
    
    .form-group {
        margin-bottom: 24px;
        animation: fade-in-up 0.6s ease-out both;
    }
    
    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }
    .form-group:nth-child(4) { animation-delay: 0.4s; }
    
    .form-label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: #374151;
        position: relative;
        padding-left: 8px;
    }
    
    .form-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 16px;
        background: linear-gradient(135deg, #8B5CF6, #3B82F6);
        border-radius: 2px;
    }
    
    .dark .form-label {
        color: #E5E7EB;
    }
    
    .form-control {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        color: #111827;
        font-size: 14px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #8B5CF6;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
    }
    
    .dark .form-control {
        background: rgba(0, 0, 0, 0.3);
        color: #F9FAFB;
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    .dark .form-control:focus {
        background: rgba(0, 0, 0, 0.4);
        border-color: #8B5CF6;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.2);
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 50%, #6366F1 100%);
        border: none;
        color: white;
        padding: 18px 36px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
    }
    
    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .submit-btn:hover::before {
        left: 100%;
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(139, 92, 246, 0.4);
    }
    
    .submit-btn:active {
        transform: translateY(0);
    }
    
    .submit-btn.loading {
        opacity: 0.8;
        cursor: not-allowed;
        transform: none;
    }
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
    }
    
    .submit-btn:active {
        transform: translateY(0);
    }
    
    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        position: relative;
    }
    
    .info-item::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #8B5CF6, #3B82F6);
        transition: width 0.3s ease;
    }
    
    .info-item:hover::before {
        width: 100%;
    }
    
    .info-item:hover {
        background: rgba(139, 92, 246, 0.05);
        padding-left: 12px;
        margin-left: -12px;
        margin-right: -12px;
        padding-right: 12px;
        border-radius: 8px;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 500;
        color: #6B7280;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-label::before {
        content: '▶';
        font-size: 8px;
        color: #8B5CF6;
        transition: transform 0.3s ease;
    }
    
    .info-item:hover .info-label::before {
        transform: rotate(90deg);
    }
    
    .dark .info-label {
        color: #9CA3AF;
    }
    
    .info-value {
        font-weight: 600;
        color: #111827;
        text-align: right;
        transition: all 0.3s ease;
    }
    
    .dark .info-value {
        color: #F9FAFB;
    }
    
    .info-item:hover .info-value {
        color: #8B5CF6;
        transform: scale(1.02);
    }
    
    .dark .info-item:hover .info-value {
        color: #A78BFA;
    }
    
    .success-amount {
        color: #10B981;
        font-weight: 700;
        font-size: 1.25rem;
        text-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        animation: pulse-glow 2s ease-in-out infinite;
    }
    
    .status-indicator {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 6px 12px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 20px;
        color: #10B981;
        font-size: 12px;
        font-weight: 600;
        animation: pulse-glow 2s ease-in-out infinite;
    }
    
    .loading-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Progress Steps Enhancement */
    .payment-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 32px;
        position: relative;
    }
    
    .payment-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 10%;
        right: 10%;
        height: 2px;
        background: linear-gradient(90deg, #E5E7EB, #8B5CF6);
        border-radius: 1px;
    }
    
    .payment-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
        z-index: 1;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8B5CF6, #3B82F6);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-bottom: 8px;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        animation: pulse-glow 2s ease-in-out infinite;
    }
    
    .step-label {
        font-size: 12px;
        color: #6B7280;
        text-align: center;
        font-weight: 500;
    }
    
    .dark .step-label {
        color: #9CA3AF;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Enhanced Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 mb-6 mx-auto">
                <i class="las la-hand-holding-usd text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                @lang('Manual Payment')
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                @lang('Complete your secure deposit via') <span class="font-semibold text-purple-600 dark:text-purple-400">{{ __($data->gateway->name) }}</span>
            </p>
            
            <!-- Status Indicator -->
            <div class="inline-flex items-center mt-4 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-full">
                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">@lang('Awaiting Payment')</span>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="payment-steps mb-12">
            <div class="payment-step">
                <div class="step-circle">
                    <i class="las la-check text-lg"></i>
                </div>
                <div class="step-label">@lang('Amount Selected')</div>
            </div>
            <div class="payment-step">
                <div class="step-circle">
                    <i class="las la-file-alt text-lg"></i>
                </div>
                <div class="step-label">@lang('Review Instructions')</div>
            </div>
            <div class="payment-step">
                <div class="step-circle">
                    <i class="las la-credit-card text-lg"></i>
                </div>
                <div class="step-label">@lang('Make Payment')</div>
            </div>
            <div class="payment-step">
                <div class="step-circle">
                    <i class="las la-upload text-lg"></i>
                </div>
                <div class="step-label">@lang('Submit Proof')</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Enhanced Payment Summary -->
            <div class="lg:col-span-1">
                <div class="payment-summary">
                    <div class="status-indicator">
                        <i class="las la-clock mr-1"></i>
                        @lang('Pending')
                    </div>
                    
                    <div class="flex items-center mb-6">
                        <img src="{{ getImage(getFilePath('gateway') . '/' . $data->gateway->image) }}" 
                             alt="{{ $data->gateway->name }}" class="gateway-icon">
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ __($data->gateway->name) }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="las la-shield-alt mr-1"></i>
                                @lang('Secure Manual Payment')
                            </p>
                        </div>
                    </div>
                    
                    <div class="amount-display mb-6">
                        <div class="flex items-center justify-center mb-2">
                            <i class="las la-coins text-2xl text-green-500 mr-2"></i>
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">@lang('You have requested')</p>
                        </div>
                        <p class="text-3xl font-bold text-green-600 mb-3">{{ showAmount($data['amount']) }} {{ gs('cur_text') }}</p>
                        <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-600 to-transparent mb-3"></div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">@lang('Please pay exactly')</p>
                        <p class="text-2xl font-bold success-amount">{{ showAmount($data['final_amount']) }} {{ $data['method_currency'] }}</p>
                    </div>

                    <div class="space-y-3">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="las la-exchange-alt text-purple-500"></i>
                                @lang('Exchange Rate')
                            </span>
                            <span class="info-value">1 {{ gs('cur_text') }} = {{ showAmount($data['rate']) }} {{ $data['method_currency'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="las la-wallet text-blue-500"></i>
                                @lang('Requested Amount')
                            </span>
                            <span class="info-value">{{ showAmount($data['amount']) }} {{ gs('cur_text') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="las la-percent text-orange-500"></i>
                                @lang('Processing Fee')
                            </span>
                            <span class="info-value">{{ showAmount($data['charge']) }} {{ gs('cur_text') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">
                                <i class="las la-credit-card text-green-500"></i>
                                @lang('Total Payable')
                            </span>
                            <span class="info-value success-amount">{{ showAmount($data['final_amount']) }} {{ $data['method_currency'] }}</span>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i class="las la-bolt text-yellow-500 mr-2"></i>
                            @lang('Quick Actions')
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="flex items-center justify-center p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg text-purple-700 dark:text-purple-300 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-all duration-300">
                                <i class="las la-copy mr-2"></i>
                                <span class="text-sm font-medium">@lang('Copy Details')</span>
                            </button>
                            <button class="flex items-center justify-center p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-300">
                                <i class="las la-download mr-2"></i>
                                <span class="text-sm font-medium">@lang('Download')</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Instructions and Form -->
            <div class="lg:col-span-2">
                <!-- Enhanced Payment Instructions -->
                <div class="instructions-card">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center mr-4">
                            <i class="las la-info-circle text-xl text-white"></i>
                        </div>
                        @lang('Payment Instructions')
                        <div class="ml-auto flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">@lang('Active')</span>
                        </div>
                    </h2>
                    
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-6">
                        <div class="prose dark:prose-invert max-w-none">
                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                @php echo $data->gateway->description @endphp
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="las la-exclamation-triangle text-2xl text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">@lang('Important Notes')</h3>
                                <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
                                    <li>• @lang('Pay the exact amount shown above')</li>
                                    <li>• @lang('Upload clear proof of payment')</li>
                                    <li>• @lang('Processing may take 24-48 hours')</li>
                                    <li>• @lang('Keep your transaction reference safe')</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Payment Form -->
                <div class="form-container">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center mr-4">
                            <i class="las la-file-upload text-xl text-white"></i>
                        </div>
                        @lang('Submit Payment Details')
                        <div class="ml-auto">
                            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                <i class="las la-shield-alt text-green-500"></i>
                                <span>@lang('Secure Upload')</span>
                            </div>
                        </div>
                    </h2>

                    <form action="{{ route('user.deposit.manual.update') }}" method="POST" class="manual-payment-form" enctype="multipart/form-data">
                        @csrf
                        <x-viser-form identifier="id" identifierValue="{{ $data->gateway->form_id }}" />
                        
                        <button type="submit" class="submit-btn" id="submit-btn">
                            <span id="submit-content" class="flex items-center justify-center">
                                <i class="las la-paper-plane text-lg mr-2"></i>
                                @lang('Submit Payment Details')
                                <i class="las la-arrow-right ml-2 transition-transform duration-300 group-hover:translate-x-1"></i>
                            </span>
                            <span id="loading-content" class="hidden flex items-center justify-center">
                                <i class="las la-spinner loading-spinner text-lg mr-2"></i>
                                @lang('Processing Payment...')
                            </span>
                        </button>
                        
                        <!-- Security Badge -->
                        <div class="mt-6 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400">
                            <i class="las la-lock text-green-500 mr-2"></i>
                            <span>@lang('Your payment details are encrypted and secure')</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enhanced Help Section -->
        <div class="mt-12 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900/20 rounded-2xl p-8 border border-gray-200 dark:border-gray-700">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 mb-4">
                    <i class="las la-question-circle text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">@lang('Need Help?')</h3>
                <p class="text-gray-600 dark:text-gray-400">@lang('Our support team is here to assist you with your payment')</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                    <i class="las la-headset text-3xl text-purple-500 mb-3"></i>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('24/7 Support')</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Contact our support team anytime')</p>
                </div>
                <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                    <i class="las la-book text-3xl text-blue-500 mb-3"></i>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('User Guide')</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Step-by-step payment instructions')</p>
                </div>
                <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                    <i class="las la-comments text-3xl text-green-500 mb-3"></i>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Live Chat')</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Get instant help from our agents')</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    "use strict";
    (function($) {
        // Form submission with loading state
        $('.manual-payment-form').on('submit', function(e) {
            const submitBtn = $('#submit-btn');
            const submitContent = $('#submit-content');
            const loadingContent = $('#loading-content');
            
            // Prevent double submission
            if (submitBtn.hasClass('loading')) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            submitBtn.addClass('loading').attr('disabled', true);
            submitContent.addClass('hidden');
            loadingContent.removeClass('hidden');
            
            // Re-enable form if there's an error (after a delay to allow for page navigation)
            setTimeout(function() {
                if (submitBtn.hasClass('loading')) {
                    submitBtn.removeClass('loading').removeAttr('disabled');
                    submitContent.removeClass('hidden');
                    loadingContent.addClass('hidden');
                }
            }, 8000);
        });
        
        // Enhanced file upload handling
        $('input[type="file"]').on('change', function() {
            const file = this.files[0];
            if (file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const label = $(this).siblings('label');
                
                if (fileSize > 10) {
                    showNotification('@lang("File size should be less than 10MB")', 'error');
                    $(this).val('');
                    return;
                }
                
                label.html(`
                    <i class="las la-file-upload text-green-500 mr-2"></i>
                    ${fileName} (${fileSize}MB)
                `);
                
                showNotification('@lang("File selected successfully")', 'success');
            }
        });
        
        // Enhanced notification system
        function showNotification(message, type = 'info') {
            const notification = $(`
                <div class="fixed top-4 right-4 z-50 max-w-sm transform translate-x-full transition-transform duration-300">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="las ${getNotificationIcon(type)} text-xl ${getNotificationColor(type)}"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${message}</p>
                            </div>
                            <button class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="$(this).closest('.fixed').remove()">
                                <i class="las la-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(notification);
            
            setTimeout(() => {
                notification.removeClass('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.addClass('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }
        
        function getNotificationIcon(type) {
            switch(type) {
                case 'success': return 'la-check-circle';
                case 'error': return 'la-exclamation-circle';
                case 'warning': return 'la-exclamation-triangle';
                default: return 'la-info-circle';
            }
        }
        
        function getNotificationColor(type) {
            switch(type) {
                case 'success': return 'text-green-500';
                case 'error': return 'text-red-500';
                case 'warning': return 'text-yellow-500';
                default: return 'text-blue-500';
            }
        }
        
        // Copy to clipboard functionality
        $('.copy-btn').on('click', function() {
            const text = $(this).data('copy');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    showNotification('@lang("Copied to clipboard!")', 'success');
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showNotification('@lang("Copied to clipboard!")', 'success');
            }
        });
        
        // Progress step animation
        $('.payment-step').each(function(index) {
            $(this).css('animation-delay', (index * 0.2) + 's');
        });
        
        // Auto-focus on first form field
        setTimeout(() => {
            $('.form-container input:first').focus();
        }, 1000);
        
    })(jQuery);
</script>
@endpush