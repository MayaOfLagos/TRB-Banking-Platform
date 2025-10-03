@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="dashboard-widget">
                <div class="card-body">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h4 class="card-title">
                            <i class="las la-upload text--primary me-2"></i>
                            @lang('Submit Rebate Request')
                        </h4>
                        <p class="text-muted">@lang('Upload your receipt and product details to claim your rebate')</p>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('user.rebate.submit.store') }}" method="POST" enctype="multipart/form-data" id="rebateForm">
                        @csrf
                        
                        <!-- Program Selection -->
                        <div class="form-group mb-4">
                            <label for="rebate_program_id" class="form-label required">@lang('Select Rebate Program')</label>
                            <select name="rebate_program_id" id="rebate_program_id" class="form-control" required>
                                <option value="">@lang('Choose a program...')</option>
                                @if(isset($programs) && $programs->count() > 0)
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" 
                                                data-rate="{{ $program->rebate_percentage }}"
                                                data-min="{{ $program->minimum_amount }}"
                                                data-max="{{ $program->maximum_amount }}"
                                                {{ request('program') == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }} ({{ $program->rebate_percentage }}% rebate)
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted">@lang('Select the rebate program that matches your purchase')</small>
                        </div>

                        <!-- Product Information -->
                        <div class="row gy-3 mb-4">
                            <div class="col-md-6">
                                <label for="product_name" class="form-label required">@lang('Product Name')</label>
                                <input type="text" name="product_name" id="product_name" class="form-control" 
                                       value="{{ old('product_name') }}" required>
                                <small class="text-muted">@lang('Enter the exact product name')</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="product_sku" class="form-label">@lang('Product SKU/Model')</label>
                                <input type="text" name="product_sku" id="product_sku" class="form-control" 
                                       value="{{ old('product_sku') }}">
                                <small class="text-muted">@lang('Product SKU or model number (if available)')</small>
                            </div>
                        </div>

                        <!-- Purchase Details -->
                        <div class="row gy-3 mb-4">
                            <div class="col-md-6">
                                <label for="purchase_amount" class="form-label required">@lang('Purchase Amount')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input type="number" name="purchase_amount" id="purchase_amount" class="form-control" 
                                           value="{{ old('purchase_amount') }}" step="0.01" min="0" required>
                                </div>
                                <small class="text-muted">@lang('Total amount you paid for the product')</small>
                                <div id="amountValidation" class="text-danger small mt-1" style="display: none;"></div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="purchase_date" class="form-label required">@lang('Purchase Date')</label>
                                <input type="date" name="purchase_date" id="purchase_date" class="form-control" 
                                       value="{{ old('purchase_date') }}" max="{{ now()->format('Y-m-d') }}" required>
                                <small class="text-muted">@lang('Date when you purchased the product')</small>
                            </div>
                        </div>

                        <!-- Store Information -->
                        <div class="row gy-3 mb-4">
                            <div class="col-md-6">
                                <label for="store_name" class="form-label required">@lang('Store Name')</label>
                                <input type="text" name="store_name" id="store_name" class="form-control" 
                                       value="{{ old('store_name') }}" required>
                                <small class="text-muted">@lang('Name of the store where you made the purchase')</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="receipt_number" class="form-label">@lang('Receipt Number')</label>
                                <input type="text" name="receipt_number" id="receipt_number" class="form-control" 
                                       value="{{ old('receipt_number') }}">
                                <small class="text-muted">@lang('Receipt or invoice number (if available)')</small>
                            </div>
                        </div>

                        <!-- File Uploads -->
                        <div class="form-group mb-4">
                            <label class="form-label required">@lang('Upload Receipt')</label>
                            <div class="upload-area" id="receiptUpload">
                                <div class="upload-content">
                                    <i class="las la-cloud-upload-alt display-4 text--primary"></i>
                                    <h6>@lang('Click to upload receipt')</h6>
                                    <p class="text-muted small mb-0">@lang('Accepted formats: JPG, PNG, PDF')</p>
                                    <p class="text-muted small">@lang('Maximum file size: 10MB')</p>
                                </div>
                                <input type="file" name="receipt_image" id="receipt_image" class="upload-input" 
                                       accept="image/*,.pdf" required>
                            </div>
                            <small class="text-muted">@lang('Upload a clear photo or scan of your receipt')</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Upload Product Image (Optional)')</label>
                            <div class="upload-area" id="productUpload">
                                <div class="upload-content">
                                    <i class="las la-image display-4 text--info"></i>
                                    <h6>@lang('Click to upload product image')</h6>
                                    <p class="text-muted small mb-0">@lang('Accepted formats: JPG, PNG')</p>
                                    <p class="text-muted small">@lang('Maximum file size: 5MB')</p>
                                </div>
                                <input type="file" name="product_image" id="product_image" class="upload-input" 
                                       accept="image/*">
                            </div>
                            <small class="text-muted">@lang('Optional: Upload a photo of the product')</small>
                        </div>

                        <!-- Additional Comments -->
                        <div class="form-group mb-4">
                            <label for="comments" class="form-label">@lang('Additional Comments')</label>
                            <textarea name="comments" id="comments" class="form-control" rows="3" 
                                      placeholder="@lang('Any additional information about your purchase...')">{{ old('comments') }}</textarea>
                            <small class="text-muted">@lang('Optional: Add any relevant details about your purchase')</small>
                        </div>

                        <!-- Rebate Calculation Display -->
                        <div id="rebateCalculation" class="alert alert--info mb-4" style="display: none;">
                            <h6 class="mb-2">@lang('Rebate Calculation'):</h6>
                            <div class="row">
                                <div class="col-4">
                                    <small class="text-muted">@lang('Purchase Amount')</small>
                                    <div class="fw-bold" id="displayAmount">{{ gs('cur_sym') }}0.00</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">@lang('Rebate Rate')</small>
                                    <div class="fw-bold" id="displayRate">0%</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">@lang('Rebate Amount')</small>
                                    <div class="fw-bold text--success" id="displayRebate">{{ gs('cur_sym') }}0.00</div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="agree_terms" id="agree_terms" class="form-check-input" required>
                                <label for="agree_terms" class="form-check-label">
                                    @lang('I agree to the') <a href="#" class="text--primary">@lang('terms and conditions')</a> 
                                    @lang('and confirm that all information provided is accurate')
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn--primary btn--lg px-5" id="submitBtn">
                                <i class="las la-paper-plane me-2"></i>
                                @lang('Submit Rebate Request')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="col-xl-4">
            <div class="dashboard-widget">
                <div class="card-body">
                    <h5 class="card-title text--info mb-3">
                        <i class="las la-question-circle me-2"></i>
                        @lang('Need Help?')
                    </h5>
                    
                    <div class="help-items">
                        <div class="help-item mb-3">
                            <h6 class="mb-1">@lang('Receipt Requirements')</h6>
                            <ul class="list-unstyled small text-muted">
                                <li>• @lang('Clear and legible image')</li>
                                <li>• @lang('Show purchase date and amount')</li>
                                <li>• @lang('Include store name/logo')</li>
                            </ul>
                        </div>
                        
                        <div class="help-item mb-3">
                            <h6 class="mb-1">@lang('Supported File Types')</h6>
                            <ul class="list-unstyled small text-muted">
                                <li>• @lang('Images: JPG, PNG')</li>
                                <li>• @lang('Documents: PDF')</li>
                                <li>• @lang('Max size: 10MB')</li>
                            </ul>
                        </div>
                        
                        <div class="help-item">
                            <h6 class="mb-1">@lang('Processing Time')</h6>
                            <p class="small text-muted mb-0">@lang('Rebate requests are typically processed within 3-5 business days.')</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <a href="{{ route('ticket.open') }}" class="btn btn--secondary btn--sm">
                            <i class="las la-headset me-1"></i>
                            @lang('Contact Support')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 10px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        background: #f8f9fa;
    }
    
    .upload-area:hover {
        border-color: #667eea;
        background: #f0f2ff;
    }
    
    .upload-area.dragover {
        border-color: #667eea;
        background: #e6f3ff;
    }
    
    .upload-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    
    .upload-preview {
        display: none;
        margin-top: 10px;
    }
    
    .upload-preview img {
        max-width: 200px;
        max-height: 150px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    
    .help-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #667eea;
    }
    
    .form-label.required::after {
        content: ' *';
        color: #dc3545;
    }
    
    #rebateCalculation {
        border-left: 4px solid #17a2b8;
    }
