@extends('admin.layouts.app')

@php
    use App\Constants\Status;
@endphp

@section('panel')
    <div class="row gy-4">
        <div class="col-xl-3 col-lg-5 col-md-5">
            <div class="row gy-4">
                <div class="col-sm-9 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title d-flex justify-content-center gap-2">
                                <div class="d-flex flex-wrap gap-2">
                                    @can('admin.users.add.sub.balance')
                                        <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn-success btn-sm bal-btn" data-act="add" title="@lang('Add Balance')">
                                            <i class="las la-plus-circle"></i>
                                        </button>

                                        <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn-danger btn-sm bal-btn" data-act="sub" title="@lang('Subtract Balance')">
                                            <i class="las la-minus-circle"></i>
                                        </button>
                                    @endcan

                                    @can('admin.users.login')
                                        <a href="{{ route('admin.users.login', $user->id) }}" target="_blank" class="btn btn-info btn-sm" title="@lang('Login as User')">
                                            <i class="las la-sign-in-alt"></i>
                                        </a>
                                    @endcan

                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#withdrawalControlModal" title="@lang('Withdrawal Control')">
                                        <i class="las la-hand-paper"></i>
                                    </button>

                                    @can('admin.users.notification.single')
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendNotificationModal" title="@lang('Send Notification')">
                                            <i class="las la-paper-plane"></i>
                                        </button>
                                    @endcan

                                    @if(gs()->billing_codes_enabled ?? 0)
                                        @can('admin.billing.codes.user')
                                            <a href="{{ route('admin.billing.codes.user', $user->id) }}" class="btn btn-warning btn-sm" title="@lang('Billing Codes')">
                                                <i class="las la-receipt"></i>
                                            </a>
                                        @endcan
                                    @endif

                                    @can('admin.report.login.history')
                                        <a href="{{ route('admin.report.login.history') }}?search={{ $user->username }}" class="btn btn-primary btn-sm" title="@lang('Login History')">
                                            <i class="las la-history"></i>
                                        </a>
                                    @endcan

                                    @can('admin.users.notification.log')
                                        <a href="{{ route('admin.users.notification.log', $user->id) }}" class="btn btn-secondary btn-sm" title="@lang('Notifications')">
                                            <i class="las la-bell"></i>
                                        </a>
                                    @endcan

                                    @can('admin.users.kyc.details')
                                        @if ($user->kyc_data)
                                            <a href="{{ route('admin.users.kyc.details', $user->id) }}" target="_blank" class="btn btn-dark btn-sm" title="@lang('KYC Data')">
                                                <i class="las la-user-shield"></i>
                                            </a>
                                        @endif
                                    @endcan

                                    @can('admin.users.status')
                                        @if ($user->status == Status::USER_ACTIVE)
                                            <button type="button" class="btn btn-warning btn-sm userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal" title="@lang('Ban Account')">
                                                <i class="las la-user-slash"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-success btn-sm userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal" title="@lang('Unban Account')">
                                                <i class="las la-user-check"></i>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <img class="account-holder-image w-100" src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, null, true) }}" alt="account-holder-image">
                            
                            <!-- Verification Status below image -->
                            <div class="d-flex justify-content-center gap-3 mt-3">
                                <h6>
                                    @if ($user->ev)
                                        <i class="la la-check-circle text--success"></i>
                                    @else
                                        <i class="la la-times-circle text--danger"></i>
                                    @endif
                                    @lang('Email')
                                </h6>
                                <h6>
                                    @if ($user->sv)
                                        <i class="la la-check-circle text--success"></i>
                                    @else
                                        <i class="la la-times-circle text--danger"></i>
                                    @endif
                                    @lang('Mobile')
                                </h6>
                                <h6>
                                    @if ($user->kv)
                                        <i class="la la-check-circle text--success"></i>
                                    @else
                                        <i class="la la-times-circle text--danger"></i>
                                    @endif
                                    @lang('KYC')
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-9 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-center">@lang('Basic Information')</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                    <small class="text-muted">@lang('Username')</small>
                                    <h6 class="mb-0">{{ $user->username }}</h6>
                                </div>

                                <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                    <small class="text-muted">@lang('Account Number')</small>
                                    <h6 class="mb-0">{{ $user->account_number }}</h6>
                                </div>

                                <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                    <small class="text-muted">@lang('Branch')</small>
                                    <h6 class="mb-0">{{ $user->branch->name ?? 'Online' }}</h6>
                                </div>

                                @if ($user->referrer)
                                    <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                        <small class="text-muted">@lang('Referred By')</small>
                                        @can('admin.users.detail')
                                            <a href="{{ route('admin.users.detail', $user->ref_by) }}">
                                                <h6 class="text--primary mb-0">{{ $user->referrer->username }}</h6>
                                            </a>
                                        @else
                                            <h6 class="text--primary mb-0">{{ $user->referrer->username }}</h6>
                                        @endcan
                                    </div>
                                @endif

                                @if ($user->branch)
                                    <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                        <small class="text-muted">@lang('Registered By')</small>
                                        <h6 class="mb-0">{{ $user->branchStaff->name }}</h6>
                                    </div>
                                @endif

                                <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                    <small class="text-muted">@lang('Joined On')</small>
                                    <h6 class="mb-0">{{ showDateTime($user->created_at, 'd M Y, h:i A') }}</h6>
                                </div>

                                @if($user->banking_profile_complete)
                                    <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                        <small class="text-muted">@lang('Banking Profile')</small>
                                        <h6 class="text-success mb-0">@lang('Completed')</h6>
                                    </div>

                                    @if($user->full_legal_name)
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                            <small class="text-muted">@lang('Legal Name')</small>
                                            <h6 class="mb-0 text-truncate" style="max-width: 120px;">{{ $user->full_legal_name }}</h6>
                                        </div>
                                    @endif

                                    @if($user->account_type_preference)
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                            <small class="text-muted">@lang('Account Type')</small>
                                            <h6 class="mb-0 text-truncate" style="max-width: 120px;">{{ ucwords(str_replace('_', ' ', $user->account_type_preference)) }}</h6>
                                        </div>
                                    @endif

                                    @if($user->preferred_currency)
                                        <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                            <small class="text-muted">@lang('Currency')</small>
                                            <h6 class="mb-0">{{ strtoupper($user->preferred_currency) }}</h6>
                                        </div>
                                    @endif
                                @else
                                    <div class="list-group-item d-flex justify-content-between align-items-center border-0">
                                        <small class="text-muted">@lang('Banking Profile')</small>
                                        <h6 class="text-warning mb-0">@lang('Incomplete')</h6>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-7 col-md-7">
            <!-- Nav Pills for switching between sections -->
            <div class="card">
                <div class="card-header position-relative">
                    <ul class="nav nav-pills nav-fill nav-pills-scroll" id="userInfoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="pill" data-bs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="true">
                                <i class="las la-tachometer-alt"></i> @lang('Dashboard')
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="basic-info-tab" data-bs-toggle="pill" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info" aria-selected="false">
                                <i class="las la-user"></i> @lang('User Information')
                            </button>
                        </li>
                        @can('admin.users.update.password')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-management-tab" data-bs-toggle="pill" data-bs-target="#password-management" type="button" role="tab" aria-controls="password-management" aria-selected="false">
                                <i class="las la-key"></i> @lang('Password Management')
                            </button>
                        </li>
                        @endcan
                        @can('admin.users.update.transfer.pin')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pin-management-tab" data-bs-toggle="pill" data-bs-target="#pin-management" type="button" role="tab" aria-controls="pin-management" aria-selected="false">
                                <i class="las la-lock"></i> @lang('PIN Management')
                            </button>
                        </li>
                        @endcan
                        @if($user->banking_profile_complete)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="banking-profile-tab" data-bs-toggle="pill" data-bs-target="#banking-profile" type="button" role="tab" aria-controls="banking-profile" aria-selected="false">
                                <i class="las la-university"></i> @lang('Banking Profile')
                            </button>
                        </li>
                        @endif
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="userInfoTabsContent">
                        <!-- Dashboard Tab -->
                        <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                            <h5 class="card-title mb-4">@lang('Account Overview for') {{ $user->fullname }}</h5>
                            
                            <!-- Financial Overview Widgets -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="las la-chart-line"></i> @lang('Financial Overview')</h6>
                                </div>
                            </div>
                            <div class="row gy-4 mb-5">
                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="1" link="admin.report.transaction" :parameters="['username' => $user->username]" title="Balance" icon="las la-money-bill-wave-alt" value="{{ showAmount($user->balance) }}" bg="info" type="2" />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="1" link="admin.deposit.list" :parameters="['username' => $user->username]" title="Deposits" icon="las la-wallet" value="{{ showAmount($widget['total_deposit']) }}" bg="success" type="2" />
                                </div>
                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="1" link="admin.withdraw.data.all" :parameters="['username' => $user->username]" title="Withdrawals" icon="la la-bank" value="{{ showAmount($widget['total_withdrawn']) }}" bg="6" type="2" />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="1" link="admin.transfers.index" :parameters="['username' => $user->username]" title="Total Transferred" icon="las la-exchange-alt" value="{{ showAmount($widget['total_transferred']) }}" bg="17" type="2" />
                                </div>
                            </div>

                            <!-- Investment & Savings Overview -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="las la-piggy-bank"></i> @lang('Investment & Savings')</h6>
                                </div>
                            </div>
                            <div class="row gy-4 mb-5">
                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <x-widget style="2" color="primary" icon="la la-money-bill" title="Running FDR" value="{{ $widget['total_fdr'] }}" link="admin.fdr.index" query_string="search={{ $user->username }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <x-widget style="2" color="green" icon="la la-box" title="Running DPS" value="{{ $widget['total_dps'] }}" link="admin.dps.index" query_string="search={{ $user->username }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <x-widget style="2" color="warning" icon="la la-hand-holding-usd" title="Running Loan" value="{{ $widget['total_loan'] }}" link="admin.loan.running" query_string="search={{ $user->username }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-xl-4 col-sm-6">
                                    <x-widget style="2" color="info" icon="la la-user-friends" title="Beneficiaries" value="{{ $widget['total_beneficiaries'] }}" link="admin.users.beneficiaries" parameters="{{ $user->id }}" overlay_icon=0 icon_style=solid />
                                </div>
                            </div>

                            <!-- Account Activity & Status -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="las la-chart-area"></i> @lang('Account Activity & Status')</h6>
                                </div>
                            </div>
                            <div class="row gy-4 mb-5">
                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="3" title="Account Status" icon="las la-user-check" value="{{ $user->status == 1 ? 'Active' : 'Banned' }}" bg="{{ $user->status == 1 ? 'success' : 'danger' }}" type="2" />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="3" title="Last Login" icon="las la-clock" value="{{ $user->last_login ? showDateTime($user->last_login, 'd M Y') : 'Never' }}" bg="info" type="2" />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="3" title="Member Since" icon="las la-calendar-plus" value="{{ showDateTime($user->created_at, 'd M Y') }}" bg="warning" type="2" />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="3" title="Account Age" icon="las la-hourglass-half" value="{{ $user->created_at->diffForHumans() }}" bg="primary" type="2" />
                                </div>
                            </div>

                            <!-- Verification & Security Status -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="las la-shield-alt"></i> @lang('Verification & Security')</h6>
                                </div>
                            </div>
                            <div class="row gy-4 mb-5">
                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="2" color="{{ $user->ev ? 'success' : 'danger' }}" icon="las la-envelope-open" title="Email Verification" value="{{ $user->ev ? 'Verified' : 'Unverified' }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="2" color="{{ $user->sv ? 'success' : 'danger' }}" icon="las la-sms" title="SMS Verification" value="{{ $user->sv ? 'Verified' : 'Unverified' }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="2" color="{{ $user->kv ? 'success' : 'warning' }}" icon="las la-user-shield" title="KYC Status" value="{{ $user->kv ? 'Verified' : 'Pending' }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="2" color="{{ $user->ts ? 'success' : 'secondary' }}" icon="las la-lock" title="2FA Security" value="{{ $user->ts ? 'Enabled' : 'Disabled' }}" overlay_icon=0 icon_style=solid />
                                </div>
                            </div>

                            <!-- Banking Profile & Preferences -->
                            @if($user->banking_profile_complete)
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="las la-university"></i> @lang('Banking Profile Summary')</h6>
                                </div>
                            </div>
                            <div class="row gy-4 mb-5">
                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="3" title="Banking Profile" icon="las la-check-circle" value="Complete" bg="success" type="2" />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="3" title="Account Type" icon="las la-credit-card" value="{{ ucwords(str_replace('_', ' ', $user->account_type_preference ?? 'Standard')) }}" bg="info" type="2" />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="3" title="Currency" icon="las la-coins" value="{{ strtoupper($user->preferred_currency ?? gs('cur_text')) }}" bg="warning" type="2" />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="3" title="Profile Completed" icon="las la-calendar-check" value="{{ showDateTime($user->banking_profile_completed_at, 'd M Y') }}" bg="primary" type="2" />
                                </div>
                            </div>
                            @endif

                            <!-- Transaction Summary -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="las la-exchange-alt"></i> @lang('Transaction Summary')</h6>
                                </div>
                            </div>
                            <div class="row gy-4">
                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="5" color="success" icon="las la-list" title="Total Transactions" value="{{ number_format(($widget['total_deposit'] + $widget['total_withdrawn'] + $widget['total_transferred'])) }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="5" color="info" icon="las la-balance-scale" title="Net Flow" value="{{ showAmount($widget['total_deposit'] - $widget['total_withdrawn']) }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    @php
                                        $riskLevel = 'Low';
                                        $riskColor = 'success';
                                        if($widget['total_withdrawn'] > $widget['total_deposit']) {
                                            $riskLevel = 'High';
                                            $riskColor = 'danger';
                                        } elseif($widget['total_withdrawn'] > ($widget['total_deposit'] * 0.7)) {
                                            $riskLevel = 'Medium';
                                            $riskColor = 'warning';
                                        }
                                    @endphp
                                    <x-widget style="5" color="{{ $riskColor }}" icon="las la-exclamation-triangle" title="Risk Level" value="{{ $riskLevel }}" overlay_icon=0 icon_style=solid />
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <x-widget style="5" color="primary" icon="las la-building" title="Branch" value="{{ $user->branch->name ?? 'Online Banking' }}" overlay_icon=0 icon_style=solid />
                                </div>
                            </div>
                        </div>

                        <!-- Basic User Information Tab -->
                        <div class="tab-pane fade" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                            <h5 class="card-title mb-3">@lang('Information of') {{ $user->fullname }}</h5>
                            <form action="{{ route('admin.users.update', [$user->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('First Name')</label>
                                            <input class="form-control" type="text" name="firstname" required value="{{ $user->firstname }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">@lang('Last Name')</label>
                                            <input class="form-control" type="text" name="lastname" required value="{{ $user->lastname }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Email')</label>
                                            <input class="form-control" type="email" name="email" value="{{ $user->email }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Mobile Number')</label>
                                            <div class="input-group ">
                                                <span class="input-group-text mobile-code">+{{ $user->dial_code }}</span>
                                                <input type="number" name="mobile" value="{{ $user->mobile }}" id="mobile" class="form-control checkUser" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('City')</label>
                                            <input class="form-control" type="text" name="city" value="{{ @$user->city }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label>@lang('State')</label>
                                            <input class="form-control" type="text" name="state" value="{{ @$user->state }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label>@lang('Zip/Postal')</label>
                                            <input class="form-control" type="text" name="zip" value="{{ @$user->zip }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label class="required">@lang('Country')</label>
                                            <select name="country" class="form-control select2">
                                                @foreach ($countries as $key => $country)
                                                    <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}" @selected($user->country_code == $key)>{{ __($country->country) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group ">
                                            <label>@lang('Address')</label>
                                            <input class="form-control" type="text" name="address" value="{{ @$user->address }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Preferred Currency')</label>
                                            <select name="preferred_currency" class="form-control select2">
                                                <option value="">@lang('System Default') ({{ gs('cur_text') }})</option>
                                                @foreach($currencies as $code => $name)
                                                    <option value="{{ $code }}" @selected($user->preferred_currency == $code)>
                                                        {{ $code }} - {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">@lang('This currency will be used for all user transactions')</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Current Currency Display')</label>
                                            <input class="form-control" type="text" value="{{ strtoupper($user->preferred_currency ?? gs('cur_text')) }}" readonly>
                                            <small class="text-muted">@lang('Currently active currency for this user')</small>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>@lang('Email Verification')</label>
                                            <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="ev" @if ($user->ev) checked @endif>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>@lang('Mobile Verification')</label>
                                            <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="sv" @if ($user->sv) checked @endif>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-12">
                                        <div class="form-group">
                                            <label>@lang('2FA Verification') </label>
                                            <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="ts" @if ($user->ts) checked @endif>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-12">
                                        <div class="form-group">
                                            <label>@lang('KYC') </label>
                                            <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="kv" @if ($user->kv == Status::KYC_VERIFIED) checked @endif>
                                        </div>
                                    </div>
                                    @can('admin.users.update')
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save Changes')
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </form>
                        </div>

                        <!-- Password Management Tab -->
                        @can('admin.users.update.password')
                        <div class="tab-pane fade" id="password-management" role="tabpanel" aria-labelledby="password-management-tab">
                            <h5 class="card-title mb-4">@lang('Password Management for') {{ $user->fullname }}</h5>
                            
                            <div class="alert alert-info">
                                <i class="las la-info-circle me-2"></i>
                                @lang('Manage the login password for user:') <strong>{{ $user->fullname }} ({{ $user->username }})</strong>
                            </div>

                            <form action="{{ route('admin.users.update.password', $user->id) }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('New Password') <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password" required autocomplete="new-password" minlength="6">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                                    <i class="las la-eye"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">@lang('Minimum 6 characters required')</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Confirm Password') <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" minlength="6">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                                    <i class="las la-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check mb-3">
                                            <input type="hidden" name="notify_user" value="0">
                                            <input class="form-check-input" type="checkbox" name="notify_user" id="notifyUserPassword" value="1" checked>
                                            <label class="form-check-label" for="notifyUserPassword">
                                                @lang('Send password change notification to user')
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="text-primary mb-3">@lang('Security Information:')</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul class="list-unstyled small">
                                                            <li><i class="las la-check text-success me-1"></i> @lang('Password will be encrypted')</li>
                                                            <li><i class="las la-check text-success me-1"></i> @lang('User will be logged out from all devices')</li>
                                                            <li><i class="las la-check text-success me-1"></i> @lang('Email notification will be sent')</li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="small text-muted">
                                                            <strong>@lang('Last Password Update:')</strong><br>
                                                            {{ $user->updated_at ? showDateTime($user->updated_at, 'd M Y, h:i A') : 'Never' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn--primary btn-lg w-100">
                                            <i class="las la-key me-2"></i>@lang('Update Password')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endcan

                        <!-- PIN Management Tab -->
                        @can('admin.users.update.transfer.pin')
                        <div class="tab-pane fade" id="pin-management" role="tabpanel" aria-labelledby="pin-management-tab">
                            <h5 class="card-title mb-4">@lang('Transfer PIN Management for') {{ $user->fullname }}</h5>
                            
                            <div class="alert alert-warning">
                                <i class="las la-shield-alt me-2"></i>
                                @lang('Managing transfer PIN for:') <strong>{{ $user->fullname }} ({{ $user->username }})</strong>
                            </div>

                            @if($user->hasTransferPin())
                                <div class="alert alert-success">
                                    <i class="las la-check-circle me-2"></i>
                                    @lang('User currently has a transfer PIN set')
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="las la-info-circle me-2"></i>
                                    @lang('User does not have a transfer PIN set')
                                </div>
                            @endif

                            <form action="{{ route('admin.users.update.transfer.pin', $user->id) }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('New Transfer PIN') <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control text-center fs-4" name="transfer_pin" maxlength="4" pattern="[0-9]{4}" required autocomplete="new-password" placeholder="••••" style="letter-spacing: 1rem;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                                    <i class="las la-eye"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">@lang('Must be exactly 4 digits (0-9)')</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Confirm Transfer PIN') <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control text-center fs-4" name="transfer_pin_confirmation" maxlength="4" pattern="[0-9]{4}" required autocomplete="new-password" placeholder="••••" style="letter-spacing: 1rem;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                                    <i class="las la-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check mb-3">
                                            <input type="hidden" name="notify_user" value="0">
                                            <input class="form-check-input" type="checkbox" name="notify_user" id="notifyUserTransferPin" value="1" checked>
                                            <label class="form-check-label" for="notifyUserTransferPin">
                                                @lang('Send PIN change notification to user')
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="text-primary mb-3">@lang('Transfer PIN Usage:')</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul class="list-unstyled small">
                                                            <li><i class="las la-check text-success me-1"></i> @lang('Wire transfer verification')</li>
                                                            <li><i class="las la-check text-success me-1"></i> @lang('Billing codes authentication')</li>
                                                            <li><i class="las la-check text-success me-1"></i> @lang('Secure transaction confirmation')</li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="small text-muted">
                                                            <strong>@lang('PIN Status:')</strong><br>
                                                            @if($user->hasTransferPin())
                                                                <span class="text-success">@lang('Active')</span>
                                                            @else
                                                                <span class="text-warning">@lang('Not Set')</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-purple btn-lg w-100">
                                            <i class="las la-lock me-2"></i>@lang('Update Transfer PIN')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endcan

                        <!-- Banking Profile Information Tab -->
                        @if($user->banking_profile_complete)
                        <div class="tab-pane fade" id="banking-profile" role="tabpanel" aria-labelledby="banking-profile-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">@lang('Banking Profile Information')</h5>
                                <small class="text-muted">@lang('Completed on') {{ showDateTime($user->banking_profile_completed_at, 'd M Y, h:i A') }}</small>
                            </div>
                            
                            <div class="row">
                                <!-- Personal Information -->
                                <div class="col-12">
                                    <h6 class="text-primary mb-3"><i class="las la-user-circle"></i> @lang('Personal Information')</h6>
                                </div>
                                
                                @if($user->title)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Title')</label>
                                        <input class="form-control" type="text" value="{{ $user->title }}" readonly>
                                    </div>
                                </div>
                                @endif

                                @if($user->full_legal_name)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Full Legal Name')</label>
                                        <input class="form-control" type="text" value="{{ $user->full_legal_name }}" readonly>
                                    </div>
                                </div>
                                @endif

                                @if($user->date_of_birth)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Date of Birth')</label>
                                        <input class="form-control" type="text" value="{{ showDateTime($user->date_of_birth, 'd M Y') }}" readonly>
                                    </div>
                                </div>
                                @endif

                                @if($user->gender)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Gender')</label>
                                        <input class="form-control" type="text" value="{{ ucfirst($user->gender) }}" readonly>
                                    </div>
                                </div>
                                @endif

                                @if($user->nationality)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Nationality')</label>
                                        <input class="form-control" type="text" value="{{ $user->nationality }}" readonly>
                                    </div>
                                </div>
                                @endif

                                <!-- Banking Preferences -->
                                <div class="col-12 mt-4">
                                    <h6 class="text-primary mb-3"><i class="las la-university"></i> @lang('Banking Preferences')</h6>
                                </div>

                                @if($user->account_type_preference)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Account Type Preference')</label>
                                        <input class="form-control" type="text" value="{{ ucwords(str_replace('_', ' ', $user->account_type_preference)) }}" readonly>
                                    </div>
                                </div>
                                @endif

                                @if($user->preferred_currency)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Preferred Currency')</label>
                                        <input class="form-control" type="text" value="{{ strtoupper($user->preferred_currency) }}" readonly>
                                    </div>
                                </div>
                                @endif

                                @if($user->purpose_of_account)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>@lang('Purpose of Account')</label>
                                        <input class="form-control" type="text" value="{{ ucwords(str_replace('_', ' ', $user->purpose_of_account)) }}" readonly>
                                    </div>
                                </div>
                                @endif

                                <!-- Employment Information -->
                                <div class="col-12 mt-4">
                                    <h6 class="text-primary mb-3"><i class="las la-briefcase"></i> @lang('Employment Information')</h6>
                                </div>

                                @if($user->employment_status)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Employment Status')</label>
                                        <input class="form-control" type="text" value="{{ ucwords(str_replace('_', ' ', $user->employment_status)) }}" readonly>
                                    </div>
                                </div>
                                @endif

                                @if($user->occupation)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Occupation')</label>
                                        <input class="form-control" type="text" value="{{ $user->occupation }}" readonly>
                                    </div>
                                </div>
                                @endif

                                @if($user->source_of_funds)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>@lang('Source of Funds')</label>
                                        <input class="form-control" type="text" value="{{ ucwords(str_replace('_', ' ', $user->source_of_funds)) }}" readonly>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('admin.users.add.sub.balance')
        {{-- Add Sub Balance MODAL --}}
        <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('admin.users.add.sub.balance', $user->id) }}" class="balanceAddSub disableSubmission" method="POST">
                        @csrf
                        <input type="hidden" name="act">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>@lang('Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="amount" class="form-control" placeholder="@lang('Please provide positive amount')" required>
                                    <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>@lang('Remark')</label>
                                <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('admin.users.notification.single')
        @php
            $defaultNotificationMethod = null;
            if (gs('pn')) {
                $defaultNotificationMethod = 'push';
            } elseif (gs('en')) {
                $defaultNotificationMethod = 'email';
            } elseif (gs('sn')) {
                $defaultNotificationMethod = 'sms';
            }
        @endphp

        <div id="sendNotificationModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="las la-bell me-2"></i>@lang('Send Notification to') {{ $user->username }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>

                    @if ($defaultNotificationMethod)
                        <form action="{{ route('admin.users.notification.single', $user->id) }}" method="POST" class="quickNotificationForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="via" value="{{ $defaultNotificationMethod }}">
                            <div class="modal-body">
                                <div class="mb-4">
                                    <div class="row g-3">
                                        @if (gs('en'))
                                            <div class="col-6 col-md-4">
                                                <div class="notification-via quick-notification-option {{ $defaultNotificationMethod == 'email' ? 'active' : '' }}" data-method="email">
                                                    <span class="active-badge"><i class="las la-check"></i></span>
                                                    <div class="send-via-method">
                                                        <i class="las la-envelope"></i>
                                                        <h5 class="mb-0">@lang('Email')</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (gs('sn'))
                                            <div class="col-6 col-md-4">
                                                <div class="notification-via quick-notification-option {{ $defaultNotificationMethod == 'sms' ? 'active' : '' }}" data-method="sms">
                                                    <span class="active-badge"><i class="las la-check"></i></span>
                                                    <div class="send-via-method">
                                                        <i class="las la-sms"></i>
                                                        <h5 class="mb-0">@lang('SMS')</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (gs('pn'))
                                            <div class="col-12 col-md-4">
                                                <div class="notification-via quick-notification-option {{ $defaultNotificationMethod == 'push' ? 'active' : '' }}" data-method="push">
                                                    <span class="active-badge"><i class="las la-check"></i></span>
                                                    <div class="send-via-method">
                                                        <i class="las la-broadcast-tower"></i>
                                                        <h5 class="mb-0">@lang('Push')</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="alert alert-info mt-3 quick-push-hint {{ $defaultNotificationMethod == 'push' ? '' : 'd-none' }}" role="alert">
                                        <i class="las la-info-circle me-1"></i>
                                        @lang("Push notifications are delivered to the user's in-app notification panel and stored for later reference.")
                                    </div>
                                </div>

                                <div class="form-group quick-subject-wrapper {{ $defaultNotificationMethod == 'sms' ? 'd-none' : '' }}">
                                    <label class="form-label">@lang('Subject / Title')</label>
                                    <input type="text" name="subject" class="form-control" placeholder="@lang('Enter subject or title')" @if($defaultNotificationMethod != 'sms') required @endif>
                                </div>

                                <div class="form-group quick-push-image {{ $defaultNotificationMethod == 'push' ? '' : 'd-none' }}">
                                    <label class="form-label">@lang('Image (optional)')</label>
                                    <input type="file" name="image" class="form-control" accept=".png,.jpg,.jpeg">
                                    <small class="text-muted">@lang('Supported formats: .png, .jpg, .jpeg')</small>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="form-label">@lang('Message')</label>
                                    <textarea name="message" rows="6" class="form-control" placeholder="@lang('Write your message...')" required></textarea>
                                    <small class="text-muted d-block mt-2">@lang("This message will appear instantly in the user's notification center when push is selected.")</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn--primary w-100 h-45">
                                    <i class="las la-paper-plane me-1"></i> @lang('Send Notification')
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="modal-body">
                            <div class="alert alert-warning mb-0" role="alert">
                                <i class="las la-exclamation-triangle me-2"></i>
                                @lang('No notification channel is enabled. Please enable email, SMS, or push notifications from the notification settings first.')
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark w-100 h-45" data-bs-dismiss="modal">@lang('Close')</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endcan

    @can('admin.users.status')
        <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if ($user->status == Status::USER_ACTIVE)
                                @lang('Ban Account')
                            @else
                                @lang('Unban Account')
                            @endif
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            @if ($user->status == Status::USER_ACTIVE)
                                <h6 class="mb-2">@lang('If you ban this account he/she won\'t able to access his/her dashboard.')</h6>
                                <div class="form-group">
                                    <label>@lang('Reason')</label>
                                    <textarea class="form-control" name="reason" rows="4" required></textarea>
                                </div>
                            @else
                                <p><span>@lang('Ban reason was'):</span></p>
                                <p>{{ $user->ban_reason }}</p>
                                <h4 class="text-center mt-3">@lang('Are you sure to unban this account?')</h4>
                            @endif
                        </div>
                        <div class="modal-footer">
                            @if ($user->status == Status::USER_ACTIVE)
                                <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                            @else
                                <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                                <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- Withdrawal Control Modal --}}
    <div id="withdrawalControlModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Withdrawal Control Settings')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.withdrawal.control.update', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Withdrawal Status')</label>
                            <select name="status" class="form-control" id="withdrawalStatus" required>
                                <option value="allowed">@lang('Allowed')</option>
                                <option value="pending_review">@lang('Pending Review')</option>
                                <option value="on_hold">@lang('On Hold')</option>
                                <option value="suspended">@lang('Suspended')</option>
                                <option value="restricted">@lang('Restricted')</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="reasonGroup">
                            <label>@lang('Reason') <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="4" id="withdrawalReason" placeholder="@lang('Provide a clear reason for this withdrawal control status...')"></textarea>
                            <small class="text-muted">@lang('This reason will be shown to the user when they attempt to withdraw.')</small>
                        </div>

                        <div id="currentControlInfo" class="alert alert-info" style="display: none;">
                            <p class="mb-1"><strong>@lang('Current Status'):</strong> <span id="currentStatus"></span></p>
                            <p class="mb-1"><strong>@lang('Set By'):</strong> <span id="setBy"></span></p>
                            <p class="mb-0"><strong>@lang('Last Updated'):</strong> <span id="lastUpdated"></span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Update Control')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('script')
    <script>
        (function($) {
            "use strict"


            $('.bal-btn').on('click', function() {

                $('.balanceAddSub')[0].reset();

                var act = $(this).data('act');
                $('#addSubModal').find('input[name=act]').val(act);
                if (act == 'add') {
                    $('.type').text('Add');
                } else {
                    $('.type').text('Subtract');
                }
            });

            let mobileElement = $('.mobile-code');
            $('select[name=country]').on('change', function() {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });

            const quickNotificationModal = $('#sendNotificationModal');
            if (quickNotificationModal.length) {
                const methodInput = quickNotificationModal.find('input[name="via"]');
                const subjectWrapper = quickNotificationModal.find('.quick-subject-wrapper');
                const subjectInput = subjectWrapper.find('input[name="subject"]');
                const imageWrapper = quickNotificationModal.find('.quick-push-image');
                const methodOptions = quickNotificationModal.find('.quick-notification-option');
                const pushHint = quickNotificationModal.find('.quick-push-hint');

                const syncState = (method) => {
                    methodOptions.removeClass('active');
                    methodOptions.each(function() {
                        if ($(this).data('method') === method) {
                            $(this).addClass('active');
                        }
                    });

                    if (method === 'sms') {
                        subjectWrapper.addClass('d-none');
                        subjectInput.prop('required', false).val('');
                    } else {
                        subjectWrapper.removeClass('d-none');
                        subjectInput.prop('required', true);
                    }

                    if (method === 'push') {
                        imageWrapper.removeClass('d-none');
                        pushHint.removeClass('d-none');
                    } else {
                        imageWrapper.addClass('d-none');
                        imageWrapper.find('input[type="file"]').val('');
                        pushHint.addClass('d-none');
                    }
                };

                methodOptions.on('click', function() {
                    const method = $(this).data('method');
                    methodInput.val(method);
                    syncState(method);
                });

                quickNotificationModal.on('shown.bs.modal', function() {
                    syncState(methodInput.val());
                    quickNotificationModal.find('textarea[name="message"]').trigger('focus');
                });

                quickNotificationModal.on('hidden.bs.modal', function() {
                    const form = quickNotificationModal.find('.quickNotificationForm')[0];
                    if (form) {
                        form.reset();
                    }
                    syncState(methodInput.val());
                    quickNotificationModal.find('textarea[name="message"]').val('');
                });
            }

            // Password toggle functionality
            window.togglePassword = function(button) {
                const input = $(button).siblings('input')[0];
                const icon = $(button).find('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.removeClass('la-eye').addClass('la-eye-slash');
                } else {
                    input.type = 'password';
                    icon.removeClass('la-eye-slash').addClass('la-eye');
                }
            };

            // Transfer PIN input validation
            $('input[name="transfer_pin"], input[name="transfer_pin_confirmation"]').on('input', function() {
                // Only allow numeric input
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Limit to 4 digits
                if (this.value.length > 4) {
                    this.value = this.value.slice(0, 4);
                }
                
                // Match validation
                var pin = $('input[name="transfer_pin"]').val();
                var confirmPin = $('input[name="transfer_pin_confirmation"]').val();
                
                if (confirmPin && pin !== confirmPin) {
                    $('input[name="transfer_pin_confirmation"]')[0].setCustomValidity('PINs do not match');
                } else {
                    $('input[name="transfer_pin_confirmation"]')[0].setCustomValidity('');
                }
            });

            // Auto-focus confirmation field when PIN is complete
            $('input[name="transfer_pin"]').on('input', function() {
                if (this.value.length === 4) {
                    $('input[name="transfer_pin_confirmation"]').focus();
                }
            });

            // Form validation for password confirmation
            $('input[name="password"], input[name="password_confirmation"]').on('input', function() {
                var password = $('input[name="password"]').val();
                var confirmPassword = $('input[name="password_confirmation"]').val();
                
                if (confirmPassword && password !== confirmPassword) {
                    $('input[name="password_confirmation"]')[0].setCustomValidity('Passwords do not match');
                } else {
                    $('input[name="password_confirmation"]')[0].setCustomValidity('');
                }
            });

            // Navigation pills smooth scroll enhancement
            function initNavScroll() {
                const navPills = document.querySelector('#userInfoTabs');
                const cardHeader = document.querySelector('.card-header.position-relative');
                if (!navPills || !cardHeader) return;

                // Add smooth scroll behavior
                navPills.style.scrollBehavior = 'smooth';

                // Update scroll shadows
                function updateScrollShadows() {
                    if (window.innerWidth <= 768) {
                        const scrollLeft = navPills.scrollLeft;
                        const scrollWidth = navPills.scrollWidth;
                        const clientWidth = navPills.clientWidth;
                        const maxScrollLeft = scrollWidth - clientWidth;

                        // Show/hide left shadow
                        if (scrollLeft > 5) {
                            cardHeader.classList.add('show-left-shadow');
                        } else {
                            cardHeader.classList.remove('show-left-shadow');
                        }

                        // Show/hide right shadow
                        if (scrollLeft < maxScrollLeft - 5) {
                            cardHeader.classList.add('show-right-shadow');
                        } else {
                            cardHeader.classList.remove('show-right-shadow');
                        }
                    } else {
                        // Remove shadows on desktop
                        cardHeader.classList.remove('show-left-shadow', 'show-right-shadow');
                    }
                }

                // Handle active tab visibility on mobile
                function scrollToActiveTab() {
                    const activeTab = navPills.querySelector('.nav-link.active');
                    if (activeTab && window.innerWidth <= 768) {
                        const containerRect = navPills.getBoundingClientRect();
                        const tabRect = activeTab.getBoundingClientRect();
                        
                        if (tabRect.left < containerRect.left || tabRect.right > containerRect.right) {
                            activeTab.scrollIntoView({ 
                                behavior: 'smooth', 
                                inline: 'center',
                                block: 'nearest'
                            });
                        }
                    }
                }

                // Listen for scroll events to update shadows
                navPills.addEventListener('scroll', updateScrollShadows);

                // Scroll to active tab when tab changes
                navPills.addEventListener('click', function(e) {
                    if (e.target.classList.contains('nav-link')) {
                        setTimeout(() => {
                            scrollToActiveTab();
                            updateScrollShadows();
                        }, 100);
                    }
                });

                // Initial setup
                setTimeout(() => {
                    scrollToActiveTab();
                    updateScrollShadows();
                }, 100);
            }

            // Initialize navigation scroll
            initNavScroll();

            // Re-initialize on window resize
            $(window).on('resize', function() {
                setTimeout(initNavScroll, 250);
            });

            // Form validation before submit
            $('#updatePasswordModal form').on('submit', function(e) {
                const password = $(this).find('input[name="password"]').val();
                const confirmation = $(this).find('input[name="password_confirmation"]').val();
                
                if (password !== confirmation) {
                    e.preventDefault();
                    alert('@lang("Passwords do not match!")');
                    return false;
                }
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('@lang("Password must be at least 6 characters!")');
                    return false;
                }
            });

            $('#updateTransferPinModal form').on('submit', function(e) {
                const pin = $(this).find('input[name="transfer_pin"]').val();
                const confirmation = $(this).find('input[name="transfer_pin_confirmation"]').val();
                
                if (pin !== confirmation) {
                    e.preventDefault();
                    alert('@lang("Transfer PINs do not match!")');
                    return false;
                }
                
                if (pin.length !== 4) {
                    e.preventDefault();
                    alert('@lang("Transfer PIN must be exactly 4 digits!")');
                    return false;
                }
            });

            // Withdrawal Control Modal
            $('#withdrawalControlModal').on('show.bs.modal', function() {
                // Load current withdrawal control status
                $.ajax({
                    url: '{{ route('admin.users.withdrawal.control.get', $user->id) }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#withdrawalStatus').val(response.data.status);
                            $('#withdrawalReason').val(response.data.reason || '');
                            
                            // Show/hide reason field based on status
                            if (response.data.status === 'allowed') {
                                $('#reasonGroup').slideUp();
                                $('#withdrawalReason').prop('required', false);
                            } else {
                                $('#reasonGroup').slideDown();
                                $('#withdrawalReason').prop('required', true);
                            }
                            
                            // Show current control info if exists
                            if (response.data.set_by) {
                                const statusLabels = {
                                    'allowed': 'Allowed',
                                    'pending_review': 'Pending Review',
                                    'on_hold': 'On Hold',
                                    'suspended': 'Suspended',
                                    'restricted': 'Restricted'
                                };
                                $('#currentStatus').text(statusLabels[response.data.status]);
                                $('#setBy').text(response.data.set_by);
                                $('#lastUpdated').text(response.data.updated_at);
                                $('#currentControlInfo').slideDown();
                            } else {
                                $('#currentControlInfo').slideUp();
                            }
                        }
                    },
                    error: function() {
                        alert('Failed to load withdrawal control settings');
                    }
                });
            });

            // Handle withdrawal status change
            $('#withdrawalStatus').on('change', function() {
                const status = $(this).val();
                if (status === 'allowed') {
                    $('#reasonGroup').slideUp();
                    $('#withdrawalReason').prop('required', false);
                } else {
                    $('#reasonGroup').slideDown();
                    $('#withdrawalReason').prop('required', true);
                }
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .account-holder-image {
            width: 226px !important;
        }
        
        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }
        
        .btn-purple:hover {
            background-color: #5a359c;
            border-color: #5a359c;
            color: white;
        }
        
        /* Navigation pills horizontal scroll */
        .nav-pills-scroll {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scroll-behavior: smooth;
        }

        .nav-pills-scroll::-webkit-scrollbar {
            height: 4px;
        }

        .nav-pills-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .nav-pills-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        .nav-pills-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .nav-pills-scroll .nav-item {
            flex-shrink: 0 !important;
        }

        .nav-pills-scroll .nav-link {
            white-space: nowrap !important;
            min-width: max-content !important;
        }

        /* Mobile scroll hint */
        @media (max-width: 768px) {
            .nav-pills-scroll {
                position: relative;
            }

            .nav-pills-scroll::before {
                content: '← scroll →';
                position: absolute;
                bottom: -2px;
                right: 10px;
                font-size: 0.6rem;
                color: #6c757d;
                opacity: 0.7;
                pointer-events: none;
                z-index: 1;
                animation: fadeInOut 3s ease-in-out 2s;
            }
        }

        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            20%, 80% { opacity: 0.7; }
        }

        /* Mobile responsive button adjustments */
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .card-header .btn-sm {
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
            }

            /* Navigation pills mobile responsive */
            .nav-pills-scroll {
                padding-bottom: 0.5rem;
                margin-bottom: 0.5rem;
            }

            .nav-pills-scroll .nav-link {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
                margin-right: 0.5rem;
            }

            /* Add scroll indicators */
            .card-header.position-relative::before,
            .card-header.position-relative::after {
                content: '';
                position: absolute;
                top: 0;
                width: 15px;
                height: 100%;
                pointer-events: none;
                z-index: 2;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .card-header.position-relative::before {
                left: 0;
                background: linear-gradient(to right, rgba(255,255,255,0.9), transparent);
            }

            .card-header.position-relative::after {
                right: 0;
                background: linear-gradient(to left, rgba(255,255,255,0.9), transparent);
            }

            .card-header.position-relative.show-left-shadow::before {
                opacity: 1;
            }

            .card-header.position-relative.show-right-shadow::after {
                opacity: 1;
            }

            /* Enhance scroll indicators on very small screens */
            @media (max-width: 480px) {
                .nav-pills-scroll .nav-link {
                    font-size: 0.75rem;
                    padding: 0.35rem 0.5rem;
                }

                .card-header.position-relative::before,
                .card-header.position-relative::after {
                    width: 20px;
                    background: linear-gradient(to right, rgba(248,249,250,0.95), transparent);
                }

                .card-header.position-relative::after {
                    background: linear-gradient(to left, rgba(248,249,250,0.95), transparent);
                }
            }
        }

        @media (max-width: 576px) {
            .nav-pills-scroll .nav-link {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
            }

            /* Mobile alert adjustments */
            .tab-pane .alert {
                margin-bottom: 1rem;
                padding: 0.875rem 1rem;
                font-size: 0.9rem;
            }

            .tab-pane .alert i {
                font-size: 1rem;
                margin-right: 0.5rem;
            }
        }

        /* PIN input styling */
        input[name="transfer_pin"],
        input[name="transfer_pin_confirmation"] {
            font-family: monospace;
            font-weight: bold;
        }

        /* Form section styling */
        .tab-pane .card.bg-light {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Tab pane alert styling */
        .tab-pane .alert {
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .tab-pane .alert i {
            font-size: 1.1rem;
            margin-right: 0.75rem;
        }

        .tab-pane .alert strong {
            font-weight: 600;
        }

        /* Navigation spacing */
        .nav-pills .nav-item:not(:last-child) {
            margin-right: 0.5rem;
        }

        @media (max-width: 576px) {
            .nav-pills .nav-item:not(:last-child) {
                margin-right: 0;
            }
        }
        
        /* PIN input styling */
        input[name="transfer_pin"], 
        input[name="transfer_pin_confirmation"] {
            letter-spacing: 0.3em;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        /* Modal form improvements */
        .modal-body .alert {
            border-radius: 8px;
            border: none;
        }
        
        .input-group .btn {
            border-left: 0;
        }
    </style>
@endpush
