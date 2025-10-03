@extends($activeTemplate.'layouts.master')
@section('content')

<div class="dashboard-inner bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="mb-4">
        <h3 class="mb-2 text-gray-900 dark:text-white">@lang('Tier Benefits & Progress')</h3>
        <p class="text-gray-600 dark:text-gray-400">@lang('Advance through tiers to unlock better rewards and exclusive benefits')</p>
    </div>

    {{-- Current Tier Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <div class="lg:col-span-8">
            <div class="dashboard-widget bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg">
                <div class="dashboard-widget__header border-b border-gray-200 dark:border-gray-700 p-6">
                    <h5 class="dashboard-widget__title text-gray-900 dark:text-white">@lang('Your Current Status')</h5>
                </div>
                <div class="dashboard-widget__body p-6">
                    <div class="tier-status-card">
                        <div class="tier-status-header">
                            <div class="tier-badge tier-badge--{{ strtolower($tierInfo['tier']) }}">
                                <i class="las la-{{ $tierInfo['tier'] == 'Platinum' ? 'crown' : ($tierInfo['tier'] == 'Gold' ? 'medal' : ($tierInfo['tier'] == 'Silver' ? 'award' : 'trophy')) }}"></i>
                                <span class="tier-name">{{ $tierInfo['tier'] }}</span>
                            </div>
                            <div class="tier-multiplier">
                                <span class="multiplier-value">{{ $tierInfo['multiplier'] }}x</span>
                                <span class="multiplier-label">@lang('Multiplier')</span>
                            </div>
                        </div>

                        <div class="tier-stats">
                            <div class="tier-stat">
                                <div class="tier-stat__value">{{ showUserAmount($tierInfo['total_earned'], auth()->user()) }}</div>
                                <div class="tier-stat__label">@lang('Total Earned')</div>
                            </div>
                            <div class="tier-stat">
                                <div class="tier-stat__value">{{ count($achievements) }}</div>
                                <div class="tier-stat__label">@lang('Achievements')</div>
                            </div>
                        </div>

                        @if($tierProgress['next_tier'])
                            <div class="tier-progress-section">
                                <h6 class="tier-progress-title">
                                    @lang('Progress to') {{ $tierProgress['next_tier'] }}
                                </h6>
                                
                                <div class="tier-progress-bar">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: {{ $tierProgress['progress_percentage'] }}%"></div>
                                    </div>
                                    <div class="progress-labels">
                                        <span>{{ showUserAmount($tierProgress['current_threshold'], auth()->user()) }}</span>
                                        <span>{{ showUserAmount($tierProgress['next_threshold'], auth()->user()) }}</span>
                                    </div>
                                </div>

                                <div class="tier-progress-info">
                                    <div class="progress-amount">
                                        <strong>{{ showUserAmount($tierProgress['amount_to_next'], auth()->user()) }}</strong> @lang('more to advance')
                                    </div>
                                    <div class="progress-percentage">
                                        {{ number_format($tierProgress['progress_percentage'], 1) }}% @lang('complete')
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="tier-maxed">
                                <i class="las la-star text-yellow-500 dark:text-yellow-400"></i>
                                <span>@lang('Congratulations! You\'ve reached the highest tier!')</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="dashboard-widget bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg">
                <div class="dashboard-widget__header border-b border-gray-200 dark:border-gray-700 p-6">
                    <h5 class="dashboard-widget__title text-gray-900 dark:text-white">@lang('Quick Stats')</h5>
                </div>
                <div class="dashboard-widget__body p-6">
                    <div class="quick-stats">
                        <div class="quick-stat">
                            <div class="quick-stat__icon quick-stat__icon--primary">
                                <i class="las la-calendar-week"></i>
                            </div>
                            <div class="quick-stat__content">
                                <div class="quick-stat__value">
                                    @php
                                        $thisMonth = \App\Models\RebateTransaction::where('user_id', auth()->id())
                                            ->where('status', 'approved')
                                            ->where('created_at', '>=', now()->startOfMonth())
                                            ->sum('rebate_amount');
                                    @endphp
                                    {{ showUserAmount($thisMonth, auth()->user()) }}
                                </div>
                                <div class="quick-stat__label">@lang('This Month')</div>
                            </div>
                        </div>

                        <div class="quick-stat">
                            <div class="quick-stat__icon quick-stat__icon--success">
                                <i class="las la-chart-line"></i>
                            </div>
                            <div class="quick-stat__content">
                                <div class="quick-stat__value">
                                    @php
                                        $avgMonthly = \App\Models\RebateTransaction::where('user_id', auth()->id())
                                            ->where('status', 'approved')
                                            ->where('created_at', '>=', now()->subMonths(3))
                                            ->sum('rebate_amount') / 3;
                                    @endphp
                                    {{ showUserAmount($avgMonthly, auth()->user()) }}
                                </div>
                                <div class="quick-stat__label">@lang('Avg. Monthly')</div>
                            </div>
                        </div>

                        <div class="quick-stat">
                            <div class="quick-stat__icon quick-stat__icon--info">
                                <i class="las la-fire"></i>
                            </div>
                            <div class="quick-stat__content">
                                <div class="quick-stat__value">
                                    @php
                                        $streak = 0;
                                        $currentDate = now();
                                        for($i = 0; $i < 30; $i++) {
                                            $hasRebate = \App\Models\RebateTransaction::where('user_id', auth()->id())
                                                ->whereDate('created_at', $currentDate->copy()->subDays($i))
                                                ->exists();
                                            if($hasRebate) {
                                                $streak++;
                                            } else {
                                                break;
                                            }
                                        }
                                    @endphp
                                    {{ $streak }}
                                </div>
                                <div class="quick-stat__label">@lang('Day Streak')</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tier Benefits Comparison --}}
    <div class="dashboard-widget mb-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg">
        <div class="dashboard-widget__header border-b border-gray-200 dark:border-gray-700 p-6">
            <h5 class="dashboard-widget__title text-gray-900 dark:text-white">@lang('All Tier Benefits')</h5>
        </div>
        <div class="dashboard-widget__body p-6">
            <div class="tier-comparison">
                @foreach($tierBenefits as $tierName => $benefits)
                    <div class="tier-column {{ strtolower($tierName) == strtolower($tierInfo['tier']) ? 'tier-column--current' : '' }}">
                        <div class="tier-header tier-header--{{ strtolower($tierName) }}">
                            <div class="tier-icon">
                                <i class="las la-{{ $tierName == 'Platinum' ? 'crown' : ($tierName == 'Gold' ? 'medal' : ($tierName == 'Silver' ? 'award' : 'trophy')) }}"></i>
                            </div>
                            <h6 class="tier-title">{{ $tierName }}</h6>
                            <div class="tier-multiplier-badge">{{ $benefits['multiplier'] }}x</div>
                            @if(strtolower($tierName) == strtolower($tierInfo['tier']))
                                <div class="current-tier-label">@lang('Current')</div>
                            @endif
                        </div>

                        <div class="tier-requirements">
                            @php
                                $requirements = [
                                    'Bronze' => 0,
                                    'Silver' => 1000,
                                    'Gold' => 5000,
                                    'Platinum' => 15000
                                ];
                            @endphp
                            @if($requirements[$tierName] > 0)
                                <div class="requirement">
                                    @lang('Requires'): {{ showUserAmount($requirements[$tierName], auth()->user()) }}
                                </div>
                            @else
                                <div class="requirement">@lang('Starting Tier')</div>
                            @endif
                        </div>

                        <div class="tier-benefits-list">
                            @foreach($benefits['benefits'] as $benefit)
                                <div class="benefit-item">
                                    <i class="las la-check text-green-600 dark:text-green-400"></i>
                                    <span>{{ $benefit }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Achievements --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8">
            <div class="dashboard-widget bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg">
                <div class="dashboard-widget__header border-b border-gray-200 dark:border-gray-700 p-6">
                    <h5 class="dashboard-widget__title text-gray-900 dark:text-white">@lang('Your Achievements')</h5>
                </div>
                <div class="dashboard-widget__body p-6">
                    @if(count($achievements) > 0)
                        <div class="achievements-grid">
                            @foreach($achievements as $achievement)
                                <div class="achievement-card achievement-card--unlocked">
                                    <div class="achievement-icon">
                                        <i class="las la-{{ $achievement['icon'] }}"></i>
                                    </div>
                                    <div class="achievement-content">
                                        <h6 class="achievement-title">{{ $achievement['title'] }}</h6>
                                        <p class="achievement-date">{{ $achievement['date'] }}</p>
                                    </div>
                                    <div class="achievement-badge">
                                        <i class="las la-check"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-achievements">
                            <i class="las la-trophy text-gray-400 dark:text-gray-600" style="font-size: 3rem;"></i>
                            <h6 class="text-gray-600 dark:text-gray-400 mt-3">@lang('No Achievements Yet')</h6>
                            <p class="text-gray-600 dark:text-gray-400">@lang('Start earning rebates to unlock achievements!')</p>
                            <a href="{{ route('user.rebate.programs') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium rounded-lg transition-colors duration-200 text-sm">
                                @lang('Browse Programs')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            {{-- Tier Tips --}}
            <div class="dashboard-widget bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg">
                <div class="dashboard-widget__header border-b border-gray-200 dark:border-gray-700 p-6">
                    <h5 class="dashboard-widget__title text-gray-900 dark:text-white">@lang('Advancement Tips')</h5>
                </div>
                <div class="dashboard-widget__body p-6">
                    <div class="tips-list">
                        <div class="tip-card">
                            <div class="tip-icon tip-icon--primary">
                                <i class="las la-upload"></i>
                            </div>
                            <div class="tip-content">
                                <h6 class="tip-title">@lang('Regular Uploads')</h6>
                                <p class="tip-description">@lang('Upload receipts regularly to maximize earnings')</p>
                            </div>
                        </div>

                        <div class="tip-card">
                            <div class="tip-icon tip-icon--success">
                                <i class="las la-percentage"></i>
                            </div>
                            <div class="tip-content">
                                <h6 class="tip-title">@lang('High-Rate Programs')</h6>
                                <p class="tip-description">@lang('Focus on programs with higher rebate rates')</p>
                            </div>
                        </div>

                        <div class="tip-card">
                            <div class="tip-icon tip-icon--info">
                                <i class="las la-users"></i>
                            </div>
                            <div class="tip-content">
                                <h6 class="tip-title">@lang('Refer Friends')</h6>
                                <p class="tip-description">@lang('Earn bonus rebates through referrals')</p>
                            </div>
                        </div>

                        <div class="tip-card">
                            <div class="tip-icon tip-icon--warning">
                                <i class="las la-clock"></i>
                            </div>
                            <div class="tip-content">
                                <h6 class="tip-title">@lang('Stay Active')</h6>
                                <p class="tip-description">@lang('Maintain consistent activity for bonuses')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Styles --}}
