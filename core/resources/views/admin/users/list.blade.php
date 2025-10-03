@extends('admin.layouts.app')
@section('panel')
    @php
        $request = request();
        $tableName = 'users_list';
        $tableConfiguration = $tableConfiguration = tableConfiguration($tableName);

        $columns = collect([
            prepareTableColumn('account_number', 'Account No.'),
            prepareTableColumn('username', 'Username'),
            prepareTableColumn('fullname', 'Name'),
            prepareTableColumn('email', 'Email'),
            prepareTableColumn(
                'banking_profile_complete',
                'Banking Profile',
                '$item->banking_profile_complete ? "<span class=\"badge badge--success\">Completed</span>" : "<span class=\"badge badge--warning\">Incomplete</span>"',
                filter: 'select',
                filterOptions: ['Yes' => 'Completed', 'No' => 'Incomplete'],
                echoable: true
            ),
            prepareTableColumn('balance', 'Balance', 'showAmount($item->balance)', filter: 'range'),
            prepareTableColumn('created_at', 'Registered At', 'showDateTime("$item->created_at", "d M, Y")', filter: 'date')
        ]);

        $action = [
            'name' => 'Action',
            'style' => 'dropdown',
            'show' => can('admin.users.detail') || can('admin.users.kyc.details') || can('admin.users.login') || can('admin.report.login.history') || can('admin.users.notification.log') || can('admin.users.notification.single'),
            'buttons' => [
                [
                    'name' => 'View Details',
                    'link' => 'route("admin.users.detail", $item->id)',
                    'show' => can('admin.users.detail'),
                ],
                [
                    'name' => 'View KYC Data',
                    'link' => 'route("admin.users.kyc.details", $item->id)',
                    'show' => can('admin.users.kyc.details'),
                ],
                [
                    'name' => 'Login As User',
                    'link' => 'route("admin.users.login", $item->id)',
                    'show' => can('admin.users.login'),
                    'attributes' => [
                        'target' => "json_encode('blank')"
                    ]
                ],
                [
                    'name' => 'Login History',
                    'link' => 'route("admin.report.login.history", $item->id)',
                    'show' => can('admin.report.login.history'),
                ],
                [
                    'name' => 'Send Notification',
                    'link' => 'route("admin.users.notification.single", $item->id)',
                    'show' => can('admin.users.notification.single'),
                ],
                [
                    'name' => 'All Notifications',
                    'link' => 'route("admin.users.notification.log", $item->id)',
                    'show' => can('admin.users.notification.log'),
                ],
            ],
        ];

        $availableColumns = $columns->pluck('id')->toArray();

        if($tableConfiguration && $tableConfiguration->visible_columns){
            $configuredColumns = array_values(array_intersect($tableConfiguration->visible_columns, $availableColumns));
            $visibleColumns = count($configuredColumns) ? $configuredColumns : $availableColumns;
        }else{
            $visibleColumns = $availableColumns;
        }

        $userSnapshot = \App\Models\User::selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN ev = 1 THEN 1 ELSE 0 END) as email_verified')
            ->selectRaw('SUM(CASE WHEN sv = 1 THEN 1 ELSE 0 END) as sms_verified')
            ->selectRaw('SUM(CASE WHEN banking_profile_complete = 1 THEN 1 ELSE 0 END) as banking_complete')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as suspended', [\App\Constants\Status::DISABLE])
            ->first();

        $totalUsers = (int) ($userSnapshot->total ?? 0);
        $emailVerified = (int) ($userSnapshot->email_verified ?? 0);
        $smsVerified = (int) ($userSnapshot->sms_verified ?? 0);
        $bankingCompleted = (int) ($userSnapshot->banking_complete ?? 0);
        $suspendedUsers = (int) ($userSnapshot->suspended ?? 0);

        $emailVerificationRate = $totalUsers ? round(($emailVerified / $totalUsers) * 100) : 0;
        $bankingCompletionRate = $totalUsers ? round(($bankingCompleted / $totalUsers) * 100) : 0;
        $smsVerificationRate = $totalUsers ? round(($smsVerified / $totalUsers) * 100) : 0;
        $newestFirstUrl = request()->fullUrlWithQuery(['order_by_column' => 'created_at', 'order_by' => 'desc']);
        $oldestFirstUrl = request()->fullUrlWithQuery(['order_by_column' => 'created_at', 'order_by' => 'asc']);
    @endphp

    <div class="d-flex flex-column flex-xl-row align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">@lang('Customer Directory')</h2>
            <p class="text-muted mb-0">@lang('Monitor onboarding progress, verification, and banking profile readiness for every customer.')</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ $newestFirstUrl }}" class="btn btn-outline-primary btn-sm">
                <i class="las la-clock me-1"></i> @lang('Newest First')
            </a>
            <a href="{{ $oldestFirstUrl }}" class="btn btn-outline-secondary btn-sm">
                <i class="las la-history me-1"></i> @lang('Oldest Accounts')
            </a>
        </div>
    </div>

    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold small mb-1">@lang('Total Users')</p>
                            <h3 class="fw-bold mb-0">{{ number_format($totalUsers) }}</h3>
                        </div>
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary" style="width: 48px; height: 48px;">
                            <i class="las la-users fs-4"></i>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-3">
                        <span class="badge bg-success bg-opacity-10 text-success">{{ $emailVerificationRate }}% @lang('verified')</span>
                        <span class="text-muted small">{{ number_format($emailVerified) }} @lang('emails')</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold small mb-1">@lang('Banking Profile')</p>
                            <h3 class="fw-bold mb-0">{{ number_format($bankingCompleted) }}</h3>
                        </div>
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10 text-success" style="width: 48px; height: 48px;">
                            <i class="las la-university fs-4"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $bankingCompletionRate }}%;" aria-valuenow="{{ $bankingCompletionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">{{ $bankingCompletionRate }}% @lang('completed profiles')</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold small mb-1">@lang('SMS Ready')</p>
                            <h3 class="fw-bold mb-0">{{ number_format($smsVerified) }}</h3>
                        </div>
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-info bg-opacity-10 text-info" style="width: 48px; height: 48px;">
                            <i class="las la-sms fs-4"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $smsVerificationRate }}%;" aria-valuenow="{{ $smsVerificationRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">{{ $smsVerificationRate }}% @lang('SMS verified')</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold small mb-1">@lang('Suspended')</p>
                            <h3 class="fw-bold mb-0">{{ number_format($suspendedUsers) }}</h3>
                        </div>
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-danger bg-opacity-10 text-danger" style="width: 48px; height: 48px;">
                            <i class="las la-user-slash fs-4"></i>
                        </span>
                    </div>
                    <p class="text-muted small mt-3 mb-0">@lang('Keep an eye on suspended accounts and reinstate when necessary.')</p>
                </div>
            </div>
        </div>
    </div>

    <x-viser_table.table
        :data="$users"
        :columns="$columns"
        :action="$action"
        :columnConfig="true"
        :tableName="$tableName"
        :visibleColumns="$visibleColumns"
        class="table-modern-wrapper table-responsive"
        table-class="table table-modern align-middle mb-0"
        card-class="card viser--table border-0 shadow-sm rounded-4 overflow-hidden"
    />
@endsection

@if($users->total() > 0 && can('admin.users.notification.all.send'))
@push('breadcrumb-plugins')
    <a href="{{appendQuery('notify', 1)}}" class="btn btn--dark">
        <i class="fas fa-bell"></i>
        @lang('Notify') <strong class="mx-1">{{$users->total()}}</strong> {{__(str_replace('All', '' ,$pageTitle))}} @lang('Holders')
        @if($request->has('filter'))(@lang('Filtered'))@endif
    </a>
    @endpush
@endif
