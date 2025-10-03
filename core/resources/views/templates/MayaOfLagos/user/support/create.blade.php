@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- Create Support Ticket Page -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-4xl mx-auto px-1 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">@lang('Create Support Ticket')</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">@lang('Need help? Create a support ticket and our team will assist you')</p>
                </div>
                
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('ticket.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all duration-200 border border-gray-300 dark:border-gray-600">
                        <i class="las la-list mr-2 text-lg"></i>
                        @lang('All Tickets')
                    </a>
                </div>
            </div>
        </div>

        <!-- Create Ticket Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 lg:px-8 py-6 lg:py-8">
                <form action="{{ route('ticket.store') }}" method="post" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Subject and Priority Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Subject') <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="subject"
                                   name="subject" 
                                   value="{{ old('subject') }}" 
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                   placeholder="@lang('Enter ticket subject')"
                                   required>
                        </div>
                        
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                @lang('Priority') <span class="text-red-500">*</span>
                            </label>
                            <select id="priority"
                                    name="priority" 
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white"
                                    required>
                                <option value="3" class="bg-white dark:bg-gray-700">@lang('High')</option>
                                <option value="2" class="bg-white dark:bg-gray-700">@lang('Medium')</option>
                                <option value="1" class="bg-white dark:bg-gray-700">@lang('Low')</option>
                            </select>
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="inputMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            @lang('Message') <span class="text-red-500">*</span>
                        </label>
                        <textarea id="inputMessage"
                                  name="message" 
                                  rows="6" 
                                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                                  placeholder="@lang('Describe your issue in detail...')"
                                  required>{{ old('message') }}</textarea>
                    </div>

                    <!-- Attachments Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between space-y-4 lg:space-y-0">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">@lang('Attachments')</h3>
                                    <button type="button" 
                                            class="addAttachment inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg transition-colors">
                                        <i class="las la-plus mr-1"></i>
                                        @lang('Add Attachment')
                                    </button>
                                </div>
                                
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-start space-x-2">
                                        <i class="las la-info-circle text-blue-500 mt-0.5"></i>
                                        <div>
                                            <p class="font-medium text-blue-800 dark:text-blue-300 mb-1">@lang('File Upload Guidelines')</p>
                                            <ul class="text-xs space-y-1 text-blue-700 dark:text-blue-400">
                                                <li>• @lang('Maximum 5 files allowed')</li>
                                                <li>• @lang('Maximum file size: ') {{ convertToReadableSize(ini_get('upload_max_filesize')) }}</li>
                                                <li>• @lang('Allowed formats: .jpg, .jpeg, .png, .pdf, .doc, .docx')</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 fileUploadsContainer">
                                    <!-- Dynamic file upload fields will be added here -->
                                </div>
                            </div>
                            
                            <div class="lg:ml-8 lg:w-48">
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                                    <i class="las la-paper-plane text-lg"></i>
                                    <span>@lang('Submit Ticket')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-indigo-200 dark:border-indigo-800">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <i class="las la-lightbulb text-2xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('Tips for Better Support')</h3>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• @lang('Provide detailed description of your issue')</li>
                        <li>• @lang('Include screenshots if applicable')</li>
                        <li>• @lang('Mention any error messages you received')</li>
                        <li>• @lang('Choose appropriate priority level')</li>
                        <li>• @lang('Include relevant transaction IDs or account details')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
/* Custom file input styling */
.file-input-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 0.5rem;
}

.file-input-wrapper input[type=file] {
    position: absolute;
    left: -9999px;
}

/* Loading animation for form submission */
.form-loading {
    position: relative;
}

.form-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Focus states for better accessibility */
input:focus, textarea:focus, select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Remove file button hover effect */
.removeFile:hover {
    background-color: #dc2626 !important;
    transform: scale(1.05);
}
</style>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    
    var fileAdded = 0;
    var maxFiles = 5;
    
    // Add attachment functionality
    $('.addAttachment').on('click', function() {
        fileAdded++;
        
        if (fileAdded >= maxFiles) {
            $(this).attr('disabled', true).addClass('opacity-50 cursor-not-allowed');
        }
        
        $(".fileUploadsContainer").append(`
            <div class="removeFileInput bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-4 transition-all hover:border-blue-300 dark:hover:border-blue-600">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        @lang('File') ${fileAdded}
                    </label>
                    <button type="button" 
                            class="removeFile text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition-colors flex items-center space-x-1">
                        <i class="las la-times"></i>
                        <span>@lang('Remove')</span>
                    </button>
                </div>
                <input type="file" 
                       name="attachments[]" 
                       class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300 dark:hover:file:bg-blue-900/50" 
                       accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" 
                       required>
            </div>
        `);
        
        // Add animation to new element
        $(".fileUploadsContainer .removeFileInput:last-child").hide().fadeIn(300);
    });
    
    // Remove file functionality
    $(document).on('click', '.removeFile', function() {
        $('.addAttachment').removeAttr('disabled').removeClass('opacity-50 cursor-not-allowed');
        fileAdded--;
        
        $(this).closest('.removeFileInput').fadeOut(300, function() {
            $(this).remove();
            // Renumber remaining files
            $('.removeFileInput').each(function(index) {
                $(this).find('label').text(`@lang('File') ${index + 1}`);
            });
        });
    });
    
    // Form validation and submission enhancement
    $('form').on('submit', function(e) {
        var subject = $('#subject').val().trim();
        var message = $('#inputMessage').val().trim();
        
        if (subject.length < 5) {
            e.preventDefault();
            notify('error', '@lang('Subject must be at least 5 characters long')');
            $('#subject').focus();
            return false;
        }
        
        if (message.length < 10) {
            e.preventDefault();
            notify('error', '@lang('Message must be at least 10 characters long')');
            $('#inputMessage').focus();
            return false;
        }
        
        // Add loading state
        $(this).addClass('form-loading');
        $('button[type="submit"]').prop('disabled', true).html(`
            <i class="las la-spinner la-spin text-lg mr-2"></i>
            <span>@lang('Submitting...')</span>
        `);
    });
    
    // Character counter for message
    $('#inputMessage').on('input', function() {
        var length = $(this).val().length;
        var minLength = 10;
        
        if (length < minLength) {
            $(this).removeClass('border-green-300').addClass('border-yellow-300');
        } else {
            $(this).removeClass('border-yellow-300').addClass('border-green-300');
        }
    });
    
    // Auto-resize textarea
    $('#inputMessage').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
})(jQuery);
</script>
@endpush