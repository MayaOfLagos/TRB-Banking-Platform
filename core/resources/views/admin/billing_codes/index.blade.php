@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('ID')</th>
                                        <th>@lang('User')</th>
                                        <th>@lang('Code Type')</th>
                                        <th>@lang('Code')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Requirement')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Expires At')</th>
                                        <th>@lang('Created')</th>
                                        @if(can('admin.billing.codes.update') || can('admin.billing.codes.delete') || can('admin.billing.codes.mark.used'))
                                            <th>@lang('Actions')</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($billingCodes as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>
                                                <div>
                                                    <span class="fw-bold">{{ $item->user->fullname ?? "N/A" }}</span><br>
                                                </div>
                                            </td>
                                            <td><span class="badge badge--primary">{{ $item->code_type }}</span></td>
                                            <td><code class="text-primary">{{ $item->code }}</code></td>
                                            <td><span class="fw-bold">{{ gs()->cur_sym . showAmount($item->amount) }}</span></td>
                                            <td>{!! $item->getRequirementBadge() !!}</td>
                                            <td>{!! $item->getStatusBadge() !!}</td>
                                            <td>{{ $item->expires_at ? showDateTime($item->expires_at) : "Never" }}</td>
                                            <td>{{ showDateTime($item->created_at) }}</td>
                                            @if(can('admin.billing.codes.update') || can('admin.billing.codes.delete') || can('admin.billing.codes.mark.used'))
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn--light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="las la-ellipsis-v m-0"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @can('admin.billing.codes.update')
                                                                <a href="javascript:void(0)" class="dropdown-item editBtn" 
                                                                   data-id="{{ $item->id }}"
                                                                   data-user_id="{{ $item->user_id }}"
                                                                   data-code_type="{{ $item->code_type }}"
                                                                   data-code="{{ $item->code }}"
                                                                   data-amount="{{ $item->amount }}"
                                                                   data-description="{{ $item->description }}"
                                                                   data-status="{{ $item->status }}"
                                                                   data-is_required="{{ $item->is_required }}"
                                                                   data-expires_at="{{ $item->expires_at ? $item->expires_at->format('Y-m-d\TH:i') : '' }}">
                                                                    <i class="las la-edit"></i> @lang('Edit')
                                                                </a>
                                                            @endcan
                                                            
                                                            @if(can('admin.billing.codes.mark.used') && !$item->isUsed())
                                                                <form action="{{ route('admin.billing.codes.mark.used', $item->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item confirmationBtn"
                                                                            data-question="Are you sure to mark this code as used?"
                                                                            style="border: none; background: none; text-align: left; width: 100%;">
                                                                        <i class="las la-check-circle"></i> @lang('Mark as Used')
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            
                                                            @can('admin.users.detail')
                                                                <a href="{{ route('admin.users.detail', $item->user_id) }}" class="dropdown-item">
                                                                    <i class="las la-user"></i> @lang('View User')
                                                                </a>
                                                            @endcan
                                                            
                                                            @can('admin.billing.codes.delete')
                                                                <form action="{{ route('admin.billing.codes.delete', $item->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item confirmationBtn text-danger"
                                                                            data-question="Are you sure to delete this billing code?"
                                                                            style="border: none; background: none; text-align: left; width: 100%;">
                                                                        <i class="las la-trash"></i> @lang('Delete')
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">{{ __($emptyMessage ?? 'No billing codes found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit Billing Code')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Code Type') <span class="text-danger">*</span></label>
                                    <select class="form-control" name="code_type" required>
                                        <option value="">@lang('Select Type')</option>
                                        <option value="IMF">@lang('IMF - International Monetary Fund')</option>
                                        <option value="TAX">@lang('TAX - Tax Clearance Code')</option>
                                        <option value="COT">@lang('COT - Certificate of Transfer')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Code') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="code" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Amount') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs()->cur_sym }}</span>
                                        <input type="number" class="form-control" name="amount" required min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Status') <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status" required>
                                        <option value="1">@lang('Active')</option>
                                        <option value="0">@lang('Inactive')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Requirement') <span class="text-danger">*</span></label>
                                    <select class="form-control" name="is_required" required>
                                        <option value="1">@lang('Required')</option>
                                        <option value="0">@lang('Optional')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Expires At')</label>
                                    <input type="datetime-local" class="form-control" name="expires_at">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control" name="description" rows="3" maxlength="500"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2">
        <a href="{{ route('admin.billing.codes.index') }}" class="btn btn-outline--primary">
            <i class="las la-list"></i> @lang('All Codes')
        </a>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.editBtn').on('click', function() {
                var modal = $('#editModal');
                var data = $(this).data();

                modal.find('form').attr('action', '{{ route("admin.billing.codes.update", "") }}/' + data.id);
                modal.find('[name=code_type]').val(data.code_type);
                modal.find('[name=code]').val(data.code);
                modal.find('[name=amount]').val(data.amount);
                modal.find('[name=description]').val(data.description);
                modal.find('[name=status]').val(data.status);
                modal.find('[name=is_required]').val(data.is_required);
                modal.find('[name=expires_at]').val(data.expires_at);

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush