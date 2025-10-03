@extends($activeTemplate . 'layouts.master')

@push('style')
<style>
    .crypto-container {
        background: linear-gradient(135deg, 
            rgba(79, 70, 229, 0.1) 0%, 
            rgba(147, 51, 234, 0.1) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
    }
    
    .crypto-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 32px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .crypto-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #f59e0b, #d97706, #92400e);
        border-radius: 16px 16px 0 0;
    }
    
    .amount-display {
        background: linear-gradient(135deg, 
            rgba(245, 158, 11, 0.1) 0%, 
            rgba(217, 119, 6, 0.1) 100%);
        border: 2px solid rgba(245, 158, 11, 0.3);
        border-radius: 16px;
        padding: 24px;
        margin: 24px 0;
        position: relative;
    }
    
    .amount-display::before {
        content: '⚡';
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    
    .crypto-amount {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
        word-break: break-all;
    }
    
    .crypto-currency {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f59e0b;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .address-display {
        background: linear-gradient(135deg, 
            rgba(16, 185, 129, 0.1) 0%, 
            rgba(5, 150, 105, 0.1) 100%);
        border: 2px solid rgba(16, 185, 129, 0.3);
        border-radius: 16px;
        padding: 20px;
        margin: 24px 0;
        position: relative;
    }
    
    .address-display::before {
        content: '🔗';
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    
    .wallet-address {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 1rem;
        font-weight: 600;
        color: #059669;
        word-break: break-all;
        line-height: 1.5;
        margin-bottom: 12px;
    }
    
    .copy-button {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .copy-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }
    
    .copy-button.copied {
        background: linear-gradient(135deg, #059669, #047857);
    }
    
    .qr-container {
        background: white;
        border-radius: 20px;
        padding: 24px;
        margin: 32px auto;
        display: inline-block;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    
    .qr-container::before {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        background: linear-gradient(45deg, #8b5cf6, #7c3aed, #6d28d9);
        border-radius: 24px;
        z-index: -1;
    }
    
    .qr-code {
        max-width: 280px;
        width: 100%;
        height: auto;
        border-radius: 12px;
    }
    
    .scan-instruction {
        font-size: 1.5rem;
        font-weight: 700;
        color: #374151;
        margin-top: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }
    
    .dark .scan-instruction {
        color: #f9fafb;
    }
    
    .scan-icon {
        font-size: 2rem;
        animation: pulse 2s ease-in-out infinite;
    }
    
    .warning-box {
        background: linear-gradient(135deg, 
            rgba(239, 68, 68, 0.1) 0%, 
            rgba(220, 38, 38, 0.1) 100%);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        padding: 16px;
        margin: 24px 0;
        color: #dc2626;
    }
    
    .dark .warning-box {
        color: #fca5a5;
    }
    
    .warning-box .warning-icon {
        font-size: 1.25rem;
        margin-right: 8px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin: 24px 0;
    }
    
    .info-item {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }
    
    .info-icon {
        font-size: 2rem;
        margin-bottom: 8px;
        color: #8b5cf6;
    }
    
    .info-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
    }
    
    .dark .info-title {
        color: #f9fafb;
    }
    
    .info-desc {
        font-size: 14px;
        color: #6b7280;
    }
    
    .dark .info-desc {
        color: #9ca3af;
    }
    
    .pulse {
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
        .crypto-amount {
            font-size: 2rem;
        }
        
        .qr-code {
            max-width: 240px;
        }
        
        .scan-instruction {
            font-size: 1.25rem;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Cryptocurrency Payment')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Complete your payment by sending the exact amount to the address below')</p>
        </div>

        <div class="crypto-container">
            <div class="crypto-card fade-in">
                <!-- Amount Section -->
                <div class="amount-display">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">@lang('SEND EXACTLY')</h2>
                    <div class="crypto-amount">{{ $data->amount }}</div>
                    <div class="crypto-currency">{{ __($data->currency) }}</div>
                </div>

                <!-- Address Section -->
                <div class="address-display">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">@lang('TO ADDRESS')</h3>
                    <div class="wallet-address" id="wallet-address">{{ $data->sendto }}</div>
                    <button class="copy-button" onclick="copyAddress()">
                        <i class="las la-copy" id="copy-icon"></i>
                        <span id="copy-text">@lang('Copy Address')</span>
                    </button>
                </div>

                <!-- QR Code Section -->
                <div class="qr-container">
                    <img src="{{ $data->img }}" alt="@lang('QR Code')" class="qr-code">
                </div>

                <div class="scan-instruction">
                    <i class="las la-qrcode scan-icon pulse"></i>
                    @lang('SCAN TO SEND')
                </div>

                <!-- Warning -->
                <div class="warning-box">
                    <i class="las la-exclamation-triangle warning-icon"></i>
                    <strong>@lang('Important:')</strong> @lang('Send only') {{ __($data->currency) }} @lang('to this address. Sending any other cryptocurrency will result in permanent loss.')
                </div>

                <!-- Info Grid -->
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="las la-clock"></i>
                        </div>
                        <div class="info-title">@lang('Processing Time')</div>
                        <div class="info-desc">@lang('1-6 confirmations')</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="las la-shield-alt"></i>
                        </div>
                        <div class="info-title">@lang('Security')</div>
                        <div class="info-desc">@lang('Blockchain secured')</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="las la-coins"></i>
                        </div>
                        <div class="info-title">@lang('Network')</div>
                        <div class="info-desc">{{ __($data->currency) }} @lang('Network')</div>
                    </div>
                </div>

                <!-- Additional Instructions -->
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">@lang('Payment Instructions')</h4>
                    <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-2">
                        <li>• @lang('Send the exact amount shown above')</li>
                        <li>• @lang('Use the provided wallet address')</li>
                        <li>• @lang('Your deposit will be credited after network confirmation')</li>
                        <li>• @lang('Save the transaction hash for your records')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    "use strict";
    
    function copyAddress() {
        const addressElement = document.getElementById('wallet-address');
        const copyButton = document.querySelector('.copy-button');
        const copyIcon = document.getElementById('copy-icon');
        const copyText = document.getElementById('copy-text');
        const address = addressElement.textContent;
        
        // Create a temporary textarea to copy the text
        const textarea = document.createElement('textarea');
        textarea.value = address;
        document.body.appendChild(textarea);
        textarea.select();
        textarea.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            document.execCommand('copy');
            
            // Update button state
            copyButton.classList.add('copied');
            copyIcon.className = 'las la-check';
            copyText.textContent = '@lang("Copied!")';
            
            // Reset button after 2 seconds
            setTimeout(() => {
                copyButton.classList.remove('copied');
                copyIcon.className = 'las la-copy';
                copyText.textContent = '@lang("Copy Address")';
            }, 2000);
            
        } catch (err) {
            console.error('Failed to copy: ', err);
            copyText.textContent = '@lang("Failed to copy")';
            
            setTimeout(() => {
                copyText.textContent = '@lang("Copy Address")';
            }, 2000);
        }
        
        document.body.removeChild(textarea);
    }
    
    // Add click handler to address for copying
    document.getElementById('wallet-address').addEventListener('click', copyAddress);
    
    // Add some interactive animations
    const qrContainer = document.querySelector('.qr-container');
    qrContainer.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.05)';
        this.style.transition = 'transform 0.3s ease';
    });
    
    qrContainer.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
    
    // Add pulse animation to amount on load
    setTimeout(() => {
        const amountDisplay = document.querySelector('.amount-display');
        amountDisplay.style.animation = 'pulse 2s ease-in-out 3';
    }, 500);
</script>
@endpush