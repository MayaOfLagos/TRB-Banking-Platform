@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Pending Rebates for Review')</h5>
                <div class="card-header-right">
                    <button class="btn btn-sm btn--success" id="bulkApproveBtn" disabled>
                        <i class="las la-check-double"></i> @lang('Bulk Approve')
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table--light style--two table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>@lang('User')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Program')</th>
                                <th>@lang('Submitted')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rebates as $rebate)
                                <tr class="rebate-row" data-id="{{ $rebate->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input rebate-checkbox" value="{{ $rebate->id }}">
                                    </td>
                                    <td>
                                        <div class="user">
                                            <div class="thumb">
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . @$rebate->user->image, getFileSize('userProfile')) }}" alt="@lang('image')">
                                            </div>
                                            <span class="name">
                                                <a href="{{ route('admin.users.detail', $rebate->user->id ?? 0) }}">
                                                    {{ __(@$rebate->user->fullname) }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ __(@$rebate->user->username) }}</small>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge--{{ $rebate->transaction_type == 'product_upload' ? 'primary' : ($rebate->transaction_type == 'referral' ? 'info' : 'success') }}">
                                            {{ __(ucwords(str_replace('_', ' ', $rebate->transaction_type))) }}
                                        </span>
                                        @if($rebate->status === 'flagged' || $rebate->review_notes)
                                            <br><small class="text--danger"><i class="las la-exclamation-triangle"></i> @lang('High Risk')</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text--primary">{{ showAmount($rebate->rebate_amount) }} {{ __($general->cur_text) }}</span>
                                        @if($rebate->original_amount != $rebate->rebate_amount)
                                            <br>
                                            <small class="text-muted">
                                                @lang('Base'): {{ showAmount($rebate->original_amount) }}
                                                @if($rebate->tier_multiplier > 1)
                                                    <span class="badge badge--success">{{ $rebate->tier_multiplier }}x</span>
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ __(@$rebate->rebateProgram->name ?? 'N/A') }}</span>
                                        <br>
                                        <small class="text-muted">{{ __(@$rebate->rebateProgram->rebateCategory?->name ?? 'No Category') }}</small>
                                    </td>
                                    <td>
                                        <div>{{ showDateTime($rebate->created_at, 'd M Y') }}</div>
                                        <small class="text--info">{{ diffForHumans($rebate->created_at) }}</small>
                                        @php
                                            $hoursSince = $rebate->created_at->diffInHours(now());
                                        @endphp
                                        @if($hoursSince > 24)
                                            <br><small class="text--warning"><i class="las la-clock"></i> @lang('Overdue')</small>
                                        @elseif($hoursSince > 12)
                                            <br><small class="text--info"><i class="las la-clock"></i> @lang('Urgent')</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.rebate.transactions.show', $rebate->id) }}" class="btn btn-sm btn-outline--info" title="@lang('View Details')">
                                                <i class="las la-desktop"></i>
                                            </a>
                                            
                                            <button class="btn btn-sm btn-outline--success quickApproveBtn" 
                                                    data-id="{{ $rebate->id }}" 
                                                    data-user="{{ @$rebate->user->username }}" 
                                                    data-amount="{{ showAmount($rebate->rebate_amount) }}"
                                                    title="@lang('Quick Approve')">
                                                <i class="las la-check"></i>
                                            </button>
                                            
                                            <button class="btn btn-sm btn-outline--danger quickRejectBtn" 
                                                    data-id="{{ $rebate->id }}" 
                                                    data-user="{{ @$rebate->user->username }}" 
                                                    data-amount="{{ showAmount($rebate->rebate_amount) }}"
                                                    title="@lang('Quick Reject')">
                                                <i class="las la-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        <div class="text-center py-4">
                                            <i class="las la-check-circle text--success" style="font-size: 3rem;"></i>
                                            <h5 class="mt-2">@lang('No Pending Rebates')</h5>
                                            <p class="text-muted">@lang('All rebates have been processed!')</p>
                                        </div>
                                    </td>
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

