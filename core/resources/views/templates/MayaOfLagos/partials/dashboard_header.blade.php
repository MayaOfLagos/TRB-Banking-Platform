<!-- Dashboard Header -->
<header class="bg-white dark:bg-gray-800 sticky top-0 z-20 transition-colors duration-300">
    <div class="flex items-center justify-between px-4 lg:px-6 py-4">
        <!-- Left Section -->
        <div class="flex items-center space-x-4">
            <!-- Mobile Menu Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-bars text-lg"></i>
            </button>

            <!-- Page Title -->
            <div class="flex items-center space-x-3">
                @php
                    $logoUrl = null;
                    try {
                        $logoUrl = siteLogo();
                    } catch (Exception $e) {
                        $logoUrl = null;
                    }
                @endphp
                
                @if($logoUrl)
                    <!-- Mobile Logo - Only visible on mobile screens -->
                    <img src="{{ $logoUrl }}" alt="{{ gs('site_name') ?? 'Site Logo' }}" class="h-8 w-auto lg:hidden">
                @endif
                
                <!-- Page Title - Hidden on mobile when logo exists, always visible on desktop -->
                <h1 class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-white {{ $logoUrl ? 'hidden lg:block' : '' }}">{{ __(@$pageTitle) }}</h1>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-3">
            <!-- Dark Mode Toggle -->
            <div class="relative" x-data="{ tooltipOpen: false }">
                <button @click="toggleDarkMode()" @mouseenter="tooltipOpen = true" @mouseleave="tooltipOpen = false" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200">
                    <!-- Light Mode Icon -->
                    <i class="las la-sun text-lg" x-show="theme === 'light'"></i>
                    <!-- Dark Mode Icon -->  
                    <i class="las la-moon text-lg" x-show="theme === 'dark'"></i>
                    <!-- System Mode Icon -->
                    <i class="las la-desktop text-lg" x-show="theme === 'system'"></i>
                </button>
                
                <!-- Tooltip - Now drops down instead of up -->
                <div x-show="tooltipOpen" x-transition class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap z-50">
                    <span x-text="theme === 'light' ? 'Light Mode' : theme === 'dark' ? 'Dark Mode' : 'System Mode'"></span>
                    <!-- Arrow pointing up -->
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-b-gray-900 dark:border-b-gray-700"></div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="relative" x-data="{ notificationsOpen: false }">
                @php
                    $user = auth()->user();
                    $transactionRemarks = ['deposit', 'received_money', 'fdr_installment', 'loan_installment', 'dps_matured', 'referral_commission', 'withdraw', 'own_bank_transfer', 'other_bank_transfer', 'wire_transfer', 'dps_installment', 'fdr_open', 'fdr_closed', 'loan_taken'];

                    $recentTransactions = \App\Models\Transaction::where('user_id', $user->id)
                        ->whereIn('remark', $transactionRemarks)
                        ->latest()
                        ->limit(5)
                        ->get();

                    $rebateActivities = \App\Models\RebateTransaction::with(['rebateProgram', 'rebateCategory'])
                        ->where('user_id', $user->id)
                        ->orderByDesc('updated_at')
                        ->orderByDesc('created_at')
                        ->limit(4)
                        ->get();

                    $pushNotifications = \Illuminate\Support\Facades\DB::table('push_notifications')
                        ->where('user_id', $user->id)
                        ->orderByDesc('sent_at')
                        ->orderByDesc('created_at')
                        ->limit(4)
                        ->get();

                    // Get system notifications from notification_logs (push only - not email/SMS)
                    $systemNotifications = \Illuminate\Support\Facades\DB::table('notification_logs')
                        ->where('user_id', $user->id)
                        ->where('notification_type', 'push')
                        ->latest('created_at')
                        ->limit(10)
                        ->get();

                    $notificationItems = collect();

                    foreach ($rebateActivities as $rebate) {
                        $status = $rebate->status ?? 'pending';
                        $programName = optional($rebate->rebateProgram)->name ?? optional($rebate->rebateCategory)->name ?? __('Rebate Program');
                        $amount = showAmount($rebate->final_amount ?? 0, currencyFormat: false);
                        $time = $rebate->updated_at ?? $rebate->created_at ?? now();

                        $rebateMeta = match ($status) {
                            'approved', 'processed' => [
                                'title' => __('Rebate Approved'),
                                'icon' => 'las la-dollar-sign',
                                'bg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                                'color' => 'text-emerald-600 dark:text-emerald-400',
                                'label' => __('Rebate'),
                                'unread' => false,
                            ],
                            'rejected', 'failed' => [
                                'title' => __('Rebate Rejected'),
                                'icon' => 'las la-times-circle',
                                'bg' => 'bg-red-100 dark:bg-red-900/30',
                                'color' => 'text-red-600 dark:text-red-400',
                                'label' => __('Rebate'),
                                'unread' => false,
                            ],
                            default => [
                                'title' => __('Rebate Submitted'),
                                'icon' => 'las la-hourglass-half',
                                'bg' => 'bg-amber-100 dark:bg-amber-900/30',
                                'color' => 'text-amber-600 dark:text-amber-400',
                                'label' => __('Rebate'),
                                'unread' => true,
                            ],
                        };

                        $notificationItems->push([
                            'id' => 'rebate-' . $rebate->id,
                            'title' => $rebateMeta['title'],
                            'message' => __(':program • :amount', [
                                'program' => $programName,
                                'amount' => gs()->cur_sym . $amount,
                            ]),
                            'meta' => $rebate->reference_id ? __('Reference #:ref', ['ref' => $rebate->reference_id]) : null,
                            'icon' => $rebateMeta['icon'],
                            'bg' => $rebateMeta['bg'],
                            'color' => $rebateMeta['color'],
                            'label' => $rebateMeta['label'],
                            'time' => $time,
                            'unread' => $rebateMeta['unread'],
                        ]);
                    }

                    foreach ($pushNotifications as $push) {
                        $payload = json_decode($push->data, true) ?? [];
                        $type = $payload['type'] ?? 'push';
                        $time = $push->sent_at ? \Illuminate\Support\Carbon::parse($push->sent_at) : \Illuminate\Support\Carbon::parse($push->created_at);

                        $pushMeta = match ($type) {
                            'admin_manual_push' => [
                                'title' => $push->title ?: __('Admin Notification'),
                                'icon' => 'las la-paper-plane',
                                'bg' => 'bg-sky-100 dark:bg-sky-900/30',
                                'color' => 'text-sky-600 dark:text-sky-400',
                                'label' => __('Admin'),
                            ],
                            'tier_advancement' => [
                                'title' => $push->title ?: __('Tier Advancement'),
                                'icon' => 'las la-trophy',
                                'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                                'color' => 'text-blue-600 dark:text-blue-400',
                                'label' => __('Tier'),
                            ],
                            'fraud_alert' => [
                                'title' => $push->title ?: __('Security Alert'),
                                'icon' => 'las la-shield-alt',
                                'bg' => 'bg-orange-100 dark:bg-orange-900/30',
                                'color' => 'text-orange-600 dark:text-orange-400',
                                'label' => __('Security'),
                            ],
                            default => [
                                'title' => $push->title ?: __('Notification'),
                                'icon' => 'las la-bell',
                                'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                                'color' => 'text-purple-600 dark:text-purple-400',
                                'label' => __('Alert'),
                            ],
                        };

                        $notificationItems->push([
                            'id' => 'push-' . $push->id,
                            'title' => $pushMeta['title'],
                            'message' => $push->body,
                            'meta' => $payload['meta'] ?? null,
                            'icon' => $pushMeta['icon'],
                            'bg' => $pushMeta['bg'],
                            'color' => $pushMeta['color'],
                            'label' => $pushMeta['label'],
                            'time' => $time,
                            'unread' => !$push->read,
                        ]);
                    }

                    foreach ($recentTransactions as $transaction) {
                        $remark = $transaction->remark;
                        $transactionMeta = [
                            'title' => __(str_replace('_', ' ', ucfirst($remark))),
                            'icon' => 'las la-exchange-alt',
                            'bg' => 'bg-gray-100 dark:bg-gray-900/30',
                            'color' => 'text-gray-600 dark:text-gray-400',
                            'label' => __('Transaction'),
                        ];

                        switch ($remark) {
                            case 'deposit':
                                $transactionMeta = [
                                    'title' => __('Deposit Successful'),
                                    'icon' => 'las la-plus',
                                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                                    'color' => 'text-green-600 dark:text-green-400',
                                    'label' => __('Transaction'),
                                ];
                                break;
                            case 'withdraw':
                                $transactionMeta = [
                                    'title' => __('Withdrawal Processed'),
                                    'icon' => 'las la-minus',
                                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                                    'color' => 'text-red-600 dark:text-red-400',
                                    'label' => __('Transaction'),
                                ];
                                break;
                            case 'received_money':
                            case 'own_bank_transfer':
                            case 'other_bank_transfer':
                            case 'wire_transfer':
                                $transactionMeta = [
                                    'title' => $transaction->trx_type === '+' ? __('Money Received') : __('Transfer Sent'),
                                    'icon' => 'las la-exchange-alt',
                                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                                    'color' => 'text-blue-600 dark:text-blue-400',
                                    'label' => __('Transaction'),
                                ];
                                break;
                            case 'loan_taken':
                                $transactionMeta = [
                                    'title' => __('Loan Disbursed'),
                                    'icon' => 'las la-hand-holding-usd',
                                    'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                                    'color' => 'text-yellow-600 dark:text-yellow-400',
                                    'label' => __('Loan'),
                                ];
                                break;
                            case 'loan_installment':
                                $transactionMeta = [
                                    'title' => __('Loan Installment Paid'),
                                    'icon' => 'las la-credit-card',
                                    'bg' => 'bg-orange-100 dark:bg-orange-900/30',
                                    'color' => 'text-orange-600 dark:text-orange-400',
                                    'label' => __('Loan'),
                                ];
                                break;
                            case 'fdr_installment':
                                $transactionMeta = [
                                    'title' => __('FDR Interest Received'),
                                    'icon' => 'las la-chart-line',
                                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                                    'color' => 'text-purple-600 dark:text-purple-400',
                                    'label' => __('Investment'),
                                ];
                                break;
                            case 'fdr_open':
                                $transactionMeta = [
                                    'title' => __('FDR Opened'),
                                    'icon' => 'las la-certificate',
                                    'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                                    'color' => 'text-purple-600 dark:text-purple-400',
                                    'label' => __('Investment'),
                                ];
                                break;
                            case 'fdr_closed':
                                $transactionMeta = [
                                    'title' => __('FDR Matured'),
                                    'icon' => 'las la-certificate',
                                    'bg' => 'bg-orange-100 dark:bg-orange-900/30',
                                    'color' => 'text-orange-600 dark:text-orange-400',
                                    'label' => __('Investment'),
                                ];
                                break;
                            case 'dps_installment':
                                $transactionMeta = [
                                    'title' => __('DPS Installment'),
                                    'icon' => 'las la-piggy-bank',
                                    'bg' => 'bg-teal-100 dark:bg-teal-900/30',
                                    'color' => 'text-teal-600 dark:text-teal-400',
                                    'label' => __('Savings'),
                                ];
                                break;
                            case 'dps_matured':
                                $transactionMeta = [
                                    'title' => __('DPS Matured'),
                                    'icon' => 'las la-trophy',
                                    'bg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                                    'color' => 'text-emerald-600 dark:text-emerald-400',
                                    'label' => __('Savings'),
                                ];
                                break;
                            case 'referral_commission':
                                $transactionMeta = [
                                    'title' => __('Referral Commission'),
                                    'icon' => 'las la-gift',
                                    'bg' => 'bg-indigo-100 dark:bg-indigo-900/30',
                                    'color' => 'text-indigo-600 dark:text-indigo-400',
                                    'label' => __('Reward'),
                                ];
                                break;
                        }

                        $transactionAmount = ($transaction->trx_type === '+' ? '+' : '-') . gs()->cur_sym . showAmount($transaction->amount, currencyFormat: false);

                        $notificationItems->push([
                            'id' => 'trx-' . $transaction->id,
                            'title' => $transactionMeta['title'],
                            'message' => $transactionAmount,
                            'meta' => $transaction->details ? \Illuminate\Support\Str::limit(strip_tags($transaction->details), 80) : null,
                            'icon' => $transactionMeta['icon'],
                            'bg' => $transactionMeta['bg'],
                            'color' => $transactionMeta['color'],
                            'label' => $transactionMeta['label'],
                            'time' => $transaction->created_at,
                            'unread' => false,
                        ]);
                    }

                    // Process system notifications from notification_logs
                    foreach ($systemNotifications as $notif) {
                        $subject = $notif->subject ?? 'Notification';
                        $time = \Illuminate\Support\Carbon::parse($notif->created_at);
                        
                        // Map subjects to notification metadata
                        $notifMeta = [
                            'title' => $subject,
                            'icon' => 'las la-bell',
                            'bg' => 'bg-gray-100 dark:bg-gray-900/30',
                            'color' => 'text-gray-600 dark:text-gray-400',
                            'label' => __('System'),
                            'unread' => false,
                        ];

                        // Withdrawal notifications
                        if (str_contains($subject, 'Withdraw')) {
                            if (str_contains($subject, 'Approved')) {
                                $notifMeta = [
                                    'title' => __('Withdrawal Approved'),
                                    'icon' => 'las la-check-circle',
                                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                                    'color' => 'text-green-600 dark:text-green-400',
                                    'label' => __('Withdrawal'),
                                    'unread' => true,
                                ];
                            } elseif (str_contains($subject, 'Reject')) {
                                $notifMeta = [
                                    'title' => __('Withdrawal Rejected'),
                                    'icon' => 'las la-times-circle',
                                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                                    'color' => 'text-red-600 dark:text-red-400',
                                    'label' => __('Withdrawal'),
                                    'unread' => true,
                                ];
                            } elseif (str_contains($subject, 'Request') || str_contains($subject, 'Requested')) {
                                $notifMeta = [
                                    'title' => __('Withdrawal Requested'),
                                    'icon' => 'las la-paper-plane',
                                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                                    'color' => 'text-blue-600 dark:text-blue-400',
                                    'label' => __('Withdrawal'),
                                    'unread' => false,
                                ];
                            } elseif (str_contains($subject, 'Blocked') || str_contains($subject, 'Block')) {
                                $notifMeta = [
                                    'title' => __('Withdrawal Blocked'),
                                    'icon' => 'las la-ban',
                                    'bg' => 'bg-orange-100 dark:bg-orange-900/30',
                                    'color' => 'text-orange-600 dark:text-orange-400',
                                    'label' => __('Withdrawal'),
                                    'unread' => true,
                                ];
                            }
                        }
                        // Deposit notifications
                        elseif (str_contains($subject, 'Deposit')) {
                            if (str_contains($subject, 'Approved') || str_contains($subject, 'Successful')) {
                                $notifMeta = [
                                    'title' => __('Deposit Approved'),
                                    'icon' => 'las la-check-double',
                                    'bg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                                    'color' => 'text-emerald-600 dark:text-emerald-400',
                                    'label' => __('Deposit'),
                                    'unread' => true,
                                ];
                            } elseif (str_contains($subject, 'Reject')) {
                                $notifMeta = [
                                    'title' => __('Deposit Rejected'),
                                    'icon' => 'las la-exclamation-triangle',
                                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                                    'color' => 'text-red-600 dark:text-red-400',
                                    'label' => __('Deposit'),
                                    'unread' => true,
                                ];
                            }
                        }
                        // Balance changes
                        elseif (str_contains($subject, 'Credit') || str_contains($subject, 'Added')) {
                            $notifMeta = [
                                'title' => __('Account Credited'),
                                'icon' => 'las la-plus-circle',
                                'bg' => 'bg-green-100 dark:bg-green-900/30',
                                'color' => 'text-green-600 dark:text-green-400',
                                'label' => __('Balance'),
                                'unread' => true,
                            ];
                        }
                        elseif (str_contains($subject, 'Debit') || str_contains($subject, 'Subtract')) {
                            $notifMeta = [
                                'title' => __('Account Debited'),
                                'icon' => 'las la-minus-circle',
                                'bg' => 'bg-red-100 dark:bg-red-900/30',
                                'color' => 'text-red-600 dark:text-red-400',
                                'label' => __('Balance'),
                                'unread' => true,
                            ];
                        }
                        // KYC notifications
                        elseif (str_contains($subject, 'KYC')) {
                            if (str_contains($subject, 'Approved')) {
                                $notifMeta = [
                                    'title' => __('KYC Approved'),
                                    'icon' => 'las la-user-check',
                                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                                    'color' => 'text-green-600 dark:text-green-400',
                                    'label' => __('KYC'),
                                    'unread' => true,
                                ];
                            } elseif (str_contains($subject, 'Reject')) {
                                $notifMeta = [
                                    'title' => __('KYC Rejected'),
                                    'icon' => 'las la-user-times',
                                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                                    'color' => 'text-red-600 dark:text-red-400',
                                    'label' => __('KYC'),
                                    'unread' => true,
                                ];
                            }
                        }
                        // Loan notifications
                        elseif (str_contains($subject, 'Loan')) {
                            if (str_contains($subject, 'Approved')) {
                                $notifMeta = [
                                    'title' => __('Loan Approved'),
                                    'icon' => 'las la-hand-holding-usd',
                                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                                    'color' => 'text-green-600 dark:text-green-400',
                                    'label' => __('Loan'),
                                    'unread' => true,
                                ];
                            } elseif (str_contains($subject, 'Reject')) {
                                $notifMeta = [
                                    'title' => __('Loan Rejected'),
                                    'icon' => 'las la-times-circle',
                                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                                    'color' => 'text-red-600 dark:text-red-400',
                                    'label' => __('Loan'),
                                    'unread' => true,
                                ];
                            } elseif (str_contains($subject, 'Paid')) {
                                $notifMeta = [
                                    'title' => __('Loan Paid'),
                                    'icon' => 'las la-check-circle',
                                    'bg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                                    'color' => 'text-emerald-600 dark:text-emerald-400',
                                    'label' => __('Loan'),
                                    'unread' => false,
                                ];
                            }
                        }
                        // Security notifications
                        elseif (str_contains($subject, 'Password')) {
                            $notifMeta = [
                                'title' => __('Password Updated'),
                                'icon' => 'las la-key',
                                'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                                'color' => 'text-purple-600 dark:text-purple-400',
                                'label' => __('Security'),
                                'unread' => true,
                            ];
                        }
                        elseif (str_contains($subject, 'PIN')) {
                            $notifMeta = [
                                'title' => __('Transfer PIN Updated'),
                                'icon' => 'las la-shield-alt',
                                'bg' => 'bg-indigo-100 dark:bg-indigo-900/30',
                                'color' => 'text-indigo-600 dark:text-indigo-400',
                                'label' => __('Security'),
                                'unread' => true,
                            ];
                        }
                        // Transfer notifications
                        elseif (str_contains($subject, 'Transfer')) {
                            if (str_contains($subject, 'Wire')) {
                                if (str_contains($subject, 'Complet')) {
                                    $notifMeta = [
                                        'title' => __('Wire Transfer Completed'),
                                        'icon' => 'las la-globe-americas',
                                        'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                                        'color' => 'text-blue-600 dark:text-blue-400',
                                        'label' => __('Transfer'),
                                        'unread' => true,
                                    ];
                                } elseif (str_contains($subject, 'Reject')) {
                                    $notifMeta = [
                                        'title' => __('Wire Transfer Rejected'),
                                        'icon' => 'las la-ban',
                                        'bg' => 'bg-red-100 dark:bg-red-900/30',
                                        'color' => 'text-red-600 dark:text-red-400',
                                        'label' => __('Transfer'),
                                        'unread' => true,
                                    ];
                                }
                            } elseif (str_contains($subject, 'Other Bank')) {
                                $notifMeta = [
                                    'title' => str_contains($subject, 'Complet') ? __('Transfer Completed') : __('Transfer Sent'),
                                    'icon' => 'las la-university',
                                    'bg' => 'bg-cyan-100 dark:bg-cyan-900/30',
                                    'color' => 'text-cyan-600 dark:text-cyan-400',
                                    'label' => __('Transfer'),
                                    'unread' => str_contains($subject, 'Complet'),
                                ];
                            }
                        }
                        // FDR & DPS notifications
                        elseif (str_contains($subject, 'FDR')) {
                            $notifMeta = [
                                'title' => str_contains($subject, 'Open') ? __('FDR Opened') : __('FDR Closed'),
                                'icon' => 'las la-file-invoice-dollar',
                                'bg' => 'bg-purple-100 dark:bg-purple-900/30',
                                'color' => 'text-purple-600 dark:text-purple-400',
                                'label' => __('Investment'),
                                'unread' => true,
                            ];
                        }
                        elseif (str_contains($subject, 'DPS')) {
                            $notifMeta = [
                                'title' => $subject,
                                'icon' => 'las la-piggy-bank',
                                'bg' => 'bg-teal-100 dark:bg-teal-900/30',
                                'color' => 'text-teal-600 dark:text-teal-400',
                                'label' => __('Savings'),
                                'unread' => str_contains($subject, 'Matured') || str_contains($subject, 'Open'),
                            ];
                        }

                        // Extract message preview - handle JSON payloads
                        $message = '';
                        $rawMessage = $notif->message ?? '';
                        
                        // Check if message is JSON (for push notifications)
                        if (!empty($rawMessage) && (str_starts_with($rawMessage, '{') || str_starts_with($rawMessage, '['))) {
                            $decoded = json_decode($rawMessage, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                // Extract readable message from JSON
                                $message = $decoded['body'] ?? $decoded['message'] ?? $decoded['text'] ?? '';
                            }
                        }
                        
                        // If not JSON or extraction failed, use raw message
                        if (empty($message)) {
                            $message = strip_tags($rawMessage);
                        }
                        
                        // Clean up the message
                        $message = strip_tags($message);
                        $message = str_replace(["\r", "\n", "\t"], ' ', $message);
                        $message = preg_replace('/\s+/', ' ', $message);
                        $message = trim($message);
                        $message = \Illuminate\Support\Str::limit($message, 100);

                        $notificationItems->push([
                            'id' => 'notif-' . $notif->id,
                            'title' => $notifMeta['title'],
                            'message' => $message ?: $subject,
                            'meta' => null,
                            'icon' => $notifMeta['icon'],
                            'bg' => $notifMeta['bg'],
                            'color' => $notifMeta['color'],
                            'label' => $notifMeta['label'],
                            'time' => $time,
                            'unread' => $notifMeta['unread'],
                        ]);
                    }

                    $notificationItems = $notificationItems->sortByDesc('time')->values();
                    $unreadNotificationCount = $notificationItems->where('unread', true)->count();
                    $notificationItems = $notificationItems->take(15);
                @endphp
                <button @click="notificationsOpen = !notificationsOpen" class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" data-notification-toggle>
                    <i class="las la-bell text-lg"></i>
                    @if($unreadNotificationCount > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" data-notification-indicator></span>
                    @endif
                </button>
                <style>.top-116 {top: 5.5rem;}</style>
                <!-- Notifications Dropdown -->
                <div x-show="notificationsOpen" @click.away="notificationsOpen = false" x-transition class="fixed sm:absolute top-116 sm:top-full right-2 sm:right-0 mt-0 sm:mt-2 w-[calc(100vw-1rem)] sm:w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-[9999]">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Notifications')</h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <div class="divide-y divide-gray-100 dark:divide-gray-700" data-notification-feed>
                            @forelse($notificationItems as $notification)
                                <div class="relative px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors notification-feed-item" data-notification-id="{{ $notification['id'] }}">
                                    @if(!empty($notification['unread']))
                                        <span class="absolute top-3 right-3 w-2 h-2 bg-primary-500 rounded-full"></span>
                                    @endif
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 rounded-full {{ $notification['bg'] }} flex items-center justify-center flex-shrink-0">
                                            <i class="{{ $notification['icon'] }} {{ $notification['color'] }}"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start gap-2">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification['title'] }}</p>
                                            </div>
                                            @if(!empty($notification['message']))
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification['message'] }}</p>
                                            @endif
                                            @if(!empty($notification['meta']))
                                                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ $notification['meta'] }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $notification['time']->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center" data-notification-empty>
                                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                                        <i class="las la-bell-slash text-gray-400 dark:text-gray-500 text-xl"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@lang('No recent notifications')</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">@lang('New alerts about your rebates and accounts will appear here.')</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-center">
                        <span class="text-sm text-primary-600 dark:text-primary-400 font-medium opacity-80">@lang('All caught up!')</span>
                    </div>
                </div>
            </div>

            <!-- Language Switcher -->
            @if (gs('multi_language'))
                @php
                    $language = App\Models\Language::all();
                    $selectLang = $language->where('code', config('app.locale'))->first();
                    $currentLang = session('lang') ? $language->where('code', session('lang'))->first() : $language->where('is_default', 1)->first();
                @endphp

                @if ($language->count())
                    <div class="relative" x-data="{ languageOpen: false }">
                        <button @click="languageOpen = !languageOpen" class="flex items-center space-x-2 px-3 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <img src="{{ getImage(getFilePath('language') . '/' . $currentLang->image, getFileSize('language')) }}" alt="@lang('image')" class="w-5 h-5 rounded-full">
                            <span class="hidden sm:block text-sm font-medium">{{ __(@$selectLang->name) }}</span>
                            <i class="las la-chevron-down text-xs"></i>
                        </button>
                        
                        <!-- Language Dropdown -->
                        <div x-show="languageOpen" @click.away="languageOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                            @foreach ($language as $item)
                                <a href="{{ route('lang', $item->code) }}" class="flex items-center space-x-3 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ session('lang') == $item->code ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : '' }}">
                                    <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}" alt="@lang('image')" class="w-4 h-4 rounded-full">
                                    <span class="text-sm">{{ __($item->name) }}</span>
                                    @if (session('lang') == $item->code)
                                        <i class="las la-check text-primary-600 dark:text-primary-400 ml-auto"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
            <!-- Account Balance -->
            <div class="hidden sm:block">
                <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 border border-primary-200 dark:border-primary-700 px-4 py-2 rounded-lg">
                    <div class="text-sm font-bold text-primary-800 dark:text-primary-200">{{ gs()->cur_sym . showAmount(auth()->user()->balance, currencyFormat: false) }}</div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative" x-data="{ userMenuOpen: false }">
                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 px-3 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                        <span class="text-primary-600 dark:text-primary-400 font-semibold text-xs">{{ substr(auth()->user()->firstname, 0, 1) }}{{ substr(auth()->user()->lastname, 0, 1) }}</span>
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->firstname }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->username }}</div>
                    </div>
                    <i class="las la-chevron-down text-xs"></i>
                </button>

                <!-- User Dropdown -->
                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</div>
                        <div class="text-xs text-primary-600 dark:text-primary-400 mt-1">@lang('Account'): {{ auth()->user()->account_number }}</div>
                    </div>

                    <!-- Menu Items -->
                    <a href="{{ route('user.profile.setting') }}" class="flex items-center space-x-3 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="las la-user-cog text-gray-400 dark:text-gray-500"></i>
                        <span class="text-sm">@lang('Profile Settings')</span>
                    </a>
                    
                    <a href="{{ route('user.change.password') }}" class="flex items-center space-x-3 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="las la-key text-gray-400 dark:text-gray-500"></i>
                        <span class="text-sm">@lang('Change Password')</span>
                    </a>
                    
                    <a href="{{ route('user.twofactor') }}" class="flex items-center space-x-3 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="las la-shield-alt text-gray-400 dark:text-gray-500"></i>
                        <span class="text-sm">@lang('2FA Security')</span>
                    </a>

                    <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>
                    
                    <a href="{{ route('user.logout') }}" class="flex items-center space-x-3 px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <i class="las la-sign-out-alt text-red-500"></i>
                        <span class="text-sm">@lang('Logout')</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

@push('script')
<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        });
        const dateString = now.toLocaleDateString('en-US', {
            weekday: 'short',
            month: 'short',
            day: 'numeric'
        });
        var currentTimeElement = document.getElementById('current-time');
        if (!currentTimeElement) {
            return;
        }
        currentTimeElement.textContent = `${dateString}, ${timeString}`;
    }

    // Update time immediately and then every minute
    updateTime();
    setInterval(updateTime, 60000);
</script>
@endpush