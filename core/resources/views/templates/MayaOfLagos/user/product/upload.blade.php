@extends($activeTemplate.'layouts.master')
@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-1 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">@lang('Upload Product Receipt')</h1>
            <p class="text-gray-600 dark:text-gray-400">@lang('Submit your purchase receipt to earn rebates from our programs')</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upload Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Receipt Details')</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('user.product.upload.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm" class="space-y-6">
                            @csrf
                            
                            <!-- Program Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Select Rebate Program')
                                    <span class="text-red-500">*</span>
                                </label>
                                <select name="rebate_program_id" id="programSelect" class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" required>
                                    <option value="">@lang('Choose a program')</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" 
                                                data-rate-type="percentage"
                                                data-rate-value="{{ $program->default_rate }}"
                                                data-max-amount="{{ $program->maximum_rebate }}"
                                                data-description="{{ $program->description }}"
                                                @selected($selectedProgram && $selectedProgram->id == $program->id)>
                                            {{ __($program->name) }} - {{ $program->default_rate }}%
                                            @if($program->maximum_rebate)
                                                (max {{ showAmount($program->maximum_rebate) }} {{ $general->cur_text }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div id="programDescription" class="mt-2 text-sm text-gray-500 dark:text-gray-400"></div>
                            </div>

                            <!-- Receipt Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Receipt Image')
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="upload-area relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center cursor-pointer bg-gray-50 dark:bg-gray-700 hover:border-purple-400 dark:hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-gray-600 transition-all duration-300" id="uploadArea">
                                    <input type="file" name="receipt_image" id="receiptImage" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" required>
                                    <div class="upload-content relative z-20 pointer-events-none">
                                        <i class="las la-cloud-upload-alt text-5xl text-gray-400 dark:text-gray-500 mb-4"></i>
                                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">@lang('Drop your receipt here or click to browse')</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('Supports JPG, PNG files up to 5MB')</p>
                                    </div>
                                    <div class="upload-preview relative" id="uploadPreview" style="display: none;">
                                        <img id="previewImage" src="" alt="Receipt Preview" class="max-w-full max-h-48 rounded-lg shadow-md mx-auto">
                                        <button type="button" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 text-sm transition-colors duration-200" id="removeImage">
                                            <i class="las la-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Purchase Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        @lang('Purchase Amount')
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="purchase_amount" id="purchaseAmount" 
                                               class="w-full px-4 py-3 pr-16 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" 
                                               step="0.01" min="0.01" placeholder="0.00" required>
                                        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm font-medium">{{ $general->cur_text }}</span>
                                    </div>
                                    <div id="rebateEstimate" class="mt-2 text-sm text-green-600 dark:text-green-400 font-medium"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        @lang('Purchase Date')
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="purchase_date" 
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" 
                                           max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- Store Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('Store Name')
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="store_name" 
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" 
                                       placeholder="@lang('Enter store name')" required>
                            </div>

                            <!-- Product Names -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        @lang('Product Names')
                                        <span class="text-gray-500">(@lang('Optional'))</span>
                                    </label>
                                    <button type="button" id="addProductName" class="inline-flex items-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-all duration-200 hover:shadow-md transform hover:-translate-y-0.5">
                                        <i class="las la-plus text-sm mr-1"></i>
                                        @lang('Add Product')
                                    </button>
                                </div>
                                <div id="productNamesContainer" class="space-y-3">
                                    <div class="product-name-group flex items-center space-x-3 group">
                                        <div class="flex-1">
                                            <input type="text" name="product_names[]" 
                                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" 
                                                   placeholder="@lang('Enter product name (e.g., Nike Air Max, iPhone 15)')">
                                        </div>
                                        <button type="button" class="remove-product-name flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200" style="display: none;" title="@lang('Remove product')">
                                            <i class="las la-times text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('Add multiple products if your receipt contains different items')
                                    <span id="productCountDisplay" class="ml-2 text-purple-600 dark:text-purple-400 font-medium"></span>
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Description (Optional)')</label>
                                <textarea name="description" rows="3" 
                                          class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 resize-none" 
                                          placeholder="@lang('Add any additional notes about your purchase')"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center" id="submitBtn">
                                    <i class="las la-upload text-xl mr-2"></i>
                                    @lang('Submit Receipt for Rebate')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Program Information Panel -->
            <div class="space-y-6">
                <!-- Program Info Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Program Information')</h2>
                    </div>
                    <div class="p-6">
                        <div id="programInfo" style="display: none;">
                            <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4 border border-purple-100 dark:border-gray-600">
                                <div class="flex items-start justify-between mb-4">
                                    <h3 id="programName" class="text-lg font-semibold text-gray-900 dark:text-white"></h3>
                                    <span id="programCategory" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200"></span>
                                </div>
                                <p id="programDesc" class="text-gray-600 dark:text-gray-300 text-sm mb-4"></p>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <i class="las la-percentage text-green-500 text-lg mr-3"></i>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Rate'):</span>
                                        <span id="programRate" class="ml-2 text-sm font-bold text-purple-600 dark:text-purple-400"></span>
                                    </div>
                                    
                                    <div class="flex items-center" id="maxAmountDetail" style="display: none;">
                                        <i class="las la-coins text-yellow-500 text-lg mr-3"></i>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">@lang('Max Rebate'):</span>
                                        <span id="programMaxAmount" class="ml-2 text-sm font-bold text-purple-600 dark:text-purple-400"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="noProgram" class="text-center py-8">
                            <i class="las la-info-circle text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400 mb-2">@lang('Select Program')</h3>
                            <p class="text-sm text-gray-400 dark:text-gray-500">@lang('Choose a rebate program to see details')</p>
                        </div>
                    </div>
                </div>

                <!-- Upload Tips Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('Upload Tips')</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="las la-camera text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">@lang('Ensure receipt is clearly visible and readable')</p>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <i class="las la-calendar text-green-600 dark:text-green-400"></i>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">@lang('Submit receipts within 30 days of purchase')</p>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                        <i class="las la-store text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">@lang('Include store name and purchase date')</p>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                        <i class="las la-shield-alt text-yellow-600 dark:text-yellow-400"></i>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">@lang('All submissions are verified for accuracy')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@push('script')
<script>
'use strict';
(function($) {
    
    // File upload handling
    const uploadArea = $('#uploadArea');
    const uploadInput = $('#receiptImage');
    const uploadContent = $('.upload-content');
    const uploadPreview = $('#uploadPreview');
    const previewImage = $('#previewImage');
    const removeImageBtn = $('#removeImage');

    // Drag and drop handlers with Tailwind classes
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).removeClass('border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700')
               .addClass('border-purple-400 dark:border-purple-500 bg-purple-50 dark:bg-purple-900/20');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('border-purple-400 dark:border-purple-500 bg-purple-50 dark:bg-purple-900/20')
               .addClass('border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('border-purple-400 dark:border-purple-500 bg-purple-50 dark:bg-purple-900/20')
               .addClass('border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            uploadInput[0].files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change handler
    uploadInput.on('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Remove image handler
    removeImageBtn.on('click', function() {
        uploadInput.val('');
        uploadContent.show();
        uploadPreview.hide();
        uploadArea.removeClass('border-green-400 dark:border-green-500 bg-green-50 dark:bg-green-900/20')
                  .addClass('border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700');
    });

    function handleFileSelect(file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showNotification('@lang("Please select a valid image file")', 'error');
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('@lang("File size must be less than 5MB")', 'error');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.attr('src', e.target.result);
            uploadContent.hide();
            uploadPreview.show();
            uploadArea.removeClass('border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700')
                      .addClass('border-green-400 dark:border-green-500 bg-green-50 dark:bg-green-900/20');
        };
        reader.readAsDataURL(file);
    }

    // Program selection handler
    $('#programSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        
        if (selectedOption.val()) {
            const programData = {
                name: selectedOption.text().split(' - ')[0],
                category: 'Program', // You might want to add this to the option data
                description: selectedOption.data('description'),
                rateType: selectedOption.data('rate-type'),
                rateValue: selectedOption.data('rate-value'),
                maxAmount: selectedOption.data('max-amount')
            };

            displayProgramInfo(programData);
            $('#programInfo').show();
            $('#noProgram').hide();
        } else {
            $('#programInfo').hide();
            $('#noProgram').show();
        }

        calculateRebateEstimate();
    });

    function displayProgramInfo(data) {
        $('#programName').text(data.name);
        $('#programCategory').text(data.category);
        $('#programDesc').text(data.description || '@lang("No description available")');
        
        let rateText = '';
        if (data.rateType === 'percentage' || !data.rateType) {
            rateText = (data.rateValue || 0) + '%';
            if (data.maxAmount && !isNaN(data.maxAmount) && data.maxAmount > 0) {
                $('#programMaxAmount').text(parseFloat(data.maxAmount).toFixed(2) + ' {{ $general->cur_text }}');
                $('#maxAmountDetail').show();
            } else {
                $('#maxAmountDetail').hide();
            }
        } else {
            rateText = (data.rateValue || 0) + ' {{ $general->cur_text }}';
            $('#maxAmountDetail').hide();
        }
        
        $('#programRate').text(rateText);
    }

    // Purchase amount change handler
    $('#purchaseAmount').on('input', function() {
        calculateRebateEstimate();
    });

    function calculateRebateEstimate() {
        const selectedOption = $('#programSelect').find('option:selected');
        const purchaseAmount = parseFloat($('#purchaseAmount').val());
        
        if (!selectedOption.val() || !purchaseAmount || purchaseAmount <= 0 || isNaN(purchaseAmount)) {
            $('#rebateEstimate').text('');
            return;
        }

        const rateType = selectedOption.data('rate-type') || 'percentage';
        const rateValue = parseFloat(selectedOption.data('rate-value'));
        const maxAmount = parseFloat(selectedOption.data('max-amount'));
        
        // Validate that we have a valid rate value
        if (isNaN(rateValue) || rateValue <= 0) {
            $('#rebateEstimate').text('');
            return;
        }
        
        let rebateAmount = 0;
        
        if (rateType === 'percentage') {
            rebateAmount = (purchaseAmount * rateValue) / 100;
            if (!isNaN(maxAmount) && maxAmount > 0 && rebateAmount > maxAmount) {
                rebateAmount = maxAmount;
            }
        } else {
            rebateAmount = rateValue;
        }

        // Add tier multiplier info
        const tierMultiplier = {{ $tierInfo['multiplier'] ?? 1 }};
        const finalAmount = rebateAmount * tierMultiplier;
        
        // Validate final calculations
        if (isNaN(rebateAmount) || isNaN(finalAmount)) {
            $('#rebateEstimate').text('');
            return;
        }
        
        let estimateText = `@lang('Estimated rebate'): ${rebateAmount.toFixed(2)} {{ $general->cur_text }}`;
        if (tierMultiplier > 1) {
            estimateText += ` × ${tierMultiplier} = ${finalAmount.toFixed(2)} {{ $general->cur_text }}`;
        }
        
        $('#rebateEstimate').html(`<i class="las la-calculator mr-1"></i>${estimateText}`);
    }

    // Form validation with better UX
    $('#uploadForm').on('submit', function(e) {
        const receiptImage = $('#receiptImage')[0].files;
        const programId = $('#programSelect').val();
        const purchaseAmount = $('#purchaseAmount').val();
        const storeName = $('input[name="store_name"]').val();

        if (!receiptImage.length) {
            e.preventDefault();
            showNotification('@lang("Please upload a receipt image")', 'error');
            $('#receiptImage').focus();
            return false;
        }

        if (!programId) {
            e.preventDefault();
            showNotification('@lang("Please select a rebate program")', 'error');
            $('#programSelect').focus();
            return false;
        }

        if (!purchaseAmount || parseFloat(purchaseAmount) <= 0) {
            e.preventDefault();
            showNotification('@lang("Please enter a valid purchase amount")', 'error');
            $('#purchaseAmount').focus();
            return false;
        }

        if (!storeName.trim()) {
            e.preventDefault();
            showNotification('@lang("Please enter the store name")', 'error');
            $('input[name="store_name"]').focus();
            return false;
        }

        // Optional: Validate product names if any are provided
        let hasValidProductName = false;
        $('input[name="product_names[]"]').each(function() {
            if ($(this).val().trim()) {
                hasValidProductName = true;
                return false; // break the loop
            }
        });
        
        // If there are product name fields but none have values, that's still valid
        // This is just for future enhancement if we want to add validation

        // Show loading state with Tailwind classes
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true)
                .removeClass('hover:from-purple-700 hover:to-blue-700 transform hover:-translate-y-0.5')
                .addClass('opacity-75 cursor-not-allowed')
                .html('<i class="las la-spinner la-spin text-xl mr-2"></i>@lang("Processing...")');
    });

    // Simple notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const bgColor = type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500';
        const notification = $(`
            <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300">
                <div class="flex items-center">
                    <i class="las ${type === 'error' ? 'la-exclamation-circle' : type === 'success' ? 'la-check-circle' : 'la-info-circle'} text-xl mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `);
        
        $('body').append(notification);
        setTimeout(() => notification.removeClass('translate-x-full'), 100);
        setTimeout(() => {
            notification.addClass('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Product Names Management
    let productNameCounter = 1;

    $('#addProductName').on('click', function() {
        const container = $('#productNamesContainer');
        const newProductGroup = $(`
            <div class="product-name-group flex items-center space-x-3 group">
                <div class="flex-1">
                    <input type="text" name="product_names[]" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200" 
                           placeholder="@lang('Enter product name (e.g., Nike Air Max, iPhone 15)')">
                </div>
                <button type="button" class="remove-product-name flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200" title="@lang('Remove product')">
                    <i class="las la-times text-lg"></i>
                </button>
            </div>
        `);
        
        container.append(newProductGroup);
        productNameCounter++;
        
        // Show remove buttons for all items when there's more than one
        updateRemoveButtons();
        
        // Focus on the new input
        newProductGroup.find('input').focus();
    });

    // Handle remove product name
    $(document).on('click', '.remove-product-name', function() {
        $(this).closest('.product-name-group').remove();
        productNameCounter--;
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        const groups = $('.product-name-group');
        const count = groups.length;
        
        if (count > 1) {
            $('.remove-product-name').show();
        } else {
            $('.remove-product-name').hide();
        }
        
        // Update counter display
        const counterText = count > 1 ? `(${count} @lang('products')})` : '';
        $('#productCountDisplay').text(counterText);
    }

    // Initialize remove buttons state
    updateRemoveButtons();

    // Initialize if program is pre-selected
    if ($('#programSelect').val()) {
        $('#programSelect').trigger('change');
    }

})(jQuery);
</script>
@endpush