<style>
/* Tier Status Card */
.tier-status-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
}

@media (prefers-color-scheme: dark) {
    .dark .tier-status-card {
        background: linear-gradient(135deg, #4c63d2 0%, #6366f1 100%);
    }
}

.tier-status-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.tier-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50px;
    backdrop-filter: blur(10px);
}

.tier-badge i {
    font-size: 1.5rem;
}

.tier-name {
    font-size: 1.25rem;
    font-weight: 600;
}

.tier-multiplier {
    text-align: right;
}

.multiplier-value {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.multiplier-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.tier-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.tier-stat__value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.tier-stat__label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.tier-progress-section {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
}

.tier-progress-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    text-align: center;
}

.tier-progress-bar {
    margin-bottom: 1rem;
}

.progress-track {
    height: 8px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 4px;
    transition: width 0.5s ease;
}

.progress-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    opacity: 0.8;
}

.tier-progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tier-maxed {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 193, 7, 0.2);
    border-radius: 8px;
}

.tier-maxed i {
    font-size: 1.5rem;
    margin-right: 0.5rem;
}

/* Quick Stats */
.quick-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.quick-stat {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

@media (prefers-color-scheme: dark) {
    .dark .quick-stat {
        background: #374151;
    }
}

.quick-stat__icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.quick-stat__icon--primary {
    background: rgba(123, 104, 238, 0.1);
    color: hsl(var(--primary));
}

.quick-stat__icon--success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.quick-stat__icon--info {
    background: rgba(23, 162, 184, 0.1);
    color: #17a2b8;
}

.quick-stat__value {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.quick-stat__label {
    font-size: 0.9rem;
    color: #666;
}

@media (prefers-color-scheme: dark) {
    .dark .quick-stat__value {
        color: #f9fafb;
    }
    
    .dark .quick-stat__label {
        color: #d1d5db;
    }
}

/* Tier Comparison */
.tier-comparison {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.tier-column {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

@media (prefers-color-scheme: dark) {
    .dark .tier-column {
        border-color: #4b5563;
        background: #1f2937;
    }
}

.tier-column--current {
    border-color: hsl(var(--primary));
    box-shadow: 0 8px 25px rgba(123, 104, 238, 0.15);
    transform: scale(1.02);
}

.tier-header {
    padding: 1.5rem;
    text-align: center;
    color: white;
    position: relative;
}

.tier-header--bronze {
    background: linear-gradient(135deg, #CD7F32 0%, #A0522D 100%);
}

.tier-header--silver {
    background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%);
}

.tier-header--gold {
    background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
}

.tier-header--platinum {
    background: linear-gradient(135deg, #E5E4E2 0%, #BCC6CC 100%);
}

.tier-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.tier-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.tier-multiplier-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.current-tier-label {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    padding: 0.25rem 0.75rem;
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.tier-requirements {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    text-align: center;
}

.requirement {
    font-size: 0.9rem;
    color: #666;
    font-weight: 500;
}

@media (prefers-color-scheme: dark) {
    .dark .tier-requirements {
        background: #374151;
    }
    
    .dark .requirement {
        color: #d1d5db;
    }
}

.tier-benefits-list {
    padding: 1.5rem;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    line-height: 1.4;
    color: #374151;
}

@media (prefers-color-scheme: dark) {
    .dark .benefit-item {
        color: #d1d5db;
    }
}

.benefit-item:last-child {
    margin-bottom: 0;
}

.benefit-item i {
    color: #28a745;
    margin-top: 0.1rem;
}

/* Achievements */
.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

.achievement-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    background: #fff;
    position: relative;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

@media (prefers-color-scheme: dark) {
    .dark .achievement-card {
        background: #1f2937;
        border-color: #4b5563;
    }
}

.achievement-card--unlocked {
    border-color: #28a745;
    background: #f8fff9;
}

@media (prefers-color-scheme: dark) {
    .dark .achievement-card--unlocked {
        background: #065f46;
        border-color: #10b981;
    }
}

.achievement-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.achievement-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #374151;
}

.achievement-date {
    font-size: 0.8rem;
    color: #666;
    margin: 0;
}

@media (prefers-color-scheme: dark) {
    .dark .achievement-title {
        color: #f9fafb;
    }
    
    .dark .achievement-date {
        color: #9ca3af;
    }
}

.achievement-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    background: #28a745;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.empty-achievements {
    text-align: center;
    padding: 3rem 1rem;
}

/* Tips */
.tips-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.tip-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

@media (prefers-color-scheme: dark) {
    .dark .tip-card {
        background: #374151;
    }
}

.tip-icon {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.tip-icon--primary {
    background: rgba(123, 104, 238, 0.1);
    color: hsl(var(--primary));
}

.tip-icon--success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.tip-icon--info {
    background: rgba(23, 162, 184, 0.1);
    color: #17a2b8;
}

.tip-icon--warning {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.tip-title {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #374151;
}

.tip-description {
    font-size: 0.85rem;
    color: #666;
    margin: 0;
    line-height: 1.4;
}

@media (prefers-color-scheme: dark) {
    .dark .tip-title {
        color: #f9fafb;
    }
    
    .dark .tip-description {
        color: #d1d5db;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .tier-status-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .tier-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
        text-align: center;
    }
    
    .tier-comparison {
        grid-template-columns: 1fr;
    }
    
    .tier-column--current {
        transform: none;
    }
    
    .achievements-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection

@push('script')
<script>
'use strict';
(function($) {
    
    // Animate progress bar on load
    $(document).ready(function() {
        $('.progress-fill').each(function() {
            const width = $(this).attr('style').match(/width:\s*(\d+(?:\.\d+)?)%/);
            if (width) {
                $(this).css('width', '0%').animate({
                    width: width[0]
                }, 2000);
            }
        });
    });

    // Add some interactivity to achievement cards
    $('.achievement-card--unlocked').hover(
        function() {
            $(this).css('transform', 'scale(1.02)');
        },
        function() {
            $(this).css('transform', 'scale(1)');
        }
    );

})(jQuery);
</script>
@endpush