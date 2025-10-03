@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row gy-4">
        <!-- Header Section -->
        <div class="col-12">
            <div class="dashboard-widget">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-2">
                                <i class="las la-gift text--primary me-2"></i>
                                @lang('Available Rebate Programs')
                            </h4>
                            <p class="text-muted mb-0">@lang('Choose from our available rebate programs and start earning today!')</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg--success fs-6">@lang('Your Tier'): {{ $userTier ?? 1 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Cards -->
        @if($programs->count() > 0)
            @foreach($programs as $program)
                <div class="col-xl-4 col-lg-6">
                    <div class="dashboard-widget program-card h-100">
                        <div class="card-body">
                            <!-- Program Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ __($program->name) }}</h5>
                                    <div class="d-flex align-items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $program->tier)
                                                <i class="las la-star text--warning"></i>
                                            @else
                                                <i class="las la-star text-muted"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-2 small text-muted">@lang('Tier') {{ $program->tier }}</span>
                                    </div>
                                </div>
                                @if($program->status == 1)
                                    <span class="badge bg--success">@lang('Active')</span>
                                @else
                                    <span class="badge bg--secondary">@lang('Inactive')</span>
                                @endif
                            </div>

                            <!-- Rebate Percentage -->
                            <div class="text-center mb-4">
                                <div class="rebate-percentage">
                                    <span class="percentage-number">{{ $program->rebate_percentage }}%</span>
                                    <div class="percentage-label">@lang('Rebate Rate')</div>
                                </div>
                            </div>

                            <!-- Program Details -->
                            <div class="program-details mb-4">
                                <div class="row gy-2">
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <small class="text-muted">@lang('Min Amount')</small>
                                            <div class="fw-bold">{{ showAmount($program->minimum_amount) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <small class="text-muted">@lang('Max Amount')</small>
                                            <div class="fw-bold">{{ showAmount($program->maximum_amount) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <small class="text-muted">@lang('Eligible Members')</small>
                                            <div class="fw-bold text--info">
                                                {{ number_format($program->getEffectiveMembersCount()) }}
                                                @if($program->isUsingManualMembersCount())
                                                    <small class="badge bg--primary badge-sm ms-1">@lang('Custom')</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <small class="text-muted">@lang('Active Users')</small>
                                            <div class="fw-bold">{{ number_format($program->rebateTransactions()->distinct('user_id')->count('user_id')) }}</div>
                                        </div>
                                    </div>
                                    @if($program->valid_until)
                                        <div class="col-12">
                                            <div class="detail-item">
                                                <small class="text-muted">@lang('Valid Until')</small>
                                                <div class="fw-bold text--warning">{{ showDateTime($program->valid_until, 'd M Y') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Description -->
                            @if($program->description)
                                <div class="program-description mb-4">
                                    <p class="text-muted small">{{ __($program->description) }}</p>
                                </div>
                            @endif

                            <!-- Requirements -->
                            @if($program->requirements)
                                <div class="program-requirements mb-4">
                                    <h6 class="mb-2">@lang('Requirements'):</h6>
                                    <ul class="list-unstyled small text-muted">
                                        @foreach(explode(',', $program->requirements) as $requirement)
                                            <li class="mb-1">
                                                <i class="las la-check-circle text--success me-1"></i>
                                                {{ trim($requirement) }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Action Button -->
                            <div class="text-center">
                                @php
                                    $isEligible = ($userTier ?? 1) >= $program->tier && $program->status == 1;
                                @endphp
                                
                                @if($isEligible)
                                    <a href="{{ route('user.rebate.submit') }}?program={{ $program->id }}" 
                                       class="btn btn--primary w-100">
                                        <i class="las la-upload me-1"></i>
                                        @lang('Submit Rebate')
                                    </a>
                                @else
                                    @if(($userTier ?? 1) < $program->tier)
                                        <button class="btn btn--secondary w-100" disabled>
                                            <i class="las la-lock me-1"></i>
                                            @lang('Tier') {{ $program->tier }} @lang('Required')
                                        </button>
                                    @else
                                        <button class="btn btn--secondary w-100" disabled>
                                            <i class="las la-pause me-1"></i>
                                            @lang('Program Inactive')
                                        </button>
                                    @endif
                                @endif
                            </div>

                            <!-- Eligibility Status -->
                            <div class="eligibility-status mt-3 text-center">
                                @if($isEligible)
                                    <small class="text--success">
                                        <i class="las la-check-circle me-1"></i>
                                        @lang('You are eligible for this program')
                                    </small>
                                @else
                                    <small class="text--danger">
                                        <i class="las la-times-circle me-1"></i>
                                        @if(($userTier ?? 1) < $program->tier)
                                            @lang('Upgrade to Tier') {{ $program->tier }} @lang('to access')
                                        @else
                                            @lang('Program currently unavailable')
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="las la-inbox display-1 text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-3">@lang('No Rebate Programs Available')</h5>
                        <p class="text-muted">@lang('There are currently no active rebate programs. Please check back later!')</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tier Advancement Info -->
        @if(($userTier ?? 1) < 5)
            <div class="col-12">
                <div class="dashboard-widget">
                    <div class="card-body">
                        <h5 class="card-title text--info mb-3">
                            <i class="las la-info-circle me-2"></i>
                            @lang('Unlock Higher Tier Programs')
                        </h5>
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <p class="mb-3">@lang('Advance to higher tiers to unlock exclusive rebate programs with better rates and higher limits!')</p>
                                
                                @php
                                    $tierRequirements = [
                                        2 => ['name' => 'Bronze', 'requirement' => 500],
                                        3 => ['name' => 'Silver', 'requirement' => 2000],
                                        4 => ['name' => 'Gold', 'requirement' => 5000],
                                        5 => ['name' => 'Platinum', 'requirement' => 10000],
                                    ];
                                @endphp
                                
                                <div class="tier-requirements">
                                    @for($tier = ($userTier ?? 1) + 1; $tier <= 5; $tier++)
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="tier-badge me-3">
                                                @for($i = 1; $i <= $tier; $i++)
                                                    <i class="las la-star text--warning"></i>
                                                @endfor
                                            </div>
                                            <div>
                                                <strong>@lang('Tier') {{ $tier }} ({{ $tierRequirements[$tier]['name'] }}):</strong>
                                                @lang('Earn') {{ showAmount($tierRequirements[$tier]['requirement']) }} @lang('in rebates')
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="col-lg-4 text-lg-end">
                                <a href="{{ route('user.rebate.dashboard') }}" class="btn btn--info">
                                    <i class="las la-chart-line me-1"></i>
                                    @lang('View Progress')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('style')
<style>
    .program-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e3e6f0;
        border-radius: 10px;
    }
    
    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .rebate-percentage {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 50%;
        width: 120px;
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    
    .percentage-number {
        font-size: 28px;
        font-weight: bold;
        line-height: 1;
    }
    
    .percentage-label {
        font-size: 12px;
        opacity: 0.9;
        margin-top: 5px;
    }
    
    .detail-item {
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    
    .tier-badge .las {
        font-size: 14px;
    }
    
    .eligibility-status {
        border-top: 1px solid #e3e6f0;
        padding-top: 15px;
    }
    
    .program-requirements ul {
        max-height: 100px;
        overflow-y: auto;
    }
    
    .tier-requirements {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
</style>
@endpush