{{-- Quick Stats --}}
<div class="row mb-30 mt-30">
    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
            <div class="widget-two__icon">
                <i class="las la-clock"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $rebates->total() }}</h3>
                <p class="text-white">@lang('Pending Reviews')</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--warning">
            <div class="widget-two__icon">
                <i class="las la-exclamation-triangle"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $rebates->where('status', 'flagged')->count() }}</h3>
                <p class="text-white">@lang('High Risk')</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--info">
            <div class="widget-two__icon">
                <i class="las la-money-bill"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ showAmount($rebates->sum('rebate_amount')) }}</h3>
                <p class="text-white">@lang('Total Value')</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--danger">
            <div class="widget-two__icon">
                <i class="las la-stopwatch"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $rebates->where('created_at', '<=', now()->subHours(24))->count() }}</h3>
                <p class="text-white">@lang('Overdue (>24h)')</p>
            </div>
        </div>
    </div>
</div>

{{-- Quick Action Filters --}}
<div class="row mb-30">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('Filter by Type')</label>
                            <select class="form-control filter-select" id="typeFilter">
                                <option value="">@lang('All Types')</option>
                                <option value="product_upload">@lang('Product Upload')</option>
                                <option value="referral">@lang('Referral')</option>
                                <option value="loyalty">@lang('Loyalty')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('Filter by Risk')</label>
                            <select class="form-control filter-select" id="riskFilter">
                                <option value="">@lang('All Risk Levels')</option>
                                <option value="high">@lang('High Risk Only')</option>
                                <option value="normal">@lang('Normal Risk Only')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('Sort by Age')</label>
                            <select class="form-control filter-select" id="ageFilter">
                                <option value="">@lang('All Ages')</option>
                                <option value="overdue">@lang('Overdue (>24h)')</option>
                                <option value="urgent">@lang('Urgent (>12h)')</option>
                                <option value="recent">@lang('Recent (<6h)')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <button class="btn btn--primary w-100" id="clearFilters">
                                <i class="las la-sync"></i> @lang('Clear Filters')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Actions Modal --}}
<div id="bulkApproveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Bulk Approve Rebates')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.rebate.transactions.bulk.approve') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert--warning">
                        <i class="las la-exclamation-triangle"></i>
                        @lang('You are about to approve') <span class="fw-bold" id="selectedCount">0</span> @lang('rebates')
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody id="selectedRebatesList">
                                <!-- Selected rebates will be populated here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <strong>@lang('Total Amount'): <span id="totalAmount" class="text--primary">0</span></strong>
                    </div>

                    <div id="bulkRebateIds"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--success">@lang('Approve All Selected')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Quick Approve Modal --}}
<div id="quickApproveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Quick Approve')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body text-center">
                    <i class="las la-check-circle text--success" style="font-size: 3rem;"></i>
                    <h6 class="mt-2">@lang('Approve this rebate?')</h6>
                    <p class="quick-approve-details"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn-sm" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--success btn-sm">@lang('Approve')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Quick Reject Modal --}}
