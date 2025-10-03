@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        {{-- Fraud Alert Summary --}}
        <div class="row mb-30">
            <div class="col-lg-3 col-sm-6 mb-30">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--danger">
                    <div class="widget-two__icon">
                        <i class="las la-exclamation-triangle"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $fraudStats['high_risk'] }}</h3>
                        <p class="text-white">@lang('High Risk Alerts')</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 mb-30">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--warning">
                    <div class="widget-two__icon">
                        <i class="las la-shield-alt"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $fraudStats['medium_risk'] }}</h3>
                        <p class="text-white">@lang('Medium Risk')</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 mb-30">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--info">
                    <div class="widget-two__icon">
                        <i class="las la-user-shield"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ $fraudStats['flagged_users'] }}</h3>
                        <p class="text-white">@lang('Flagged Users')</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 mb-30">
                <div class="widget-two style--two box--shadow2 b-radius--5 bg--success">
                    <div class="widget-two__icon">
                        <i class="las la-money-bill-wave"></i>
                    </div>
                    <div class="widget-two__content">
                        <h3 class="text-white">{{ showAmount($fraudStats['blocked_amount']) }}</h3>
                        <p class="text-white">@lang('Amount Blocked')</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-30">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label>@lang('Risk Level')</label>
                            <select name="risk_level" class="form-control">
                                <option value="">@lang('All Levels')</option>
                                <option value="high" @selected(request('risk_level') == 'high')>@lang('High Risk')</option>
                                <option value="medium" @selected(request('risk_level') == 'medium')>@lang('Medium Risk')</option>
                                <option value="low" @selected(request('risk_level') == 'low')>@lang('Low Risk')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label>@lang('Flag Type')</label>
                            <select name="flag_type" class="form-control">
                                <option value="">@lang('All Types')</option>
                                <option value="duplicate_submission" @selected(request('flag_type') == 'duplicate_submission')>@lang('Duplicate')</option>
                                <option value="unusual_pattern" @selected(request('flag_type') == 'unusual_pattern')>@lang('Pattern')</option>
                                <option value="high_velocity" @selected(request('flag_type') == 'high_velocity')>@lang('Velocity')</option>
                                <option value="suspicious_ip" @selected(request('flag_type') == 'suspicious_ip')>@lang('Suspicious IP')</option>
                                <option value="fake_receipt" @selected(request('flag_type') == 'fake_receipt')>@lang('Fake Receipt')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <select name="status" class="form-control">
                                <option value="">@lang('All Status')</option>
                                <option value="pending" @selected(request('status') == 'pending')>@lang('Pending')</option>
                                <option value="investigating" @selected(request('status') == 'investigating')>@lang('Investigating')</option>
                                <option value="resolved" @selected(request('status') == 'resolved')>@lang('Resolved')</option>
                                <option value="false_positive" @selected(request('status') == 'false_positive')>@lang('False Positive')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>@lang('Date Range')</label>
                            <input name="date_range" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control" value="{{ request('date_range') }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn--primary flex-fill">
                                <i class="las la-search"></i> @lang('Filter')
                            </button>
                            <a href="{{ route('admin.fraud.index') }}" class="btn btn--secondary">
                                <i class="las la-sync"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Fraud Logs Table --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Fraud Detection Logs')</h5>
                <div class="card-header-right">
                    <button class="btn btn-sm btn--warning" id="bulkInvestigateBtn" disabled>
                        <i class="las la-search"></i> @lang('Bulk Investigate')
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
                                <th>@lang('Risk Level')</th>
                                <th>@lang('Flag Type')</th>
                                <th>@lang('Rebate')</th>
                                <th>@lang('Details')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Detected')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fraudLogs as $log)
                                <tr class="fraud-row" data-id="{{ $log->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input fraud-checkbox" value="{{ $log->id }}">
                                    </td>
                                    <td>
                                        <div class="user">
                                            <div class="thumb">
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . @$log->user->image, getFileSize('userProfile')) }}" alt="@lang('image')">
                                            </div>
                                            <span class="name">
                                                <a href="{{ route('admin.users.detail', $log->user->id ?? 0) }}">
                                                    {{ __(@$log->user->fullname) }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ __(@$log->user->username) }}</small>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $riskClass = match($log->risk_level) {
                                                'high' => 'danger',
                                                'medium' => 'warning',
                                                'low' => 'info',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge badge--{{ $riskClass }}">
                                            {{ __(ucfirst($log->risk_level)) }} @lang('Risk')
                                        </span>
                                        <br>
                                        <small class="text-muted">@lang('Score'): {{ $log->fraud_score }}/100</small>
                                    </td>
                                    <td>
                                        <span class="badge badge--primary">
                                            {{ __(str_replace('_', ' ', Str::title($log->flag_type))) }}
                                        </span>
                                        @if($log->auto_flagged)
                                            <br><small class="text--warning"><i class="las la-robot"></i> @lang('Auto-detected')</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->userRebate)
                                            <div>
                                                <span class="fw-bold text--primary">{{ showAmount($log->userRebate->rebate_amount) }} {{ $general->cur_text }}</span>
                                                <br>
                                                <small class="text-muted">{{ __(@$log->userRebate->rebateProgram->name) }}</small>
                                                <br>
                                                <a href="{{ route('admin.rebate.show', $log->userRebate->id) }}" class="text--info">
                                                    <small><i class="las la-external-link-alt"></i> @lang('View Rebate')</small>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">@lang('N/A')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fraud-details">
                                            @php
                                                $details = json_decode($log->details, true) ?? [];
                                            @endphp
                                            
                                            @if(isset($details['ip_address']))
                                                <small><strong>@lang('IP'):</strong> {{ $details['ip_address'] }}</small><br>
                                            @endif
                                            
                                            @if(isset($details['user_agent']))
                                                <small><strong>@lang('Device'):</strong> {{ Str::limit($details['user_agent'], 30) }}</small><br>
                                            @endif
                                            
                                            @if(isset($details['submission_count']))
                                                <small><strong>@lang('Submissions'):</strong> {{ $details['submission_count'] }} @lang('in') {{ $details['time_window'] ?? '24h' }}</small><br>
                                            @endif
                                            
                                            @if(isset($details['similarity_score']))
                                                <small><strong>@lang('Similarity'):</strong> {{ $details['similarity_score'] }}%</small><br>
                                            @endif
                                            
                                            @if($log->reason)
                                                <small class="text--danger"><strong>@lang('Reason'):</strong> {{ $log->reason }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($log->status) {
                                                'pending' => 'warning',
                                                'investigating' => 'info',
                                                'resolved' => 'success',
                                                'false_positive' => 'secondary',
                                                default => 'dark'
                                            };
                                        @endphp
                                        <span class="badge badge--{{ $statusClass }}">
                                            {{ __(str_replace('_', ' ', Str::title($log->status))) }}
                                        </span>
                                        
                                        @if($log->investigated_by)
                                            <br>
                                            <small class="text-muted">
                                                @lang('By'): {{ __(@$log->investigatedBy->name) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ showDateTime($log->created_at, 'd M Y') }}</div>
                                        <small class="text--info">{{ showDateTime($log->created_at, 'h:i A') }}</small>
                                        <br>
                                        <small class="text-muted">{{ diffForHumans($log->created_at) }}</small>
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <button class="btn btn-sm btn-outline--info viewDetailsBtn" 
                                                    data-id="{{ $log->id }}" 
                                                    data-details="{{ $log->details }}"
                                                    title="@lang('View Details')">
                                                <i class="las la-eye"></i>
                                            </button>
                                            
                                            @if($log->status == 'pending')
                                                <button class="btn btn-sm btn-outline--warning investigateBtn" 
                                                        data-id="{{ $log->id }}"
                                                        title="@lang('Investigate')">
                                                    <i class="las la-search"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-outline--success markSafeBtn" 
                                                        data-id="{{ $log->id }}"
                                                        title="@lang('Mark as Safe')">
                                                    <i class="las la-check"></i>
                                                </button>
                                            @endif
                                            
                                            @if($log->status == 'investigating')
                                                <button class="btn btn-sm btn-outline--success resolveBtn" 
                                                        data-id="{{ $log->id }}"
                                                        title="@lang('Resolve')">
                                                    <i class="las la-check-double"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        <div class="text-center py-4">
                                            <i class="las la-shield-alt text--success" style="font-size: 3rem;"></i>
                                            <h5 class="mt-2">@lang('No Fraud Detected')</h5>
                                            <p class="text-muted">@lang('All systems secure!')</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($fraudLogs->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($fraudLogs) }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Fraud Details Modal --}}
