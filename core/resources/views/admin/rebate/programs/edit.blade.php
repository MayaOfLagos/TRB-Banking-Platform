@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Edit Rebate Program')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.rebate.programs.update', $program->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        {{-- Basic Information --}}
                        <div class="col-lg-8">
                            <div class="card border--primary">
                                <div class="card-header bg--primary">
                                    <h6 class="card-title text-white mb-0">@lang('Basic Information')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Program Name') <span class="text--danger">*</span></label>
                                                <input type="text" class="form-control" name="name" value="{{ old('name', $program->name) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Status')</label>
                                                <input name="is_active" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Active')" data-off="@lang('Inactive')" type="checkbox" @if(old('is_active', $program->is_active)) checked @endif>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Description')</label>
                                        <textarea class="form-control" name="description" rows="4" placeholder="@lang('Optional program description')">{{ old('description', $program->description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status & Quick Info --}}
                        <div class="col-lg-4">
                            <div class="card border--info">
                                <div class="card-header bg--info">
                                    <h6 class="card-title text-white mb-0">@lang('Program Status')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <span class="badge badge--{{ $program->is_active ? 'success' : 'danger' }} badge-lg">
                                                {{ $program->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            @lang('Created'): {{ showDateTime($program->created_at, 'd M Y') }}
                                        </small>
                                    </div>
                                    
                                    {{-- Progress Indicator --}}
                                    <div class="mt-3">
                                        <h6 class="text-center mb-2">@lang('Form Completion')</h6>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-info" id="formProgress" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted d-block text-center mt-1" id="progressText">0% @lang('Complete')</small>
                                    </div>
                                    
                                    {{-- Quick Stats --}}
                                    <div class="mt-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="border-end">
                                                    <h6 class="mb-0 text-primary" id="transactionCount">
                                                        {{ number_format($program->rebate_transactions_count ?? 0) }}
                                                    </h6>
                                                    <small class="text-muted">@lang('Transactions')</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="mb-0 text-success" id="totalRebates">
                                                    {{ showAmount($program->rebate_transactions_sum_amount ?? 0) }}
                                                </h6>
                                                <small class="text-muted">@lang('Total Rebates')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Rebate Settings --}}
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card border--success">
                                <div class="card-header bg--success">
                                    <h6 class="card-title text-white mb-0">@lang('Rebate Settings')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Default Rate') (%) <span class="text--danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="default_rate" 
                                                           value="{{ old('default_rate', $program->default_rate) }}" 
                                                           step="0.01" min="0" max="100" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="form-text text-muted">@lang('Base rebate percentage for this program')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Minimum Amount') <span class="text--danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                    <input type="number" class="form-control" name="minimum_amount" 
                                                           value="{{ old('minimum_amount', $program->minimum_amount) }}" 
                                                           step="0.01" min="0" required>
                                                </div>
                                                <small class="form-text text-muted">@lang('Minimum purchase amount to qualify')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Maximum Rebate')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                    <input type="number" class="form-control" name="maximum_rebate" 
                                                           value="{{ old('maximum_rebate', $program->maximum_rebate) }}" 
                                                           step="0.01" min="0">
                                                </div>
                                                <small class="form-text text-muted">@lang('Maximum rebate amount per transaction (leave empty for no limit)')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Daily Limit')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                    <input type="number" class="form-control" name="daily_limit" 
                                                           value="{{ old('daily_limit', $program->daily_limit) }}" 
                                                           step="0.01" min="0">
                                                </div>
                                                <small class="form-text text-muted">@lang('Daily rebate limit per user (leave empty for no limit)')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Monthly Limit')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                    <input type="number" class="form-control" name="monthly_limit" 
                                                           value="{{ old('monthly_limit', $program->monthly_limit) }}" 
                                                           step="0.01" min="0">
                                                </div>
                                                <small class="form-text text-muted">@lang('Monthly rebate limit per user (leave empty for no limit)')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Manual Members Count')</label>
                                                <input type="number" class="form-control" name="manual_members_count" 
                                                       value="{{ old('manual_members_count', $program->manual_members_count) }}" 
                                                       min="0" placeholder="@lang('Leave empty to use system count')">
                                                <small class="form-text text-muted">
                                                    @lang('Override system member count')
                                                    <span class="text-info">(@lang('System count'): {{ $program->getSystemMembersCount() }})</span>
                                                    @if($program->isUsingManualMembersCount())
                                                        <br><span class="text-success">@lang('Currently using manual count'): {{ $program->manual_members_count }}</span>
                                                    @else
                                                        <br><span class="text-muted">@lang('Currently using system count'): {{ $program->getEffectiveMembersCount() }}</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Date Settings --}}
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card border--warning">
                                <div class="card-header bg--warning">
                                    <h6 class="card-title text-white mb-0">@lang('Schedule Settings')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Start Date')</label>
                                                <input type="datetime-local" class="form-control" name="starts_at" 
                                                       value="{{ old('starts_at', $program->starts_at ? $program->starts_at->format('Y-m-d\TH:i') : '') }}">
                                                <small class="form-text text-muted">@lang('When this program becomes active (leave empty for immediate)')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('End Date')</label>
                                                <input type="datetime-local" class="form-control" name="ends_at" 
                                                       value="{{ old('ends_at', $program->ends_at ? $program->ends_at->format('Y-m-d\TH:i') : '') }}">
                                                <small class="form-text text-muted">@lang('When this program expires (leave empty for no expiry)')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Category Management --}}
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card border--info">
                                <div class="card-header bg--info">
                                    <h6 class="card-title text-white mb-0">@lang('Category Management')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert--primary">
                                        <i class="las la-info-circle"></i>
                                        @lang('Categories help organize rebate programs. You can assign existing categories or create new ones.')
                                    </div>

                                    {{-- Current Categories --}}
                                    @if($program->categories->count() > 0)
                                    <div class="form-group">
                                        <label>@lang('Current Categories')</label>
                                        <div class="row mb-3">
                                            @foreach($program->categories as $category)
                                            <div class="col-md-4 mb-2">
                                                <div class="alert alert--success">
                                                    <strong>{{ $category->name }}</strong> ({{ $category->rebate_rate }}%)
                                                    <br><small>@lang('Code'): {{ $category->code }}</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Existing Categories --}}
                                    @if(isset($allCategories) && $allCategories->count() > 0)
                                    <div class="form-group">
                                        <label>@lang('Assign Existing Categories')</label>
                                        <div class="row">
                                            @foreach($allCategories as $category)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categories[]" 
                                                           value="{{ $category->id }}" id="category_{{ $category->id }}"
                                                           @if($category->rebate_program_id == $program->id) checked @endif>
                                                    <label class="form-check-label" for="category_{{ $category->id }}">
                                                        {{ $category->name }} ({{ $category->rebate_rate }}%)
                                                        @if($category->rebate_program_id == $program->id)
                                                            <small class="text-success">- @lang('Currently assigned')</small>
                                                        @elseif($category->rebate_program_id)
                                                            <small class="text-warning">- @lang('Assigned to') {{ $category->program->name ?? 'Unknown Program' }} (@lang('can reassign'))</small>
                                                        @else
                                                            <small class="text-info">- @lang('Available')</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">@lang('Select categories to assign to this program. Categories can be reassigned from other programs.')</small>
                                    </div>
                                    @else
                                    <div class="alert alert--info">
                                        <i class="las la-info-circle"></i>
                                        @lang('No categories exist yet. You can create new categories for this program below.')
                                    </div>
                                    @endif

                                    {{-- New Categories --}}
                                    <div class="form-group">
                                        <label>@lang('Create New Categories')</label>
                                        <div id="new-categories-container">
                                            <div class="new-category-row row mb-2">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="new_categories[0][name]" 
                                                           placeholder="@lang('Category name')">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="new_categories[0][rebate_rate]" 
                                                               placeholder="@lang('Rate')" step="0.01" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn--success btn-sm add-category">
                                                        <i class="las la-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">@lang('Leave rate empty to use the program default rate')</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Advanced Settings --}}
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card border--dark">
                                <div class="card-header bg--dark">
                                    <h6 class="card-title text-white mb-0">@lang('Advanced Settings')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert--info">
                                        <i class="las la-info-circle"></i>
                                        @lang('Advanced settings allow you to configure additional program parameters. These are optional and stored as JSON.')
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Auto Approval')</label>
                                                <input name="settings[auto_approval]" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if(json_decode($program->settings ?? '{}', true)['auto_approval'] ?? false) checked @endif>
                                                <small class="text-muted">@lang('Enable automatic approval for low-risk transactions')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Tier Multiplier')</label>
                                                <input name="settings[tier_multiplier]" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if(json_decode($program->settings ?? '{}', true)['tier_multiplier'] ?? true) checked @endif>
                                                <small class="text-muted">@lang('Apply user tier multipliers to rebates')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Fraud Detection')</label>
                                                <input name="settings[fraud_detection]" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if(json_decode($program->settings ?? '{}', true)['fraud_detection'] ?? true) checked @endif>
                                                <small class="text-muted">@lang('Enable fraud detection for transactions')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Email Notifications')</label>
                                                <input name="settings[email_notifications]" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Enable')" data-off="@lang('Disable')" type="checkbox" @if(json_decode($program->settings ?? '{}', true)['email_notifications'] ?? false) checked @endif>
                                                <small class="text-muted">@lang('Send email notifications for rebate activities')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Risk Threshold') (%)</label>
                                                <div class="input-group">
                                                    <input type="range" class="form-range" id="risk_threshold" 
                                                           name="settings[risk_threshold]" 
                                                           value="{{ json_decode($program->settings ?? '{}', true)['risk_threshold'] ?? 75 }}" 
                                                           min="0" max="100" step="5">
                                                    <span class="input-group-text risk-threshold-value">
                                                        {{ json_decode($program->settings ?? '{}', true)['risk_threshold'] ?? 75 }}%
                                                    </span>
                                                </div>
                                                <small class="text-muted">@lang('Transactions above this risk score require manual approval')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Preview Section --}}
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card border--info" id="previewCard" style="display: none;">
                                <div class="card-header bg--info">
                                    <h6 class="card-title text-white mb-0">
                                        <i class="las la-eye"></i> @lang('Preview Changes')
                                    </h6>
                                </div>
                                <div class="card-body" id="previewContent">
                                    {{-- Preview content will be populated by JavaScript --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                        {{-- Left side buttons --}}
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn--info" id="previewBtn">
                                                <i class="las la-eye"></i> @lang('Preview Changes')
                                            </button>
                                            <button type="button" class="btn btn--warning" id="resetBtn">
                                                <i class="las la-undo"></i> @lang('Reset Form')
                                            </button>
                                        </div>

                                        {{-- Center status --}}
                                        <div class="text-center">
                                            <div id="formStatus" class="small text-muted">
                                                @lang('Ready to update')
                                            </div>
                                        </div>

                                        {{-- Right side main actions --}}
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.rebate.programs.show', $program->id) }}" class="btn btn--secondary">
                                                <i class="las la-times"></i> @lang('Cancel')
                                            </a>
                                            <button type="submit" class="btn btn--success btn-lg" id="updateBtn">
                                                <i class="las la-save"></i> @lang('Update Program')
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Quick Actions --}}
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                <button type="button" class="btn btn-sm btn--primary quick-action" data-action="activate">
                                                    <i class="las la-play"></i> @lang('Save & Activate')
                                                </button>
                                                <button type="button" class="btn btn-sm btn--warning quick-action" data-action="deactivate">
                                                    <i class="las la-pause"></i> @lang('Save & Deactivate')
                                                </button>
                                                <a href="{{ route('admin.rebate.programs.create') }}" class="btn btn-sm btn--info">
                                                    <i class="las la-copy"></i> @lang('Save & Duplicate')
                                                </a>
                                                <a href="{{ route('admin.rebate.programs.index') }}" class="btn btn-sm btn--dark">
                                                    <i class="las la-list"></i> @lang('Back to List')
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.rebate.programs.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-list"></i> @lang('All Programs')
    </a>
    <a href="{{ route('admin.rebate.programs.show', $program->id) }}" class="btn btn-sm btn-outline--info">
        <i class="las la-eye"></i> @lang('View Program')
    </a>
