{{--
    Example: Using Reusable Wizard Components
    This demonstrates how to use the wizard components we created
--}}

@extends($activeTemplate . 'layouts.app')

@push('style')
<link href="{{ asset('assets/global/css/wizard-components.css') }}" rel="stylesheet">
@endpush

@section('app')
    <!-- Include wizard container component -->
    @component($activeTemplate . 'partials.wizard_container', [
        'title' => 'Complete Your Profile',
        'subtitle' => 'Just a few more steps to get started',
        'maxWidth' => '3xl',
        'theme' => 'emerald'
    ])
        
        <!-- Include wizard progress component -->
        @include($activeTemplate . 'partials.wizard_progress', [
            'steps' => [
                ['id' => 1, 'label' => 'Personal Info'],
                ['id' => 2, 'label' => 'Contact Details'],
                ['id' => 3, 'label' => 'Verification'],
                ['id' => 4, 'label' => 'Complete']
            ],
            'currentStep' => 1,
            'theme' => 'emerald'
        ])

        <!-- Form -->
        <form method="POST" action="{{ route('example.submit') }}" id="exampleWizard">
            @csrf
            
            <!-- Step 1: Personal Information -->
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 1,
                'title' => 'Personal Information',
                'subtitle' => 'Tell us about yourself',
                'icon' => 'user',
                'iconBg' => 'emerald',
                'isActive' => true
            ])
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                    </div>
                </div>

                <!-- Include wizard navigation -->
                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 1,
                    'totalSteps' => 4,
                    'nextText' => 'Continue',
                    'alignment' => 'end'
                ])
            @endcomponent

            <!-- Step 2: Contact Details -->
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 2,
                'title' => 'Contact Details',
                'subtitle' => 'How can we reach you?',
                'icon' => 'envelope',
                'iconBg' => 'blue'
            ])
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input type="tel" name="phone" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                    </div>
                </div>

                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 2,
                    'totalSteps' => 4,
                    'prevText' => 'Back',
                    'nextText' => 'Continue'
                ])
            @endcomponent

            <!-- Step 3: Verification -->
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 3,
                'title' => 'Verification',
                'subtitle' => 'Verify your information',
                'icon' => 'shield-alt',
                'iconBg' => 'purple'
            ])
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Please review your information before proceeding.
                    </p>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Summary</h3>
                        <div id="summary-content" class="text-left space-y-2">
                            <!-- Summary will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 3,
                    'totalSteps' => 4,
                    'prevText' => 'Back',
                    'nextText' => 'Verify & Continue'
                ])
            @endcomponent

            <!-- Step 4: Complete -->
            @component($activeTemplate . 'partials.wizard_step', [
                'stepId' => 4,
                'title' => 'Complete Registration',
                'subtitle' => 'You\'re almost done!',
                'icon' => 'check-circle',
                'iconBg' => 'green'
            ])
                <div class="text-center">
                    <div class="mb-6">
                        <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="las la-check text-3xl"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Review the terms and conditions below, then click submit to complete your registration.
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-6 mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="agree_terms" required 
                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                I agree to the <a href="#" class="text-emerald-600 hover:text-emerald-700">Terms of Service</a> 
                                and <a href="#" class="text-emerald-600 hover:text-emerald-700">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                </div>

                @include($activeTemplate . 'partials.wizard_navigation', [
                    'currentStep' => 4,
                    'totalSteps' => 4,
                    'prevText' => 'Back',
                    'submitText' => 'Complete Registration',
                    'formId' => 'exampleWizard',
                    'loadingText' => 'Creating account...'
                ])
            @endcomponent
        </form>

    @endcomponent
@endsection

@push('script')
<script src="{{ asset('assets/global/js/wizard-manager.js') }}"></script>
<script>
    // Initialize wizard with custom options
    document.addEventListener('DOMContentLoaded', function() {
        const wizard = new WizardManager({
            formId: 'exampleWizard',
            totalSteps: 4,
            validateOnNext: true,
            autoFocus: true
        });

        // Listen for step changes to update summary
        document.addEventListener('wizard:stepChanged', function(e) {
            if (e.detail.step === 3) {
                updateSummary();
            }
        });

        function updateSummary() {
            const form = document.getElementById('exampleWizard');
            const summaryContent = document.getElementById('summary-content');
            
            const firstName = form.querySelector('[name="first_name"]').value;
            const lastName = form.querySelector('[name="last_name"]').value;
            const email = form.querySelector('[name="email"]').value;
            const phone = form.querySelector('[name="phone"]').value;

            summaryContent.innerHTML = `
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                    <span class="font-medium">${firstName} ${lastName}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Email:</span>
                    <span class="font-medium">${email}</span>
                </div>
                ${phone ? `
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                    <span class="font-medium">${phone}</span>
                </div>
                ` : ''}
            `;
        }
    });
</script>
@endpush