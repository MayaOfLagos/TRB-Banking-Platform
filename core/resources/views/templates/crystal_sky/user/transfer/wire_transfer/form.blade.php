@extends($activeTemplate . 'user.transfer.layout')
@section('transfer-content')
    <div class="row gy-4 justify-content-center">
        <div class="col-xl-4">
            <div class="card custom--card">
                <div class="card-body">
                    <h6 class="card-title text-center">@lang('Transfer Limit')</h6>
                    <ul>
                        <li class="pricing-card__list flex-between">
                            <span>@lang('Minimum Per Transaction')</span>
                            <span class="fw-bold">{{ showAmount(@$setting->minimum_limit) }}</span>
                        </li>

                        <li class="pricing-card__list flex-between">
                            <span>@lang('Maximum Per Transaction')</span>
                            <span class="fw-bold">{{ showAmount(@$setting->maximum_limit) }}</span>
                        </li>

                        <li class="pricing-card__list flex-between">
                            <span>@lang('Daily Maximum')</span>
                            <span class="fw-bold">{{ showAmount(@$setting->daily_maximum_limit) }}</span>
                        </li>

                        <li class="pricing-card__list flex-between">
                            <span>@lang('Monthly Maximum')</span>
                            <span class="fw-bold">{{ showAmount(@$setting->monthly_maximum_limit) }}</span>
                        </li>

                        <li class="pricing-card__list flex-between">
                            <span>@lang('Daily Maximum Transaction')</span>
                            <span class="fw-bold">{{ @$setting->daily_total_transaction }}</span>
                        </li>

                        <li class="pricing-card__list flex-between">
                            <span> @lang('Monthly Maximum Transaction')</span>
                            <span class="fw-bold">{{ @$setting->monthly_total_transaction }}</span>
                        </li>
                    </ul>

                    @php $transferCharge = $setting->chargeText(); @endphp

                    @if ($transferCharge)
                        <small class="text--danger">* @lang('Charge') {{ $transferCharge }}</small>
                    @endif
                </div>
            </div>

            @if ($setting->instruction)
                <div class="card custom--card mt-3">
                    <div class="card-body">
                        <h6 class="card-title text-center">@lang('Instruction')</h6>
                        <p>@php echo $setting->instruction; @endphp</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-8">
            <div class="card custom--card">
                <div class="card-body">
                    @if (@$setting->instruction)
                        <div class="text-center">
                            @php echo @$setting->instruction;  @endphp
                        </div>
                    @endif
                    <form method="POST" action="{{ route('user.transfer.wire.request') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">@lang('Amount')</label>
                            <div class="input-group custom-input-group">
                                <input type="number" step="any" class="form-control form--control" name="amount">
                                <span class="input-group-text">{{ __(gs()->cur_text) }}</span>
                            </div>
                            <span class="fw-bold @if(auth()->user()->balance > @$setting->minimum_limit) text--info @else text--danger @endif">@lang('Current Balance'): {{ showAmount(auth()->user()->balance) }}</span>
                        </div>
                        <x-viser-form identifier="act" identifierValue="wire_transfer" />
                        
                        @php $user = auth()->user(); @endphp
                        
                        <!-- Billing Codes Section -->
                        @if($user->requiresBillingCodes())
                            @php $requiredCodes = $user->requiredBillingCodes()->get(); @endphp
                            @foreach($requiredCodes as $code)
                                <div class="form-group">
                                    <label class="form-label">@lang('Billing Code') ({{ ucfirst($code->code_type) }}) <span class="text--danger">*</span></label>
                                    <input type="text" 
                                           name="billing_code_{{ strtolower($code->code_type) }}"
                                           class="form-control form--control"
                                           placeholder="@lang('Enter your') {{ strtolower($code->code_type) }} @lang('billing code')"
                                           required>
                                    <small class="form-text text-muted">@lang('This code will be marked as used after successful transfer')</small>
                                </div>
                            @endforeach
                        @endif
                        
                        <!-- Transfer PIN Section -->
                        @if($user->hasTransferPin())
                            <div class="form-group">
                                <label class="form-label">@lang('Transfer PIN') <span class="text--danger">*</span></label>
                                <input type="password" 
                                       name="transfer_pin"
                                       class="form-control form--control text-center"
                                       placeholder="@lang('Enter your 4-digit transfer PIN')"
                                       maxlength="4"
                                       pattern="[0-9]{4}"
                                       required>
                                <small class="form-text text-muted">@lang('Enter your 4-digit transfer PIN for additional security')</small>
                            </div>
                        @endif
                        
                        @include($activeTemplate . 'partials.otp_field')
                        <button type="submit" class="btn btn--base w-100 ">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
