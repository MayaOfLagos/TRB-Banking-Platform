@extends($activeTemplate.'layouts.master')
@section('content')

{{-- Hero Section --}}
<div class="min-h-screen py-8 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Welcome back'), {{ auth()->user()->fullname }}!</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Track your rebates, view available programs, and manage your tier benefits')</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Earned Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($stats['total_earned']) }} <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __($general->cur_text) }}</span></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Total Earned')</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <i class="las la-money-bill-wave text-2xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
                @if($stats['this_month'] > 0)
                    <div class="mt-4 flex items-center text-green-600 dark:text-green-400">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        <span class="text-sm font-medium">{{ showAmount($stats['this_month']) }} @lang('this month')</span>
                    </div>
                @endif
            </div>

            {{-- Pending Amount Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ showAmount($stats['pending_amount']) }} <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __($general->cur_text) }}</span></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Pending Amount')</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="las la-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
                @if($pendingRebatesCount > 0)
                    <div class="mt-4 flex items-center text-yellow-600 dark:text-yellow-400">
                        <span class="text-sm font-medium">{{ $pendingRebatesCount }} @lang('pending rebates')</span>
                    </div>
                @endif
            </div>

            {{-- Total Rebates Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_rebates'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Total Rebates')</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="las la-chart-line text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-blue-600 dark:text-blue-400">
                    <span class="text-sm font-medium">{{ $stats['approved_rebates'] }} @lang('approved')</span>
                </div>
            </div>

            {{-- Current Tier Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tierInfo['tier'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">@lang('Current Tier')</p>
                    </div>
                    @php
                        $tierColors = match($tierInfo['tier']) {
                            'Gold' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-600 dark:text-yellow-400'],
                            'Silver' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400'],
                            default => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-600 dark:text-green-400']
                        };
                        $tierIcon = match($tierInfo['tier']) {
                            'Gold' => 'crown',
                            'Silver' => 'medal',
                            default => 'trophy'
                        };
                    @endphp
                    <div class="w-12 h-12 {{ $tierColors['bg'] }} rounded-lg flex items-center justify-center">
                        <i class="las la-{{ $tierIcon }} text-2xl {{ $tierColors['text'] }}"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-purple-600 dark:text-purple-400">
                    <span class="text-sm font-medium">{{ $tierInfo['multiplier'] }}x @lang('Multiplier')</span>
                </div>
            </div>
        </div>

        {{-- Tier Progress Section --}}
        @if($tierProgress['next_tier'])
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 mb-8">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">@lang('Tier Progression')</h2>
                <p class="text-gray-600 dark:text-gray-400">@lang('Progress to') {{ $tierProgress['next_tier'] }} @lang('tier')</p>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-purple-600 dark:text-purple-400">{{ $tierProgress['current_tier'] }}</span>
                    <span class="font-bold text-green-600 dark:text-green-400">{{ $tierProgress['next_tier'] }}</span>
                </div>
                
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 h-3 rounded-full transition-all duration-300" style="width: {{ $tierProgress['progress_percentage'] }}%"></div>
                </div>
                
                <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ showAmount($tierProgress['total_earned']) }} {{ $general->cur_text }}</span>
                    <span>{{ showAmount($tierProgress['next_threshold']) }} {{ $general->cur_text }}</span>
                </div>
                
                @if($tierProgress['amount_to_next'] > 0)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 flex items-center">
                        <i class="las la-info-circle text-blue-600 dark:text-blue-400 text-xl mr-3"></i>
                        <div>
                            <span class="text-blue-800 dark:text-blue-300">@lang('Earn') <strong>{{ showAmount($tierProgress['amount_to_next']) }} {{ $general->cur_text }}</strong> @lang('more to reach') <strong>{{ $tierProgress['next_tier'] }}</strong> @lang('tier')</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Recent Rebates --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Recent Rebates')</h2>
                        <a href="{{ route('user.rebate.history') }}" class="px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-800 transition-colors duration-200 text-sm font-medium">@lang('View All')</a>
                    </div>
                    <div class="p-6">
                        @if($recentRebates->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-600">
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">@lang('Program')</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">@lang('Amount')</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">@lang('Status')</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">@lang('Date')</th>
                                            <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
                                        @foreach($recentRebates as $rebate)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                                <td class="py-4 px-4">
                                                    <div>
                                                        <h6 class="font-semibold text-gray-900 dark:text-white text-sm">{{ __($rebate->rebateCategory?->program?->name ?? 'Unknown Program') }}</h6>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __($rebate->rebateCategory?->name ?? 'Uncategorized') }}</p>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <span class="font-bold text-purple-600 dark:text-purple-400">{{ showAmount($rebate->final_amount) }} {{ $general->cur_text }}</span>
                                                    @if($rebate->tier_multiplier > 1)
                                                        <br><small class="text-green-600 dark:text-green-400 text-xs flex items-center mt-1"><i class="las la-star mr-1"></i> {{ $rebate->tier_multiplier }}x @lang('bonus')</small>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-4">
                                                    @php
                                                        $statusConfig = match($rebate->status) {
                                                            'processed' => ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-800 dark:text-green-300', 'label' => 'Processed'],
                                                            'pending' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-800 dark:text-yellow-300', 'label' => 'Pending'],
                                                            'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900', 'text' => 'text-red-800 dark:text-red-300', 'label' => 'Rejected'],
                                                            'failed' => ['bg' => 'bg-red-100 dark:bg-red-900', 'text' => 'text-red-800 dark:text-red-300', 'label' => 'Failed'],
                                                            default => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-800 dark:text-gray-300', 'label' => ucfirst($rebate->status)]
                                                        };
                                                    @endphp
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                                        {{ __($statusConfig['label']) }}
                                                    </span>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <div class="text-sm text-gray-900 dark:text-white">{{ showDateTime($rebate->created_at, 'd M Y') }}</div>
                                                    <small class="text-xs text-gray-500 dark:text-gray-400">{{ diffForHumans($rebate->created_at) }}</small>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <a href="{{ route('user.rebate.show', $rebate->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-800 transition-colors duration-200">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="las la-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('No Rebates Yet')</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('Start earning rebates by participating in available programs')</p>
                                <a href="{{ route('user.rebate.programs') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 font-medium">
                                    <i class="las la-plus mr-2"></i> @lang('Browse Programs')
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Available Programs --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Featured Programs')</h2>
                        <a href="{{ route('user.rebate.programs') }}" class="px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-800 transition-colors duration-200 text-sm font-medium">@lang('View All')</a>
                    </div>
                    <div class="p-6">
                        @if($availablePrograms->count() > 0)
                            <div class="space-y-4">
                                @foreach($availablePrograms as $program)
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all duration-200">
                                        <div class="flex justify-between items-start mb-3">
                                            <h6 class="font-semibold text-gray-900 dark:text-white text-sm flex-1 mr-2">{{ __($program->name) }}</h6>
                                            <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-md text-xs font-medium">{{ __($program->rebateCategory?->name ?? 'Uncategorized') }}</span>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">{{ Str::limit(__($program->description), 60) }}</p>
                                        <div class="flex items-center justify-between mb-4">
                                            @if($program->rate_type == 'percentage')
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-md text-sm font-bold">{{ $program->rate_value }}%</span>
                                                    @if($program->max_amount)
                                                        <small class="text-gray-500 dark:text-gray-400 text-xs">@lang('up to') {{ showAmount($program->max_amount) }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded-md text-sm font-bold">{{ showAmount($program->rate_value) }} {{ $general->cur_text }}</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('user.product.upload', $program->id) }}" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-2 px-4 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 text-sm font-medium flex items-center justify-center">
                                            <i class="las la-upload mr-2"></i> @lang('Upload Receipt')
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="las la-exclamation-triangle text-yellow-500 dark:text-yellow-400 text-4xl mb-3"></i>
                                <p class="text-gray-600 dark:text-gray-400">@lang('No programs available at the moment')</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 mt-8">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">@lang('Quick Actions')</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('user.rebate.programs') }}" class="group block p-6 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-800 transition-colors duration-200">
                            <i class="las la-list text-2xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Browse Programs')</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Discover available rebate programs')</p>
                    </a>
                    
                    <a href="{{ route('user.product.upload') }}" class="group block p-6 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-200 dark:group-hover:bg-green-800 transition-colors duration-200">
                            <i class="las la-upload text-2xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Upload Receipt')</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">@lang('Submit purchase receipt for rebate')</p>
                    </a>
                    
                    <a href="{{ route('user.rebate.history') }}" class="group block p-6 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-800 transition-colors duration-200">
                            <i class="las la-history text-2xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Rebate History')</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">@lang('View all your rebate transactions')</p>
                    </a>
                    
                    <a href="{{ route('user.rebate.tiers') }}" class="group block p-6 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-yellow-300 dark:hover:border-yellow-600 hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mb-4 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-800 transition-colors duration-200">
                            <i class="las la-trophy text-2xl text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('Tier Benefits')</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">@lang('View your tier status and benefits')</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    'use strict';
    (function($) {
        // Auto-refresh stats every 5 minutes
        setInterval(function() {
            if (!document.hidden) {
                // You could add AJAX refresh logic here
                console.log('Auto-refresh check - dashboard is visible');
            }
        }, 300000);
        
        // Add smooth scroll behavior for any anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    })(jQuery);
</script>
@endpush