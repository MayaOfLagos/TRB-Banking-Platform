@extends($activeTemplate . 'layouts.master')
@section('content')

<!-- View Support Ticket Page -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 sm:py-8">
    <div class="max-w-5xl mx-auto px-1 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">@lang('Support Ticket')</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">@lang('View and manage your support ticket conversation')</p>
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

        <!-- Ticket Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 lg:px-8 py-6 lg:py-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="transform-bootstrap-badge">
                                @php echo $myTicket->statusBadge; @endphp
                            </div>
                            <span class="text-sm font-mono text-gray-500 dark:text-gray-400">#{{ $myTicket->ticket }}</span>
                        </div>
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">{{ $myTicket->subject }}</h2>
                    </div>
                    
                    @if ($myTicket->status != Status::TICKET_CLOSE && $myTicket->user)
                    <div class="lg:ml-6">
                        <button class="confirmationBtn inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 font-medium rounded-lg transition-all duration-200 border border-red-200 dark:border-red-800" 
                                data-question="@lang('Are you sure to close this ticket?')" 
                                data-action="{{ route('ticket.close', $myTicket->id) }}" 
                                type="button">
                            <i class="las la-times-circle mr-2 text-lg"></i>
                            @lang('Close Ticket')
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reply Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 lg:px-8 py-6 lg:py-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <i class="las la-reply text-blue-500 mr-2 text-xl"></i>
                    @lang('Add Reply')
                </h3>
                
                <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Message -->
                    <div>
                        <label for="replyMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            @lang('Your Message') <span class="text-red-500">*</span>
                        </label>
                        <textarea id="replyMessage"
                                  name="message" 
                                  rows="4" 
                                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 resize-none"
                                  placeholder="@lang('Type your reply here...')"
                                  required>{{ old('message') }}</textarea>
                    </div>

                    <!-- Attachments and Submit -->
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between space-y-4 lg:space-y-0">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white">@lang('Attachments')</h4>
                                <button type="button" 
                                        class="addAttachment inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg transition-colors">
                                    <i class="las la-plus mr-1"></i>
                                    @lang('Add Attachment')
                                </button>
                            </div>
                            
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-4 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-blue-700 dark:text-blue-400">
                                    @lang('Max 5 files | Size limit: ') {{ convertToReadableSize(ini_get('upload_max_filesize')) }} @lang(' | Formats: .jpg, .jpeg, .png, .pdf, .doc, .docx')
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 fileUploadsContainer">
                                <!-- Dynamic file upload fields will be added here -->
                            </div>
                        </div>
                        
                        <div class="lg:ml-8 lg:w-48">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                                <i class="las la-paper-plane text-lg"></i>
                                <span>@lang('Send Reply')</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Conversation Thread -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 lg:px-8 py-6 lg:py-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <i class="las la-comments text-purple-500 mr-2 text-xl"></i>
                    @lang('Conversation History')
                </h3>
                
                <div class="space-y-6">
                    @forelse ($messages as $message)
                        @if ($message->admin_id == 0)
                            <!-- User Message -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                            <i class="las la-user text-blue-600 dark:text-blue-400 text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300">{{ $message->ticket->name }}</h4>
                                            <time class="text-xs text-blue-600 dark:text-blue-400">
                                                {{ $message->created_at->format('M d, Y \a\t H:i') }}
                                            </time>
                                        </div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                            {!! nl2br(e($message->message)) !!}
                                        </div>
                                        
                                        @if ($message->attachments->count() > 0)
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                @foreach ($message->attachments as $k => $image)
                                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-800/30 dark:hover:bg-blue-800/50 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-lg transition-colors">
                                                        <i class="las la-paperclip mr-1"></i>
                                                        @lang('Attachment') {{ ++$k }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Admin Message -->
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                                            <i class="las la-user-tie text-amber-600 dark:text-amber-400 text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-300">{{ $message->admin->name }}</h4>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                                    @lang('Staff')
                                                </span>
                                            </div>
                                            <time class="text-xs text-amber-600 dark:text-amber-400">
                                                {{ $message->created_at->format('M d, Y \a\t H:i') }}
                                            </time>
                                        </div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                            {!! nl2br(e($message->message)) !!}
                                        </div>
                                        
                                        @if ($message->attachments->count() > 0)
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                @foreach ($message->attachments as $k => $image)
                                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 bg-amber-100 hover:bg-amber-200 dark:bg-amber-800/30 dark:hover:bg-amber-800/50 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-lg transition-colors">
                                                        <i class="las la-paperclip mr-1"></i>
                                                        @lang('Attachment') {{ ++$k }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12">
                            <div class="flex flex-col items-center">
                                <i class="las la-comment-dots text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">@lang('No Messages Yet')</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('Start the conversation by sending your first message above.')</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
/* Transform Bootstrap badges to Tailwind equivalent */
.transform-bootstrap-badge .badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.transform-bootstrap-badge .badge--success {
    @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300;
}

.transform-bootstrap-badge .badge--warning {
    @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300;
}

.transform-bootstrap-badge .badge--danger {
    @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300;
}

.transform-bootstrap-badge .badge--primary {
    @apply bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300;
}

.transform-bootstrap-badge .badge--info {
    @apply bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300;
}

.transform-bootstrap-badge .badge--dark {
    @apply bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300;
}

.transform-bootstrap-badge .badge--secondary {
    @apply bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400;
}

/* Auto-resize textarea */
.auto-resize {
    resize: none;
    overflow: hidden;
}

/* Loading animation for form submission */
.form-loading {
    position: relative;
    opacity: 0.7;
    pointer-events: none;
}

/* Conversation thread animations */
.conversation-message {
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Focus states for better accessibility */
input:focus, textarea:focus, select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Custom scrollbar for conversation */
.conversation-container {
    max-height: 600px;
    overflow-y: auto;
}

.conversation-container::-webkit-scrollbar {
    width: 6px;
}

.conversation-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.conversation-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.conversation-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .conversation-container::-webkit-scrollbar-track {
    background: #374151;
}

.dark .conversation-container::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.dark .conversation-container::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
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
        var message = $('#replyMessage').val().trim();
        
        if (message.length < 5) {
            e.preventDefault();
            notify('error', '@lang('Message must be at least 5 characters long')');
            $('#replyMessage').focus();
            return false;
        }
        
        // Add loading state
        $(this).addClass('form-loading');
        $('button[type="submit"]').prop('disabled', true).html(`
            <i class="las la-spinner la-spin text-lg mr-2"></i>
            <span>@lang('Sending...')</span>
        `);
    });
    
    // Auto-resize textarea
    $('#replyMessage').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Character counter for message
    $('#replyMessage').on('input', function() {
        var length = $(this).val().length;
        var minLength = 5;
        
        if (length < minLength) {
            $(this).removeClass('border-green-300').addClass('border-yellow-300');
        } else {
            $(this).removeClass('border-yellow-300').addClass('border-green-300');
        }
    });
    
    // Smooth scroll to bottom of conversation on page load
    $(document).ready(function() {
        $('html, body').animate({
            scrollTop: $(document).height()
        }, 1000);
    });
    
    // Enhanced confirmation modal styling
    $("#confirmationModal").find('.btn--primary').removeClass('btn--primary').addClass('btn--base');
    $("#confirmationModal").find('.modal-header button[data-bs-dismiss="modal"]').remove();
    
})(jQuery);
</script>
@endpush

@push('modal')
    <x-confirmation-modal height="h-none" />
@endpush