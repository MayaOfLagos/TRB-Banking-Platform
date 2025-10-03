<div class="transaction-details">
    <div class="row">
        <div class="col-md-6">
            <h6 class="mb-3">@lang('Rebate Information')</h6>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Transaction ID'):</span>
                    <span class="text-muted">#{{ $rebate->id }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Program'):</span>
                    <span>{{ __($rebate->rebateCategory?->program?->name ?? 'Unknown Program') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Category'):</span>
                    <span>{{ __($rebate->rebateCategory?->name ?? 'Uncategorized') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Type'):</span>
                    <span class="badge badge--primary">{{ __(ucwords(str_replace('_', ' ', $rebate->type ?? 'N/A'))) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Amount'):</span>
                    <span class="text-primary fw-bold">{{ showUserAmount($rebate->rebate_amount, auth()->user()) }}</span>
                </li>
                @if($rebate->tier_multiplier > 1)
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Tier Bonus'):</span>
                    <span class="text-success">{{ $rebate->tier_multiplier }}x @lang('multiplier')</span>
                </li>
                @endif
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Status'):</span>
                    @php
                        $statusClass = match($rebate->status) {
                            'approved' => 'success',
                            'pending' => 'warning', 
                            'rejected' => 'danger',
                            default => 'dark'
                        };
                    @endphp
                    <span class="badge badge--{{ $statusClass }}">{{ __(ucfirst($rebate->status)) }}</span>
                </li>
            </ul>
        </div>
        
        @if($rebate->product_upload)
        <div class="col-md-6">
            <h6 class="mb-3">@lang('Product Upload Details')</h6>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Product Name'):</span>
                    <span>{{ $rebate->product_upload->product_name ?: ($rebate->product_upload->description ?: ($rebate->product_upload->store_name ? 'Purchase from ' . $rebate->product_upload->store_name : 'Product Purchase')) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Purchase Amount'):</span>
                    <span>{{ showUserAmount($rebate->product_upload->purchase_amount ?? 0, auth()->user()) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Receipt Number'):</span>
                    <span>{{ $rebate->product_upload->receipt_number ?? 'N/A' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Upload Status'):</span>
                    @php
                        $uploadStatus = $rebate->product_upload->status ?? 'unknown';
                        $uploadStatusClass = match($uploadStatus) {
                            'approved' => 'success',
                            'pending' => 'warning',
                            'rejected' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge badge--{{ $uploadStatusClass }}">{{ __(ucfirst($uploadStatus)) }}</span>
                </li>
                @if($rebate->product_upload->receipt_image)
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                    <span class="fw-bold">@lang('Receipt Image'):</span>
                    <a href="{{ getImage(getFilePath('productUploads') . '/' . $rebate->product_upload->receipt_image) }}" 
                       target="_blank" class="btn btn-sm btn-outline--primary">
                        <i class="las la-eye"></i> @lang('View Receipt')
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <h6 class="mb-3">@lang('Timeline')</h6>
            <ul class="timeline">
                <li class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">@lang('Rebate Created')</h6>
                        <p class="timeline-text text-muted">{{ showDateTime($rebate->created_at) }}</p>
                    </div>
                </li>
                
                @if($rebate->approved_at)
                <li class="timeline-item">
                    <div class="timeline-marker timeline-marker--success"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title text-success">@lang('Rebate Approved')</h6>
                        <p class="timeline-text text-muted">{{ showDateTime($rebate->approved_at) }}</p>
                    </div>
                </li>
                @elseif($rebate->rejected_at)
                <li class="timeline-item">
                    <div class="timeline-marker timeline-marker--danger"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title text-danger">@lang('Rebate Rejected')</h6>
                        <p class="timeline-text text-muted">{{ showDateTime($rebate->rejected_at) }}</p>
                        @if($rebate->review_notes)
                        <p class="timeline-text"><strong>@lang('Reason'):</strong> {{ $rebate->review_notes }}</p>
                        @endif
                    </div>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>

<style>
.transaction-details .list-group-item {
    background-color: transparent;
    padding: 0.5rem 0;
}

.timeline {
    list-style: none;
    padding: 0;
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-left: 2rem;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -5px;
    top: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-marker--success {
    background: #28a745;
}

.timeline-marker--danger {
    background: #dc3545;
}

.timeline-title {
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.timeline-text {
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
}
</style>