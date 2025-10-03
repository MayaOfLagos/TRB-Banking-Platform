@extends($activeTemplate.'layouts.master')
@section('content')

<div class="dashboard-inner">
    <!-- Page Header -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@lang('Available Rebate Programs')</h3>
        <p class="text-gray-600 dark:text-gray-400">@lang('Browse and join rebate programs to start earning rewards')</p>
    </div>

    <!-- Program Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Available Programs')</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_programs'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-clipboard-list text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Joined Programs')</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['joined_programs'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-check-circle text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Avg. Rebate Rate')</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['avg_rebate_rate'] ?? '0' }}%</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-percentage text-amber-600 dark:text-amber-400 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">@lang('Total Earnings')</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ showUserAmount($stats['total_program_earnings'] ?? 0, auth()->user()) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="las la-money-bill-wave text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Filters -->
    <div x-data="{ 
        filtersOpen: false,
        init() {
            this.filtersOpen = window.innerWidth >= 768;
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    this.filtersOpen = true;
                }
            });
        }
    }" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6 p-2">
        {{-- Filter Header with Toggle --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('Filter Programs')</h3>
            {{-- Mobile Toggle Button --}}
            <button @click="filtersOpen = !filtersOpen" class="md:hidden inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200">
                <span x-text="filtersOpen ? '@lang('Hide Filters')' : '@lang('Show Filters')'"></span>
                <i class="las la-angle-down ml-2 transform transition-transform duration-200" :class="{ 'rotate-180': filtersOpen }"></i>
            </button>
        </div>
        
        {{-- Filter Content --}}
        <div class="p-6" x-show="filtersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Category')</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" name="category" onchange="filterPrograms()">
                    <option value="">@lang('All Categories')</option>
                    <option value="electronics">@lang('Electronics')</option>
                    <option value="fashion">@lang('Fashion')</option>
                    <option value="home">@lang('Home & Garden')</option>
                    <option value="travel">@lang('Travel')</option>
                    <option value="food">@lang('Food & Dining')</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Rebate Rate')</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" name="rate" onchange="filterPrograms()">
                    <option value="">@lang('Any Rate')</option>
                    <option value="high">@lang('5%+ High Rate')</option>
                    <option value="medium">@lang('2-5% Medium Rate')</option>
                    <option value="low">@lang('Under 2%')</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Status')</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" name="status" onchange="filterPrograms()">
                    <option value="">@lang('All Programs')</option>
                    <option value="joined">@lang('Joined')</option>
                    <option value="available">@lang('Available')</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" class="w-full bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="resetFilters()">
                    <i class="las la-refresh mr-2"></i>@lang('Reset Filters')
                </button>
            </div>
        </div>
    </div>

    <!-- Programs Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-2" id="programs-grid">
        @forelse($programs as $program)
            <div class="program-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300" 
                 data-category="{{ optional($program->categories->first())->name ?? 'general' }}" 
                 data-rate="{{ $program->default_rate ?? 0 }}" 
                 data-status="available">
                
                <!-- Program Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        @if($program->logo ?? null)
                            <img src="{{ getImage(getFilePath('rebateProgram') . '/' . $program->logo) }}" alt="{{ $program->name }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="las la-store text-white text-xl"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $program->name }}</h5>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ optional($program->categories->first())->name ?: __('General') }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-xs font-medium rounded-full">@lang('Available')</span>
                    </div>
                </div>

                <!-- Program Rate -->
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-center text-white mb-4">
                    <div class="text-2xl font-bold">{{ number_format($program->default_rate, 1) }}%</div>
                    <div class="text-purple-100 text-sm">@lang('Rebate Rate')</div>
                </div>
                
                <!-- Program Description -->
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 leading-relaxed">
                    {{ Str::limit($program->description ?: __('Earn cashback on your purchases with this exclusive rebate program. Shop more, save more!'), 120) }}
                </p>

                <!-- Program Stats -->
                @php
                    $userCount = $program->rebateTransactions()->distinct('user_id')->count('user_id');
                    $memberCount = $program->getEffectiveMembersCount();
                    $avgProcessingTime = 24; // This could be calculated from actual data if needed
                @endphp
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-6">
                    <div class="flex items-center space-x-1">
                        <i class="las la-users"></i>
                        <span>{{ number_format($memberCount) }} + @lang('eligible members')</span>
                        @if($program->isUsingManualMembersCount())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 ml-1">
                                @lang('Running')
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-1">
                        <i class="las la-clock"></i>
                        <span>@lang('Avg') {{ $avgProcessingTime }} @lang('hours processing')</span>
                    </div>
                </div>

                <!-- Program Actions -->
                <div class="mt-auto">
                    @php
                        $userJoined = $program->rebateTransactions()->where('user_id', auth()->id())->exists();
                    @endphp
                    @if($userJoined)
                        <a href="{{ route('user.product.upload') }}?program={{ $program->id }}" class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center inline-block">
                            <i class="las la-upload mr-2"></i>@lang('Upload Receipt')
                        </a>
                    @else
                        <button type="button" class="w-full bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="joinProgram({{ $program->id }})">
                            <i class="las la-plus mr-2"></i>@lang('Join Program')
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <!-- No Programs Found -->
            <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                    <i class="las la-gift text-4xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('No Programs Available')</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md">@lang('There are currently no active rebate programs available. Please check back later or contact support for more information.')</p>
                <a href="{{ route('user.home') }}" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                    <i class="las la-arrow-left mr-2"></i>@lang('Back to Dashboard')
                </a>
            </div>
        @endforelse
    </div>

    <!-- Empty State -->
    <div class="text-center py-12" id="empty-state" style="display: none;">
        <div class="max-w-md mx-auto">
            <i class="las la-search text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <h5 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('No Programs Found')</h5>
            <p class="text-gray-600 dark:text-gray-400 mb-6">@lang('No programs match your current filters. Try adjusting your search criteria.')</p>
            <button type="button" class="bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium py-2 px-6 rounded-lg transition-colors" onclick="resetFilters()">
                <i class="las la-refresh mr-2"></i>@lang('Reset Filters')
            </button>
        </div>
    </div>
</div>

<!-- Join Program Modal -->
<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="joinProgramModal" x-data="{ show: false }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>
        
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="las la-gift text-purple-600 dark:text-purple-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('Join Rebate Program')</h3>
                <p class="text-gray-600 dark:text-gray-400">@lang('You will start earning cashback on eligible purchases immediately after joining.')</p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">@lang('Benefits:')</h4>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="las la-check text-green-500 mr-2"></i>
                        @lang('Instant cashback on purchases')
                    </li>
                    <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="las la-check text-green-500 mr-2"></i>
                        @lang('Tier-based bonus rewards')
                    </li>
                    <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="las la-check text-green-500 mr-2"></i>
                        @lang('Exclusive member promotions')
                    </li>
                    <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="las la-check text-green-500 mr-2"></i>
                        @lang('24/7 customer support')
                    </li>
                </ul>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" class="flex-1 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg transition-colors" @click="show = false">
                    @lang('Cancel')
                </button>
                <button type="button" class="flex-1 bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="confirmJoinProgram()">
                    @lang('Join Program')
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    let currentProgramId = null;

    function filterPrograms() {
        const category = document.querySelector('select[name="category"]').value;
        const rate = document.querySelector('select[name="rate"]').value;
        const status = document.querySelector('select[name="status"]').value;
        
        const programCards = document.querySelectorAll('.program-card');
        let visibleCount = 0;

        programCards.forEach(card => {
            let show = true;
            
            // Category filter
            if (category && !card.dataset.category.includes(category)) {
                show = false;
            }
            
            // Rate filter
            if (rate) {
                const cardRate = parseFloat(card.dataset.rate);
                if (rate === 'high' && cardRate < 5) show = false;
                if (rate === 'medium' && (cardRate < 2 || cardRate >= 5)) show = false;
                if (rate === 'low' && cardRate >= 2) show = false;
            }
            
            // Status filter
            if (status && card.dataset.status !== status) {
                show = false;
            }
            
            card.style.display = show ? 'block' : 'none';
            if (show) visibleCount++;
        });

        // Show/hide empty state
        document.getElementById('empty-state').style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function resetFilters() {
        document.querySelector('select[name="category"]').value = '';
        document.querySelector('select[name="rate"]').value = '';
        document.querySelector('select[name="status"]').value = '';
        
        document.querySelectorAll('.program-card').forEach(card => {
            card.style.display = 'block';
        });
        
        document.getElementById('empty-state').style.display = 'none';
    }

    function joinProgram(programId) {
        currentProgramId = programId;
        // Show modal using Alpine.js
        document.getElementById('joinProgramModal').style.display = 'block';
        document.getElementById('joinProgramModal').__x.$data.show = true;
    }

    function confirmJoinProgram() {
        if (!currentProgramId) return;
        
        // Here you would make an AJAX call to join the program
        fetch(`{{ route('user.rebate.programs') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                program_id: currentProgramId,
                action: 'join'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to show updated status
            } else {
                alert(data.message || '@lang("Error joining program")');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('@lang("Network error occurred")');
        });
        
        // Hide modal
        document.getElementById('joinProgramModal').__x.$data.show = false;
    }
</script>
@endpush