@extends('admin.layouts.app')

@section('panel')

{{-- Statistics Cards --}}
<div class="row mb-30 mt-30">
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
            <div class="widget-two__icon">
                <i class="las la-coins"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $stats['total_processed'] ?? 0 }}</h3>
                <p class="text-white">@lang('Total Processed')</p>
            </div>
            <a href="{{ route('admin.rebate.transactions.index') }}" class="widget-two__btn">@lang('View All')</a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--success">
            <div class="widget-two__icon">
                <i class="las la-check-circle"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $stats['approved'] ?? 0 }}</h3>
                <p class="text-white">@lang('Approved')</p>
            </div>
            <a href="{{ route('admin.rebate.transactions.index', ['status' => 'approved']) }}" class="widget-two__btn">@lang('View All')</a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--warning">
            <div class="widget-two__icon">
                <i class="las la-clock"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $stats['pending'] ?? 0 }}</h3>
                <p class="text-white">@lang('Pending Review')</p>
            </div>
            <a href="{{ route('admin.rebate.transactions.pending') }}" class="widget-two__btn">@lang('Review Now')</a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--danger">
            <div class="widget-two__icon">
                <i class="las la-times-circle"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $stats['rejected'] ?? 0 }}</h3>
                <p class="text-white">@lang('Rejected')</p>
            </div>
            <a href="{{ route('admin.rebate.transactions.index', ['status' => 'rejected']) }}" class="widget-two__btn">@lang('View All')</a>
        </div>
    </div>
</div>

{{-- Fraud Alert Card --}}
@if($fraudReport && ($fraudReport['flagged_uploads'] > 0 || $fraudReport['high_risk_users'] > 0))
<div class="row mb-30">
    <div class="col-lg-12">
        <div class="card border--danger">
            <div class="card-header bg--danger">
                <h5 class="card-title text-white mb-0">
                    <i class="las la-exclamation-triangle"></i> @lang('Fraud Alert')
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 class="text--danger">{{ $fraudReport['flagged_uploads'] }}</h3>
                            <p class="text-muted">@lang('Flagged Uploads')</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 class="text--danger">{{ $fraudReport['high_risk_users'] }}</h3>
                            <p class="text-muted">@lang('High Risk Users')</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
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

{{-- Filters --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="">
                    <div class="d-flex flex-wrap gap-4">
                        <div class="flex-grow-1">
                            <label>@lang('Status')</label>
                            <select name="status" class="form-control">
                                <option value="all">@lang('All Status')</option>
                                <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                                <option value="approved" {{ request()->status == 'approved' ? 'selected' : '' }}>@lang('Approved')</option>
                                <option value="rejected" {{ request()->status == 'rejected' ? 'selected' : '' }}>@lang('Rejected')</option>
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label>@lang('Type')</label>
                            <select name="type" class="form-control">
                                <option value="all">@lang('All Types')</option>
                                <option value="product_upload" {{ request()->type == 'product_upload' ? 'selected' : '' }}>@lang('Product Upload')</option>
                                <option value="referral" {{ request()->type == 'referral' ? 'selected' : '' }}>@lang('Referral')</option>
                                <option value="loyalty" {{ request()->type == 'loyalty' ? 'selected' : '' }}>@lang('Loyalty')</option>
                            </select>
                        </div>
                        <div class="flex-grow-1">
                            <label>@lang('Date Range')</label>
                            <select name="date_range" class="form-control">
                                <option value="7" {{ request()->date_range == '7' ? 'selected' : '' }}>@lang('Last 7 Days')</option>
                                <option value="30" {{ request()->date_range == '30' ? 'selected' : '' }}>@lang('Last 30 Days')</option>
                                <option value="90" {{ request()->date_range == '90' ? 'selected' : '' }}>@lang('Last 90 Days')</option>
                                <option value="all" {{ request()->date_range == 'all' ? 'selected' : '' }}>@lang('All Time')</option>
                            </select>
                        </div>
                        <div class="flex-grow-1 align-self-end">
                            <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Approve Rebate Modal --}}
<div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Approve Rebate Confirmation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>@lang('Are you sure you want to approve this rebate?')</p>
                    <ul class="list-group mt-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('User'):</span> 
                            <span class="approve-user fw-bold text--primary"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Amount'):</span> 
                            <span class="approve-amount fw-bold text--success"></span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--success">@lang('Approve Rebate')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Rebate Modal --}}
