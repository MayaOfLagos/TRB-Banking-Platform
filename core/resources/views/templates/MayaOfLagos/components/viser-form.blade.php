@php
    // Group fields into rows based on width
    $rows = [];
    $currentRow = [];
    $currentRowWidth = 0;
    
    foreach($formData as $data) {
        $fieldWidth = (int) (@$data->width ?? 12);
        
        // If adding this field would exceed 12 columns, start a new row
        if ($currentRowWidth + $fieldWidth > 12) {
            if (!empty($currentRow)) {
                $rows[] = $currentRow;
            }
            $currentRow = [$data];
            $currentRowWidth = $fieldWidth;
        } else {
            $currentRow[] = $data;
            $currentRowWidth += $fieldWidth;
        }
    }
    
    // Add the last row if it has content
    if (!empty($currentRow)) {
        $rows[] = $currentRow;
    }
    
    // Function to convert width to Tailwind class
    function getColSpanClass($width) {
        $width = (int) $width;
        switch($width) {
            case 1: return 'col-span-1';
            case 2: return 'col-span-2';
            case 3: return 'col-span-3';
            case 4: return 'col-span-4';
            case 5: return 'col-span-5';
            case 6: return 'col-span-6';
            case 7: return 'col-span-7';
            case 8: return 'col-span-8';
            case 9: return 'col-span-9';
            case 10: return 'col-span-10';
            case 11: return 'col-span-11';
            case 12: return 'col-span-12';
            default: return 'col-span-12';
        }
    }
@endphp

<div class="space-y-6">
    @foreach($rows as $row)
        <div class="grid grid-cols-12 gap-4">
            @foreach($row as $data)
                @php
                    $fieldWidth = (int) (@$data->width ?? 12);
                    $colSpanClass = getColSpanClass($fieldWidth);
                @endphp
                <div class="{{ $colSpanClass }}">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __($data->name) }} 
                            @if(@$data->instruction) 
                                <span class="group relative inline-block">
                                    <i class="fas fa-info-circle text-blue-500 dark:text-blue-400 cursor-help"></i>
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-10">
                                        {{ __($data->instruction) }}
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                    </div>
                                </span>
                            @endif 
                            @if($data->is_required == 'required' && ($data->type == 'checkbox' || $data->type == 'radio')) 
                                <span class="text-red-500">*</span> 
                            @endif
                        </label>
                    
                    @if($data->type == 'text')
                        <input type="text"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            name="{{ $data->label }}"
                            value="{{ old($data->label) }}"
                            placeholder="{{ __($data->name) }}"
                            @if($data->is_required == 'required') required @endif
                        >
                    @elseif($data->type == 'url')
                        <input type="url"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            name="{{ $data->label }}"
                            value="{{ old($data->label) }}"
                            placeholder="{{ __($data->name) }}"
                            @if($data->is_required == 'required') required @endif
                        >
                    @elseif($data->type == 'email')
                        <input type="email"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            name="{{ $data->label }}"
                            value="{{ old($data->label) }}"
                            placeholder="{{ __($data->name) }}"
                            @if($data->is_required == 'required') required @endif
                        >
                    @elseif($data->type == 'datetime')
                        <input type="datetime-local"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                            name="{{ $data->label }}"
                            value="{{ old($data->label) }}"
                            @if($data->is_required == 'required') required @endif
                        >
                    @elseif($data->type == 'date')
                        <input type="date"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                            name="{{ $data->label }}"
                            value="{{ old($data->label) }}"
                            @if($data->is_required == 'required') required @endif
                        >
                    @elseif($data->type == 'time')
                        <input type="time"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white"
                            name="{{ $data->label }}"
                            value="{{ old($data->label) }}"
                            @if($data->is_required == 'required') required @endif
                        >
                    @elseif($data->type == 'number')
                        <input type="number"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            name="{{ $data->label }}"
                            value="{{ old($data->label) }}"
                            step="any"
                            placeholder="{{ __($data->name) }}"
                            @if($data->is_required == 'required') required @endif
                        >
                    @elseif($data->type == 'textarea')
                        <textarea
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white resize-vertical placeholder-gray-500 dark:placeholder-gray-400"
                            name="{{ $data->label }}"
                            rows="4"
                            placeholder="{{ __($data->name) }}"
                            @if($data->is_required == 'required') required @endif
                        >{{ old($data->label) }}</textarea>
                    @elseif($data->type == 'select')
                        <div class="relative">
                            <select
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white appearance-none pr-12"
                                name="{{ $data->label }}"
                                @if($data->is_required == 'required') required @endif
                            >
                                <option value="" class="text-gray-500">@lang('Select One')</option>
                                @foreach ($data->options as $item)
                                    <option value="{{ $item }}" @selected($item == old($data->label))>{{ __($item) }}</option>
                                @endforeach
                            </select>
                            <!-- Custom dropdown arrow -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="las la-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    @elseif($data->type == 'checkbox')
                        <div class="space-y-3">
                            @foreach($data->options as $option)
                                <div class="flex items-center">
                                    <input
                                        class="w-4 h-4 text-blue-600 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2"
                                        name="{{ $data->label }}[]"
                                        type="checkbox"
                                        value="{{ $option }}"
                                        id="{{ $data->label }}_{{ titleToKey($option) }}"
                                    >
                                    <label class="ml-3 text-sm text-gray-700 dark:text-gray-300" for="{{ $data->label }}_{{ titleToKey($option) }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="checkbox-required-error text-red-500 text-sm mt-1 hidden"></div>
                    @elseif($data->type == 'radio')
                        <div class="space-y-3">
                            @foreach($data->options as $option)
                                <div class="flex items-center">
                                    <input
                                        class="w-4 h-4 text-blue-600 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2"
                                        name="{{ $data->label }}"
                                        type="radio"
                                        value="{{ $option }}"
                                        id="{{ $data->label }}_{{ titleToKey($option) }}"
                                        @checked($option == old($data->label))
                                    >
                                    <label class="ml-3 text-sm text-gray-700 dark:text-gray-300" for="{{ $data->label }}_{{ titleToKey($option) }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @elseif($data->type == 'file')
                        <div class="space-y-2">
                            <div class="relative">
                                <input
                                    type="file"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all duration-200 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300"
                                    name="{{ $data->label }}"
                                    @if($data->is_required == 'required') required @endif
                                    accept="@foreach(explode(',',$data->extensions) as $ext) .{{ $ext }}, @endforeach"
                                >
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                                <i class="las la-info-circle mr-1"></i>
                                @lang('Supported formats'): {{ $data->extensions }}
                            </p>
                        </div>
                    @endif
                    
                    @error($data->label)
                        <p class="text-red-500 text-sm mt-1 flex items-center">
                            <i class="las la-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

