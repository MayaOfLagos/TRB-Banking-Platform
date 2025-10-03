@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Rebate Details') #{{ $rebate->id }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- User Information --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card border--primary">
                            <div class="card-header bg--primary">
                                <h6 class="card-title text-white mb-0">@lang('User Information')</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Username')</span>
                                        <span class="fw-bold">
                                            <a href="{{ route('admin.users.detail', $rebate->user->id ?? 0) }}" class="text--primary">
                                                {{ __(@$rebate->user->username) }}
                                            </a>
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Full Name')</span>
                                        <span class="fw-bold">{{ __(@$rebate->user->fullname) }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Email')</span>
                                        <span class="fw-bold">{{ __(@$rebate->user->email) }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Balance')</span>
                                        <span class="fw-bold text--success">{{ showAmount(@$rebate->user->balance) }} {{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Rebate Information --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card border--info">
                            <div class="card-header bg--info">
                                <h6 class="card-title text-white mb-0">@lang('Rebate Information')</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Type')</span>
                                                                            <td>
                                        <span class="badge badge--primary">{{ __(ucwords(str_replace('_', ' ', $rebate->transaction_type))) }}</span>
                                    </td>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Amount')</span>
                                        <span class="fw-bold text--primary">{{ showAmount($rebate->rebate_amount) }} {{ __($general->cur_text) }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Base Amount')</span>
                                                                            <td>
                                        <span>{{ showAmount($rebate->original_amount) }} {{ __($general->cur_text) }}</span>
                                    </td>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Tier Multiplier')</span>
                                        <span class="badge badge--success">{{ $rebate->tier_multiplier }}x</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Status')</span>
                                        @if($rebate->status == 'pending')
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif($rebate->status == 'approved')
                                            <span class="badge badge--success">@lang('Approved')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Rejected')</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Program Information --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card border--success">
                            <div class="card-header bg--success">
                                <h6 class="card-title text-white mb-0">@lang('Program Information')</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Program Name')</span>
                                        <span class="fw-bold">{{ __(@$rebate->rebateProgram->name) }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Category')</span>
                                        <span>{{ __(@$rebate->rebateProgram->rebateCategory->name) }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Transaction Type')</span>
                                        <span class="badge badge--dark">{{ __(ucwords(str_replace('_', ' ', $rebate->transaction_type))) }}</span>
                                    </div>
                                    @if(@$rebate->rebateProgram->default_rate > 0)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Default Rate')</span>
                                        <span>{{ @$rebate->rebateProgram->default_rate }}%</span>
                                    </div>
                                    @endif
                                    @if(@$rebate->rebateProgram->minimum_amount > 0)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Minimum Amount')</span>
                                        <span>{{ showAmount(@$rebate->rebateProgram->minimum_amount) }} {{ __($general->cur_text) }}</span>
                                    </div>
                                    @endif
                                    @if(@$rebate->rebateProgram->maximum_rebate > 0)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Maximum Rebate')</span>
                                        <span>{{ showAmount(@$rebate->rebateProgram->maximum_rebate) }} {{ __($general->cur_text) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Timestamps --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card border--dark">
                            <div class="card-header bg--dark">
                                <h6 class="card-title text-white mb-0">@lang('Timeline')</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Created At')</span>
                                        <div class="text-end">
                                            <div>{{ showDateTime($rebate->created_at) }}</div>
                                            <small class="text-muted">{{ diffForHumans($rebate->created_at) }}</small>
                                        </div>
                                    </div>
                                    @if($rebate->approved_at)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Approved At')</span>
                                        <div class="text-end">
                                            <div>{{ showDateTime($rebate->approved_at) }}</div>
                                            <small class="text-muted">{{ diffForHumans($rebate->approved_at) }}</small>
                                        </div>
                                    </div>
                                    @endif
                                    @if($rebate->rejected_at)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>@lang('Rejected At')</span>
                                        <div class="text-end">
                                            <div>{{ showDateTime($rebate->rejected_at) }}</div>
                                            <small class="text-muted">{{ diffForHumans($rebate->rejected_at) }}</small>
                                        </div>
                                    </div>
                                    @endif
                                    @if($rebate->rejection_reason)
                                    <div class="list-group-item">
                                        <span class="fw-bold text--danger">@lang('Rejection Reason')</span>
                                        <p class="mt-2 mb-0">{{ __($rebate->rejection_reason) }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product Upload Details --}}
                    @if($rebate->product_upload)
                    <div class="col-lg-12 mb-4">
                        <div class="card border--warning">
                            <div class="card-header bg--warning">
                                <h6 class="card-title text-white mb-0">@lang('Product Upload Details')</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>@lang('Product Name')</span>
                                                <span class="fw-bold">{{ __($rebate->product_upload->product_name ?: ($rebate->product_upload->description ?: ($rebate->product_upload->store_name ? 'Purchase from ' . $rebate->product_upload->store_name : 'Product Purchase'))) }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>@lang('Purchase Amount')</span>
                                                <span class="fw-bold">{{ showAmount($rebate->product_upload->purchase_amount ?? $rebate->product_upload->amount ?? 0) }} {{ __($general->cur_text) }}</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>@lang('Purchase Date')</span>
                                                <span>{{ showDateTime($rebate->product_upload->purchase_date) }}</span>
                                            </div>
                                            @if($rebate->product_upload->store_name)
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>@lang('Store Name')</span>
                                                <span>{{ __($rebate->product_upload->store_name) }}</span>
                                            </div>
                                            @endif
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>@lang('Upload Status')</span>
                                                <span class="badge badge--{{ $rebate->product_upload->status == 'approved' ? 'success' : ($rebate->product_upload->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ __(ucfirst($rebate->product_upload->status ?? 'unknown')) }}
                                                </span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>@lang('Uploaded At')</span>
                                                <span class="text-muted">{{ showDateTime($rebate->product_upload->created_at) }}</span>
                                            </div>
                                            @if($rebate->product_upload->verified_at)
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>@lang('Verified At')</span>
                                                <span class="text-success">{{ showDateTime($rebate->product_upload->verified_at) }}</span>
                                            </div>
                                            @endif
                                            @if($rebate->product_upload->rewarded_at)
                                            <div class="list-group-item d-flex justify-content-between">
                                                <span>@lang('Rewarded At')</span>
                                                <span class="text-success">{{ showDateTime($rebate->product_upload->rewarded_at) }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @if($rebate->product_upload->receipt_image)
                                        <div class="text-center">
                                            <label class="fw-bold">@lang('Receipt Image')</label>
                                            <div class="mt-2">
                                                <img src="{{ getImage(getFilePath('productUploads') . '/' . $rebate->product_upload->receipt_image, getFileSize('productUploads')) }}" 
                                                     alt="@lang('Receipt')" 
                                                     class="img-thumbnail"
                                                     style="max-height: 200px; cursor: pointer;"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#receiptModal">
                                            </div>
                                            <p class="text-muted mt-1 small">@lang('Click to enlarge')</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @if($rebate->product_upload->description)
                                <div class="mt-3">
                                    <label class="fw-bold">@lang('Product Description')</label>
                                    <p class="mt-1">{{ __($rebate->product_upload->description) }}</p>
                                </div>
                                @endif
                                
                                {{-- Additional Details Row --}}
                                <div class="row mt-3">
                                    @if($rebate->product_upload->quantity || $rebate->product_upload->submission_ip || $rebate->product_upload->file_hash)
                                    <div class="col-md-6">
                                        <div class="list-group list-group-flush">
                                            @if($rebate->product_upload->quantity)
                                            <div class="list-group-item d-flex justify-content-between border-0 px-0">
                                                <span class="fw-bold">@lang('Quantity'):</span>
                                                <span>{{ $rebate->product_upload->quantity }}</span>
                                            </div>
                                            @endif
                                            @if($rebate->product_upload->submission_ip)
                                            <div class="list-group-item d-flex justify-content-between border-0 px-0">
                                                <span class="fw-bold">@lang('Submission IP'):</span>
                                                <span class="text-muted">{{ $rebate->product_upload->submission_ip }}</span>
                                            </div>
                                            @endif
                                            @if($rebate->product_upload->file_hash)
                                            <div class="list-group-item d-flex justify-content-between border-0 px-0">
                                                <span class="fw-bold">@lang('File Hash'):</span>
                                                <span class="text-muted font-monospace small">{{ substr($rebate->product_upload->file_hash, 0, 16) }}...</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($rebate->product_upload->calculated_rebate || $rebate->product_upload->final_rebate_amount || $rebate->product_upload->admin_notes)
                                    <div class="col-md-6">
                                        <div class="list-group list-group-flush">
                                            @if($rebate->product_upload->calculated_rebate)
                                            <div class="list-group-item d-flex justify-content-between border-0 px-0">
                                                <span class="fw-bold">@lang('Calculated Rebate'):</span>
                                                <span>{{ showAmount($rebate->product_upload->calculated_rebate) }} {{ __($general->cur_text) }}</span>
                                            </div>
                                            @endif
                                            @if($rebate->product_upload->final_rebate_amount)
                                            <div class="list-group-item d-flex justify-content-between border-0 px-0">
                                                <span class="fw-bold">@lang('Final Rebate Amount'):</span>
                                                <span class="text-success">{{ showAmount($rebate->product_upload->final_rebate_amount) }} {{ __($general->cur_text) }}</span>
                                            </div>
                                            @endif
                                            @if($rebate->product_upload->admin_notes)
                                            <div class="list-group-item border-0 px-0">
                                                <span class="fw-bold">@lang('Admin Notes'):</span>
                                                <p class="mb-0 mt-1 text-info">{{ $rebate->product_upload->admin_notes }}</p>
                                            </div>
                                            @endif
                                            @if($rebate->product_upload->verifier)
                                            <div class="list-group-item border-0 px-0">
                                                <span class="fw-bold">@lang('Verified By'):</span>
                                                <span class="text-success">{{ $rebate->product_upload->verifier->username ?? $rebate->product_upload->verifier->fullname }}</span>
                                            </div>
                                            @endif
                                            @if($rebate->product_upload->rejection_reason)
                                            <div class="list-group-item border-0 px-0">
                                                <span class="fw-bold text-danger">@lang('Rejection Reason'):</span>
                                                <p class="mb-0 mt-1 text-danger">{{ $rebate->product_upload->rejection_reason }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Fraud Analysis --}}
                    @if($fraudAnalysis)
                    <div class="col-lg-12 mb-4">
                        <div class="card border--danger">
                            <div class="card-header bg--danger">
                                <h6 class="card-title text-white mb-0">@lang('Fraud Analysis')</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            @php
                                                $isValid = $fraudAnalysis['valid'] ?? true;
                                                $fraudScore = $fraudAnalysis['score'] ?? 0;
                                            @endphp
                                            <h4 class="text--{{ $isValid ? 'success' : 'danger' }}">
                                                {{ $fraudScore }}/100
                                            </h4>
                                            <p>@lang('Fraud Score')</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            @php
                                                $riskLevel = $fraudAnalysis['risk_level'] ?? 'low';
                                            @endphp
                                            <span class="badge badge--{{ $riskLevel == 'high' ? 'danger' : ($riskLevel == 'medium' ? 'warning' : 'success') }}">
                                                {{ __(ucfirst($riskLevel)) }} @lang('Risk')
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @if(isset($fraudAnalysis['flags']) && $fraudAnalysis['flags'])
                                        <div>
                                            <span class="fw-bold">@lang('Flags'):</span>
                                            @foreach($fraudAnalysis['flags'] as $flag)
                                                <span class="badge badge--dark">{{ __(ucwords(str_replace('_', ' ', $flag))) }}</span>
                                            @endforeach
                                        </div>
                                        @endif
                                        @if(isset($fraudAnalysis['reason']) && $fraudAnalysis['reason'])
                                        <div class="mt-2">
                                            <span class="fw-bold">@lang('Reason'):</span>
                                            <span class="text--danger">{{ __($fraudAnalysis['reason']) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Action Buttons --}}
                    @if($rebate->status == 'pending')
                    <div class="col-lg-12">
                        <div class="text-center">
                            <button class="btn btn--success btn-lg approveBtn" 
                                    data-id="{{ $rebate->id }}" 
                                    data-user="{{ @$rebate->user->username }}" 
                                    data-amount="{{ showAmount($rebate->rebate_amount) }}">
                                <i class="las la-check"></i> @lang('Approve Rebate')
                            </button>
                            <button class="btn btn--danger btn-lg rejectBtn" 
                                    data-id="{{ $rebate->id }}" 
                                    data-user="{{ @$rebate->user->username }}" 
                                    data-amount="{{ showAmount($rebate->rebate_amount) }}">
                                <i class="las la-times"></i> @lang('Reject Rebate')
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



{{-- Approve Modal --}}
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
                    <p class="fw-bold text--success">@lang('Are you sure you want to approve this rebate?')</p>
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
                    <div class="alert alert--info mt-3">
                        <i class="las la-info-circle"></i>
                        @lang('This will credit the amount to user\'s account and mark the rebate as approved.')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--success">@lang('Confirm Approval')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
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
                        <textarea name="rejection_reason" class="form-control" rows="4" placeholder="@lang('Enter detailed reason for rejection')" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--danger">@lang('Confirm Rejection')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.rebate.transactions.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-arrow-left"></i> @lang('Back to List')
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

    })(jQuery);
</script>

{{-- Receipt Image Modal --}}
@if($rebate->product_upload && $rebate->product_upload->receipt_image)
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">@lang('Receipt Image - Full Size')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ getImage(getFilePath('productUploads') . '/' . $rebate->product_upload->receipt_image) }}" 
                     alt="@lang('Receipt Image')" 
                     class="img-fluid">
                <div class="mt-3">
                    <a href="{{ getImage(getFilePath('productUploads') . '/' . $rebate->product_upload->receipt_image) }}" 
                       target="_blank" 
                       class="btn btn-outline--primary">
                        <i class="las la-external-link-alt"></i> @lang('Open in New Tab')
                    </a>
                    <a href="{{ getImage(getFilePath('productUploads') . '/' . $rebate->product_upload->receipt_image) }}" 
                       download 
                       class="btn btn-outline--success">
                        <i class="las la-download"></i> @lang('Download')
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endpush