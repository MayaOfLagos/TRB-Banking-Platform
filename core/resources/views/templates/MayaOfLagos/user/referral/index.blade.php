@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- Referral System Page -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">@lang('Referral Program')</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">@lang('Invite friends and earn rewards from their activities')</p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button onclick="copyReferralLink()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="las la-copy mr-2 text-lg"></i>
                        @lang('Copy Link')
                    </button>
                    <button onclick="shareReferralLink()" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="las la-share-alt mr-2 text-lg"></i>
                        @lang('Share')
                    </button>
                </div>
            </div>
        </div>

        <!-- Referral Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Referrals -->
            <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-users text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Total Referrals')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->allReferees->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Referrals -->
            <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-user-check text-green-600 dark:text-green-400 text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Active Users')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->allReferees->where('status', 1)->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Commission Earned -->
            <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-coins text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Total Earned')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($user->transactions()->where('remark', 'referral_commission')->sum('amount')) }}</p>
                    </div>
                </div>
            </div>

            <!-- Max Level -->
            <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                            <i class="las la-layer-group text-purple-600 dark:text-purple-400 text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('Max Levels')</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maxLevel }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Referral Link Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 lg:px-8 py-6 lg:py-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="las la-link text-blue-500 mr-2 text-xl"></i>
                        @lang('Your Referral Link')
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Referral Link Display -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Share this link')</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       id="referralLinkInput"
                                       value="{{ route('home') . '?reference=' . $user->username }}" 
                                       readonly
                                       class="flex-1 px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <button onclick="copyReferralLink()" 
                                        class="px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg transition-colors">
                                    <i class="las la-copy text-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Referrer Info -->
                        @if ($user->referrer)
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mr-3">
                                    <i class="las la-user-tie text-amber-600 dark:text-amber-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-amber-800 dark:text-amber-300">@lang('You were referred by')</p>
                                    <p class="text-lg font-bold text-amber-900 dark:text-amber-200">{{ $user->referrer->username }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Share Options -->
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">@lang('Share via')</p>
                            <div class="flex space-x-2">
                                <button onclick="shareToWhatsApp()" 
                                        class="flex-1 flex items-center justify-center px-3 py-2 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg transition-colors text-sm font-medium">
                                    <i class="lab la-whatsapp mr-1"></i>
                                    WhatsApp
                                </button>
                                <button onclick="shareToTelegram()" 
                                        class="flex-1 flex items-center justify-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg transition-colors text-sm font-medium">
                                    <i class="lab la-telegram mr-1"></i>
                                    Telegram
                                </button>
                                <button onclick="shareToEmail()" 
                                        class="flex-1 flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors text-sm font-medium">
                                    <i class="las la-envelope mr-1"></i>
                                    Email
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Referrals -->
            <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 lg:px-8 py-6 lg:py-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="las la-clock text-green-500 mr-2 text-xl"></i>
                        @lang('Recent Referrals')
                    </h3>
                    
                    <div class="space-y-3">
                        @forelse ($user->allReferees->take(5) as $referee)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                    <i class="las la-user text-blue-600 dark:text-blue-400 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $referee->username }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $referee->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $referee->status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                    {{ $referee->status ? __('Active') : __('Inactive') }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <i class="las la-users text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('No referrals yet')</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Tree -->
        <div class="mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 lg:px-8 py-6 lg:py-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="las la-sitemap text-purple-500 mr-2 text-xl"></i>
                        @lang('Referral Tree')
                    </h3>
                    
                    <div class="treeview-container bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <ul class="treeview">
                            @if ($user->allReferees->count() > 0 && $maxLevel > 0)
                                <li class="items-expanded">
                                    <div class="tree-node-wrapper">
                                        <div class="tree-node tree-node-root">
                                            <div class="tree-node-content">
                                                <i class="las la-user-crown text-yellow-500 mr-2"></i>
                                                <span class="font-semibold text-gray-900 dark:text-white">{{ $user->username }}</span>
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">(@lang('You'))</span>
                                            </div>
                                        </div>
                                    </div>
                                    @include($activeTemplate . 'partials.under_tree', ['user' => $user, 'layer' => 0, 'isFirst' => true])
                                </li>
                            @else
                                <li class="items-expanded">
                                    <div class="text-center py-8">
                                        <i class="las la-info-circle text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-500 dark:text-gray-400">@lang('No referrals found. Start sharing your referral link to build your network!')</p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- How It Works Section -->
        <div class="mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 lg:px-8 py-6 lg:py-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="las la-question-circle text-blue-500 mr-2 text-xl"></i>
                        @lang('How Referral Program Works')
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="las la-share-alt text-blue-600 dark:text-blue-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('1. Share Your Link')</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Share your unique referral link with friends and family through social media, email, or messaging apps.')</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="las la-user-plus text-green-600 dark:text-green-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('2. Friends Join')</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('When someone clicks your link and registers, they become your referral and you start earning from their activities.')</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="las la-coins text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('3. Earn Rewards')</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Earn commission from your referrals\' deposits, transfers, and other qualifying activities automatically.')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/tree-view.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/tree-view.js') }}"></script>
@endpush

@push('style')
<style>
/* Enhanced Tree View Styling */
.treeview-container {
    max-height: 600px;
    overflow-y: auto;
}

.treeview {
    font-family: inherit;
}

.tree-node-wrapper {
    position: relative;
    margin: 8px 0;
}

.tree-node {
    @apply bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-lg px-4 py-3 shadow-sm hover:shadow-md transition-all duration-200;
}

.tree-node-root {
    @apply bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border-blue-200 dark:border-blue-700;
}

.tree-node-content {
    @apply flex items-center text-sm font-medium text-gray-900 dark:text-white;
}

.treeview ul {
    margin-left: 20px;
    border-left: 2px dashed #e5e7eb;
}

.dark .treeview ul {
    border-left-color: #4b5563;
}

.treeview li {
    position: relative;
    list-style: none;
    margin: 8px 0;
}

.treeview li::before {
    content: '';
    position: absolute;
    top: 20px;
    left: -20px;
    width: 18px;
    height: 2px;
    background: #e5e7eb;
}

.dark .treeview li::before {
    background: #4b5563;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .treeview-container {
        max-height: 400px;
    }
    
    .tree-node {
        @apply px-3 py-2;
    }
    
    .tree-node-content {
        @apply text-xs;
    }
}