<div id="fraudDetailsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Fraud Detection Details')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="fraudDetailsContent">
                    <!-- Details will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

{{-- Investigate Modal --}}
<div id="investigateModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Start Investigation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="fw-bold required">@lang('Investigation Notes')</label>
                        <textarea name="investigation_notes" class="form-control" rows="4" placeholder="@lang('Enter investigation details and findings...')" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="fw-bold">@lang('Priority Level')</label>
                        <select name="priority" class="form-control">
                            <option value="normal">@lang('Normal')</option>
                            <option value="high">@lang('High Priority')</option>
                            <option value="urgent">@lang('Urgent')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--warning">@lang('Start Investigation')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Resolve Modal --}}
<div id="resolveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Resolve Investigation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="fw-bold required">@lang('Resolution')</label>
                        <select name="resolution" class="form-control" required>
                            <option value="">@lang('Select Resolution')</option>
                            <option value="confirmed_fraud">@lang('Confirmed Fraud')</option>
                            <option value="false_positive">@lang('False Positive')</option>
                            <option value="user_error">@lang('User Error')</option>
                            <option value="system_error">@lang('System Error')</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="fw-bold required">@lang('Resolution Notes')</label>
                        <textarea name="resolution_notes" class="form-control" rows="4" placeholder="@lang('Describe the investigation outcome and actions taken...')" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="block_user" id="blockUser">
                            <label class="form-check-label" for="blockUser">
                                @lang('Block user from future rebates')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--success">@lang('Resolve Case')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.fraud.settings') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-cog"></i> @lang('Detection Settings')
    </a>
    <button class="btn btn-sm btn-outline--warning" id="exportFraudData">
        <i class="las la-download"></i> @lang('Export Data')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';

        // Date range picker
        $('.datepicker-here').datepicker({
            range: true,
            multipleDatesSeparator: ' - ',
            language: 'en',
            autoClose: true
        });

        // Select All functionality
        $('#selectAll').on('change', function() {
            $('.fraud-checkbox').prop('checked', $(this).is(':checked'));
            updateBulkButtons();
        });

        $(document).on('change', '.fraud-checkbox', function() {
            updateBulkButtons();
        });

        function updateBulkButtons() {
            var selectedCount = $('.fraud-checkbox:checked').length;
            $('#bulkInvestigateBtn').prop('disabled', selectedCount === 0);
        }

        // View details
        $('.viewDetailsBtn').on('click', function() {
            var details = $(this).data('details');
            var parsedDetails = '';
            
            try {
                var detailsObj = JSON.parse(details);
                parsedDetails = '<div class="row">';
                
                Object.keys(detailsObj).forEach(function(key) {
                    parsedDetails += `
                        <div class="col-md-6 mb-2">
                            <strong>${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</strong>
                            <span class="text-muted">${detailsObj[key]}</span>
                        </div>
                    `;
                });
                
                parsedDetails += '</div>';
            } catch (e) {
                parsedDetails = '<p class="text-muted">No additional details available</p>';
            }
            
            $('#fraudDetailsContent').html(parsedDetails);
            $('#fraudDetailsModal').modal('show');
        });

        // Investigate
        $('.investigateBtn').on('click', function() {
            var fraudId = $(this).data('id');
            var modal = $('#investigateModal');
            modal.find('form').attr('action', `{{ route('admin.fraud.investigate', '') }}/${fraudId}`);
            modal.modal('show');
        });

        // Resolve
        $('.resolveBtn').on('click', function() {
            var fraudId = $(this).data('id');
            var modal = $('#resolveModal');
            modal.find('form').attr('action', `{{ route('admin.fraud.resolve', '') }}/${fraudId}`);
            modal.modal('show');
        });

        // Mark as safe
        $('.markSafeBtn').on('click', function() {
            var fraudId = $(this).data('id');
            
            if (confirm('@lang("Are you sure this is a false positive?")')) {
                $.ajax({
                    url: `{{ route('admin.fraud.mark-safe', '') }}/${fraudId}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message || '@lang("Something went wrong")');
                        }
                    },
                    error: function() {
                        alert('@lang("Something went wrong")');
                    }
                });
            }
        });

        // Export fraud data
        $('#exportFraudData').on('click', function() {
            var params = new URLSearchParams(window.location.search);
            params.append('export', 'true');
            window.open(`{{ route('admin.fraud.index') }}?${params.toString()}`, '_blank');
        });

        // Auto-refresh every 3 minutes for new fraud alerts
        setInterval(function() {
            if (!$('.modal:visible').length) {
                location.reload();
            }
        }, 180000);

    })(jQuery);
</script>
@endpush