@push('script')
<script>
'use strict';
(function($) {
    // Enhanced form interactions
    
    // Checkbox validation for required checkboxes
    $('input[type="checkbox"]').on('change', function() {
        const name = $(this).attr('name');
        const container = $(this).closest('.space-y-3');
        const errorElement = container.next('.checkbox-required-error');
        const isRequired = container.closest('.space-y-2').find('label .text-red-500').length > 0;
        
        if (isRequired) {
            const checkedCount = container.find(`input[name="${name}"]:checked`).length;
            if (checkedCount > 0) {
                errorElement.addClass('hidden');
                container.find('input[type="checkbox"]').removeClass('border-red-500');
            } else {
                errorElement.removeClass('hidden').text('@lang("Please select at least one option")');
                container.find('input[type="checkbox"]').addClass('border-red-500');
            }
        }
    });
    
    // Form validation enhancement
    $('form').on('submit', function(e) {
        let hasErrors = false;
        
        // Validate required checkboxes
        $(this).find('.space-y-3').each(function() {
            const container = $(this);
            const isRequired = container.closest('.space-y-2').find('label .text-red-500').length > 0;
            
            if (isRequired && container.find('input[type="checkbox"]').length > 0) {
                const name = container.find('input[type="checkbox"]').first().attr('name');
                const checkedCount = container.find(`input[name="${name}"]:checked`).length;
                
                if (checkedCount === 0) {
                    hasErrors = true;
                    const errorElement = container.next('.checkbox-required-error');
                    errorElement.removeClass('hidden').text('@lang("Please select at least one option")');
                    container.find('input[type="checkbox"]').addClass('border-red-500');
                }
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            // Scroll to first error
            const firstError = $('.checkbox-required-error:not(.hidden)').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    });
    
    // Input focus effects
    $('input, textarea, select').on('focus', function() {
        $(this).closest('.space-y-2').addClass('form-field-focused');
    }).on('blur', function() {
        $(this).closest('.space-y-2').removeClass('form-field-focused');
    });
    
})(jQuery);
</script>

<style>
/* Additional styles for the Tailwind form */
.form-field-focused label {
    @apply text-blue-600 dark:text-blue-400;
}

/* Custom select dropdown styling */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
    padding-right: 40px;
}

/* Dark mode select dropdown */
.dark select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%9ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
}

/* File input styling improvements */
input[type="file"]::-webkit-file-upload-button {
    @apply mr-4 py-2 px-4 rounded-lg border-0 text-sm font-medium bg-blue-50 text-blue-700 cursor-pointer;
    @apply hover:bg-blue-100 transition-colors duration-200;
}

.dark input[type="file"]::-webkit-file-upload-button {
    @apply bg-blue-900/30 text-blue-300 hover:bg-blue-900/50;
}

/* Tooltip styling */
.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}

.group:hover .group-hover\:visible {
    visibility: visible;
}
</style>
@endpush