@endpush

@push('style')
<style>
    .form-range {
        width: 100%;
        margin: 10px 0;
    }
    
    .toggle-setting {
        transform: scale(1.2);
    }
    
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    
    .form-check-label {
        cursor: pointer;
        font-weight: 500;
    }
    
    .card-header.bg--primary,
    .card-header.bg--success,
    .card-header.bg--warning,
    .card-header.bg--info,
    .card-header.bg--dark {
        border-radius: 0.375rem 0.375rem 0 0;
    }
    
    .example-calculation {
        margin-top: 0.5rem;
        padding: 0.5rem;
        background: rgba(13, 110, 253, 0.1);
        border-radius: 0.25rem;
        border-left: 3px solid #0d6efd;
    }
    
    .quick-action {
        transition: all 0.3s ease;
    }
    
    .quick-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    #previewCard {
        border-left: 4px solid #0dcaf0;
    }
    
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 0.75rem 0;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .input-group-text {
        font-weight: 500;
    }
    
    .risk-threshold-value {
        font-weight: bold;
        color: #0d6efd;
        min-width: 50px;
        text-align: center;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: box-shadow 0.15s ease-in-out;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        
        .d-flex.gap-2 {
            justify-content: center;
        }
    }
</style>
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';
        
        let originalFormData = {};
        let hasChanges = false;
        
        // Store original form data
        function storeOriginalData() {
            $('form').find('input, textarea, select').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    if ($(this).is(':checkbox')) {
                        originalFormData[name] = $(this).is(':checked');
                    } else {
                        originalFormData[name] = $(this).val();
                    }
                }
            });
        }
        
        // Check for form changes
        function checkForChanges() {
            let changes = false;
            $('form').find('input, textarea, select').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    let currentValue;
                    if ($(this).is(':checkbox')) {
                        currentValue = $(this).is(':checked');
                    } else {
                        currentValue = $(this).val();
                    }
                    
                    if (originalFormData[name] !== currentValue) {
                        changes = true;
                        return false;
                    }
                }
            });
            
            hasChanges = changes;
            updateFormStatus();
            updateFormProgress();
        }
        
        // Update form completion progress
        function updateFormProgress() {
            const requiredFields = [
                'input[name="name"]',
                'input[name="default_rate"]',
                'input[name="minimum_amount"]'
            ];
            
            const optionalFields = [
                'textarea[name="description"]',
                'input[name="maximum_rebate"]',
                'input[name="daily_limit"]',
                'input[name="monthly_limit"]',
                'input[name="starts_at"]',
                'input[name="ends_at"]'
            ];
            
            let filledRequired = 0;
            let filledOptional = 0;
            
            // Check required fields
            requiredFields.forEach(selector => {
                const value = $(selector).val();
                if (value && value.trim() !== '') {
                    filledRequired++;
                }
            });
            
            // Check optional fields
            optionalFields.forEach(selector => {
                const value = $(selector).val();
                if (value && value.trim() !== '') {
                    filledOptional++;
                }
            });
            
            // Calculate progress (required fields worth 70%, optional 30%)
            const requiredPercent = (filledRequired / requiredFields.length) * 70;
            const optionalPercent = (filledOptional / optionalFields.length) * 30;
            const totalPercent = Math.round(requiredPercent + optionalPercent);
            
            // Update progress bar
            $('#formProgress').css('width', totalPercent + '%');
            $('#progressText').text(totalPercent + '% @lang("Complete")');
            
            // Change color based on completion
            $('#formProgress').removeClass('bg-danger bg-warning bg-info bg-success');
            if (totalPercent < 50) {
                $('#formProgress').addClass('bg-danger');
            } else if (totalPercent < 75) {
                $('#formProgress').addClass('bg-warning');
            } else if (totalPercent < 100) {
                $('#formProgress').addClass('bg-info');
            } else {
                $('#formProgress').addClass('bg-success');
            }
        }
        
        // Update form status display
        function updateFormStatus() {
            const statusEl = $('#formStatus');
            if (hasChanges) {
                statusEl.html('<span class="text--warning"><i class="las la-exclamation-triangle"></i> @lang("Unsaved changes")</span>');
                $('#updateBtn').removeClass('btn--success').addClass('btn--primary').prop('disabled', false);
            } else {
                statusEl.html('<span class="text--success"><i class="las la-check"></i> @lang("No changes")</span>');
                $('#updateBtn').removeClass('btn--primary').addClass('btn--success').prop('disabled', false);
            }
        }
        
        // Track toggle changes
        $('input[data-bs-toggle="toggle"]').on('change', function() {
            checkForChanges();
        });
        
        // Risk threshold slider
        $('#risk_threshold').on('input', function() {
            $('.risk-threshold-value').text($(this).val() + '%');
            checkForChanges();
        });
        
        // Auto-calculate example rebate amount
        function calculateExample() {
            const rate = parseFloat($('input[name="default_rate"]').val()) || 0;
            const minAmount = parseFloat($('input[name="minimum_amount"]').val()) || 0;
            const maxRebate = parseFloat($('input[name="maximum_rebate"]').val()) || 0;
            
            if (rate > 0 && minAmount > 0) {
                let exampleAmount = minAmount * 2; // Example with double minimum
                let rebateAmount = (exampleAmount * rate) / 100;
                
                if (maxRebate > 0 && rebateAmount > maxRebate) {
                    rebateAmount = maxRebate;
                }
                
                $('.example-calculation').html(`
                    <small class="text--info">
                        <i class="las la-calculator"></i> 
                        Example: {{ __($general->cur_text) }}${exampleAmount.toFixed(2)} purchase = {{ __($general->cur_text) }}${rebateAmount.toFixed(2)} rebate
                    </small>
                `);
            } else {
                $('.example-calculation').html('');
            }
        }
        
        // Preview functionality
        $('#previewBtn').on('click', function() {
            const previewCard = $('#previewCard');
            const previewContent = $('#previewContent');
            
            if (previewCard.is(':visible')) {
                previewCard.slideUp();
                $(this).html('<i class="las la-eye"></i> @lang("Preview Changes")');
                return;
            }
            
            // Generate preview content
            const name = $('input[name="name"]').val();
            const rate = $('input[name="default_rate"]').val();
            const minAmount = $('input[name="minimum_amount"]').val();
            const isActive = $('input[name="is_active"]').is(':checked');
            const autoApproval = $('input[name="settings[auto_approval]"]').is(':checked');
            const tierMultiplier = $('input[name="settings[tier_multiplier]"]').is(':checked');
            const fraudDetection = $('input[name="settings[fraud_detection]"]').is(':checked');
            const emailNotifications = $('input[name="settings[email_notifications]"]').is(':checked');
            const riskThreshold = $('#risk_threshold').val();
            
            const previewHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>@lang('Basic Information')</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Name'):</span> <strong>${name || '@lang("Not set")'}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Status'):</span> 
                                <span class="badge badge--${isActive ? 'success' : 'danger'}">${isActive ? '@lang("Active")' : '@lang("Inactive")'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Default Rate'):</span> <strong>${rate}%</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Minimum Amount'):</span> <strong>{{ __($general->cur_text) }}${minAmount}</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>@lang('Advanced Settings')</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Auto Approval'):</span> 
                                <span class="badge badge--${autoApproval ? 'success' : 'secondary'}">${autoApproval ? '@lang("Enabled")' : '@lang("Disabled")'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Tier Multiplier'):</span> 
                                <span class="badge badge--${tierMultiplier ? 'success' : 'secondary'}">${tierMultiplier ? '@lang("Enabled")' : '@lang("Disabled")'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Fraud Detection'):</span> 
                                <span class="badge badge--${fraudDetection ? 'warning' : 'secondary'}">${fraudDetection ? '@lang("Enabled")' : '@lang("Disabled")'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Email Notifications'):</span> 
                                <span class="badge badge--${emailNotifications ? 'primary' : 'secondary'}">${emailNotifications ? '@lang("Enabled")' : '@lang("Disabled")'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Risk Threshold'):</span> <strong>${riskThreshold}%</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            `;
            
            previewContent.html(previewHtml);
            previewCard.slideDown();
            $(this).html('<i class="las la-eye-slash"></i> @lang("Hide Preview")');
        });
        
        // Reset form
        $('#resetBtn').on('click', function() {
            if (confirm('@lang("Are you sure you want to reset all changes?")')) {
                location.reload();
            }
        });
        
        // Quick actions
        $('.quick-action').on('click', function() {
            const action = $(this).data('action');
            
            if (action === 'activate') {
                $('input[name="is_active"]').prop('checked', true);
                checkForChanges();
                $('form').submit();
            } else if (action === 'deactivate') {
                $('input[name="is_active"]').prop('checked', false);
                checkForChanges();
                $('form').submit();
            }
        });
        
        // Add example calculation display
        $('input[name="default_rate"]').closest('.form-group').append('<div class="example-calculation"></div>');
        
        // Update example on input change
        $('input[name="default_rate"], input[name="minimum_amount"], input[name="maximum_rebate"]').on('input', function() {
            calculateExample();
            checkForChanges();
        });
        
        // Monitor all form changes
        $('form').find('input, textarea, select').on('input change', checkForChanges);
        
        // Form validation
        $('form').on('submit', function(e) {
            const startDate = $('input[name="starts_at"]').val();
            const endDate = $('input[name="ends_at"]').val();
            
            if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
                e.preventDefault();
                alert('@lang("End date must be after start date")');
                return false;
            }
            
            // Show loading state
            $('#updateBtn').prop('disabled', true).html('<i class="las la-spinner la-spin"></i> @lang("Updating...")');
        });
        
        // Warn before leaving with unsaved changes
        $(window).on('beforeunload', function(e) {
            if (hasChanges) {
                return '@lang("You have unsaved changes. Are you sure you want to leave?")';
            }
        });
        
        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl+S to save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                $('form').submit();
            }
            
            // Ctrl+R to reset (with confirmation)
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                $('#resetBtn').click();
            }
            
            // Ctrl+P to preview
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                $('#previewBtn').click();
            }
        });
        
        // Add tooltips to form elements
        $('input[required]').each(function() {
            $(this).attr('title', '@lang("This field is required")');
        });
        
        // Initialize tooltips
        $('[title]').each(function() {
            $(this).attr('data-bs-toggle', 'tooltip');
        });
        
        // Add keyboard shortcut hints
        const shortcutHints = `
            <div class="alert alert--info mt-2" style="font-size: 0.85rem;">
                <strong>@lang('Keyboard Shortcuts'):</strong> 
                Ctrl+S (@lang('Save')), Ctrl+R (@lang('Reset')), Ctrl+P (@lang('Preview'))
            </div>
        `;
        $('#formStatus').closest('.text-center').append(shortcutHints);
        
        // Initialize
        storeOriginalData();
        calculateExample();
        updateFormStatus();
        updateFormProgress();
        
    })(jQuery);
</script>
@endpush