@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-4">
        <!-- Rebate Overview Cards -->
        <div class="col-xl-3 col-lg-6 col-md-4 col-sm-6 col-xsm-6">
            <div class="dashboard-widget">
                <div class="dashboard-widget__content flex-align">
                    <span class="dashboard-widget__icon flex-center">
                        <i class="las la-gift text--primary"></i>
                    </span>
                    <span class="dashboard-widget__text">@lang('Total Earned')</span>
                </div>
                <h4 class="dashboard-widget__number text--success">
                    {{ showAmount($stats['total_earned'] ?? 0) }}
                </h4>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-4 col-sm-6 col-xsm-6">
            <div class="dashboard-widget">
                <div class="dashboard-widget__content flex-align">
                    <span class="dashboard-widget__icon flex-center">
                        <i class="las la-clock text--warning"></i>
                    </span>
                    <span class="dashboard-widget__text">@lang('Pending Amount')</span>
                </div>
                <h4 class="dashboard-widget__number text--warning">
                    {{ showAmount($stats['pending_amount'] ?? 0) }}
                </h4>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-4 col-sm-6 col-xsm-6">
            <div class="dashboard-widget">
                <div class="dashboard-widget__content flex-align">
                    <span class="dashboard-widget__icon flex-center">
                        <i class="las la-star text--info"></i>
                    </span>
                    <span class="dashboard-widget__text">@lang('Current Tier')</span>
                </div>
                <h4 class="dashboard-widget__number text--info">
                    @lang('Tier') {{ $stats['current_tier'] ?? 1 }}
                </h4>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-4 col-sm-6 col-xsm-6">
            <div class="dashboard-widget">
                <div class="dashboard-widget__content flex-align">
                    <span class="dashboard-widget__icon flex-center">
                        <i class="las la-chart-line text--success"></i>
                    </span>
                    <span class="dashboard-widget__text">@lang('Success Rate')</span>
                </div>
                <h4 class="dashboard-widget__number text--success">
                    {{ $stats['success_rate'] ?? 0 }}%
                </h4>
            </div>
        </div>

        <!-- Tier Progress -->
        <div class="col-xl-8">
            <div class="dashboard-widget">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class="las la-trophy text--primary me-2"></i>
                        @lang('Tier Progress')
                    </h5>
                    
                    @php
                        $currentTier = $stats['current_tier'] ?? 1;
                        $totalEarned = $stats['total_earned'] ?? 0;
                        $tierRequirements = [
                            1 => ['name' => 'Basic', 'requirement' => 0, 'next' => 500],
                            2 => ['name' => 'Bronze', 'requirement' => 500, 'next' => 2000],
                            3 => ['name' => 'Silver', 'requirement' => 2000, 'next' => 5000],
                            4 => ['name' => 'Gold', 'requirement' => 5000, 'next' => 10000],
                            5 => ['name' => 'Platinum', 'requirement' => 10000, 'next' => null],
                        ];
                        $current = $tierRequirements[$currentTier];
                        $progress = 0;
                        
                        if ($currentTier < 5) {
                            $progress = (($totalEarned - $current['requirement']) / ($current['next'] - $current['requirement'])) * 100;
                            $progress = min(100, max(0, $progress));
                        } else {
                            $progress = 100;
                        }
                    @endphp

                    <div class="tier-progress">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">@lang('Current Tier'): <span class="text--primary">{{ $current['name'] }}</span></h6>
                                <small class="text-muted">@lang('Total Earned'): {{ showAmount($totalEarned) }}</small>
                            </div>
                            @if($currentTier < 5)
                                <div class="text-end">
                                    <small class="text-muted">@lang('Next Tier Requirement')</small>
                                    <div class="fw-bold">{{ showAmount($current['next']) }}</div>
                                </div>
                            @else
                                <div class="text-end">
                                    <div class="badge bg--success">@lang('Max Tier Reached')</div>
                                </div>
                            @endif
                        </div>

                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg--primary" role="progressbar" 
                                 style="width: {{ $progress }}%" 
                                 aria-valuenow="{{ $progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        
                        @if($currentTier < 5)
                            <div class="d-flex justify-content-between small text-muted">
                                <span>{{ showAmount($current['requirement']) }}</span>
                                <span>{{ round($progress, 1) }}%</span>
                                <span>{{ showAmount($current['next']) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4">
            <div class="dashboard-widget">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class="las la-bolt text--warning me-2"></i>
                        @lang('Quick Actions')
                    </h5>
                    
                    <div class="row gy-3">
                        <div class="col-12">
                            <a href="{{ route('user.rebate.submit') }}" class="btn btn--primary btn--lg w-100">
                                <i class="las la-upload me-2"></i>
                                @lang('Submit New Rebate')
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.rebate.programs') }}" class="btn btn--secondary w-100">
                                <i class="las la-list-alt me-1"></i>
                                @lang('Programs')
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.rebate.history') }}" class="btn btn--info w-100">
                                <i class="las la-history me-1"></i>
                                @lang('History')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-12">
            <div class="dashboard-widget">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="las la-history text--info me-2"></i>
                            @lang('Recent Rebate Transactions')
                        </h5>
                        <a href="{{ route('user.rebate.history') }}" class="btn btn--primary btn--sm">
                            @lang('View All')
                        </a>
                    </div>

                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table--light">
                                <thead>
                                    <tr>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Product')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Rebate')</th>
                                        <th>@lang('Status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ showDateTime($transaction->created_at, 'd M Y') }}</div>
                                                <small class="text-muted">{{ diffForHumans($transaction->created_at) }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $transaction->product_name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $transaction->product_sku ?? '' }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ showAmount($transaction->original_amount ?? 0) }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text--success">{{ showAmount($transaction->rebate_amount) }}</div>
                                                <small class="text-muted">{{ $transaction->rebate_rate }}%</small>
                                            </td>
                                            <td>
                                                @if($transaction->status == 'pending')
                                                    <span class="badge bg--warning">@lang('Pending')</span>
                                                @elseif($transaction->status == 'approved')
                                                    <span class="badge bg--success">@lang('Approved')</span>
                                                @elseif($transaction->status == 'rejected')
                                                    <span class="badge bg--danger">@lang('Rejected')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="las la-inbox display-1 text-muted"></i>
                            </div>
                            <h6 class="text-muted">@lang('No rebate transactions found')</h6>
                            <a href="{{ route('user.rebate.submit') }}" class="btn btn--primary btn--sm mt-3">
                                <i class="las la-plus me-1"></i>
                                @lang('Submit Your First Rebate')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .tier-progress .progress {
        border-radius: 10px;
        background-color: #e9ecef;
    }
    
    .tier-progress .progress-bar {
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    
    .dashboard-widget {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .dashboard-widget:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .quick-action-btn {
        transition: all 0.2s ease;
    }
    
    .quick-action-btn:hover {
        transform: scale(1.05);
    }
</style>
@endpush