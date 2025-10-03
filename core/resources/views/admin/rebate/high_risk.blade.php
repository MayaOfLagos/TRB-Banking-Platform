@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">@lang('High Risk Rebate Transactions')</h6>
                        <small class="text-muted">@lang('Rebates flagged for manual review due to high fraud risk')</small>
                    </div>
                    <div class="gap-2">
                        <span class="badge badge--danger">
                            <i class="fas fa-shield-alt"></i>
                            {{ $rebates->total() }} @lang('High Risk')
                        </span>
                        <a href="{{ route('admin.rebate.transactions.index') }}" class="btn btn--primary btn-sm">
                            <i class="fas fa-list"></i> @lang('All Transactions')
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Program')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Risk Level')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Actions')</th>
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
                                                <div class="content">
                                                    <h6 class="title">
                                                        <a href="{{ route('admin.users.detail', $rebate->user_id) }}">{{ __($rebate->user->fullname) }}</a>
                                                    </h6>
                                                    <span class="small">
                                                        <a href="mailto:{{ $rebate->user->email }}">{{ $rebate->user->email }}</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __(@$rebate->rebateProgram->name ?? 'N/A') }}</span><br>
                                            <small class="text-muted">{{ __(@$rebate->rebateCategory->name ?? 'N/A') }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($rebate->final_amount) }} {{ __($general->cur_text) }}</span><br>
                                            <small class="text-muted">@lang('Purchase'): {{ showAmount($rebate->purchase_amount) }} {{ __($general->cur_text) }}</small>
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
                                            @if($rebate->review_notes)
                                                <br><button type="button" class="btn btn--dark btn-sm mt-1 notesBtn" 
                                                        data-id="{{ $rebate->id }}"
                                                        data-notes="{{ $rebate->review_notes }}"
                                                        data-user="{{ $rebate->user->fullname }}"
                                                        data-bs-toggle="offcanvas" 
                                                        data-bs-target="#notesOffcanvas">
                                                    <i class="las la-sticky-note"></i> @lang('Notes')
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rebate->status === 'flagged')
                                                <span class="badge badge--danger">
                                                    <i class="fas fa-exclamation-triangle"></i> @lang('Flagged')
                                                </span>
                                            @elseif($rebate->status === 'rejected')
                                                <span class="badge badge--warning">
                                                    <i class="fas fa-times-circle"></i> @lang('Rejected')
                                                </span>
                                            @else
                                                <span class="badge badge--info">
                                                    <i class="fas fa-shield-alt"></i> @lang('Review')
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showDateTime($rebate->created_at) }}</span><br>
                                            <small class="text-muted">{{ diffForHumans($rebate->created_at) }}</small>
                                        </td>
                                        <td>
                                            <div class="button-group">
                                                <a href="{{ route('admin.rebate.transactions.show', $rebate->id) }}" class="btn btn--primary btn-sm">
                                                    <i class="las la-desktop"></i> @lang('Details')
                                                </a>
                                                
                                                @if($rebate->status == 'pending')
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn--success btn-sm approveBtn" 
                                                                data-id="{{ $rebate->id }}" 
                                                                data-user="{{ $rebate->user->fullname }}"
                                                                data-amount="{{ showAmount($rebate->final_amount) }} {{ __($general->cur_text) }}">
                                                            <i class="las la-check"></i> @lang('Approve')
                                                        </button>
                                                        <button type="button" class="btn btn--danger btn-sm rejectBtn" 
                                                                data-id="{{ $rebate->id }}" 
                                                                data-user="{{ $rebate->user->fullname }}"
                                                                data-amount="{{ showAmount($rebate->final_amount) }} {{ __($general->cur_text) }}">
                                                            <i class="las la-times"></i> @lang('Reject')
                                                        </button>
                                                    </div>
                                                @endif
                                                
                                                @if(in_array($rebate->status, ['pending', 'rejected', 'flagged', 'failed']) && $rebate->product_upload)
                                                    <button type="button" class="btn btn--info btn-sm reprocessBtn" 
                                                            data-id="{{ $rebate->id }}">
                                                        <i class="las la-redo"></i> @lang('Reprocess')
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
                @if($rebates->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($rebates) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Approve Rebate Transaction')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to approve this rebate transaction?')</p>
                        <div class="form-group">
                            <strong>@lang('User'): </strong> <span class="user-name"></span>
                        </div>
                        <div class="form-group">
                            <strong>@lang('Amount'): </strong> <span class="rebate-amount"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success">@lang('Approve')</button>
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
                    <h5 class="modal-title">@lang('Reject Rebate Transaction')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to reject this rebate transaction?')</p>
                        <div class="form-group">
                            <strong>@lang('User'): </strong> <span class="user-name"></span>
                        </div>
                        <div class="form-group">
                            <strong>@lang('Amount'): </strong> <span class="rebate-amount"></span>
                        </div>
                        <div class="form-group">
                            <label>@lang('Rejection Reason')</label>
                            <textarea name="rejection_reason" class="form-control" rows="4" placeholder="@lang('Enter reason for rejection')" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Reject')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reprocess Modal --}}
    <div id="reprocessModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reprocess Rebate Transaction')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('This will reprocess the rebate transaction and run it through the fraud detection system again.')</p>
                        <div class="alert alert--warning">
                            @lang('Reprocessing may change the rebate amount or approval status based on current program settings.')
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--info">@lang('Reprocess')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Notes Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="notesOffcanvas" aria-labelledby="notesOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="notesOffcanvasLabel">@lang('Admin Notes')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-3">
                <h6 class="text-muted">@lang('Transaction ID'): <span id="noteTransactionId" class="text-primary"></span></h6>
                <h6 class="text-muted">@lang('User'): <span id="noteUserName" class="text-info"></span></h6>
            </div>
            <hr>
            <div class="notes-content">
                <h6 class="mb-3">@lang('Review Notes'):</h6>
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <p id="noteContent" class="mb-0 text-dark"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        
        $('.approveBtn').on('click', function () {
            var modal = $('#approveModal');
            var id = $(this).data('id');
            var user = $(this).data('user');
            var amount = $(this).data('amount');
            
            modal.find('.user-name').text(user);
            modal.find('.rebate-amount').text(amount);
            modal.find('form').attr('action', `{{ route('admin.rebate.transactions.approve', 'REBATE_ID') }}`.replace('REBATE_ID', id));
            modal.modal('show');
        });

        $('.rejectBtn').on('click', function () {
            var modal = $('#rejectModal');
            var id = $(this).data('id');
            var user = $(this).data('user');
            var amount = $(this).data('amount');
            
            modal.find('.user-name').text(user);
            modal.find('.rebate-amount').text(amount);
            modal.find('form').attr('action', `{{ route('admin.rebate.transactions.reject', 'REBATE_ID') }}`.replace('REBATE_ID', id));
            modal.modal('show');
        });

        $('.reprocessBtn').on('click', function () {
            var modal = $('#reprocessModal');
            var id = $(this).data('id');
            
            modal.find('form').attr('action', `{{ route('admin.rebate.transactions.reprocess', 'TRANSACTION_ID') }}`.replace('TRANSACTION_ID', id));
            modal.modal('show');
        });

        $('.notesBtn').on('click', function () {
            var id = $(this).data('id');
            var notes = $(this).data('notes');
            var user = $(this).data('user');
            
            $('#noteTransactionId').text('#' + id);
            $('#noteUserName').text(user);
            $('#noteContent').text(notes);
        });

    })(jQuery);
</script>
@endpush