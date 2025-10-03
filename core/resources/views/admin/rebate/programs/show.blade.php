@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __($program->name) }}</h5>
                <div class="card-header-right">
                    <a href="{{ route('admin.rebate.programs.edit', $program->id) }}" class="btn btn-sm btn--primary">
                        <i class="las la-edit"></i> @lang('Edit Program')
                    </a>
                    <a href="{{ route('admin.rebate.programs.index') }}" class="btn btn-sm btn--secondary">
                        <i class="las la-list"></i> @lang('All Programs')
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Program Information --}}
                    <div class="col-lg-8">
                        <div class="card border--primary">
                            <div class="card-header bg--primary">
                                <h6 class="card-title text-white mb-0">@lang('Program Details')</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>@lang('Program Name'):</strong>
                                        <p>{{ __($program->name) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>@lang('Status'):</strong>
                                        <p>
                                            @if($program->isActive())
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @elseif(!$program->is_active)
                                                <span class="badge badge--danger">@lang('Disabled')</span>
                                            @elseif($program->starts_at && $program->starts_at > now())
                                                <span class="badge badge--warning">@lang('Scheduled')</span>
                                            @elseif($program->ends_at && $program->ends_at < now())
                                                <span class="badge badge--secondary">@lang('Expired')</span>
                                            @else
                                                <span class="badge badge--info">@lang('Unknown')</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if($program->description)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <strong>@lang('Description'):</strong>
                                        <p>{{ __($program->description) }}</p>
                                    </div>
                                </div>
                                @endif

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>@lang('Default Rate'):</strong>
                                        <p>{{ $program->default_rate }}%</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>@lang('Minimum Amount'):</strong>
                                        <p>{{ showAmount($program->minimum_amount) }} {{ __($general->cur_text) }}</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>@lang('Maximum Rebate'):</strong>
                                        <p>
                                            @if($program->maximum_rebate)
                                                {{ showAmount($program->maximum_rebate) }} {{ __($general->cur_text) }}
                                            @else
                                                @lang('No limit')
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>@lang('Daily Limit'):</strong>
                                        <p>
                                            @if($program->daily_limit)
                                                {{ showAmount($program->daily_limit) }} {{ __($general->cur_text) }}
                                            @else
                                                @lang('No limit')
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>@lang('Monthly Limit'):</strong>
                                        <p>
                                            @if($program->monthly_limit)
                                                {{ showAmount($program->monthly_limit) }} {{ __($general->cur_text) }}
                                            @else
                                                @lang('No limit')
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>@lang('Created At'):</strong>
                                        <p>{{ showDateTime($program->created_at) }}</p>
                                    </div>
                                </div>

                                @if($program->starts_at || $program->ends_at)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>@lang('Start Date'):</strong>
                                        <p>
                                            @if($program->starts_at)
                                                {{ showDateTime($program->starts_at, 'd M Y') }}
                                            @else
                                                @lang('No start date')
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>@lang('End Date'):</strong>
                                        <p>
                                            @if($program->ends_at)
                                                {{ showDateTime($program->ends_at, 'd M Y') }}
                                            @else
                                                @lang('No end date')
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Program Statistics --}}
                    <div class="col-lg-4">
                        <div class="card border--info">
                            <div class="card-header bg--info">
                                <h6 class="card-title text-white mb-0">@lang('Program Statistics')</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Total Transactions')</span>
                                        <span class="badge badge--primary badge-pill">{{ $stats['total_transactions'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Total Rebates Paid')</span>
                                        <span class="text--success fw-bold">
                                            {{ showAmount($stats['total_paid']) }} {{ __($general->cur_text) }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Approved Transactions')</span>
                                        <span class="badge badge--success badge-pill">{{ $stats['approved_transactions'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Pending Transactions')</span>
                                        <span class="badge badge--warning badge-pill">{{ $stats['pending_transactions'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Rejected Transactions')</span>
                                        <span class="badge badge--danger badge-pill">{{ $stats['rejected_transactions'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Unique Users')</span>
                                        <span class="badge badge--info badge-pill">{{ $stats['unique_users'] }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Eligible Members')</span>
                                        <span class="badge badge--{{ $program->isUsingManualMembersCount() ? 'success' : 'secondary' }} badge-pill">
                                            {{ number_format($program->getEffectiveMembersCount()) }}
                                            @if($program->isUsingManualMembersCount())
                                                <small class="text-white">(@lang('Manual'))</small>
                                            @else
                                                <small class="text-white">(@lang('System'))</small>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Average Rebate')</span>
                                        <span class="text--info fw-bold">
                                            {{ showAmount($stats['avg_rebate'] ?? 0) }} {{ __($general->cur_text) }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Pending Amount')</span>
                                        <span class="text--warning fw-bold">
                                            {{ showAmount($stats['total_pending']) }} {{ __($general->cur_text) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Program Performance --}}
<div class="row mt-30">
    <div class="col-lg-3">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
            <div class="widget-two__icon">
                <i class="las la-chart-line"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $stats['total_transactions'] }}</h3>
                <p class="text-white">@lang('Total Transactions')</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--success">
            <div class="widget-two__icon">
                <i class="las la-dollar-sign"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ showAmount($stats['total_paid']) }}</h3>
                <p class="text-white">@lang('Total Paid Out')</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--info">
            <div class="widget-two__icon">
                <i class="las la-users"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $stats['unique_users'] }}</h3>
                <p class="text-white">@lang('Unique Users')</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--{{ $program->isUsingManualMembersCount() ? 'primary' : 'dark' }}">
            <div class="widget-two__icon">
                <i class="las la-user-check"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ number_format($program->getEffectiveMembersCount()) }}</h3>
                <p class="text-white">
                    @lang('Eligible Members')
                    @if($program->isUsingManualMembersCount())
                        <small class="d-block">(@lang('Manual Override'))</small>
                    @else
                        <small class="d-block">(@lang('System Count'))</small>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Recent Transactions --}}
<div class="row mt-30">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">@lang('Recent Transactions')</h5>
                <small class="text-muted">@lang('Last 10 transactions')</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRebates as $rebate)
                                <tr>
                                    <td>
                                        <div class="user">
                                            <span class="name">
                                                {{ __($rebate->username) }}
                                                <br>
                                                <small class="text-muted">{{ __($rebate->firstname) }} {{ __($rebate->lastname) }}</small>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text--primary">{{ showAmount($rebate->final_amount ?? $rebate->rebate_amount) }} {{ __($general->cur_text) }}</span>
                                        @if($rebate->original_amount && $rebate->original_amount != ($rebate->final_amount ?? $rebate->rebate_amount))
                                            <br><small class="text-muted">@lang('Base'): {{ showAmount($rebate->original_amount) }}</small>
                                        @endif
                                        @if($rebate->tier_multiplier && $rebate->tier_multiplier > 1)
                                            <br><small class="text-info">{{ $rebate->tier_multiplier }}x @lang('multiplier')</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($rebate->status) {
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'pending' => 'warning',
                                                'flagged' => 'danger',
                                                default => 'info'
                                            };
                                        @endphp
                                        <span class="badge badge--{{ $statusClass }}">{{ __(ucfirst($rebate->status)) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ showDateTime($rebate->created_at, 'd M Y') }}</span>
                                        <br>
                                        <small class="text-muted">{{ diffForHumans($rebate->created_at) }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.rebate.transactions.show', $rebate->id) }}" class="btn btn--primary btn-sm">
                                            <i class="las la-desktop"></i> @lang('Details')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        <div class="text-center py-4">
                                            <i class="las la-chart-line text--info" style="font-size: 3rem;"></i>
                                            <h5 class="mt-2">@lang('No Transactions Yet')</h5>
                                            <p class="text-muted">@lang('No rebate transactions found for this program')</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Program Settings --}}
@if($program->settings && json_decode($program->settings, true))
<div class="row mt-30">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Program Settings')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $settings = json_decode($program->settings, true);
                    @endphp
                    @foreach($settings as $key => $value)
                        <div class="col-md-4 mb-3">
                            <strong>{{ __(ucwords(str_replace('_', ' ', $key))) }}:</strong>
                            <p>
                                @if(is_bool($value))
                                    <span class="badge badge--{{ $value ? 'success' : 'danger' }}">
                                        {{ $value ? __('Yes') : __('No') }}
                                    </span>
                                @elseif(is_numeric($value))
                                    {{ $value }}
                                @else
                                    {{ __($value) }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.rebate.programs.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-list"></i> @lang('All Programs')
    </a>
    <a href="{{ route('admin.rebate.programs.edit', $program->id) }}" class="btn btn-sm btn-outline--info">
        <i class="las la-edit"></i> @lang('Edit Program')
    </a>
    <form action="{{ route('admin.rebate.programs.toggle.status', $program->id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline--{{ $program->is_active ? 'danger' : 'success' }}">
            <i class="las la-{{ $program->is_active ? 'ban' : 'check' }}"></i> 
            {{ $program->is_active ? __('Deactivate') : __('Activate') }}
        </button>
    </form>
@endpush