@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="space-y-6 pb-20 lg:pb-6">
    <!-- Premium Profile Header -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 rounded-3xl p-6 lg:p-8 text-white shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-2xl lg:text-3xl font-bold mb-2">@lang('Profile Settings')</h1>
                <p class="text-primary-100">@lang('Manage your account information and personal details')</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                    <i class="las la-user-edit text-2xl"></i>
                </div>
                <div>
                    <div class="text-primary-100 text-sm">@lang('Account Number')</div>
                    <div class="text-xl font-bold">{{ $user->account_number }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Navigation Pills -->
    @include($activeTemplate . 'partials.user_nav_pills', [
        'currentPageTitle' => __('Profile Setting'),
        'currentPageIcon' => 'las la-user-circle'
    ])

    <!-- Main Profile Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Profile Information Card -->
        <div class="lg:col-span-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm profile-card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">@lang('Account Information')</h3>
                
                <!-- Profile Image -->
                <div class="text-center mb-6">
                    <div class="relative inline-block">
                        <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 mx-auto border-4 border-primary-100 dark:border-primary-900/50 profile-image-preview">
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, null, true) }}" 
                                 alt="@lang('Profile Image')"
                                 class="w-full h-full object-cover"
                                 id="profileImagePreview">
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-primary-600 hover:bg-primary-700 rounded-full flex items-center justify-center text-white cursor-pointer transition-all upload-trigger"
                             onclick="document.getElementById('imageUpload').click()">
                            <i class="las la-camera text-lg"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->firstname }} {{ $user->lastname }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('Premium Banking Customer')</p>
                    </div>
                </div>

                <!-- Account Details -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Account No.')</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->account_number }}</span>
                    </div>
                    
                    @if ($user->branch)
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Branch')</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ __(@$user->branch->name) }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Username')</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->username }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Email')</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->email }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Mobile')</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->mobile }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">@lang('Country')</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ __($user->country_name) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Update Form -->
        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 lg:p-6 shadow-sm profile-card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">@lang('Update Profile Information')</h3>
                
                <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Hidden Image Upload -->
                    <input type="file" id="imageUpload" name="image" accept=".png, .jpg, .jpeg" class="hidden">
                    
                    <!-- Personal Information -->
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="las la-user-circle text-primary-600 dark:text-primary-400 text-xl mr-2"></i>
                            @lang('Personal Information')
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('First Name')</label>
                                <input type="text" name="firstname" value="{{ $user->firstname }}" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors form-input">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Last Name')</label>
                                <input type="text" name="lastname" value="{{ $user->lastname }}" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors form-input">
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="las la-map-marker-alt text-primary-600 dark:text-primary-400 text-xl mr-2"></i>
                            @lang('Address Information')
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('State')</label>
                                <input type="text" name="state" value="{{ $user->state }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors form-input">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('City')</label>
                                <input type="text" name="city" value="{{ $user->city }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors form-input">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Zip Code')</label>
                                <input type="text" name="zip" value="{{ $user->zip }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors form-input">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('Address')</label>
                                <textarea name="address" rows="3"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors form-input">{{ $user->address }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Image Upload Info -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                        <div class="flex items-start">
                            <i class="las la-info-circle text-blue-600 dark:text-blue-400 text-xl mr-3 mt-0.5"></i>
                            <div>
                                <h5 class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-1">@lang('Profile Image Guidelines')</h5>
                                <p class="text-sm text-blue-700 dark:text-blue-400">
                                    @lang('For optimal results, please upload an image with a 3.5:3 aspect ratio, which will be resized to') {{ getFileSize('userProfile') }} @lang('pixels. Supported formats: PNG, JPG, JPEG.')
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl">
                            <i class="las la-save"></i>
                            <span>@lang('Update Profile')</span>
                        </button>
                        
                        <button type="reset" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors flex items-center justify-center space-x-2">
                            <i class="las la-undo"></i>
                            <span>@lang('Reset Changes')</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    /* Enhanced Profile Image Preview */
    #profileImagePreview {
        transition: all 0.3s ease;
    }
    
    #profileImagePreview:hover {
        transform: scale(1.05);
    }
    
    /* Custom Form Focus States */
    .form-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
    }
    
    /* Enhanced Card Shadows */
    .profile-card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: box-shadow 0.3s ease;
    }
    
    .profile-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Custom Upload Button Animation */
    .upload-trigger {
        transition: all 0.3s ease;
    }
    
    .upload-trigger:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
</style>
@endpush

@push('script')
<script>
    'use strict';
    
    $(document).ready(function() {
        // Enhanced Image Upload Preview
        $("#imageUpload").on('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Validate file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('@lang("Image size should be less than 5MB")', 'error');
                    return;
                }
                
                // Validate file type
                if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                    showNotification('@lang("Please select a valid image file (JPG, JPEG, PNG)")', 'error');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('.profile-image-preview img').attr('src', e.target.result);
                    showNotification('@lang("Image uploaded successfully! Remember to save your changes.")', 'success');
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Form validation enhancement
        $('form').on('submit', function(e) {
            const firstName = $('input[name="firstname"]').val().trim();
            const lastName = $('input[name="lastname"]').val().trim();
            
            if (!firstName || !lastName) {
                e.preventDefault();
                showNotification('@lang("First name and last name are required")', 'error');
                return false;
            }
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="las la-spinner la-spin mr-2"></i>@lang("Updating...")');
        });
        
        // Reset form functionality
        $('button[type="reset"]').on('click', function(e) {
            e.preventDefault();
            if (confirm('@lang("Are you sure you want to reset all changes?")')) {
                location.reload();
            }
        });
    });
    
    // Enhanced notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="las ${type === 'success' ? 'la-check-circle' : type === 'error' ? 'la-exclamation-circle' : 'la-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 4000);
    }
</script>
@endpush