@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Create New Rebate Program')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.rebate.programs.store') }}" method="POST">
                    @csrf
                    
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
                                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Status')</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">
                                                        @lang('Active')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Description')</label>
                                        <textarea class="form-control" name="description" rows="4" placeholder="@lang('Optional program description')">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Info --}}
                        <div class="col-lg-4">
                            <div class="card border--info">
                                <div class="card-header bg--info">
                                    <h6 class="card-title text-white mb-0">@lang('Quick Guide')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="las la-lightbulb text--primary" style="font-size: 3rem;"></i>
                                        <p class="mt-2 mb-0">
                                            <small class="text-muted">
                                                @lang('Create rebate programs to reward users for their purchases and activities.')
                                            </small>
                                        </p>
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
                                                           value="{{ old('default_rate', '5.00') }}" 
                                                           step="0.01" min="0" max="100" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="form-text text-muted">@lang('Base rebate percentage for this program')</small>
                                                <div class="example-calculation"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Minimum Amount') <span class="text--danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                    <input type="number" class="form-control" name="minimum_amount" 
                                                           value="{{ old('minimum_amount', '10.00') }}" 
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
                                                           value="{{ old('maximum_rebate') }}" 
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
                                                           value="{{ old('daily_limit') }}" 
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
                                                           value="{{ old('monthly_limit') }}" 
                                                           step="0.01" min="0">
                                                </div>
                                                <small class="form-text text-muted">@lang('Monthly rebate limit per user (leave empty for no limit)')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Manual Members Count')</label>
                                                <input type="number" class="form-control" name="manual_members_count" 
                                                       value="{{ old('manual_members_count') }}" 
                                                       min="0" placeholder="@lang('Leave empty to use system count')">
                                                <small class="form-text text-muted">
                                                    @lang('Override system member count') 
                                                    <span class="text-info">(@lang('System count'): {{ \App\Models\User::where('status', 1)->count() }})</span>
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
                                                       value="{{ old('starts_at') }}">
                                                <small class="form-text text-muted">@lang('When this program becomes active (leave empty for immediate)')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('End Date')</label>
                                                <input type="datetime-local" class="form-control" name="ends_at" 
                                                       value="{{ old('ends_at') }}">
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

                                    {{-- Existing Categories --}}
                                    @if(isset($categories) && $categories->count() > 0)
                                    <div class="form-group">
                                        <label>@lang('Assign Existing Categories')</label>
                                        <div class="row">
                                            @foreach($categories as $category)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="categories[]" 
                                                           value="{{ $category->id }}" id="category_{{ $category->id }}">
                                                    <label class="form-check-label" for="category_{{ $category->id }}">
                                                        {{ $category->name }} ({{ $category->rebate_rate }}%)
                                                        @if($category->rebate_program_id)
                                                            <small class="text-warning">- @lang('Currently assigned to') {{ $category->program->name ?? 'Unknown Program' }} (@lang('can reassign'))</small>
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
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="auto_approval" id="auto_approval">
                                                    <label class="form-check-label" for="auto_approval">
                                                        @lang('Enable automatic approval for low-risk transactions')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Require Receipt')</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="require_receipt" id="require_receipt" checked>
                                                    <label class="form-check-label" for="require_receipt">
                                                        @lang('Require receipt upload for rebate claims')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn--primary btn-lg">
                                    <i class="las la-save"></i> @lang('Create Program')
                                </button>
                                <a href="{{ route('admin.rebate.programs.index') }}" class="btn btn--secondary btn-lg">
                                    <i class="las la-times"></i> @lang('Cancel')
                                </a>
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
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';
        
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
                        Example: $${exampleAmount.toFixed(2)} purchase = $${rebateAmount.toFixed(2)} rebate
                    </small>
                `);
            } else {
                $('.example-calculation').html('');
            }
        }
        
        // Update example on input change
        $('input[name="default_rate"], input[name="minimum_amount"], input[name="maximum_rebate"]').on('input', calculateExample);
        
        // Initial calculation
        calculateExample();
        
        // Dynamic category management
        let categoryIndex = 1;
        
        $(document).on('click', '.add-category', function() {
            const newRow = `
                <div class="new-category-row row mb-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="new_categories[${categoryIndex}][name]" 
                               placeholder="@lang('Category name')">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="number" class="form-control" name="new_categories[${categoryIndex}][rebate_rate]" 
                                   placeholder="@lang('Rate')" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn--danger btn-sm remove-category">
                            <i class="las la-minus"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#new-categories-container').append(newRow);
            categoryIndex++;
        });
        
        $(document).on('click', '.remove-category', function() {
            $(this).closest('.new-category-row').remove();
        });

        // Form validation
        $('form').on('submit', function(e) {
            const startDate = $('input[name="starts_at"]').val();
            const endDate = $('input[name="ends_at"]').val();
            
            if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
                e.preventDefault();
                alert('@lang("End date must be after start date")');
                return false;
            }
        });
        
    })(jQuery);
</script>
@endpush