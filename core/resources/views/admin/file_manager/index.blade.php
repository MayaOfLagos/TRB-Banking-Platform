@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="row align-items-center p-3">
                        <div class="col-lg-6">
                            <button type="button" class="btn btn--primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="las la-cloud-upload-alt"></i> @lang('Upload Files')
                            </button>
                            <button type="button" class="btn btn--danger btn-sm bulk-delete-btn" style="display:none;">
                                <i class="las la-trash"></i> @lang('Delete Selected')
                            </button>
                        </div>
                        <div class="col-lg-6">
                            <form action="{{ route('admin.frontend.file.manager.search') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('Search files...')" value="{{ $search ?? '' }}">
                                    <button class="btn btn--primary" type="submit">
                                        <i class="las la-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>@lang('Preview')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Size')</th>
                                    <th>@lang('Uploaded By')</th>
                                    <th>@lang('Uploaded At')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($files as $file)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="file-checkbox" value="{{ $file->id }}">
                                        </td>
                                        <td>
                                            @if($file->is_image)
                                                <img src="{{ $file->url }}" alt="{{ $file->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <i class="las la-file la-3x"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ Str::limit($file->name, 30) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ strtoupper($file->type) }}</span>
                                        </td>
                                        <td>{{ $file->formatted_size }}</td>
                                        <td>{{ $file->uploader->name ?? 'N/A' }}</td>
                                        <td>{{ showDateTime($file->created_at) }}</td>
                                        <td>
                                            <div class="button-group">
                                                <button type="button" class="btn btn-sm btn-outline--info copy-path-btn" data-path="{{ $file->url }}" title="@lang('Copy URL')">
                                                    <i class="las la-copy"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline--secondary copy-asset-btn" data-path="{{ $file->path }}" title="@lang('Copy Asset Path')">
                                                    <i class="las la-code"></i>
                                                </button>
                                                <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-outline--success" title="@lang('View')">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline--danger delete-btn" data-id="{{ $file->id }}" title="@lang('Delete')">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="empty-message-box">
                                                <img src="{{ asset('assets/images/empty_list.png') }}" alt="empty">
                                                <p>@lang('No files found')</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($files->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($files) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Upload Files')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.frontend.file.manager.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Select Files')</label>
                            <input type="file" name="files[]" class="form-control" multiple required>
                            <small class="text-muted">@lang('You can select multiple files. Max size: 10MB per file')</small>
                        </div>
                        <div id="preview-container" class="row mt-3"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Upload')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirm Delete')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" id="deleteForm">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to delete this file?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Delete')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .button-group {
        display: flex;
        gap: 5px;
    }
    .empty-message-box img {
        width: 100px;
        opacity: 0.5;
    }
    #preview-container img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";

        // Select All Checkbox
        $('#selectAll').on('change', function() {
            $('.file-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkDeleteBtn();
        });

        $('.file-checkbox').on('change', function() {
            toggleBulkDeleteBtn();
        });

        function toggleBulkDeleteBtn() {
            if ($('.file-checkbox:checked').length > 0) {
                $('.bulk-delete-btn').show();
            } else {
                $('.bulk-delete-btn').hide();
            }
        }

        // Bulk Delete
        $('.bulk-delete-btn').on('click', function() {
            let selectedIds = [];
            $('.file-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                notify('error', 'Please select files to delete');
                return;
            }

            if (confirm('Are you sure you want to delete ' + selectedIds.length + ' file(s)?')) {
                $.ajax({
                    url: "{{ route('admin.frontend.file.manager.bulk.delete') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: selectedIds
                    },
                    success: function(response) {
                        notify('success', 'Files deleted successfully');
                        location.reload();
                    },
                    error: function(xhr) {
                        notify('error', 'Error deleting files');
                    }
                });
            }
        });

        // Single Delete
        $('.delete-btn').on('click', function() {
            let fileId = $(this).data('id');
            $('#deleteForm').attr('action', "{{ route('admin.frontend.file.manager.delete', '') }}/" + fileId);
            $('#deleteModal').modal('show');
        });

        // Copy URL
        $('.copy-path-btn').on('click', function() {
            let path = $(this).data('path');
            copyToClipboard(path);
            notify('success', 'URL copied to clipboard');
        });

        // Copy Asset Path
        $('.copy-asset-btn').on('click', function() {
            let path = $(this).data('path');
            let assetPath = "{{ asset('') }}" + path;
            copyToClipboard(assetPath);
            notify('success', 'Asset path copied to clipboard');
        });

        function copyToClipboard(text) {
            let temp = $('<input>');
            $('body').append(temp);
            temp.val(text).select();
            document.execCommand('copy');
            temp.remove();
        }

        // File Preview
        $('input[name="files[]"]').on('change', function() {
            let files = this.files;
            let previewContainer = $('#preview-container');
            previewContainer.html('');

            if (files.length > 0) {
                $.each(files, function(index, file) {
                    if (file.type.startsWith('image/')) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            previewContainer.append(`
                                <div class="col-md-3 mb-2">
                                    <img src="${e.target.result}" alt="${file.name}">
                                </div>
                            `);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

    })(jQuery);
</script>
@endpush