/* Smooth animations */
.tree-node {
    animation: slideInLeft 0.3s ease-out;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Custom scrollbar */
.treeview-container::-webkit-scrollbar {
    width: 6px;
}

.treeview-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.treeview-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.treeview-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .treeview-container::-webkit-scrollbar-track {
    background: #374151;
}

.dark .treeview-container::-webkit-scrollbar-thumb {
    background: #6b7280;
}
</style>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    
    // Initialize tree view
    $('.treeview').treeView();
    
    // Referral link functions
    window.copyReferralLink = function() {
        const referralInput = document.getElementById('referralLinkInput');
        referralInput.select();
        referralInput.setSelectionRange(0, 99999); // For mobile devices
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(referralInput.value).then(function() {
                notify('success', '@lang('Referral link copied to clipboard!')');
            }).catch(function() {
                // Fallback for older browsers
                document.execCommand('copy');
                notify('success', '@lang('Referral link copied to clipboard!')');
            });
        } else {
            // Fallback for older browsers
            document.execCommand('copy');
            notify('success', '@lang('Referral link copied to clipboard!')');
        }
    };
    
    // Share functions
    window.shareReferralLink = function() {
        const referralLink = document.getElementById('referralLinkInput').value;
        const shareText = '@lang('Join me on') {{ gs()->site_name }} @lang('and start earning together!')';
        
        if (navigator.share) {
            navigator.share({
                title: '{{ gs()->site_name }} @lang('Referral')',
                text: shareText,
                url: referralLink
            }).catch(function(error) {
                console.log('Error sharing:', error);
            });
        } else {
            // Fallback - copy to clipboard
            copyReferralLink();
        }
    };
    
    window.shareToWhatsApp = function() {
        const referralLink = document.getElementById('referralLinkInput').value;
        const message = encodeURIComponent(`🚀 @lang('Join me on') {{ gs()->site_name }} @lang('and start earning together!') ${referralLink}`);
        window.open(`https://wa.me/?text=${message}`, '_blank');
    };
    
    window.shareToTelegram = function() {
        const referralLink = document.getElementById('referralLinkInput').value;
        const message = encodeURIComponent(`🚀 @lang('Join me on') {{ gs()->site_name }} @lang('and start earning together!') ${referralLink}`);
        window.open(`https://t.me/share/url?url=${encodeURIComponent(referralLink)}&text=${message}`, '_blank');
    };
    
    window.shareToEmail = function() {
        const referralLink = document.getElementById('referralLinkInput').value;
        const subject = encodeURIComponent(`@lang('Join') {{ gs()->site_name }} @lang('with my referral link')`);
        const body = encodeURIComponent(`@lang('Hi!') \n\n@lang('I wanted to share') {{ gs()->site_name }} @lang('with you. It\'s a great platform and I think you\'ll love it!') \n\n@lang('Use my referral link to get started:')\n${referralLink}\n\n@lang('Looking forward to having you on board!')`);
        window.open(`mailto:?subject=${subject}&body=${body}`);
    };
    
    // Add copy button animation
    $(document).on('click', '[onclick="copyReferralLink()"]', function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        $btn.html('<i class="las la-check mr-2 text-lg"></i>@lang('Copied!')');
        
        setTimeout(function() {
            $btn.html(originalHtml);
        }, 2000);
    });
    
    // Add hover effects to statistics cards
    $('.bg-white.dark\\:bg-gray-800').hover(
        function() {
            $(this).addClass('transform scale-105 shadow-lg');
        },
        function() {
            $(this).removeClass('transform scale-105 shadow-lg');
        }
    );
    
})(jQuery);
</script>
@endpush