@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- Support Tickets Page -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">@lang('Support Tickets')</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">@lang('Manage your support requests and get help from our team')</p>
                </div>
                
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('ticket.open') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="las la-plus mr-2 text-lg"></i>
                        @lang('Open New Ticket')
                    </a>
                </div>
            </div>
        </div>

        <!-- Support Tickets Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Ticket ID')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Subject')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Status')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Priority')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Last Reply')</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($supports as $support)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-mono font-medium text-gray-900 dark:text-white">#{{ $support->ticket }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ strLimit($support->subject, 40) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="transform-bootstrap-badge">
                                    @php echo $support->statusBadge; @endphp
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="transform-bootstrap-badge">
                                    @php echo $support->priorityBadge; @endphp
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ diffForHumans($support->last_reply) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('ticket.view', $support->ticket) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg transition-colors">
                                    <i class="las la-eye mr-1"></i>
                                    @lang('View')
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-6 py-12 text-center text-gray-500 dark:text-gray-400" colspan="6">
                                <div class="flex flex-col items-center">
                                    <i class="las la-headset text-4xl mb-4 text-gray-400"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No Support Tickets')</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __($emptyMessage) }}</p>
                                    <a href="{{ route('ticket.open') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                        <i class="las la-plus mr-2"></i>
                                        @lang('Create Your First Ticket')
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($supports as $support)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-mono font-medium text-gray-900 dark:text-white">#{{ $support->ticket }}</span>
                                <div class="transform-bootstrap-badge">
                                    @php echo $support->statusBadge; @endphp
                                </div>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ strLimit($support->subject, 30) }}</h3>
                            <div class="flex items-center space-x-2">
                                <div class="transform-bootstrap-badge">
                                    @php echo $support->priorityBadge; @endphp
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ diffForHumans($support->last_reply) }}</span>
                            </div>
                        </div>
                        
                        <div class="ml-4">
                            <a href="{{ route('ticket.view', $support->ticket) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg transition-colors">
                                <i class="las la-eye mr-1"></i>
                                @lang('View')
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="flex flex-col items-center">
                        <i class="las la-headset text-4xl mb-4 text-gray-400"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No Support Tickets')</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __($emptyMessage) }}</p>
                        <a href="{{ route('ticket.open') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <i class="las la-plus mr-2"></i>
                            @lang('Create Your First Ticket')
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($supports->hasPages())
            <div class="px-4 lg:px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                {{ paginateLinks($supports) }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
/* Transform Bootstrap badges to Tailwind equivalent */
.transform-bootstrap-badge .badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.transform-bootstrap-badge .badge--success {
    @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300;
}

.transform-bootstrap-badge .badge--warning {
    @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300;
}

.transform-bootstrap-badge .badge--danger {
    @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300;
}

.transform-bootstrap-badge .badge--primary {
    @apply bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300;
}

.transform-bootstrap-badge .badge--info {
    @apply bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300;
}

.transform-bootstrap-badge .badge--dark {
    @apply bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300;
}

.transform-bootstrap-badge .badge--secondary {
    @apply bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400;
}
</style>
@endpush