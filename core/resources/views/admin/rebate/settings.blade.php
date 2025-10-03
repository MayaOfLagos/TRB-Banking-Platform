@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">@lang('Rebate System Settings')</h6>
                        <div class="d-flex gap-2">
                            <span class="badge badge--{{ $defaultSettings['system']['enabled'] ? 'success' : 'danger' }}">
                                <i class="fas fa-{{ $defaultSettings['system']['enabled'] ? 'check' : 'times' }}"></i>
                                System {{ $defaultSettings['system']['enabled'] ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Overview -->
                    <div class="row gy-4 mb-30">
                        <div class="col-xl-3 col-sm-6">
                            <div class="widget-two style--two box--shadow2 b-radius--5 bg--info h-100">
                                <div class="widget-two__icon">
                                    <i class="las la-receipt"></i>
                                </div>
                                <div class="widget-two__content">
                                    <h3 class="text-white">{{ $stats['total_rebates_today'] }}</h3>
                                    <p class="text-white mb-0">@lang('Rebates Today')</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="widget-two style--two box--shadow2 b-radius--5 bg--warning h-100">
                                <div class="widget-two__icon">
                                    <i class="las la-clock"></i>
                                </div>
                                <div class="widget-two__content">
                                    <h3 class="text-white">{{ $stats['pending_rebates'] }}</h3>
                                    <p class="text-white mb-0">@lang('Pending Review')</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="widget-two style--two box--shadow2 b-radius--5 bg--success h-100">
                                <div class="widget-two__icon">
                                    <i class="las la-hand-holding-usd"></i>
                                </div>
                                <div class="widget-two__content">
                                    <h3 class="text-white">{{ showAmount($stats['total_amount_paid_today']) }}</h3>
                                    <p class="text-white mb-0">@lang('Paid Today')</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="widget-two style--two box--shadow2 b-radius--5 bg--danger h-100">
                                <div class="widget-two__icon">
                                    <i class="las la-user-shield"></i>
                                </div>
                                <div class="widget-two__content">
                                    <h3 class="text-white">{{ $stats['fraud_flags_today'] }}</h3>
                                    <p class="text-white mb-0">@lang('Fraud Flags Today')</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.rebate.settings.update') }}" method="POST">
                        @csrf
                        
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-pills nav-fill" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#system-settings" role="tab" aria-selected="true">
                                    <i class="fas fa-cogs"></i> @lang('System Settings')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tier-settings" role="tab" aria-selected="false">
                                    <i class="fas fa-trophy"></i> @lang('Tier Settings')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#fraud-settings" role="tab" aria-selected="false">
                                    <i class="fas fa-shield-alt"></i> @lang('Fraud Prevention')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#notification-settings" role="tab" aria-selected="false">
                                    <i class="fas fa-bell"></i> @lang('Notifications')
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-4">
                            <!-- System Settings Tab -->
                            <div class="tab-pane fade show active" id="system-settings" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('System Status')</label>
                                            <input type="hidden" name="system[enabled]" value="0">
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="system[enabled]" value="1" @if($defaultSettings['system']['enabled']) checked @endif>
                                            <small class="form-text text-muted">@lang('Enable or disable the entire rebate system')</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Auto Approval')</label>
                                            <input type="hidden" name="system[auto_approval]" value="0">
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="system[auto_approval]" value="1" @if($defaultSettings['system']['auto_approval']) checked @endif>
                                            <small class="form-text text-muted">@lang('Automatically approve rebates below the limit')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Auto Approval Limit')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="system[auto_approval_limit]" value="{{ getAmount($defaultSettings['system']['auto_approval_limit']) }}" required>
                                            </div>
                                            <small class="form-text text-muted">@lang('Maximum amount for automatic approval')</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Daily Limit Per User')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="system[daily_limit_per_user]" value="{{ getAmount($defaultSettings['system']['daily_limit_per_user']) }}" required>
                                            </div>
                                            <small class="form-text text-muted">@lang('Maximum rebate amount per user per day')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Monthly Limit Per User')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="system[monthly_limit_per_user]" value="{{ getAmount($defaultSettings['system']['monthly_limit_per_user']) }}" required>
                                            </div>
                                            <small class="form-text text-muted">@lang('Maximum rebate amount per user per month')</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Minimum Rebate Amount')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="system[minimum_rebate_amount]" value="{{ getAmount($defaultSettings['system']['minimum_rebate_amount']) }}" required>
                                            </div>
                                            <small class="form-text text-muted">@lang('Minimum rebate amount to process')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Maximum Rebate Amount')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="system[maximum_rebate_amount]" value="{{ getAmount($defaultSettings['system']['maximum_rebate_amount']) }}" required>
                                            </div>
                                            <small class="form-text text-muted">@lang('Maximum rebate amount per transaction')</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tier Settings Tab -->
                            <div class="tab-pane fade" id="tier-settings" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>@lang('Tier System Status')</label>
                                            <input type="hidden" name="tiers[enabled]" value="0">
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="tiers[enabled]" value="1" @if($defaultSettings['tiers']['enabled']) checked @endif>
                                            <small class="form-text text-muted">@lang('Enable tier-based rebate multipliers')</small>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3">@lang('Tier Thresholds') <small class="text-muted">(@lang('Total earnings required to reach tier'))</small></h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Bronze Tier') <span class="badge badge--dark">@lang('Default')</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="tiers[bronze_threshold]" value="{{ getAmount($defaultSettings['tiers']['bronze_threshold']) }}" required readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Silver Tier')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="tiers[silver_threshold]" value="{{ getAmount($defaultSettings['tiers']['silver_threshold']) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Gold Tier')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="tiers[gold_threshold]" value="{{ getAmount($defaultSettings['tiers']['gold_threshold']) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Platinum Tier')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="tiers[platinum_threshold]" value="{{ getAmount($defaultSettings['tiers']['platinum_threshold']) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Diamond Tier')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="tiers[diamond_threshold]" value="{{ getAmount($defaultSettings['tiers']['diamond_threshold']) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3 mt-4">@lang('Tier Multipliers') <small class="text-muted">(@lang('Rebate rate multiplier for each tier'))</small></h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Bronze Multiplier')</label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" class="form-control" name="tiers[bronze_multiplier]" value="{{ $defaultSettings['tiers']['bronze_multiplier'] }}" required>
                                                <span class="input-group-text">x</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Silver Multiplier')</label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" class="form-control" name="tiers[silver_multiplier]" value="{{ $defaultSettings['tiers']['silver_multiplier'] }}" required>
                                                <span class="input-group-text">x</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Gold Multiplier')</label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" class="form-control" name="tiers[gold_multiplier]" value="{{ $defaultSettings['tiers']['gold_multiplier'] }}" required>
                                                <span class="input-group-text">x</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Platinum Multiplier')</label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" class="form-control" name="tiers[platinum_multiplier]" value="{{ $defaultSettings['tiers']['platinum_multiplier'] }}" required>
                                                <span class="input-group-text">x</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Diamond Multiplier')</label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" class="form-control" name="tiers[diamond_multiplier]" value="{{ $defaultSettings['tiers']['diamond_multiplier'] }}" required>
                                                <span class="input-group-text">x</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fraud Prevention Tab -->
                            <div class="tab-pane fade" id="fraud-settings" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>@lang('Fraud Detection Status')</label>
                                            <input type="hidden" name="fraud[enabled]" value="0">
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="fraud[enabled]" value="1" @if($defaultSettings['fraud']['enabled']) checked @endif>
                                            <small class="form-text text-muted">@lang('Enable fraud detection and prevention system')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Fraud Score Threshold')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="fraud[fraud_score_threshold]" value="{{ $defaultSettings['fraud']['fraud_score_threshold'] }}" min="1" max="100" required>
                                                <span class="input-group-text">/ 100</span>
                                            </div>
                                            <small class="form-text text-muted">@lang('Minimum score to flag as fraudulent')</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Max Daily Uploads')</label>
                                            <input type="number" class="form-control" name="fraud[max_daily_uploads]" value="{{ $defaultSettings['fraud']['max_daily_uploads'] }}" min="1" required>
                                            <small class="form-text text-muted">@lang('Maximum uploads per user per day')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Max Rapid Uploads')</label>
                                            <input type="number" class="form-control" name="fraud[max_rapid_uploads]" value="{{ $defaultSettings['fraud']['max_rapid_uploads'] }}" min="1" required>
                                            <small class="form-text text-muted">@lang('Maximum uploads per hour')</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Velocity Threshold')</label>
                                            <input type="number" class="form-control" name="fraud[velocity_threshold]" value="{{ $defaultSettings['fraud']['velocity_threshold'] }}" min="1" required>
                                            <small class="form-text text-muted">@lang('Upload velocity check threshold')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('IP Sharing Limit')</label>
                                            <input type="number" class="form-control" name="fraud[ip_sharing_limit]" value="{{ $defaultSettings['fraud']['ip_sharing_limit'] }}" min="1" required>
                                            <small class="form-text text-muted">@lang('Maximum users per IP address')</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Duplicate Detection')</label>
                                            <input type="hidden" name="fraud[duplicate_detection]" value="0">
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="fraud[duplicate_detection]" value="1" @if($defaultSettings['fraud']['duplicate_detection']) checked @endif>
                                            <small class="form-text text-muted">@lang('Detect duplicate receipt uploads')</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notifications Tab -->
                            <div class="tab-pane fade" id="notification-settings" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email on Approval')</label>
                                            <input type="hidden" name="notifications[email_on_approval]" value="0">
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="notifications[email_on_approval]" value="1" @if($defaultSettings['notifications']['email_on_approval']) checked @endif>
                                            <small class="form-text text-muted">@lang('Send email when rebate is approved')</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email on Rejection')</label>
                                            <input type="hidden" name="notifications[email_on_rejection]" value="0">
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="notifications[email_on_rejection]" value="1" @if($defaultSettings['notifications']['email_on_rejection']) checked @endif>
                                            <small class="form-text text-muted">@lang('Send email when rebate is rejected')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email on Tier Upgrade')</label>
                                            <input type="hidden" name="notifications[email_on_tier_upgrade]" value="0">
                                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="notifications[email_on_tier_upgrade]" value="1" @if($defaultSettings['notifications']['email_on_tier_upgrade']) checked @endif>
                                            <small class="form-text text-muted">@lang('Send email when user is upgraded to higher tier')</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Admin Notification Threshold')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                <input type="number" step="0.01" class="form-control" name="notifications[admin_notification_threshold]" value="{{ getAmount($defaultSettings['notifications']['admin_notification_threshold']) }}" required>
                                            </div>
                                            <small class="form-text text-muted">@lang('Notify admin for rebates above this amount')</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn--primary w-100 h-45">
                                <i class="fas fa-save"></i> @lang('Update Settings')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        
        // Initialize Bootstrap toggles
        $('[data-bs-toggle="toggle"]').bootstrapToggle();
        
        // Tab change events
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if (target) {
                $(target).find('input, select, textarea').first().focus();
            }
        });
        
    })(jQuery);
</script>
@endpush