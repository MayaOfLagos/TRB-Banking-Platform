@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-4xl mx-auto px-1 sm:px-6 lg:px-8">
        <!-- Success Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6 relative" id="success-container">
            <!-- Confetti Container -->
            <div id="confetti-container" class="absolute inset-0 pointer-events-none z-50 overflow-hidden rounded-2xl md:rounded-3xl"></div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 md:px-8 py-8 text-center relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-transparent via-white/20 to-transparent transform rotate-12"></div>
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/10 rounded-full"></div>
                </div>
                
                <div class="relative z-10">
                    <div class="w-20 h-20 md:w-24 md:h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 checkmark-container">
                        <i class="las la-check text-white text-3xl md:text-4xl checkmark-icon"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-2 fade-in-up">@lang('Transaction Successful')</h1>
                    <p class="text-green-100 text-lg fade-in-up-delay">@lang('Your transfer has been processed successfully')</p>
                    <div class="mt-4 inline-flex items-center px-4 py-2 bg-white/20 rounded-full fade-in-up-delay-2">
                        <i class="las la-clock text-white mr-2"></i>
                        <span class="text-white font-medium">{{ showDateTime($transfer->created_at, 'M d, Y \a\t h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <!-- Card Header -->
            <div class="px-6 md:px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">@lang('Transfer Details')</h2>
                    <div class="flex items-center space-x-3">
                        <button id="transferInfoBtn" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg transition-all duration-200 hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <i class="las la-info-circle mr-2 text-lg"></i>
                            @lang('More Info')
                        </button>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                            <i class="las la-check-circle mr-1"></i>
                            @lang('Completed')
                        </span>
                    </div>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - From Details -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="las la-user-circle text-blue-500 mr-2 text-xl"></i>
                                @lang('From Account')
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Account Holder')</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $transfer->user->username }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Account Number')</span>
                                    <span class="text-sm font-mono text-gray-900 dark:text-white">{{ $transfer->user->account_number }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Email')</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $transfer->user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Amount -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 rounded-xl p-6 border border-green-200 dark:border-green-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="las la-money-bill-wave text-green-500 mr-2 text-xl"></i>
                                @lang('Transfer Amount')
                            </h3>
                            <div class="text-center">
                                <div class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                                    {{ gs('cur_text') }} {{ showAmount($transfer->amount, currencyFormat: false) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    @lang('Transfer Amount') • {{ showDateTime($transfer->created_at, 'M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - To Details -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="las la-user-check text-green-500 mr-2 text-xl"></i>
                                @lang('To Account')
                            </h3>
                            <div class="space-y-3">
                                @if ($transfer->wire_transfer_data)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Recipient Name')</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $transfer->wireTransferAccountName() }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Account Number')</span>
                                        <span class="text-sm font-mono text-gray-900 dark:text-white">{{ $transfer->wireTransferAccountNumber() }}</span>
                                    </div>
                                @elseif ($transfer->beneficiary)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Account Holder')</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $transfer->beneficiary->account_name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Account Number')</span>
                                        <span class="text-sm font-mono text-gray-900 dark:text-white">{{ $transfer->beneficiary->account_number }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Recipient')</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">@lang('N/A')</span>
                                    </div>
                                @endif
                                
                                @if ($transfer->beneficiary && $transfer->beneficiary->beneficiary_type == 'App\\Models\\OtherBank')
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Bank Name')</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ __($transfer->beneficiary->beneficiaryOf->name) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Transaction Info -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="las la-info-circle text-purple-500 mr-2 text-xl"></i>
                                @lang('Transaction Info')
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Transaction ID')</span>
                                    <span class="text-sm font-mono text-gray-900 dark:text-white">#{{ $transfer->trx }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Date')</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ showDateTime($transfer->created_at, 'F d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Time')</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ showDateTime($transfer->created_at, 'h:i A') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Status')</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                        <i class="las la-check-circle mr-1"></i>
                                        @lang('Success')
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl md:rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="p-6 md:p-8">
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 justify-center">
                    <a href="{{ route('user.transfer.details', $transfer->trx) }}?download" 
                       class="flex-1 sm:flex-none bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 transform hover:scale-105">
                        <i class="las la-download text-lg"></i>
                        <span>@lang('Download Receipt')</span>
                    </a>
                    
                    <a href="{{ route('user.transfer.history') }}" 
                       class="flex-1 sm:flex-none px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-200 font-medium flex items-center justify-center space-x-2 transform hover:scale-105">
                        <i class="las la-history text-lg"></i>
                        <span>@lang('View History')</span>
                    </a>
                    
                    <a href="{{ route('user.home') }}" 
                       class="flex-1 sm:flex-none px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-200 font-medium flex items-center justify-center space-x-2 transform hover:scale-105">
                        <i class="las la-home text-lg"></i>
                        <span>@lang('Dashboard')</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">@lang('Make Another Transfer')</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-3xl mx-auto">
                <a href="{{ route('user.transfer.own.bank.beneficiaries') }}" 
                   class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-600 transition-all duration-200 group shadow-lg hover:shadow-xl transform hover:scale-105">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                        <i class="las la-university text-green-600 dark:text-green-400 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Own Bank Transfer')</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Send to same bank accounts')</p>
                </a>
                
                <a href="{{ route('user.transfer.other.bank.beneficiaries') }}" 
                   class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200 group shadow-lg hover:shadow-xl transform hover:scale-105">
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                        <i class="las la-exchange-alt text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Other Bank Transfer')</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Send to other bank accounts')</p>
                </a>
                
                <a href="{{ route('user.transfer.wire.index') }}" 
                   class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-200 group shadow-lg hover:shadow-xl transform hover:scale-105">
                    <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors">
                        <i class="las la-globe text-purple-600 dark:text-purple-400 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Wire Transfer')</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">@lang('International wire transfers')</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
'use strict';
(function($) {
    // Confetti Animation Class
    class TransferConfetti {
        constructor(container) {
            this.container = container;
            this.colors = ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#fbbf24', '#f59e0b', '#d97706', '#22c55e'];
            this.confettiCount = 100;
            this.isActive = false;
        }
        
        createConfettiPiece() {
            const confetti = document.createElement('div');
            confetti.className = 'confetti-piece';
            
            // Random properties
            const color = this.colors[Math.floor(Math.random() * this.colors.length)];
            const size = Math.random() * 10 + 6; // 6-16px
            const startX = Math.random() * this.container.offsetWidth;
            const duration = Math.random() * 4 + 3; // 3-7 seconds
            const delay = Math.random() * 1.5; // 0-1.5 seconds delay
            const rotation = Math.random() * 360;
            
            // Different shapes for variety
            const shapes = ['circle', 'square', 'triangle', 'star', 'diamond'];
            const shape = shapes[Math.floor(Math.random() * shapes.length)];
            
            confetti.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                background-color: ${color};
                left: ${startX}px;
                top: -20px;
                z-index: 1000;
                pointer-events: none;
                border-radius: ${shape === 'circle' ? '50%' : '2px'};
                animation: confetti-fall-${Math.floor(Math.random() * 3)} ${duration}s ease-in-out ${delay}s forwards;
            `;
            
            // Special shapes
            if (shape === 'triangle') {
                confetti.style.backgroundColor = 'transparent';
                confetti.style.borderLeft = `${size/2}px solid transparent`;
                confetti.style.borderRight = `${size/2}px solid transparent`;
                confetti.style.borderBottom = `${size}px solid ${color}`;
                confetti.style.width = '0';
                confetti.style.height = '0';
                confetti.style.borderRadius = '0';
            } else if (shape === 'star') {
                confetti.innerHTML = '★';
                confetti.style.backgroundColor = 'transparent';
                confetti.style.color = color;
                confetti.style.fontSize = `${size}px`;
                confetti.style.lineHeight = '1';
                confetti.style.fontWeight = 'bold';
                confetti.style.textAlign = 'center';
            } else if (shape === 'diamond') {
                confetti.style.transform = 'rotate(45deg)';
            }
            
            return confetti;
        }
        
        startConfetti() {
            if (this.isActive) return;
            this.isActive = true;
            
            // Create confetti burst
            for (let i = 0; i < this.confettiCount; i++) {
                const confetti = this.createConfettiPiece();
                this.container.appendChild(confetti);
                
                // Remove confetti after animation
                setTimeout(() => {
                    if (confetti && confetti.parentNode) {
                        confetti.parentNode.removeChild(confetti);
                    }
                }, 8000);
            }
            
            // Stop after duration
            setTimeout(() => {
                this.isActive = false;
            }, 6000);
        }
        
        stopConfetti() {
            this.isActive = false;
            // Clear all confetti
            const confettiPieces = this.container.querySelectorAll('.confetti-piece');
            confettiPieces.forEach(piece => {
                if (piece.parentNode) {
                    piece.parentNode.removeChild(piece);
                }
            });
        }
    }
    
    // Initialize confetti
    let transferConfetti;
    
    // Start celebration when page loads
    $(document).ready(function() {
        const confettiContainer = document.getElementById('confetti-container');
        if (confettiContainer) {
            transferConfetti = new TransferConfetti(confettiContainer);
        }
        
        // Delay to let the page settle
        setTimeout(() => {
            // Start checkmark animation
            $('.checkmark-container').addClass('animate-checkmark breathing-green');
            $('.checkmark-icon').addClass('animate-checkmark-icon');
            
            // Start confetti after checkmark animation starts
            setTimeout(() => {
                if (transferConfetti) {
                    transferConfetti.startConfetti();
                }
            }, 800);
            
            // Start fade-in animations
            setTimeout(() => {
                $('.fade-in-up').addClass('animate-fade-in-up');
            }, 400);
            
            setTimeout(() => {
                $('.fade-in-up-delay').addClass('animate-fade-in-up-delay');
            }, 700);
            
            setTimeout(() => {
                $('.fade-in-up-delay-2').addClass('animate-fade-in-up-delay-2');
            }, 1000);
            
        }, 500);
    });
    
    // Add celebration function for manual trigger
    window.celebrateTransfer = function() {
        if (transferConfetti) {
            transferConfetti.startConfetti();
        }
        $('.checkmark-container').removeClass('animate-checkmark breathing-green').addClass('animate-checkmark breathing-green');
        $('.checkmark-icon').removeClass('animate-checkmark-icon').addClass('animate-checkmark-icon');
    };
    
    // Transfer Information Modal Functionality
    class TransferInfoModal {
        constructor() {
            this.modal = document.getElementById('transferInfoModal');
            this.initializeEventListeners();
        }
        
        initializeEventListeners() {
            // Open modal
            const openBtn = document.getElementById('transferInfoBtn');
            if (openBtn) {
                openBtn.addEventListener('click', () => {
                    this.openModal();
                });
            }
            
            // Close modal buttons
            const closeBtn = document.getElementById('closeInfoModalBtn');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    this.closeModal();
                });
            }
            
            const closeBtn2 = document.getElementById('closeInfoBtn');
            if (closeBtn2) {
                closeBtn2.addEventListener('click', () => {
                    this.closeModal();
                });
            }
            
            // Close on overlay click
            if (this.modal) {
                this.modal.addEventListener('click', (e) => {
                    if (e.target === this.modal) {
                        this.closeModal();
                    }
                });
            }
            
            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal && this.modal.classList.contains('active')) {
                    this.closeModal();
                }
            });
        }
        
        openModal() {
            if (this.modal) {
                this.modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
        
        closeModal() {
            if (this.modal) {
                this.modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    }
    
    // Initialize Transfer Information Modal
    let transferInfoModal;
    
    $(document).ready(function() {
        transferInfoModal = new TransferInfoModal();
    });
    
    // Auto-hide success message after some time
    setTimeout(function() {
        if ($('.alert-success').length) {
            $('.alert-success').fadeOut(500);
        }
    }, 5000);
    
    // Print functionality
    window.printReceipt = function() {
        // Add print styles before printing
        const printCSS = `
            @media print {
                body * { visibility: hidden; }
                .print-area, .print-area * { visibility: visible; }
                .print-area { 
                    position: absolute; 
                    left: 0; 
                    top: 0; 
                    width: 100%; 
                    background: white !important;
                    color: black !important;
                }
                .no-print { display: none !important; }
                .bg-gradient-to-r { background: #10b981 !important; }
                .dark\\:bg-gray-800 { background: white !important; }
                .dark\\:text-white { color: black !important; }
                .dark\\:border-gray-700 { border-color: #e5e7eb !important; }
                .confetti-piece { display: none !important; }
            }
        `;
        
        if (!document.getElementById('print-styles')) {
            const style = document.createElement('style');
            style.id = 'print-styles';
            style.textContent = printCSS;
            document.head.appendChild(style);
        }
        
        // Mark the main content as print area
        document.querySelector('.min-h-screen').classList.add('print-area');
        
        window.print();
    }
    
    // Share functionality
    window.shareReceipt = function() {
        const transferData = {
            trx: '#{{ $transfer->trx }}',
            amount: '{{ gs("cur_text") }} {{ showAmount($transfer->amount, currencyFormat: false) }}',
            date: '{{ showDateTime($transfer->created_at, "M d, Y") }}',
            from: '{{ $transfer->user->username }}',
            to: '{{ $transfer->wire_transfer_data ? $transfer->wireTransferAccountName() : ($transfer->beneficiary ? $transfer->beneficiary->account_name : "N/A") }}'
        };
        
        const shareText = `🎉 Transfer Successful!\n\nTransaction ID: ${transferData.trx}\nAmount: ${transferData.amount}\nFrom: ${transferData.from}\nTo: ${transferData.to}\nDate: ${transferData.date}\n\n✅ Transfer completed successfully!\n\nView details: ${window.location.href}`;
        
        if (navigator.share) {
            navigator.share({
                title: '🎉 Transfer Receipt',
                text: shareText,
                url: window.location.href
            }).catch(console.error);
        } else {
            // Fallback: Copy to clipboard
            navigator.clipboard.writeText(shareText).then(function() {
                notify('success', '@lang("Receipt details copied to clipboard")');
                // Trigger celebration on successful copy
                celebrateTransfer();
            }).catch(function() {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = shareText;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                notify('success', '@lang("Receipt details copied to clipboard")');
                celebrateTransfer();
            });
        }
    }
    
    // Add copy transaction ID functionality
    window.copyTransactionId = function() {
        const trxId = '{{ $transfer->trx }}';
        navigator.clipboard.writeText(trxId).then(function() {
            notify('success', '@lang("Transaction ID copied to clipboard")');
        }).catch(function() {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = trxId;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            notify('success', '@lang("Transaction ID copied to clipboard")');
        });
    }
    
    // Add click to copy functionality for transaction ID
    $(document).on('click', '[data-copy-trx]', function(e) {
        e.preventDefault();
        copyTransactionId();
    });
    
    // Add smooth scroll for better UX
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
    
    // Add loading state for download button
    $('a[href*="download"]').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.html('<i class="las la-spinner la-spin mr-2"></i>@lang("Generating...")');
        $btn.addClass('opacity-75 cursor-not-allowed');
        
        // Reset after 3 seconds (assuming download completes)
        setTimeout(function() {
            $btn.html(originalText);
            $btn.removeClass('opacity-75 cursor-not-allowed');
        }, 3000);
    });
    
})(jQuery);
</script>
@endpush

@push('style')
<style>
    /* Confetti Animations - Multiple variations for natural movement */
    @keyframes confetti-fall-0 {
        0% {
            transform: translateY(-100px) translateX(0px) rotateZ(0deg) scale(1);
            opacity: 1;
        }
        25% {
            transform: translateY(100px) translateX(-20px) rotateZ(90deg) scale(0.8);
            opacity: 1;
        }
        50% {
            transform: translateY(300px) translateX(30px) rotateZ(180deg) scale(1.1);
            opacity: 0.8;
        }
        75% {
            transform: translateY(500px) translateX(-10px) rotateZ(270deg) scale(0.9);
            opacity: 0.6;
        }
        100% {
            transform: translateY(700px) translateX(20px) rotateZ(360deg) scale(0.7);
            opacity: 0;
        }
    }
    
    @keyframes confetti-fall-1 {
        0% {
            transform: translateY(-100px) translateX(0px) rotateZ(0deg) scale(1);
            opacity: 1;
        }
        30% {
            transform: translateY(150px) translateX(40px) rotateZ(120deg) scale(1.2);
            opacity: 1;
        }
        60% {
            transform: translateY(400px) translateX(-30px) rotateZ(240deg) scale(0.8);
            opacity: 0.7;
        }
        100% {
            transform: translateY(700px) translateX(10px) rotateZ(360deg) scale(0.5);
            opacity: 0;
        }
    }
    
    @keyframes confetti-fall-2 {
        0% {
            transform: translateY(-100px) translateX(0px) rotateZ(0deg) scale(1);
            opacity: 1;
        }
        20% {
            transform: translateY(80px) translateX(-50px) rotateZ(72deg) scale(1.3);
            opacity: 1;
        }
        40% {
            transform: translateY(250px) translateX(20px) rotateZ(144deg) scale(0.9);
            opacity: 0.9;
        }
        70% {
            transform: translateY(450px) translateX(-25px) rotateZ(252deg) scale(1.1);
            opacity: 0.5;
        }
        100% {
            transform: translateY(700px) translateX(35px) rotateZ(360deg) scale(0.6);
            opacity: 0;
        }
    }
    
    /* Checkmark Animations */
    .checkmark-container.animate-checkmark {
        animation: checkmark-bounce 1.2s ease-out forwards;
    }
    
    .checkmark-icon.animate-checkmark-icon {
        animation: checkmark-draw 1.5s ease-out forwards;
    }
    
    /* Continuous breathing animation for checkmark */
    .checkmark-container.breathing-green {
        animation: checkmark-bounce 1.2s ease-out forwards, green-breathing 3s ease-in-out infinite 1.2s;
    }
    
    @keyframes checkmark-bounce {
        0% {
            transform: scale(0) rotate(0deg);
            opacity: 0;
        }
        50% {
            transform: scale(1.4) rotate(180deg);
            opacity: 1;
        }
        70% {
            transform: scale(0.8) rotate(360deg);
        }
        85% {
            transform: scale(1.2) rotate(360deg);
        }
        100% {
            transform: scale(1) rotate(360deg);
            opacity: 1;
        }
    }
    
    @keyframes checkmark-draw {
        0% {
            transform: scale(0) rotate(-45deg);
            opacity: 0;
        }
        40% {
            transform: scale(0.7) rotate(0deg);
            opacity: 0.8;
        }
        70% {
            transform: scale(1.3) rotate(15deg);
            opacity: 1;
        }
        90% {
            transform: scale(0.9) rotate(-5deg);
            opacity: 1;
        }
        100% {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }
    }
    
    /* Continuous green breathing effect */
    @keyframes green-breathing {
        0% {
            background-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            transform: scale(1);
        }
        50% {
            background-color: rgba(34, 197, 94, 0.3);
            box-shadow: 0 0 0 15px rgba(34, 197, 94, 0);
            transform: scale(1.05);
        }
        100% {
            background-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            transform: scale(1);
        }
    }
    
    /* Confetti container positioning */
    #confetti-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1000;
        overflow: hidden;
        border-radius: inherit;
    }
    
    /* Individual confetti pieces */
    .confetti-piece {
        position: absolute;
        pointer-events: none;
        user-select: none;
        will-change: transform, opacity;
    }
    
    /* Success container positioning */
    #success-container {
        position: relative;
        overflow: hidden;
    }
    
    /* Fade-in animations for text */
    .fade-in-up {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease-out;
    }
    
    .fade-in-up.animate-fade-in-up {
        opacity: 1;
        transform: translateY(0);
    }
    
    .fade-in-up-delay {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease-out;
    }
    
    .fade-in-up-delay.animate-fade-in-up-delay {
        opacity: 1;
        transform: translateY(0);
    }
    
    .fade-in-up-delay-2 {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease-out;
    }
    
    .fade-in-up-delay-2.animate-fade-in-up-delay-2 {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Custom animations */
    .transform {
        transition: transform 0.2s ease-in-out;
    }
    
    .hover\:scale-105:hover {
        transform: scale(1.05);
    }
    
    /* Custom gradient animations */
    .bg-gradient-to-r {
        background-size: 200% 200%;
        animation: gradientShift 6s ease infinite;
    }
    
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    /* Success pulse animation */
    .checkmark-container {
        position: relative;
    }
    
    .checkmark-container::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        animation: success-pulse 2s ease-out infinite;
        z-index: -1;
    }
    
    @keyframes success-pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.7;
        }
        100% {
            transform: scale(1.2);
            opacity: 0;
        }
    }
    
    /* Enhanced money celebration */
    .money-symbol {
        position: absolute;
        font-size: 24px;
        font-weight: bold;
        color: #10b981;
        pointer-events: none;
        animation: money-float 4s ease-out forwards;
    }
    
    @keyframes money-float {
        0% {
            transform: translateY(0px) rotate(0deg);
            opacity: 1;
        }
        25% {
            transform: translateY(-50px) rotate(90deg);
            opacity: 0.8;
        }
        50% {
            transform: translateY(-100px) rotate(180deg);
            opacity: 0.6;
        }
        75% {
            transform: translateY(-150px) rotate(270deg);
            opacity: 0.4;
        }
        100% {
            transform: translateY(-200px) rotate(360deg);
            opacity: 0;
        }
    }
    
    /* Custom scrollbar for dark mode */
    .dark ::-webkit-scrollbar {
        width: 8px;
    }
    
    .dark ::-webkit-scrollbar-track {
        background: #374151;
        border-radius: 4px;
    }
    
    .dark ::-webkit-scrollbar-thumb {
        background: #6b7280;
        border-radius: 4px;
    }
    
    .dark ::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    /* Print specific styles */
    @media print {
        .min-h-screen {
            min-height: auto !important;
        }
        
        .no-print,
        .confetti-piece,
        #confetti-container {
            display: none !important;
        }
        
        .shadow-xl,
        .shadow-lg {
            box-shadow: none !important;
        }
        
        .rounded-2xl,
        .rounded-3xl,
        .rounded-xl {
            border-radius: 8px !important;
        }
        
        /* Ensure good contrast in print */
        .bg-gray-50 {
            background-color: #f9fafb !important;
        }
        
        .text-gray-500 {
            color: #6b7280 !important;
        }
        
        /* Stop animations in print */
        * {
            animation-duration: 0s !important;
            animation-delay: 0s !important;
            transition-duration: 0s !important;
        }
    }
    
    /* Loading animation for buttons */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }
    
    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive improvements */
    @media (max-width: 640px) {
        .text-3xl {
            font-size: 1.875rem;
            line-height: 2.25rem;
        }
        
        .text-4xl {
            font-size: 2.25rem;
            line-height: 2.5rem;
        }
        
        .p-6 {
            padding: 1rem;
        }
        
        .md\:p-8 {
            padding: 1.5rem;
        }
        
        /* Reduce confetti on mobile for performance */
        .confetti-piece {
            animation-duration: 2s;
        }
    }
    
    /* Focus states for accessibility */
    .focus\:ring-2:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 2px #3b82f6;
    }
    
    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .bg-gray-50 {
            background-color: #ffffff;
        }
        
        .dark .bg-gray-700\/50 {
            background-color: #000000;
        }
        
        .border-gray-200 {
            border-color: #000000;
        }
        
        .dark .border-gray-700 {
            border-color: #ffffff;
        }
        
        /* High contrast confetti */
        .confetti-piece {
            border: 1px solid #000000;
        }
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        .confetti-piece,
        .checkmark-container,
        .checkmark-icon,
        .fade-in-up,
        .fade-in-up-delay,
        .fade-in-up-delay-2 {
            animation: none !important;
            transition: none !important;
        }
        
        .fade-in-up,
        .fade-in-up-delay,
        .fade-in-up-delay-2 {
            opacity: 1 !important;
            transform: none !important;
        }
    }
    
    /* Edit Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }
    
    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .modal-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        max-width: 90vw;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 10000;
        transition: all 0.3s ease;
    }
    
    .modal-overlay.active .modal-container {
        transform: translate(-50%, -50%) scale(1);
    }
    
    @media (min-width: 640px) {
        .modal-container {
            max-width: 600px;
        }
    }
</style>

<!-- Transfer Information Modal -->
<div id="transferInfoModal" class="modal-overlay">
    <div class="modal-container">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="las la-info-circle text-blue-600 mr-2 text-2xl"></i>
                        @lang('Transfer Information')
                    </h3>
                    <button id="closeInfoModalBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1">
                        <i class="las la-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <div class="space-y-6">
                    
                    <!-- Transaction Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="las la-file-alt text-gray-600 dark:text-gray-400 mr-2"></i>
                            @lang('Transaction Summary')
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="bg-white dark:bg-gray-600/50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">@lang('Transaction ID'):</span>
                                    <span class="font-mono text-gray-900 dark:text-white">#{{ $transfer->trx }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">@lang('Status'):</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                        <i class="las la-check-circle mr-1"></i>
                                        @lang('Completed')
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">@lang('Processing Time'):</span>
                                    <span class="text-gray-900 dark:text-white">@lang('Instant')</span>
                                </div>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-600/50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">@lang('Amount'):</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ gs('cur_text') }} {{ showAmount($transfer->amount) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">@lang('Date'):</span>
                                    <span class="text-gray-900 dark:text-white">{{ showDateTime($transfer->created_at, 'M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">@lang('Time'):</span>
                                    <span class="text-gray-900 dark:text-white">{{ showDateTime($transfer->created_at, 'h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        type="button" 
                        id="closeInfoBtn"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center justify-center">
                        <i class="las la-check mr-2"></i>
                        @lang('Got It')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush