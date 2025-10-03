@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <!-- Current Fund Cards -->
        <div class="col-xxl-6 col-xl-7 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">@lang('Current Fund')</h5>
                    <div class="row g-3 fund-widget">
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount($funds['balance'], currencyFormat: false) }}" title="Users Wallet" :box_shadow=false style="2" bg="white" color="success" icon="las la-wallet" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount($funds['fdr'], currencyFormat: false) }}" title="On Account of FDR" :box_shadow=false style="2" bg="white" color="primary" icon="las la-certificate" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount($funds['dps'], currencyFormat: false) }}" title="On Account of DPS" :box_shadow=false style="2" bg="white" color="info" icon="las la-piggy-bank" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount(array_sum($funds), currencyFormat: false) }}" title="Total In Fund" :box_shadow=false style="2" bg="white" color="warning" icon="las la-coins" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount($funds['loans'] ?? 0, currencyFormat: false) }}" title="Outstanding Loans" :box_shadow=false style="2" bg="white" color="danger" icon="las la-hand-holding-usd" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount($funds['deposits'] ?? 0, currencyFormat: false) }}" title="Total Deposits" :box_shadow=false style="2" bg="white" color="teal" icon="las la-credit-card" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount($funds['withdrawals'] ?? 0, currencyFormat: false) }}" title="Total Withdrawals" :box_shadow=false style="2" bg="white" color="orange" icon="las la-money-bill-wave" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount($funds['interest'] ?? 0, currencyFormat: false) }}" title="Interest Earned" :box_shadow=false style="2" bg="white" color="purple" icon="las la-chart-line" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 1: Pending Requests -->
        <div class="col-xxl-6 col-xl-7 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">@lang('Pending Requests')</h5>
                    <div class="row g-3 pending-widget">
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_deposit_pending'] }}" title="Deposit Requests" :box_shadow=false style="2" bg="white" color="{{ $widget['total_deposit_pending'] ? 'warning' : 'success' }}" icon="las la-credit-card" link="admin.deposit.pending" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_withdraw_pending'] }}" title="Withdrawal Requests" :box_shadow=false style="2" bg="white" color="{{ $widget['total_withdraw_pending'] ? 'warning' : 'success' }}" icon="las la-money-bill-wave" link="admin.withdraw.data.pending" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_pending_loan'] }}" title="Loan Applications" :box_shadow=false style="2" bg="white" color="{{ $widget['total_pending_loan'] ? 'danger' : 'success' }}" icon="las la-hand-holding-usd" link="admin.loan.pending" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['pending_tickets'] }}" title="Support Tickets" :box_shadow=false style="2" bg="white" color="{{ $widget['pending_tickets'] ? 'info' : 'success' }}" icon="las la-ticket-alt" link="admin.ticket.pending" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['kyc_pending_users'] }}" title="KYC Verifications" :box_shadow=false style="2" bg="white" color="{{ $widget['kyc_pending_users'] ? 'primary' : 'success' }}" icon="las la-user-shield" link="admin.users.kyc.pending" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['pending_transfers'] }}" title="Money Transfers" :box_shadow=false style="2" bg="white" color="{{ $widget['pending_transfers'] ? 'secondary' : 'success' }}" icon="las la-exchange-alt" link="admin.transfers.pending" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['pending_fdr'] ?? 0 }}" title="FDR Applications" :box_shadow=false style="2" bg="white" color="{{ ($widget['pending_fdr'] ?? 0) ? 'warning' : 'success' }}" icon="las la-certificate" link="admin.fdr.running" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['pending_dps'] ?? 0 }}" title="DPS Applications" :box_shadow=false style="2" bg="white" color="{{ ($widget['pending_dps'] ?? 0) ? 'warning' : 'success' }}" icon="las la-piggy-bank" link="admin.dps.index" icon_style="solid" overlay_icon=0 />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Installment Due -->
        <div class="col-xxl-6 col-xl-7 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">@lang('Installment Due')</h5>
                    <div class="row g-3 due-widget">
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_due_fdr'] }}" title="FDR Due" :box_shadow=false style="2" bg="white" color="{{ $widget['total_due_fdr'] ? 'warning' : 'success' }}" icon="las la-certificate" link="admin.fdr.due" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_due_dps'] }}" title="DPS Due" :box_shadow=false style="2" bg="white" color="{{ $widget['total_due_dps'] ? 'warning' : 'success' }}" icon="las la-piggy-bank" link="admin.dps.due" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_due_loan'] }}" title="Loan Due" :box_shadow=false style="2" bg="white" color="{{ $widget['total_due_loan'] ? 'danger' : 'success' }}" icon="las la-hand-holding-usd" link="admin.loan.due" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['overdue_fdr'] ?? 0 }}" title="Overdue FDR" :box_shadow=false style="2" bg="white" color="{{ ($widget['overdue_fdr'] ?? 0) ? 'danger' : 'success' }}" icon="las la-exclamation-triangle" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['overdue_dps'] ?? 0 }}" title="Overdue DPS" :box_shadow=false style="2" bg="white" color="{{ ($widget['overdue_dps'] ?? 0) ? 'danger' : 'success' }}" icon="las la-exclamation-triangle" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['overdue_loan'] ?? 0 }}" title="Overdue Loans" :box_shadow=false style="2" bg="white" color="{{ ($widget['overdue_loan'] ?? 0) ? 'danger' : 'success' }}" icon="las la-exclamation-triangle" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['upcoming_due'] ?? 0 }}" title="Due This Week" :box_shadow=false style="2" bg="white" color="{{ ($widget['upcoming_due'] ?? 0) ? 'info' : 'success' }}" icon="las la-calendar-week" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ gs('cur_sym') . showAmount($widget['total_due_amount'] ?? 0, currencyFormat: false) }}" title="Total Due Amount" :box_shadow=false style="2" bg="white" color="primary" icon="las la-dollar-sign" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (gs('rebate_system_enabled'))
        <!-- Card 3: Rebate System -->
        <div class="col-xxl-6 col-xl-7 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">@lang('Rebate System')</h5>
                    <div class="row g-3 rebate-widget">
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_rebate_programs'] ?? 0 }}" title="Total Programs" :box_shadow=false style="2" bg="white" color="info" icon="las la-list-alt" link="admin.rebate.programs.index" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['pending_rebate_transactions'] ?? 0 }}" title="Pending Approvals" :box_shadow=false style="2" bg="white" color="{{ ($widget['pending_rebate_transactions'] ?? 0) ? 'warning' : 'success' }}" icon="las la-hourglass-half" link="admin.rebate.transactions.index" query_string="status=pending" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_rebate_transactions'] ?? 0 }}" title="Total Transactions" :box_shadow=false style="2" bg="white" color="primary" icon="las la-exchange-alt" link="admin.rebate.transactions.index" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ showAmount($widget['total_rebate_amount_paid'] ?? 0) }}" title="Total Paid Out" :box_shadow=false style="2" bg="white" color="success" icon="las la-money-bill" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_rebate_categories'] ?? 0 }}" title="Active Categories" :box_shadow=false style="2" bg="white" color="purple" icon="las la-tags" link="admin.rebate.transactions.index" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['monthly_rebate_amount'] ?? 0 }}" title="This Month Paid" :box_shadow=false style="2" bg="white" color="teal" icon="las la-calendar-check" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['avg_rebate_amount'] ?? 0 }}" title="Average Rebate" :box_shadow=false style="2" bg="white" color="orange" icon="las la-chart-bar" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['active_rebate_users'] ?? 0 }}" title="Active Users" :box_shadow=false style="2" bg="white" color="cyan" icon="las la-user-friends" link="javascript:void(0)" icon_style="solid" overlay_icon=0 />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Card 4: Ongoing Activities -->
        <div class="col-xxl-6 col-xl-7 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">@lang('Ongoing')</h5>
                    <div class="row g-3 ongoing-widget">
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_running_fdr'] }}" title="Running FDR" :box_shadow=false style="2" bg="white" color="amber" icon="las la-store" link="admin.fdr.running" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_running_dps'] }}" title="Running DPS" :box_shadow=false style="2" bg="white" color="7" icon="las la-coins" link="admin.dps.running" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_matured_dps'] }}" title="Matured DPS" :box_shadow=false style="2" bg="white" color="warning" icon="las la-coins" link="admin.dps.matured" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_running_loan'] }}" title="Running Loan" :box_shadow=false style="2" bg="white" color="indigo" icon="las la-hand-holding-usd" link="admin.loan.running" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_closed_fdr'] ?? 0 }}" title="Closed FDR" :box_shadow=false style="2" bg="white" color="secondary" icon="las la-archive" link="admin.fdr.closed" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_closed_dps'] ?? 0 }}" title="Closed DPS" :box_shadow=false style="2" bg="white" color="secondary" icon="las la-archive" link="admin.dps.closed" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_paid_loan'] ?? 0 }}" title="Paid Loans" :box_shadow=false style="2" bg="white" color="success" icon="las la-check-circle" link="admin.loan.paid" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_rejected_loan'] ?? 0 }}" title="Rejected Loans" :box_shadow=false style="2" bg="white" color="danger" icon="las la-times-circle" link="admin.loan.rejected" icon_style="solid" overlay_icon=0 />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5: Accounts -->
        <div class="col-xxl-6 col-xl-7 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">@lang('Accounts')</h5>
                    <div class="row g-3 account-widget">
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['total_users'] }}" title="Total Registered" :box_shadow=false style="2" bg="white" color="info" icon="la la-users" link="admin.users.all" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['profile_completed'] }}" title="Profile Completed"  :box_shadow=false style="2" bg="white" color="success" icon="la la-user-check" link="admin.users.profile.completed" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['active_users'] }}" title="Active"  :box_shadow=false style="2" bg="white" color="green" icon="la la-user-check" link="admin.users.active" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['banned_users'] }}" title="Banned"  :box_shadow=false style="2" bg="white" color="danger" icon="la la-user-slash" link="admin.users.banned" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['email_unverified_users'] }}" title="Email Unverified"  :box_shadow=false style="2" bg="white" color="5" icon="la la-envelope" link="admin.users.email.unverified" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['mobile_unverified_users'] }}" title="Mobile Unverified"  :box_shadow=false style="2" bg="white" color="2" icon="la la-mobile" link="admin.users.mobile.unverified" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['kyc_unverified_users'] }}" title="KYC Unverified"  :box_shadow=false style="2" bg="white" color="3" icon="la la-user-slash" link="admin.users.kyc.unverified" icon_style="solid" overlay_icon=0 />
                        </div>
                        <div class="col-sm-6">
                            <x-widget value="{{ $widget['kyc_pending_users'] }}" title="KYC Pending"  :box_shadow=false style="2" bg="white" color="warning" icon="la la-user" link="admin.users.kyc.pending" icon_style="solid" overlay_icon=0 />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart Cards -->
    <div class="row gy-4 mt-3">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Deposit & Withdraw Report')</h5>

                        <div id="dwDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>

                    <div id="dwChartArea"> </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Transactions Report')</h5>

                        <div id="trxDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>

                    <div id="transactionChartArea"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Analytics Cards -->
    <div class="row gy-4 mt-3">
        <div class="col-xl-4 col-lg-6">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Browser') (@lang('Last 30 days'))</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By OS') (@lang('Last 30 days'))</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Country') (@lang('Last 30 days'))</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->guard('admin')->id() == 1)
        @include('admin.partials.cron_modal')
    @endif
@endsection

@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }

        .card .list-group-item {
            padding: .57rem 1rem;
        }
        .account-widget .widget-two, .ongoing-widget .widget-two, .rebate-widget .widget-two, .fund-widget .widget-two, .pending-widget .widget-two, .due-widget .widget-two{
            border: 1px solid #eee !important;
        }
        .list-group-item {
            border-color: #eee !important;
        }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}" />
@endpush

@push('script')
    <script>
        "use strict";

        const start = moment().subtract(14, 'days');
        const end = moment();

        const dateRangeOptions = {
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            }
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
        }

        let dwChart = barChart(
            document.querySelector("#dwChartArea"),
            @json(__(gs('cur_text'))),
            [{
                    name: 'Deposited',
                    data: []
                },
                {
                    name: 'Withdrawn',
                    data: []
                }
            ],
            [],
        );

        let trxChart = lineChart(
            document.querySelector("#transactionChartArea"),
            [{
                    name: "Plus Transactions",
                    data: []
                },
                {
                    name: "Minus Transactions",
                    data: []
                }
            ],
            []
        );


        const depositWithdrawChart = (startDate, endDate) => {

            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }

            const url = @json(route('admin.chart.deposit.withdraw'));

            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        dwChart.updateSeries(data.data);
                        dwChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        const transactionChart = (startDate, endDate) => {

            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }

            const url = @json(route('admin.chart.transaction'));


            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {


                        trxChart.updateSeries(data.data);
                        trxChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }



        $('#dwDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#dwDatePicker span', start, end));
        $('#trxDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#trxDatePicker span', start, end));

        changeDatePickerText('#dwDatePicker span', start, end);
        changeDatePickerText('#trxDatePicker span', start, end);

        depositWithdrawChart(start, end);
        transactionChart(start, end);

        $('#dwDatePicker').on('apply.daterangepicker', (event, picker) => depositWithdrawChart(picker.startDate, picker.endDate));
        $('#trxDatePicker').on('apply.daterangepicker', (event, picker) => transactionChart(picker.startDate, picker.endDate));

        piChart(
            document.getElementById('userBrowserChart'),
            @json(@$chartData['user_browser_counter']->keys()),
            @json(@$chartData['user_browser_counter']->flatten())
        );

        piChart(
            document.getElementById('userOsChart'),
            @json(@$chartData['user_os_counter']->keys()),
            @json(@$chartData['user_os_counter']->flatten())
        );

        piChart(
            document.getElementById('userCountryChart'),
            @json(@$chartData['user_country_counter']->keys()),
            @json(@$chartData['user_country_counter']->flatten())
        );
    </script>
@endpush
