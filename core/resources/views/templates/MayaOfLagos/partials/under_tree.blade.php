<ul @if ($isFirst) class="firstList" @endif>
    @foreach ($user->allReferees as $under)
        @if ($loop->first)
            @php $layer++ @endphp
        @endif
        <li>
            <div class="tree-node-wrapper">
                <div class="tree-node">
                    <div class="tree-node-content">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                    <i class="las la-user text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $under->username }}</span>
                                        @if ($under->status)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                @lang('Active')
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                                @lang('Inactive')
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span class="flex items-center">
                                            <i class="las la-calendar mr-1"></i>
                                            @lang('Joined') {{ $under->created_at->format('M d, Y') }}
                                        </span>
                                        @if ($under->allReferees->count() > 0)
                                            <span class="flex items-center">
                                                <i class="las la-users mr-1"></i>
                                                {{ $under->allReferees->count() }} @lang('referrals')
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <!-- User Level Indicator -->
                                <div class="flex items-center">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mr-1">@lang('L'){{ $layer }}</span>
                                    <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">{{ $layer }}</span>
                                    </div>
                                </div>
                                
                                <!-- Activity Indicator -->
                                <div class="flex items-center">
                                    @php
                                        $lastActivity = $under->updated_at;
                                        $isRecentlyActive = $lastActivity->diffInDays(now()) <= 7;
                                    @endphp
                                    <div class="w-3 h-3 rounded-full {{ $isRecentlyActive ? 'bg-green-400' : 'bg-gray-300' }}" 
                                         title="{{ $isRecentlyActive ? __('Active this week') : __('Last active') . ' ' . $lastActivity->diffForHumans() }}"></div>
                                </div>
                                
                                <!-- Balance Display (if has balance) -->
                                @if ($under->balance > 0)
                                    <div class="flex items-center text-xs">
                                        <i class="las la-wallet text-yellow-500 mr-1"></i>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ showAmount($under->balance) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if ($under->allReferees->count() > 0 && $layer < $maxLevel)
                @include($activeTemplate . 'partials.under_tree', ['user' => $under, 'layer' => $layer, 'isFirst' => false])
            @endif
        </li>
    @endforeach
</ul>