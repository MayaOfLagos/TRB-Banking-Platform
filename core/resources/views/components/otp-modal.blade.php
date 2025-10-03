@props([
    'modalId' => 'otpModal',
    'title' => 'Verification Required',
    'description' => 'Verify your identity to proceed',
    'action' => '',
    'method' => 'POST',
    'showSummary' => false,
    'summaryData' => [],
    'hiddenFields' => [],
    'submitText' => 'Confirm',
    'icon' => 'las la-shield-alt'
])

@if (checkIsOtpEnable())
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">@lang($title)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="{{ $icon }} text--base" style="font-size: 48px;"></i>
                    <h6 class="mt-2">@lang($description)</h6>
                </div>
                
                <form id="{{ $modalId }}Form" action="{{ $action }}" method="{{ $method }}">
                    @csrf
                    
                    <!-- Hidden Fields -->
                    @foreach($hiddenFields as $field)
                        <input type="hidden" name="{{ $field['name'] }}" id="{{ $modalId }}_{{ $field['name'] }}" value="{{ $field['value'] ?? '' }}">
                    @endforeach
                    
                    <!-- Summary Section -->
                    @if($showSummary)
                        <div class="card custom--card mb-3">
                            <div class="card-body">
                                <h6 class="text-center mb-3">{{ $summaryData['title'] ?? __('Transaction Summary') }}</h6>
                                @if(isset($summaryData['items']))
                                    @foreach($summaryData['items'] as $item)
                                        <div class="d-flex justify-content-between">
                                            <span>@lang($item['label']):</span>
                                            <span class="fw-bold {{ $item['class'] ?? '' }} summary-{{ $item['key'] }}">{{ $item['value'] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                @endif
                                @if(isset($summaryData['total']))
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">@lang($summaryData['total']['label']):</span>
                                        <span class="fw-bold text--base summary-{{ $summaryData['total']['key'] }}">{{ $summaryData['total']['value'] ?? '' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- OTP Field -->
                    @include($activeTemplate . 'partials.otp_field')
                    
                    <button type="submit" class="btn btn--base w-100 mt-3">
                        @lang($submitText)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif