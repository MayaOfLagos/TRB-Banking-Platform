<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->site_name }} - @lang('Processing Payment')</title>
    <link rel="shortcut icon" href="{{ getImage(getFilePath('logoIcon') . '/favicon.png') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        
        .redirect-container {
            text-align: center;
            padding: 50px 40px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(25px);
            border-radius: 24px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            max-width: 480px;
            width: 100%;
            margin: 20px;
            position: relative;
        }
        
        .logo {
            width: 90px;
            height: 90px;
            margin: 0 auto 30px;
            border-radius: 20px;
            object-fit: contain;
            padding: 16px;
        }
        
        .loading-container {
            position: relative;
            width: 60px;
            height: 60px;
            margin: 0 auto 30px;
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-left: 4px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #fff;
            animation: textGlow 3s ease-in-out infinite;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        
        @keyframes textGlow {
            0%, 100% { text-shadow: 0 0 10px rgba(255, 255, 255, 0.3); }
            50% { text-shadow: 0 0 20px rgba(255, 255, 255, 0.6), 0 0 30px rgba(255, 255, 255, 0.4); }
        }
        
        p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
            line-height: 1.6;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        .payment-details {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3);
            animation: slideInLeft 0.8s ease-out;
        }
        
        @keyframes slideInLeft {
            0% { opacity: 0; transform: translateX(-30px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .detail-row:hover {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding-left: 8px;
            padding-right: 8px;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }
        
        .detail-value {
            font-weight: 700;
            color: #fff;
            font-size: 15px;
        }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: statusPulse 2s ease-in-out infinite;
        }
        
        @keyframes statusPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.3); }
        }
        
        .progress-container {
            margin: 30px 0;
            position: relative;
        }
        
        .progress-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .progress-percentage {
            font-weight: 700;
            color: #10b981;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669, #34d399);
            background-size: 200% 100%;
            border-radius: 4px;
            animation: progressAnimation 4s ease-out, progressShimmer 2s linear infinite;
            position: relative;
        }
        
        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: progressGlow 2s ease-in-out infinite;
        }
        
        @keyframes progressAnimation {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        
        @keyframes progressShimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        @keyframes progressGlow {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .security-section {
            margin-top: 25px;
            padding: 20px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(16, 185, 129, 0.3);
            animation: slideInUp 1s ease-out 0.5s both;
        }
        
        @keyframes slideInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        .security-note {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .security-icon {
            font-size: 16px;
            animation: securityGlow 2s ease-in-out infinite;
        }
        
        @keyframes securityGlow {
            0%, 100% { filter: drop-shadow(0 0 3px rgba(16, 185, 129, 0.5)); }
            50% { filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.8)); }
        }
        
        .security-features {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .loading-state {
            margin: 20px 0;
            opacity: 0;
            animation: fadeIn 0.5s ease-out 1s both;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        .loading-message {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 10px;
            animation: typewriter 3s ease-out infinite;
        }
        
        .click-hint {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            animation: bounce 2s ease-in-out infinite 3s;
        }
        
        .click-hint:hover {
            color: #fff;
            transform: scale(1.05);
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        @media (max-width: 480px) {
            .redirect-container {
                padding: 40px 25px;
                margin: 15px;
                max-width: 350px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            p {
                font-size: 14px;
            }
            
            .logo {
                width: 70px;
                height: 70px;
            }
            
            .loading-container {
                width: 80px;
                height: 80px;
            }
            
            .spinner-outer {
                width: 60px;
                height: 60px;
            }
            
            .security-features {
                gap: 8px;
            }
            
            .security-badge {
                font-size: 10px;
                padding: 4px 8px;
            }
        }
        
        .pulse {
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        @media (max-width: 480px) {
            .redirect-container {
                padding: 40px 25px;
                margin: 15px;
                max-width: 350px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            p {
                font-size: 14px;
            }
            
            .logo {
                width: 70px;
                height: 70px;
            }
            
            .loading-container {
                width: 80px;
                height: 80px;
            }
            
            .spinner-outer {
                width: 60px;
                height: 60px;
            }
            
            .security-features {
                gap: 8px;
            }
            
            .security-badge {
                font-size: 10px;
                padding: 4px 8px;
            }
        }
        
        #auto_submit {
            display: none;
        }
    </style>
</head>

<body>
    <div class="redirect-container">
        <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="{{ gs()->site_name }}" class="logo">
        
        <!-- Simple Single Loading Spinner -->
        <div class="loading-container">
            <div class="spinner"></div>
        </div>
        
        <h1 class="pulse">@lang('Redirecting to Payment Gateway')</h1>
        <p>@lang('Please wait while we securely redirect you to complete your payment. This process is automated and secure.')</p>
        
        <!-- Dynamic Loading States -->
        <div class="loading-state" id="loadingState">
            <div class="loading-message" id="loadingMessage">@lang('Preparing secure connection...')</div>
        </div>
        
        @if(isset($data->amount))
        <div class="payment-details">
            <div class="detail-row">
                <span class="detail-label">@lang('Amount')</span>
                <span class="detail-value">{{ $data->amount ?? '' }} {{ $data->currency ?? '' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">@lang('Gateway')</span>
                <span class="detail-value">{{ $data->gateway_name ?? 'Payment Gateway' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">@lang('Status')</span>
                <span class="detail-value">
                    <span class="status-indicator">
                        <span class="status-dot"></span>
                        <span id="statusText">@lang('Processing')</span>
                    </span>
                </span>
            </div>
        </div>
        @endif
        
        <!-- Enhanced Progress Bar -->
        <div class="progress-container">
            <div class="progress-label">
                <span>@lang('Redirect Progress')</span>
                <span class="progress-percentage" id="progressPercent">0%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>
        
        <!-- Enhanced Security Section -->
        <div class="security-section">
            <div class="security-note">
                <span class="security-icon">🔒</span>
                @lang('Your transaction is secured with SSL encryption')
            </div>
            <div class="security-features">
                <div class="security-badge">
                    <span>🛡️</span>
                    <span>@lang('256-bit SSL')</span>
                </div>
                <div class="security-badge">
                    <span>✅</span>
                    <span>@lang('PCI Compliant')</span>
                </div>
                <div class="security-badge">
                    <span>🔐</span>
                    <span>@lang('Encrypted')</span>
                </div>
            </div>
        </div>
        
        <div class="click-hint" id="clickHint" onclick="submitForm()">
            @lang('Taking longer than expected? Click here to continue manually.')
        </div>
    </div>

    <form action="{{ $data->url }}" method="{{ $data->method }}" id="auto_submit">
        @foreach($data->val as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}"/>
        @endforeach
    </form>

    <script>
        "use strict";
        
        class PaymentRedirect {
            constructor() {
                this.progress = 0;
                this.messages = [
                    '@lang("Initializing secure connection...")',
                    '@lang("Verifying payment details...")',
                    '@lang("Connecting to payment gateway...")',
                    '@lang("Preparing redirect...")',
                    '@lang("Finalizing connection...")',
                    '@lang("Almost ready...")'
                ];
                this.messageIndex = 0;
                this.redirected = false;
                this.init();
            }
            
            init() {
                this.startProgressAnimation();
                this.startMessageCycle();
                this.setupEventListeners();
                this.scheduleRedirect();
            }
            
            startProgressAnimation() {
                const progressFill = document.getElementById('progressFill');
                const progressPercent = document.getElementById('progressPercent');
                
                const interval = setInterval(() => {
                    if (this.progress >= 100) {
                        clearInterval(interval);
                        return;
                    }
                    
                    this.progress += Math.random() * 15 + 5; // Random progress increments
                    if (this.progress > 100) this.progress = 100;
                    
                    if (progressFill) {
                        progressFill.style.width = this.progress + '%';
                    }
                    if (progressPercent) {
                        progressPercent.textContent = Math.round(this.progress) + '%';
                    }
                }, 200);
            }
            
            startMessageCycle() {
                const loadingMessage = document.getElementById('loadingMessage');
                const statusText = document.getElementById('statusText');
                
                const cycleMessages = () => {
                    if (this.messageIndex < this.messages.length && loadingMessage) {
                        loadingMessage.textContent = this.messages[this.messageIndex];
                        this.messageIndex++;
                        
                        // Update status text
                        if (statusText) {
                            const statuses = ['@lang("Initializing")', '@lang("Verifying")', '@lang("Connecting")', '@lang("Processing")', '@lang("Finalizing")', '@lang("Ready")'];
                            statusText.textContent = statuses[Math.min(this.messageIndex - 1, statuses.length - 1)];
                        }
                        
                        setTimeout(cycleMessages, 800);
                    }
                };
                
                setTimeout(cycleMessages, 500);
            }
            
            setupEventListeners() {
                // Enhanced keyboard and mouse interactions
                document.addEventListener('click', () => this.submitForm());
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.submitForm();
                    }
                });
                
                // Touch events for mobile
                document.addEventListener('touchstart', () => this.submitForm());
                
                // Show click hint after delay
                setTimeout(() => {
                    const clickHint = document.getElementById('clickHint');
                    if (clickHint && !this.redirected) {
                        clickHint.style.opacity = '1';
                        clickHint.style.transform = 'translateY(0)';
                    }
                }, 4000);
            }
            
            scheduleRedirect() {
                // Primary redirect after 3 seconds
                setTimeout(() => {
                    if (!this.redirected) {
                        this.submitForm();
                    }
                }, 3000);
                
                // Fallback redirect after 8 seconds
                setTimeout(() => {
                    if (!this.redirected) {
                        this.submitForm();
                    }
                }, 8000);
            }
            
            submitForm() {
                if (this.redirected) return;
                
                this.redirected = true;
                
                const container = document.querySelector('.redirect-container');
                const loadingMessage = document.getElementById('loadingMessage');
                const statusText = document.getElementById('statusText');
                
                if (container) {
                    container.style.transform = 'scale(0.95)';
                    container.style.opacity = '0.8';
                }
                
                if (loadingMessage) {
                    loadingMessage.textContent = '@lang("Redirecting now...")';
                }
                
                if (statusText) {
                    statusText.textContent = '@lang("Redirecting")';
                }
                
                const progressFill = document.getElementById('progressFill');
                const progressPercent = document.getElementById('progressPercent');
                if (progressFill) progressFill.style.width = '100%';
                if (progressPercent) progressPercent.textContent = '100%';
                
                setTimeout(() => {
                    const form = document.getElementById("auto_submit");
                    if (form) {
                        form.submit();
                    }
                }, 300);
            }
            
            addSparkleEffect() {
                const container = document.querySelector('.redirect-container');
                if (!container) return;
                
                for (let i = 0; i < 5; i++) {
                    setTimeout(() => {
                        const sparkle = document.createElement('div');
                        sparkle.innerHTML = '✨';
                        sparkle.style.position = 'absolute';
                        sparkle.style.fontSize = '20px';
                        sparkle.style.pointerEvents = 'none';
                        sparkle.style.left = Math.random() * 100 + '%';
                        sparkle.style.top = Math.random() * 100 + '%';
                        sparkle.style.animation = 'sparkle 2s ease-out forwards';
                        sparkle.style.zIndex = '1000';
                        
                        container.appendChild(sparkle);
                        
                        setTimeout(() => {
                            if (sparkle.parentNode) {
                                sparkle.parentNode.removeChild(sparkle);
                            }
                        }, 2000);
                    }, i * 200);
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const paymentRedirect = new PaymentRedirect();
            
            // Add sparkle effect after a delay
            setTimeout(() => {
                paymentRedirect.addSparkleEffect();
            }, 2000);
        });
        
        function submitForm() {
            if (window.paymentRedirect) {
                window.paymentRedirect.submitForm();
            } else {
                document.getElementById("auto_submit").submit();
            }
        }
        
        const sparkleStyle = document.createElement('style');
        sparkleStyle.innerHTML = '@keyframes sparkle { 0% { opacity: 0; transform: scale(0) rotate(0deg); } 50% { opacity: 1; transform: scale(1) rotate(180deg); } 100% { opacity: 0; transform: scale(0) rotate(360deg); } }';
        document.head.appendChild(sparkleStyle);
        
        const link = document.createElement('link');
        link.rel = 'preconnect';
        link.href = '{{ parse_url($data->url ?? "", PHP_URL_SCHEME) }}://{{ parse_url($data->url ?? "", PHP_URL_HOST) }}';
        document.head.appendChild(link);
    </script>
</body>
</html>