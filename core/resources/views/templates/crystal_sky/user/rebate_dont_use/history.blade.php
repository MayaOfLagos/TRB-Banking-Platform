@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-4">
        <!-- Header Section -->
        <div class="col-12">
            <div class="dashboard-widget">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h4 class="card-title mb-2">
                                <i class="las la-history text--primary me-2"></i>
                                @lang('Rebate Transaction History')
                            </h4>
                            <p class="text-muted mb-0">@lang('View all your rebate transactions and their status')</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('user.rebate.submit') }}" class="btn btn--primary btn--sm">
                                <i class="las la-plus me-1"></i>
                                @lang('New Rebate')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="col-12">
            <div class="dashboard-widget">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.rebate.history') }}">
                        <div class="row gy-3 align-items-end">
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label">@lang('Status')</label>
                                <select name="status" class="form-control">
                                    <option value="">@lang('All Status')</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>@lang('Approved')</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>@lang('Rejected')</option>
                                </select>
                            </div>
                            
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label">@lang('Date From')</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label">@lang('Date To')</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn--primary flex-fill">
                                        <i class="las la-search me-1"></i>
                                        @lang('Filter')
                                    </button>
                                    <a href="{{ route('user.rebate.history') }}" class="btn btn--secondary">
                                        <i class="las la-undo me-1"></i>
                                        @lang('Reset')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="dashboard-widget">
                <div class="dashboard-widget__content flex-align">
                    <span class="dashboard-widget__icon flex-center">
                        <i class="las la-list-alt text--info"></i>
                    </span>
                    <span class="dashboard-widget__text">@lang('Total Transactions')</span>
                </div>
                <h4 class="dashboard-widget__number">{{ $transactions->total() }}</h4>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="dashboard-widget">
                <div class="dashboard-widget__content flex-align">
                    <span class="dashboard-widget__icon flex-center">
                        <i class="las la-check-circle text--success"></i>
                    </span>
                    <span class="dashboard-widget__text">@lang('Approved')</span>
                </div>
                <h4 class="dashboard-widget__number text--success">{{ $summary['approved_count'] ?? 0 }}</h4>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="dashboard-widget">
                <div class="dashboard-widget__content flex-align">
                    <span class="dashboard-widget__icon flex-center">
                        <i class="las la-clock text--warning"></i>
                    </span>
                    <span class="dashboard-widget__text">@lang('Pending')</span>
                </div>
                <h4 class="dashboard-widget__number text--warning">{{ $summary['pending_count'] ?? 0 }}</h4>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="dashboard-widget">
                <div class="dashboard-widget__content flex-align">
                    <span class="dashboard-widget__icon flex-center">
                        <i class="las la-dollar-sign text--success"></i>
                    </span>
                    <span class="dashboard-widget__text">@lang('Total Earned')</span>
                </div>
                <h4 class="dashboard-widget__number text--success">{{ showAmount($summary['total_earned'] ?? 0) }}</h4>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="col-12">
            <div class="dashboard-widget">
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table--light">
                                <thead>
                                    <tr>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Product Details')</th>
                                        <th>@lang('Store')</th>
                                        <th>@lang('Purchase Amount')</th>
                                        <th>@lang('Rebate')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ showDateTime($transaction->created_at, 'd M Y') }}</div>
                                                <small class="text-muted">{{ showDateTime($transaction->created_at, 'h:i A') }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $transaction->product_name ?? 'N/A' }}</div>
                                                @if($transaction->product_sku)
                                                    <small class="text-muted">SKU: {{ $transaction->product_sku }}</small>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ showDateTime($transaction->purchase_date ?? $transaction->created_at, 'd M Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $transaction->store_name ?? 'N/A' }}</div>
                                                @if($transaction->receipt_number)
                                                    <small class="text-muted">Receipt: {{ $transaction->receipt_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ showAmount($transaction->original_amount ?? 0) }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text--success">{{ showAmount($transaction->rebate_amount) }}</div>
                                                <small class="text-muted">{{ $transaction->rebate_rate ?? 0 }}% rate</small>
                                            </td>
                                            <td>
                                                @if($transaction->status == 'pending')
                                                    <span class="badge bg--warning">@lang('Pending')</span>
                                                @elseif($transaction->status == 'approved')
                                                    <span class="badge bg--success">@lang('Approved')</span>
                                                    @if($transaction->processed_at)
                                                        <br><small class="text-muted">{{ diffForHumans($transaction->processed_at) }}</small>
                                                    @endif
                                                @elseif($transaction->status == 'rejected')
                                                    <span class="badge bg--danger">@lang('Rejected')</span>
                                                    @if($transaction->processed_at)
                                                        <br><small class="text-muted">{{ diffForHumans($transaction->processed_at) }}</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn--secondary btn--sm dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        @lang('Actions')
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="#" 
                                                               onclick="showTransactionDetails({{ $transaction->id }})">
                                                                <i class="las la-eye me-1"></i>
                                                                @lang('View Details')
                                                            </a>
                                                        </li>
                                                        @if($transaction->receipt_image)
                                                            <li>
                                                                <a class="dropdown-item" href="{{ getImage(getFilePath('productUploads') . '/' . $transaction->receipt_image) }}" 
                                                                   target="_blank">
                                                                    <i class="las la-file-image me-1"></i>
                                                                    @lang('View Receipt')
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if($transaction->product_image)
                                                            <li>
                                                                <a class="dropdown-item" href="{{ getImage(getFilePath('productUploads') . '/' . $transaction->product_image) }}" 
                                                                   target="_blank">
                                                                    <i class="las la-image me-1"></i>
                                                                    @lang('View Product')
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if($transaction->status == 'rejected' && $transaction->rejection_reason)
                                                            <li>
                                                                <a class="dropdown-item" href="#" 
                                                                   onclick="showRejectionReason('{{ $transaction->rejection_reason }}')">
                                                                    <i class="las la-info-circle me-1"></i>
                                                                    @lang('Rejection Reason')
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($transactions->hasPages())
                            <div class="mt-4">
                                {{ paginateLinks($transactions) }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="las la-inbox display-1 text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-3">@lang('No Transactions Found')</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['status', 'date_from', 'date_to']))
                                    @lang('No transactions match your filter criteria.')
                                @else
                                    @lang('You haven\'t submitted any rebate requests yet.')
                                @endif
                            </p>
                            @if(!request()->hasAny(['status', 'date_from', 'date_to']))
                                <a href="{{ route('user.rebate.submit') }}" class="btn btn--primary">
                                    <i class="las la-plus me-1"></i>
                                    @lang('Submit Your First Rebate')
                                </a>
                            @else
                                <a href="{{ route('user.rebate.history') }}" class="btn btn--secondary">
                                    <i class="las la-undo me-1"></i>
                                    @lang('Clear Filters')
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Transaction Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="transactionDetailsBody">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectionReasonModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Rejection Reason')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert--danger">
                        <i class="las la-exclamation-triangle me-2"></i>
                        <span id="rejectionReasonText"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .dropdown-menu {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 8px;
    }
    
    .dropdown-item {
        padding: 8px 16px;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    
    .transaction-details {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: #495057;
    }
    
    .detail-value {
        color: #6c757d;
    }
</style>
@endpush

@push('script')
<script>
    'use strict';
    
    function showTransactionDetails(transactionId) {
        // You can implement AJAX call to fetch transaction details
        $('#transactionDetailsModal').modal('show');
        $('#transactionDetailsBody').html('<div class="text-center"><i class="las la-spinner la-spin"></i> Loading...</div>');
        
        // Example implementation - replace with actual AJAX call
        setTimeout(() => {
            $('#transactionDetailsBody').html(`
                <div class="transaction-details">
                    <h6 class="mb-3">@lang('Transaction Information')</h6>
                    <div class="detail-row">
                        <span class="detail-label">@lang('Transaction ID'):</span>
                        <span class="detail-value">${transactionId}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">@lang('Status'):</span>
                        <span class="detail-value"><span class="badge bg--info">@lang('Pending')</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">@lang('Submitted'):</span>
                        <span class="detail-value">${new Date().toLocaleDateString()}</span>
                    </div>
                </div>
                <div class="text-center">
                    <small class="text-muted">@lang('For detailed information, please contact support.')</small>
                </div>
            `);
        }, 1000);
    }
    
    function showRejectionReason(reason) {
        $('#rejectionReasonText').text(reason);
        $('#rejectionReasonModal').modal('show');
    }
</script>
@endpush