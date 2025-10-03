@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card custom--card">
                <div class="card-header bg--base text-white">
                    <h5 class="card-title text-white mb-0">
                        <i class="las la-shield-alt me-2"></i>
                        @lang('Transfer PIN Management')
                    </h5>
                </div>
                <div class="card-body">
                    @if(!$user->hasTransferPin())
                        {{-- Set New Transfer PIN --}}
                        <div class="alert alert-info">
                            <i class="las la-info-circle me-2"></i>
                            @lang('You haven\'t set a transfer PIN yet. A transfer PIN is required for secure wire transfers and billing code verification.')
                        </div>
                        
                        <form action="{{ route('user.transfer.pin.set') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">@lang('Current Password') <span class="text-danger">*</span></label>
                                <input class="form-control form--control" name="current_password" type="password" required autocomplete="current-password">
                                <small class="text-muted">@lang('Enter your account password for verification')</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Transfer PIN') <span class="text-danger">*</span></label>
                                        <input class="form-control form--control text-center" name="transfer_pin" type="password" maxlength="4" pattern="[0-9]{4}" required autocomplete="new-password">
                                        <small class="text-muted">@lang('Enter exactly 4 digits')</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Confirm Transfer PIN') <span class="text-danger">*</span></label>
                                        <input class="form-control form--control text-center" name="transfer_pin_confirmation" type="password" maxlength="4" pattern="[0-9]{4}" required autocomplete="new-password">
                                        <small class="text-muted">@lang('Re-enter your 4-digit PIN')</small>
                                    </div>
                                </div>
                            </div>
                            
                            <input class="btn btn--base w-100" type="submit" value="@lang('Set Transfer PIN')">
                        </form>
                    @else
                        {{-- Update Transfer PIN --}}
                        <div class="alert alert-success">
                            <i class="las la-check-circle me-2"></i>
                            @lang('Transfer PIN is active and ready for secure transactions.')
                        </div>
                        
                        <form action="{{ route('user.transfer.pin.update') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">@lang('Current Transfer PIN') <span class="text-danger">*</span></label>
                                <input class="form-control form--control text-center" name="current_pin" type="password" maxlength="4" pattern="[0-9]{4}" required autocomplete="current-password">
                                <small class="text-muted">@lang('Enter your current 4-digit transfer PIN')</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('New Transfer PIN') <span class="text-danger">*</span></label>
                                        <input class="form-control form--control text-center" name="transfer_pin" type="password" maxlength="4" pattern="[0-9]{4}" required autocomplete="new-password">
                                        <small class="text-muted">@lang('Enter new 4-digit PIN')</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Confirm New PIN') <span class="text-danger">*</span></label>
                                        <input class="form-control form--control text-center" name="transfer_pin_confirmation" type="password" maxlength="4" pattern="[0-9]{4}" required autocomplete="new-password">
                                        <small class="text-muted">@lang('Re-enter new PIN')</small>
                                    </div>
                                </div>
                            </div>
                            
                            <input class="btn btn--base w-100" type="submit" value="@lang('Update Transfer PIN')">
                        </form>
                    @endif
                    
                    {{-- Info Section --}}
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-primary mb-3">
                            <i class="las la-info-circle me-2"></i>
                            @lang('Important Information')
                        </h6>
                        <ul class="list-unstyled mb-0 text-muted small">
                            <li class="mb-2">
                                <i class="las la-check text-success me-2"></i>
                                @lang('Your transfer PIN is used for wire transfer verification')
                            </li>
                            <li class="mb-2">
                                <i class="las la-check text-success me-2"></i>
                                @lang('Required for billing codes (IMF, TAX, COT) authentication')
                            </li>
                            <li class="mb-2">
                                <i class="las la-check text-success me-2"></i>
                                @lang('PIN must be exactly 4 digits (0-9)')
                            </li>
                            <li class="mb-2">
                                <i class="las la-shield-alt text-primary me-2"></i>
                                @lang('PIN is encrypted and stored securely')
                            </li>
                            <li class="mb-0">
                                <i class="las la-exclamation-triangle text-warning me-2"></i>
                                @lang('Never share your transfer PIN with anyone')
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .alert {
        border: none;
        border-radius: 8px;
    }
    
    input[type="password"].text-center {
        letter-spacing: 0.5em;
        font-size: 1.2em;
        font-weight: bold;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .nav-buttons .btn.active {
        background-color: hsl(var(--base)) !important;
        color: white !important;
        border-color: hsl(var(--base)) !important;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // Only allow numeric input for PIN fields
        $('input[name="transfer_pin"], input[name="transfer_pin_confirmation"], input[name="current_pin"]').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4);
            }
        });
        
        // Auto-focus next field after 4 digits
        $('input[name="transfer_pin"]').on('input', function() {
            if (this.value.length === 4) {
                $('input[name="transfer_pin_confirmation"]').focus();
            }
        });
        
        // Show/hide PIN toggle (optional enhancement)
        $('.toggle-pin').on('click', function() {
            let input = $(this).siblings('input');
            let type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            $(this).find('i').toggleClass('la-eye la-eye-slash');
        });
    });
</script>
@endpush

@push('bottom-menu')
    <div class="col-12 order-lg-3 order-4">
        <div class="d-flex nav-buttons flex-align gap-md-3 gap-2">
            <a href="{{ route('user.profile.setting') }}" class="btn btn-outline--base">@lang('Profile Setting')</a>
            <a href="{{ route('user.change.password') }}" class="btn btn-outline--base">@lang('Change Password')</a>
            <a href="{{ route('user.transfer.pin') }}" class="btn btn--base active">@lang('Transfer PIN')</a>
            <a href="{{ route('user.twofactor') }}" class="btn btn-outline--base">@lang('2FA Security')</a>
        </div>
    </div>
@endpush