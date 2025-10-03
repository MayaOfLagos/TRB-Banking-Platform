@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table--light style--two table">
                        <thead>
                            <tr>
                                <th>@lang('Program')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Rate')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Members')</th>
                                <th>@lang('Total Paid')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programs as $program)
                                <tr>
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ __($program->name) }}</span>
                                            <br>
                                            <small class="text-muted">{{ Str::limit(__($program->description), 50) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge--primary">{{ __(@$program->rebateCategory->name) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text--success">{{ $program->default_rate }}%</span>
                                        @if($program->maximum_rebate)
                                            <br><small class="text-muted">@lang('Max'): {{ showAmount($program->maximum_rebate) }} {{ __($general->cur_text) }}</small>
                                        @endif
                                        @if($program->minimum_amount)
                                            <br><small class="text-muted">@lang('Min'): {{ showAmount($program->minimum_amount) }} {{ __($general->cur_text) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $isActive = $program->is_active;
                                            $isScheduled = $program->starts_at && $program->starts_at > now();
                                            $isExpired = $program->ends_at && $program->ends_at < now();
                                            
                                            if (!$isActive) {
                                                $badgeClass = 'danger';
                                                $statusText = 'Inactive';
                                            } elseif ($isExpired) {
                                                $badgeClass = 'secondary';
                                                $statusText = 'Expired';
                                            } elseif ($isScheduled) {
                                                $badgeClass = 'warning';
                                                $statusText = 'Scheduled';
                                            } else {
                                                $badgeClass = 'success';
                                                $statusText = 'Active';
                                            }
                                        @endphp
                                        <span class="badge badge--{{ $badgeClass }}">@lang($statusText)</span>
                                        
                                        @if($program->starts_at && $program->ends_at)
                                            <br>
                                            <small class="text-muted">
                                                {{ showDateTime($program->starts_at, 'd M Y') }} - {{ showDateTime($program->ends_at, 'd M Y') }}
                                            </small>
                                        @elseif($program->starts_at)
                                            <br>
                                            <small class="text-muted">
                                                @lang('Starts'): {{ showDateTime($program->starts_at, 'd M Y') }}
                                            </small>
                                        @elseif($program->ends_at)
                                            <br>
                                            <small class="text-muted">
                                                @lang('Ends'): {{ showDateTime($program->ends_at, 'd M Y') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-bold text--{{ $program->isUsingManualMembersCount() ? 'primary' : 'dark' }}">
                                                {{ number_format($program->getEffectiveMembersCount()) }}
                                            </span>
                                            @if($program->isUsingManualMembersCount())
                                                <span class="badge badge--primary badge-sm">@lang('Manual')</span>
                                            @else
                                                <span class="badge badge--secondary badge-sm">@lang('System')</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                @lang('Active Users'): {{ number_format($program->user_rebates_count ?? 0) }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text--primary">
                                            {{ showAmount($program->user_rebates_sum_rebate_amount ?? 0) }} {{ __($general->cur_text) }}
                                        </span>
                                        @if($program->monthly_limit)
                                            <br>
                                            <small class="text-muted">
                                                @lang('Monthly Limit'): {{ showAmount($program->monthly_limit) }} {{ __($general->cur_text) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.rebate.programs.show', $program->id) }}" 
                                               class="btn btn-sm btn-outline--primary" 
                                               title="@lang('View Details')">
                                                <i class="las la-desktop"></i>
                                            </a>
                                            
                                            <a href="{{ route('admin.rebate.programs.edit', $program->id) }}" 
                                               class="btn btn-sm btn-outline--success" 
                                               title="@lang('Edit')">
                                                <i class="las la-pen"></i>
                                            </a>
                                            
                                            @if($program->is_active)
                                                <button class="btn btn-sm btn-outline--warning confirmationBtn" 
                                                        data-question="@lang('Are you sure to deactivate this program?')" 
                                                        data-action="{{ route('admin.rebate.programs.toggle.status', $program->id) }}"
                                                        data-method="POST"
                                                        title="@lang('Deactivate')">
                                                    <i class="las la-eye-slash"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--success confirmationBtn" 
                                                        data-question="@lang('Are you sure to activate this program?')" 
                                                        data-action="{{ route('admin.rebate.programs.toggle.status', $program->id) }}"
                                                        data-method="POST"
                                                        title="@lang('Activate')">
                                                    <i class="las la-eye"></i>
                                                </button>
                                            @endif

                                            @if($program->user_rebates_count == 0)
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" 
                                                        data-question="@lang('Are you sure to delete this program?')" 
                                                        data-action="{{ route('admin.rebate.programs.destroy', $program->id) }}"
                                                        data-method="DELETE"
                                                        title="@lang('Delete')">
                                                    <i class="las la-trash"></i>
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
            @if ($programs->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($programs) }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row mt-30">
    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
            <div class="widget-two__icon">
                <i class="las la-list"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $totalPrograms }}</h3>
                <p class="text-white">@lang('Total Programs')</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--success">
            <div class="widget-two__icon">
                <i class="las la-play-circle"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ $activePrograms }}</h3>
                <p class="text-white">@lang('Active Programs')</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--info">
            <div class="widget-two__icon">
                <i class="las la-users"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ number_format($totalUsers) }}</h3>
                <p class="text-white">@lang('Total Participants')</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 mb-30">
        <div class="widget-two style--two box--shadow2 b-radius--5 bg--warning">
            <div class="widget-two__icon">
                <i class="las la-money-bill-wave"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="text-white">{{ showAmount($totalPaid) }}</h3>
                <p class="text-white">@lang('Total Paid')</p>
            </div>
        </div>
    </div>
</div>

{{-- Program Categories Summary --}}
<div class="row mt-30">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">@lang('Programs by Category')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table--light style--two table">
                        <thead>
                            <tr>
                                <th>@lang('Category')</th>
                                <th>@lang('Programs')</th>
                                <th>@lang('Active')</th>
                                <th>@lang('Participants')</th>
                                <th>@lang('Total Paid')</th>
                                <th>@lang('Avg. Rate')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryStats as $stat)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ __($stat->name) }}</span>
                                        <br>
                                        <small class="text-muted">{{ Str::limit(__($stat->description), 40) }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $stat->programs_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge--success">{{ $stat->active_programs_count }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($stat->total_participants) }}</span>
                                    </td>
                                    <td>
                                        <span class="text--primary fw-bold">
                                            {{ showAmount($stat->total_paid) }} {{ $general->cur_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($stat->avg_rate, 2) }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="6">@lang('No categories found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modal --}}
<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST" id="methodField">
                <div class="modal-body">
                    <p class="question"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    <a href="{{ route('admin.rebate.programs.create') }}" class="btn btn-sm btn--primary">
        <i class="las la-plus"></i>@lang('Add New Program')
    </a>
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';

        $('.confirmationBtn').on('click', function () {
            var modal = $('#confirmationModal');
            var data = $(this).data();
            
            modal.find('.question').text(`${data.question}`);
            modal.find('form').attr('action', `${data.action}`);
            
            // Set the correct HTTP method based on data-method attribute
            var method = data.method || 'POST';
            modal.find('#methodField').val(method);
            
            modal.modal('show');
        });

    })(jQuery);
</script>
@endpush