<div id="quickRejectModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Quick Reject')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="quick-reject-details"></p>
                    
                    <div class="form-group mt-3">
                        <label class="fw-bold">@lang('Quick Rejection Reason')</label>
                        <select name="rejection_reason" class="form-control" required>
                            <option value="">@lang('Select Reason')</option>
                            <option value="Invalid receipt">@lang('Invalid receipt')</option>
                            <option value="Duplicate submission">@lang('Duplicate submission')</option>
                            <option value="Product not eligible">@lang('Product not eligible')</option>
                            <option value="Insufficient information">@lang('Insufficient information')</option>
                            <option value="Fraud detected">@lang('Fraud detected')</option>
                            <option value="Other">@lang('Other (see notes)')</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>@lang('Additional Notes')</label>
                        <textarea name="additional_notes" class="form-control" rows="3" placeholder="@lang('Optional additional notes')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--danger">@lang('Reject')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.rebate.transactions.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-list"></i> @lang('All Rebates')
    </a>
    <a href="{{ route('admin.rebate.transactions.high.risk') }}" class="btn btn-sm btn-outline--danger">
        <i class="las la-exclamation-triangle"></i> @lang('High Risk Only')
    </a>
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';

        // Select All functionality
        $('#selectAll').on('change', function() {
            $('.rebate-checkbox').prop('checked', $(this).is(':checked'));
            updateBulkButtons();
        });

        // Individual checkbox functionality
        $(document).on('change', '.rebate-checkbox', function() {
            updateBulkButtons();
        });

        // Update bulk action buttons
        function updateBulkButtons() {
            var selectedCount = $('.rebate-checkbox:checked').length;
            $('#bulkApproveBtn').prop('disabled', selectedCount === 0);
            
            if (selectedCount === 0) {
                $('#selectAll').prop('indeterminate', false);
            } else if (selectedCount === $('.rebate-checkbox').length) {
                $('#selectAll').prop('checked', true).prop('indeterminate', false);
            } else {
                $('#selectAll').prop('checked', false).prop('indeterminate', true);
            }
        }

        // Bulk approve functionality
        $('#bulkApproveBtn').on('click', function() {
            var selectedRebates = [];
            var totalAmount = 0;
            
            $('.rebate-checkbox:checked').each(function() {
                var row = $(this).closest('tr');
                var rebateId = parseInt($(this).val());
                var user = row.find('.name a').text().trim();
                var type = row.find('td:nth-child(3) .badge').text().trim();
                var amount = parseFloat(row.find('td:nth-child(4)').text().replace(/[^\d.-]/g, ''));
                
                // Only add if rebateId is valid
                if (rebateId && !isNaN(rebateId)) {
                    selectedRebates.push({
                        id: rebateId,
                        user: user,
                        type: type,
                        amount: amount
                    });
                    
                    totalAmount += amount;
                }
            });

            $('#selectedCount').text(selectedRebates.length);
            $('#totalAmount').text(totalAmount.toFixed(2) + ' {{ $general->cur_text }}');
            
            // Populate selected rebates list
            var listHtml = '';
            selectedRebates.forEach(function(rebate) {
                listHtml += `
                    <tr>
                        <td>${rebate.user}</td>
                        <td><span class="badge badge--primary badge-sm">${rebate.type}</span></td>
                        <td>${rebate.amount.toFixed(2)} {{ $general->cur_text }}</td>
                    </tr>
                `;
            });
            $('#selectedRebatesList').html(listHtml);
            
            // Create hidden inputs for rebate IDs
            var idsHtml = '';
            selectedRebates.forEach(function(rebate) {
                idsHtml += `<input type="hidden" name="rebate_ids[]" value="${rebate.id}">`;
            });
            $('#bulkRebateIds').html(idsHtml);
            
            $('#bulkApproveModal').modal('show');
        });

        // Quick approve
        $('.quickApproveBtn').on('click', function() {
            var modal = $('#quickApproveModal');
            var rebateId = $(this).data('id');
            var user = $(this).data('user');
            var amount = $(this).data('amount');
            
            modal.find('form').attr('action', `{{ route('admin.rebate.transactions.approve', 'REBATE_ID') }}`.replace('REBATE_ID', rebateId));
            modal.find('.quick-approve-details').html(`<strong>${user}</strong><br>${amount} {{ $general->cur_text }}`);
            modal.modal('show');
        });

        // Quick reject
        $('.quickRejectBtn').on('click', function() {
            var modal = $('#quickRejectModal');
            var rebateId = $(this).data('id');
            var user = $(this).data('user');
            var amount = $(this).data('amount');
            
            modal.find('form').attr('action', `{{ route('admin.rebate.transactions.reject', 'REBATE_ID') }}`.replace('REBATE_ID', rebateId));
            modal.find('.quick-reject-details').html(`<strong>User:</strong> ${user}<br><strong>Amount:</strong> ${amount} {{ $general->cur_text }}`);
            modal.modal('show');
        });

        // Filtering functionality
        $('.filter-select').on('change', function() {
            applyFilters();
        });

        $('#clearFilters').on('click', function() {
            $('.filter-select').val('');
            $('.rebate-row').show();
        });

        function applyFilters() {
            var typeFilter = $('#typeFilter').val();
            var riskFilter = $('#riskFilter').val();
            var ageFilter = $('#ageFilter').val();

            $('.rebate-row').each(function() {
                var row = $(this);
                var show = true;

                // Type filter
                if (typeFilter && !row.find('.badge').text().toLowerCase().includes(typeFilter.replace('_', ' '))) {
                    show = false;
                }

                // Risk filter
                if (riskFilter) {
                    var hasHighRisk = row.find('.text--danger').length > 0;
                    if (riskFilter === 'high' && !hasHighRisk) show = false;
                    if (riskFilter === 'normal' && hasHighRisk) show = false;
                }

                // Age filter - would need server-side data for accurate filtering
                // This is a simplified client-side implementation

                if (show) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        }

        // Auto-refresh every 2 minutes for new pending rebates
        setInterval(function() {
            if (!$('.modal:visible').length) {
                location.reload();
            }
        }, 120000);

    })(jQuery);
</script>
@endpush