</style>
@endpush

@push('script')
<script>
    'use strict';
    
    $(document).ready(function() {
        // File upload handling
        $('.upload-area').on('click', function() {
            $(this).find('.upload-input').click();
        });
        
        $('.upload-area').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });
        
        $('.upload-area').on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        });
        
        $('.upload-area').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                $(this).find('.upload-input')[0].files = files;
                handleFilePreview($(this).find('.upload-input')[0], $(this));
            }
        });
        
        $('.upload-input').on('change', function() {
            handleFilePreview(this, $(this).closest('.upload-area'));
        });
        
        // Program selection and rebate calculation
        $('#rebate_program_id, #purchase_amount').on('change', function() {
            calculateRebate();
            validateAmount();
        });
        
        function handleFilePreview(input, uploadArea) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                
                uploadArea.find('.upload-content h6').text(fileName);
                uploadArea.find('.upload-content p').first().text('File selected successfully');
                uploadArea.css('border-color', '#28a745');
            }
        }
        
        function calculateRebate() {
            const program = $('#rebate_program_id').find(':selected');
            const amount = parseFloat($('#purchase_amount').val()) || 0;
            
            if (program.val() && amount > 0) {
                const rate = parseFloat(program.data('rate')) || 0;
                const rebateAmount = (amount * rate) / 100;
                
                $('#displayAmount').text('{{ gs("cur_sym") }}' + amount.toFixed(2));
                $('#displayRate').text(rate + '%');
                $('#displayRebate').text('{{ gs("cur_sym") }}' + rebateAmount.toFixed(2));
                $('#rebateCalculation').show();
            } else {
                $('#rebateCalculation').hide();
            }
        }
        
        function validateAmount() {
            const program = $('#rebate_program_id').find(':selected');
            const amount = parseFloat($('#purchase_amount').val()) || 0;
            
            if (program.val() && amount > 0) {
                const min = parseFloat(program.data('min')) || 0;
                const max = parseFloat(program.data('max')) || 0;
                
                if (amount < min) {
                    $('#amountValidation').text(`Minimum amount required: {{ gs('cur_sym') }}${min}`).show();
                    $('#submitBtn').prop('disabled', true);
                } else if (max > 0 && amount > max) {
                    $('#amountValidation').text(`Maximum amount allowed: {{ gs('cur_sym') }}${max}`).show();
                    $('#submitBtn').prop('disabled', true);
                } else {
                    $('#amountValidation').hide();
                    $('#submitBtn').prop('disabled', false);
                }
            } else {
                $('#amountValidation').hide();
                $('#submitBtn').prop('disabled', false);
            }
        }
        
        // Form validation
        $('#rebateForm').on('submit', function(e) {
            if (!$('#agree_terms').is(':checked')) {
                e.preventDefault();
                alert('Please agree to the terms and conditions');
                return false;
            }
            
            if (!$('#receipt_image')[0].files.length) {
                e.preventDefault();
                alert('Please upload a receipt image');
                return false;
            }
            
            $('#submitBtn').prop('disabled', true).html('<i class="las la-spinner la-spin me-2"></i>@lang("Processing...")');
        });
    });
</script>
@endpush