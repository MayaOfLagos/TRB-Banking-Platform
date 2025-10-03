@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        {{-- Summary Stats --}}
        <div class="row mb-30">
            <div class="col-lg-3 col-md-6 mb-30">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
                    <div class="widget-two__icon">
                        <i class="las la-money-bill-wave"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text--white">{{ showAmount($analytics['total_paid']) }} {{ $general->cur_text }}</h3>
                        <p>@lang('Total Paid Out')</p>
                        @if($analytics['total_paid_growth'] > 0)
                            <span class="text--success">
                                <i class="las la-arrow-up"></i> {{ number_format($analytics['total_paid_growth'], 1) }}% @lang('vs last month')
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-30">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
                    <div class="widget-two__icon">
                        <i class="las la-users"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text--white">{{ number_format($analytics['active_users']) }}</h3>
                        <p>@lang('Active Users')</p>
                        <span class="text-white">{{ number_format($analytics['new_users_this_month']) }} @lang('new this month')</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-30">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--success">
                    <div class="widget-two__icon">
                        <i class="las la-chart-line"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text--white">{{ showAmount($analytics['avg_rebate_amount']) }}</h3>
                        <p>@lang('Average Rebate')</p>
                        <span class="text-white">{{ $analytics['total_rebates_count'] }} @lang('total rebates')</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-30">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--info">
                    <div class="widget-two__icon">
                        <i class="las la-percentage"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text--white">{{ number_format($analytics['approval_rate'], 1) }}%</h3>
                        <p>@lang('Approval Rate')</p>
                        <span class="text-white">@lang('Last 30 days')</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Date Range Filter --}}
        <div class="card mb-30">
            <div class="card-body">
                <form action="" method="GET" class="row align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('Date Range')</label>
                            <input name="date_range" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" autocomplete="off" class="datepicker-here form-control" value="{{ request()->date_range }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label>@lang('Program')</label>
                            <select name="program_id" class="form-control">
                                <option value="">@lang('All Programs')</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" @selected(request()->program_id == $program->id)>
                                        {{ __($program->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label>@lang('Category')</label>
                            <select name="category_id" class="form-control">
                                <option value="">@lang('All Categories')</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(request()->category_id == $category->id)>
                                        {{ __($category->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <select name="status" class="form-control">
                                <option value="">@lang('All Status')</option>
                                <option value="approved" @selected(request()->status == 'approved')>@lang('Approved')</option>
                                <option value="pending" @selected(request()->status == 'pending')>@lang('Pending')</option>
                                <option value="rejected" @selected(request()->status == 'rejected')>@lang('Rejected')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn--primary flex-fill">
                                <i class="las la-search"></i> @lang('Filter')
                            </button>
                            <a href="{{ route('admin.rebate.analytics') }}" class="btn btn--secondary">
                                <i class="las la-sync"></i> @lang('Reset')
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row mb-30">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Rebate Trends')</h5>
                        <div class="card-header-right">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline--primary active" data-chart="volume">@lang('Volume')</button>
                                <button type="button" class="btn btn-sm btn-outline--primary" data-chart="amount">@lang('Amount')</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="rebateChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Program Distribution')</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="programChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Performance Metrics --}}
        <div class="row mb-30">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Top Performing Programs')</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table--light style--two table">
                                <thead>
                                    <tr>
                                        <th>@lang('Program')</th>
                                        <th>@lang('Total Users')</th>
                                        <th>@lang('Total Amount')</th>
                                        <th>@lang('Avg. Rebate')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topPrograms as $program)
                                        <tr>
                                            <td>
                                                <div>
                                                    <span class="fw-bold">{{ $program ? __($program->program_name) : 'N/A' }}</span>
                                                    <br>
                                                    <small class="text-muted">@lang('Top Program')</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ number_format($program->rebate_count) }}</span>
                                            </td>
                                            <td>
                                                <span class="text--primary fw-bold">
                                                    {{ showAmount($program->total_amount) }} {{ $general->cur_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">
                                                    {{ showAmount($program->total_amount / ($program->rebate_count ?: 1)) }} {{ $general->cur_text }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="4">@lang('No data available')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Top Users by Rebates')</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table--light style--two table">
                                <thead>
                                    <tr>
                                        <th>@lang('User')</th>
                                        <th>@lang('Total Rebates')</th>
                                        <th>@lang('Total Amount')</th>
                                        <th>@lang('Current Tier')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="user">
                                                    <div class="thumb">
                                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$user->image, getFileSize('userProfile')) }}" alt="@lang('image')">
                                                    </div>
                                                    <span class="name">
                                                        @if($user->id)
                                                            <a href="{{ route('admin.users.detail', $user->id) }}">{{ __($user->firstname . ' ' . $user->lastname) }}</a>
                                                        @else
                                                            {{ __($user->firstname . ' ' . $user->lastname) }}
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">{{ __($user->username) }}</small>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ number_format($user->rebates_count) }}</span>
                                            </td>
                                            <td>
                                                <span class="text--success fw-bold">
                                                    {{ showAmount($user->total_rebate_amount) }} {{ $general->cur_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge--{{ $user->tier_name == 'Gold' ? 'warning' : ($user->tier_name == 'Silver' ? 'info' : 'secondary') }}">
                                                    {{ __($user->tier_name ?? 'Bronze') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="4">@lang('No data available')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Breakdown --}}
        <div class="row mb-30">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text--success">@lang('Approved Rebates')</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="text--success" style="font-size: 3rem;">
                            <i class="las la-check-circle"></i>
                        </div>
                        <h3 class="text--success">{{ number_format($statusBreakdown['approved']['count']) }}</h3>
                        <p class="text-muted">@lang('Rebates Approved')</p>
                        <h4 class="text--success">{{ showAmount($statusBreakdown['approved']['amount']) }} {{ $general->cur_text }}</h4>
                        <p class="text-muted">@lang('Total Amount Paid')</p>
                        <div class="mt-3">
                            <span class="badge badge--success">
                                {{ number_format(($statusBreakdown['approved']['count'] / max($analytics['total_rebates_count'], 1)) * 100, 1) }}% @lang('of all rebates')
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text--warning">@lang('Pending Rebates')</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="text--warning" style="font-size: 3rem;">
                            <i class="las la-clock"></i>
                        </div>
                        <h3 class="text--warning">{{ number_format($statusBreakdown['pending']['count']) }}</h3>
                        <p class="text-muted">@lang('Awaiting Review')</p>
                        <h4 class="text--warning">{{ showAmount($statusBreakdown['pending']['amount']) }} {{ $general->cur_text }}</h4>
                        <p class="text-muted">@lang('Potential Payout')</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.rebate.transactions.pending') }}" class="btn btn--warning btn-sm">
                                <i class="las la-eye"></i> @lang('Review Now')
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text--danger">@lang('Rejected Rebates')</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="text--danger" style="font-size: 3rem;">
                            <i class="las la-times-circle"></i>
                        </div>
                        <h3 class="text--danger">{{ number_format($statusBreakdown['rejected']['count']) }}</h3>
                        <p class="text-muted">@lang('Rebates Rejected')</p>
                        <h4 class="text--danger">{{ showAmount($statusBreakdown['rejected']['amount']) }} {{ $general->cur_text }}</h4>
                        <p class="text-muted">@lang('Amount Declined')</p>
                        <div class="mt-3">
                            <span class="badge badge--danger">
                                {{ number_format(($statusBreakdown['rejected']['count'] / max($analytics['total_rebates_count'], 1)) * 100, 1) }}% @lang('rejection rate')
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fraud Detection Summary --}}
        @if($fraudStats['total_flags'] > 0)
        <div class="row mb-30">
            <div class="col-lg-12">
                <div class="card border--danger">
                    <div class="card-header bg--danger">
                        <h5 class="card-title text-white mb-0">
                            <i class="las la-shield-alt"></i> @lang('Fraud Detection Summary')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 text-center">
                                <div class="text--danger" style="font-size: 2rem;">
                                    <i class="las la-exclamation-triangle"></i>
                                </div>
                                <h4 class="text--danger">{{ $fraudStats['total_flags'] }}</h4>
                                <p class="text-muted">@lang('Fraud Flags')</p>
                            </div>
                            <div class="col-lg-3 col-md-6 text-center">
                                <div class="text--warning" style="font-size: 2rem;">
                                    <i class="las la-user-shield"></i>
                                </div>
                                <h4 class="text--warning">{{ $fraudStats['flagged_users'] }}</h4>
                                <p class="text-muted">@lang('Flagged Users')</p>
                            </div>
                            <div class="col-lg-3 col-md-6 text-center">
                                <div class="text--info" style="font-size: 2rem;">
                                    <i class="las la-money-bill-wave"></i>
                                </div>
                                <h4 class="text--info">{{ showAmount($fraudStats['blocked_amount']) }}</h4>
                                <p class="text-muted">@lang('Amount Blocked')</p>
                            </div>
                            <div class="col-lg-3 col-md-6 text-center">
                                <div class="mt-2">
                                    <a href="{{ route('admin.rebate.transactions.pending') }}" class="btn btn--danger">
                                        <i class="las la-eye"></i> @lang('Review Pending')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Export Options --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Export Analytics')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <p class="text-muted mb-3">@lang('Export detailed analytics data for further analysis. Choose your preferred format and date range.')</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.rebate.export', ['format' => 'excel'] + request()->all()) }}" class="btn btn--success">
                                        <i class="las la-file-excel"></i> @lang('Export to Excel')
                                    </a>
                                    <a href="{{ route('admin.rebate.export', ['format' => 'csv'] + request()->all()) }}" class="btn btn--info">
                                        <i class="las la-file-csv"></i> @lang('Export to CSV')
                                    </a>
                                    <a href="{{ route('admin.rebate.export', ['format' => 'pdf'] + request()->all()) }}" class="btn btn--danger">
                                        <i class="las la-file-pdf"></i> @lang('Export to PDF')
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 text-center">
                                <div class="text-muted" style="font-size: 4rem;">
                                    <i class="las la-chart-bar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.rebate.transactions.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-list"></i> @lang('All Rebates')
    </a>
    <button class="btn btn-sm btn-outline--secondary" id="refreshData">
        <i class="las la-sync"></i> @lang('Refresh Data')
    </button>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';

        // Chart data from backend
        const chartData = @json($chartData);
        const programData = @json($programData);

        // Rebate trends chart
        const ctx = document.getElementById('rebateChart').getContext('2d');
        let rebateChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Rebate Volume',
                    data: chartData.volume,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart type switcher
        $('[data-chart]').on('click', function() {
            $('[data-chart]').removeClass('active');
            $(this).addClass('active');
            
            const type = $(this).data('chart');
            
            if (type === 'volume') {
                rebateChart.data.datasets[0] = {
                    label: 'Rebate Volume',
                    data: chartData.volume,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4
                };
            } else {
                rebateChart.data.datasets[0] = {
                    label: 'Rebate Amount ({{ $general->cur_text }})',
                    data: chartData.amount,
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.1)',
                    tension: 0.4
                };
            }
            
            rebateChart.update();
        });

        // Program distribution chart
        const programCtx = document.getElementById('programChart').getContext('2d');
        new Chart(programCtx, {
            type: 'doughnut',
            data: {
                labels: programData.labels,
                datasets: [{
                    data: programData.data,
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)', 
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Date range picker
        $('.datepicker-here').daterangepicker({
            autoUpdateInput: false,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            }
        });

        $('.datepicker-here').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.datepicker-here').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Refresh data
        $('#refreshData').on('click', function() {
            location.reload();
        });

        // Auto-refresh every 5 minutes
        setInterval(function() {
            if (!document.hidden) {
                location.reload();
            }
        }, 300000);

    })(jQuery);
</script>
@endpush
