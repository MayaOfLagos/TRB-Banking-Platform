@extends($activeTemplate . 'layouts.master')
@section('content')

@php
    $user = auth()->user();
    $canWithdraw = $user->canWithdraw();
    $withdrawalStatus = $user->getWithdrawalStatus();
    $withdrawalReason = $user->getWithdrawalBlockReason();
    $statusLabels = \App\Models\WithdrawalControl::getStatuses();
@endphp

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom--card">
                <div class="card-body">
                    <form action="{{ route('user.withdraw.submit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="bg--light text--info mb-2 rounded p-3 text-center">
                            @php
                                echo $withdraw->method->description;
                            @endphp
                        </div>
                        <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form->id }}" />
                        @if (auth()->user()->ts)
                            <div class="form-group">
                                <label>@lang('Google Authenticator Code')</label>
                                <input class="form--control" name="authenticator_code" type="text" required>
                            </div>
                        @endif
                        <button class="btn btn-md btn--base w-100" type="submit">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        "use strict";
        (function($) {
            $('label').removeClass('fw-bold');

            // Withdrawal Control Check
            @if(!$canWithdraw)
                $('form').on('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        icon: 'error',
                        title: '{{ $statusLabels[$withdrawalStatus] ?? 'Withdrawal Restricted' }}',
                        html: '<div style="text-align: left; margin-top: 15px;"><strong>Reason:</strong><br>{{ $withdrawalReason ?? 'Your withdrawal has been restricted. Please contact support for assistance.' }}</div>',
                        confirmButtonText: 'Understood',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('user.withdraw.blocked.attempt') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    withdrawal_id: '{{ $withdraw->id }}',
                                    status: '{{ $withdrawalStatus }}',
                                    reason: '{{ addslashes($withdrawalReason ?? '') }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        window.location.href = '{{ route('user.withdraw.history') }}';
                                    }
                                }
                            });
                        }
                    });
                    
                    return false;
                });
            @endif
        })(jQuery);
    </script>
    
    {{-- SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