<div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Reject Rebate')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('User'):</span> 
                            <span class="reject-user fw-bold text--primary"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>@lang('Amount'):</span> 
                            <span class="reject-amount fw-bold text--danger"></span>
                        </li>
                    </ul>
                    
                    <div class="form-group mt-3">
                        <label class="fw-bold">@lang('Rejection Reason') <span class="text--danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" placeholder="@lang('Enter reason for rejection')" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--danger">@lang('Reject Rebate')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mt-30">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table--light style--two table">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Program')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rebates as $rebate)
                                <tr>
                                    <td>
                                        <div class="user">
                                            <div class="thumb">
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . @$rebate->user->image, getFileSize('userProfile')) }}" alt="@lang('image')">
                                            </div>
                                            <span class="name">
                                                <a href="{{ route('admin.users.detail', $rebate->user->id ?? 0) }}">
                                                    {{ __(@$rebate->user->fullname) }}
                                                </a>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ __(ucwords(str_replace('_', ' ', $rebate->transaction_type))) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text--primary">{{ showAmount($rebate->rebate_amount) }} {{ __($general->cur_text) }}</span>
                                    </td>
                                    <td>
                                        @if($rebate->status == 'pending')
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif($rebate->status == 'approved')
                                            <span class="badge badge--success">@lang('Approved')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Rejected')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ __(@$rebate->rebateProgram->name) }}</span>
                                    </td>
                                    <td>
                                        {{ showDateTime($rebate->created_at) }}<br>
                                        <span class="text--info">{{ diffForHumans($rebate->created_at) }}</span>
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.rebate.transactions.show', $rebate->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
                                            
                                            @if($rebate->status == 'pending')
                                                <button class="btn btn-sm btn-outline--success approveBtn" 
                                                        data-id="{{ $rebate->id }}" 
                                                        data-user="{{ @$rebate->user->username }}" 
                                                        data-amount="{{ showAmount($rebate->rebate_amount) }}">
                                                    <i class="las la-check"></i> @lang('Approve')
                                                </button>
                                                <button class="btn btn-sm btn-outline--danger rejectBtn" 
                                                        data-id="{{ $rebate->id }}" 
                                                        data-user="{{ @$rebate->user->username }}" 
                                                        data-amount="{{ showAmount($rebate->rebate_amount) }}">
                                                    <i class="las la-times"></i> @lang('Reject')
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($rebates->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($rebates) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <a href="{{ route('admin.rebate.analytics') }}" class="btn btn-sm btn-outline--primary">
            <i class="las la-chart-bar"></i> @lang('Analytics')
        </a>
        <a href="{{ route('admin.rebate.programs.index') }}" class="btn btn-sm btn-outline--info">
            <i class="las la-cogs"></i> @lang('Programs')
        </a>
    </div>
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';

        $('.approveBtn').on('click', function () {
            var modal = $('#approveModal');
            var rebateId = $(this).data('id');
            var user = $(this).data('user');
            var amount = $(this).data('amount');
            
            modal.find('form').attr('action', `{{ route('admin.rebate.transactions.approve', 'REBATE_ID') }}`.replace('REBATE_ID', rebateId));
            modal.find('.approve-user').text(user);
            modal.find('.approve-amount').text(amount + ' {{ $general->cur_text }}');
            modal.modal('show');
        });

        $('.rejectBtn').on('click', function () {
            var modal = $('#rejectModal');
            var rebateId = $(this).data('id');
            var user = $(this).data('user');
            var amount = $(this).data('amount');
            
            modal.find('form').attr('action', `{{ route('admin.rebate.transactions.reject', 'REBATE_ID') }}`.replace('REBATE_ID', rebateId));
            modal.find('.reject-user').text(user);
            modal.find('.reject-amount').text(amount + ' {{ $general->cur_text }}');
            modal.modal('show');
        });

        // Auto-refresh for real-time updates
        setInterval(function() {
            if (!$('.modal:visible').length) {
                location.reload();
            }
        }, 60000); // Refresh every minute if no modal is open

    })(jQuery);
</script>
@endpush