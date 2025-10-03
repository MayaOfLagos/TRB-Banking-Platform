/**
 * Multi-Step Registration Wizard
 * Enhanced form navigation with validation and UX improvements
 */
(function() {
    'use strict';

    let currentStep = 1;
    const totalSteps = 3;

    // Form elements
    const form = document.getElementById('registrationForm');
    const steps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    const progressLines = document.querySelectorAll('.progress-line');
    const nextButtons = document.querySelectorAll('.btn-next');
    const prevButtons = document.querySelectorAll('.btn-prev');
    const submitButton = document.getElementById('submit-btn');

    // Theme toggle
    const themeToggle = document.getElementById('theme-toggle');

    /**
     * Initialize the wizard
     */
    function init() {
        setupEventListeners();
        setupThemeToggle();
        setupPasswordToggles();
        setupPasswordStrength();
        setupFormValidation();
        setupCountrySelection();
        setupUserExistCheck();
        resetSubmitButtonState(); // Ensure button is not stuck in disabled state
        showStep(1);
    }

    /**
     * Reset submit button state on page load
     * This prevents the button from being stuck in disabled state after page refresh
     */
    function resetSubmitButtonState() {
        if (submitButton) {
            const defaultText = submitButton.querySelector('.default-text');
            const loadingText = submitButton.querySelector('.loading-text');

            submitButton.disabled = false;

            if (defaultText) {
                defaultText.classList.remove('hidden');
            }
            if (loadingText) {
                loadingText.classList.add('hidden');
            }
        }
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Navigation buttons
        nextButtons.forEach(btn => {
            btn.addEventListener('click', handleNext);
        });

        prevButtons.forEach(btn => {
            btn.addEventListener('click', handlePrevious);
        });

        // Form submission
        if (form) {
            form.addEventListener('submit', handleSubmit);
        }

        // Enter key navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.target.matches('textarea')) {
                e.preventDefault();
                if (currentStep < totalSteps) {
                    handleNext();
                } else {
                    handleSubmit(e);
                }
            }
        });

        // Handle page visibility change to reset button state
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                resetSubmitButtonState();
            }
        });

        // Handle page focus to reset button state
        window.addEventListener('focus', function() {
            resetSubmitButtonState();
        });
    }

    /**
     * Setup theme toggle functionality
     */
    function setupThemeToggle() {
        if (!themeToggle) return;

        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.classList.toggle('dark', currentTheme === 'dark');

        themeToggle.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');

            // Add animation effect
            themeToggle.style.transform = 'scale(0.9)';
            setTimeout(() => {
                themeToggle.style.transform = 'scale(1)';
            }, 150);
        });
    }

    /**
     * Setup password visibility toggles
     */
    function setupPasswordToggles() {
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (!targetInput || !icon) return;

                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.classList.remove('la-eye');
                    icon.classList.add('la-eye-slash');
                    this.setAttribute('title', 'Hide password');
                } else {
                    targetInput.type = 'password';
                    icon.classList.remove('la-eye-slash');
                    icon.classList.add('la-eye');
                    this.setAttribute('title', 'Show password');
                }
            });

            // Set initial title
            button.setAttribute('title', 'Show password');
        });
    }

    /**
     * Setup password strength indicator
     */
    function setupPasswordStrength() {
        const passwordInput = document.getElementById('password');
        const strengthContainer = document.getElementById('password-strength-container');
        const strengthBar = document.getElementById('password-strength');
        const strengthText = document.getElementById('password-strength-text');

        if (!passwordInput || !strengthContainer) return;

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);

            if (password.length > 0) {
                strengthContainer.classList.remove('hidden');
                updateStrengthIndicator(strength, strengthBar, strengthText);
            } else {
                strengthContainer.classList.add('hidden');
            }
        });
    }

    /**
     * Calculate password strength
     */
    function calculatePasswordStrength(password) {
        let score = 0;
        const checks = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            numbers: /\d/.test(password),
            symbols: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        Object.values(checks).forEach(check => {
            if (check) score++;
        });

        return {
            score: score,
            percentage: (score / 5) * 100,
            text: getStrengthText(score)
        };
    }

    /**
     * Get strength text based on score
     */
    function getStrengthText(score) {
        const texts = {
            0: 'Very Weak',
            1: 'Weak',
            2: 'Fair',
            3: 'Good',
            4: 'Strong',
            5: 'Very Strong'
        };
        return texts[score] || 'Very Weak';
    }

    /**
     * Update strength indicator UI
     */
    function updateStrengthIndicator(strength, bar, text) {
        const colors = {
            0: 'bg-red-500',
            1: 'bg-red-400',
            2: 'bg-yellow-500',
            3: 'bg-blue-500',
            4: 'bg-green-500',
            5: 'bg-emerald-500'
        };

        // Remove all color classes
        Object.values(colors).forEach(color => {
            bar.classList.remove(color);
        });

        // Add current color
        bar.classList.add(colors[strength.score]);
        bar.style.width = strength.percentage + '%';
        text.textContent = strength.text;
    }

    /**
     * Setup form validation
     */
    function setupFormValidation() {
        const inputs = form.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('border-red-500')) {
                    validateField(this);
                }
            });
        });
    }

    /**
     * Validate individual field
     */
    function validateField(field) {
        const isValid = field.checkValidity();

        if (isValid) {
            field.classList.remove('border-red-500');
            field.classList.add('border-emerald-500');

            // Hide error message
            const errorMsg = field.parentNode.querySelector('.text-red-500');
            if (errorMsg && !errorMsg.classList.contains('mt-1')) {
                errorMsg.style.display = 'none';
            }
        } else {
            field.classList.remove('border-emerald-500');
            field.classList.add('border-red-500');
        }

        return isValid;
    }

    /**
     * Setup country selection and mobile code
     */
    function setupCountrySelection() {
        const countrySelect = document.getElementById('country');
        const mobileCodeSpan = document.querySelector('.mobile-code');
        const mobileCodeInput = document.querySelector('input[name="mobile_code"]');
        const countryCodeInput = document.querySelector('input[name="country_code"]');

        if (!countrySelect) return;

        countrySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const mobileCode = selectedOption.getAttribute('data-mobile_code');
            const countryCode = selectedOption.getAttribute('data-code');

            if (mobileCodeSpan) {
                mobileCodeSpan.textContent = '+' + mobileCode;
            }
            if (mobileCodeInput) {
                mobileCodeInput.value = mobileCode;
            }
            if (countryCodeInput) {
                countryCodeInput.value = countryCode;
            }
        });

        // Trigger change event on page load
        countrySelect.dispatchEvent(new Event('change'));
    }

    /**
     * Setup user existence check
     */
    function setupUserExistCheck() {
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');

        if (usernameInput) {
            let usernameTimeout;
            usernameInput.addEventListener('input', function() {
                clearTimeout(usernameTimeout);
                usernameTimeout = setTimeout(() => {
                    checkUserExists('username', this.value);
                }, 500);
            });
        }

        if (emailInput) {
            let emailTimeout;
            emailInput.addEventListener('input', function() {
                clearTimeout(emailTimeout);
                emailTimeout = setTimeout(() => {
                    checkUserExists('email', this.value);
                }, 500);
            });
        }
    }

    /**
     * Check if user exists
     */
    function checkUserExists(type, value) {
        if (!value || value.length < 3) return;

        // Add loading indicator (optional enhancement)
        const field = document.getElementById(type);

        // Simulate API call (replace with actual endpoint)
        // This should be implemented on the backend
        console.log(`Checking ${type}: ${value}`);
    }

    /**
     * Handle next button click
     */
    function handleNext() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                updateProgress();
            }
        }
    }

    /**
     * Handle previous button click
     */
    function handlePrevious() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            updateProgress();
        }
    }

    /**
     * Validate current step
     */
    function validateCurrentStep() {
        const currentStepElement = document.getElementById(`step-${currentStep}`);
        const requiredFields = currentStepElement.querySelectorAll('input[required], select[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        // Additional validation for step 2 (password confirmation)
        if (currentStep === 2) {
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            if (password && passwordConfirm) {
                if (password.value !== passwordConfirm.value) {
                    passwordConfirm.classList.add('border-red-500');
                    showNotification('Passwords do not match', 'error');
                    isValid = false;
                } else {
                    passwordConfirm.classList.remove('border-red-500');
                    passwordConfirm.classList.add('border-emerald-500');
                }
            }
        }

        if (!isValid) {
            showNotification('Please fill in all required fields correctly', 'error');
        }

        return isValid;
    }

    /**
     * Show specific step
     */
    function showStep(stepNumber) {
        // Hide all steps
        steps.forEach(step => {
            step.classList.remove('active');
        });

        // Show current step
        const currentStepElement = document.getElementById(`step-${stepNumber}`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');

            // Focus first input
            const firstInput = currentStepElement.querySelector('input, select');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 300);
            }
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /**
     * Update progress indicators
     */
    function updateProgress() {
        stepIndicators.forEach((indicator, index) => {
            const stepNum = index + 1;
            const circle = indicator.querySelector('.step-circle');
            const number = indicator.querySelector('.step-number');
            const check = indicator.querySelector('.step-check');

            if (stepNum < currentStep) {
                // Completed step
                indicator.classList.add('completed');
                indicator.classList.remove('active');
                circle.classList.remove('bg-gray-300', 'dark:bg-gray-600', 'text-gray-600', 'dark:text-gray-400');
                circle.classList.add('bg-emerald-600', 'text-white');
                number.classList.add('hidden');
                check.classList.remove('hidden');
            } else if (stepNum === currentStep) {
                // Current step
                indicator.classList.add('active');
                indicator.classList.remove('completed');
                circle.classList.remove('bg-gray-300', 'dark:bg-gray-600', 'text-gray-600', 'dark:text-gray-400');
                circle.classList.add('bg-emerald-600', 'text-white');
                number.classList.remove('hidden');
                check.classList.add('hidden');
            } else {
                // Future step
                indicator.classList.remove('active', 'completed');
                circle.classList.add('bg-gray-300', 'dark:bg-gray-600', 'text-gray-600', 'dark:text-gray-400');
                circle.classList.remove('bg-emerald-600', 'text-white');
                number.classList.remove('hidden');
                check.classList.add('hidden');
            }
        });

        // Update progress lines
        progressLines.forEach((line, index) => {
            const fill = line.querySelector('.progress-fill');
            const lineNum = index + 1;

            if (lineNum < currentStep) {
                fill.style.width = '100%';
            } else {
                fill.style.width = '0%';
            }
        });
    }

    /**
     * Handle form submission
     */
    function handleSubmit(e) {
        e.preventDefault();

        if (!validateCurrentStep()) {
            return;
        }

        // Show loading state
        showLoadingState(true);

        // Add error recovery mechanism
        const submitTimeout = setTimeout(() => {
            showLoadingState(false);
            showNotification('Request timed out. Please try again.', 'error');
        }, 30000); // 30 seconds timeout

        // Handle form submission errors
        window.addEventListener('beforeunload', function() {
            clearTimeout(submitTimeout);
        });

        // Submit form
        setTimeout(() => {
            form.submit();
        }, 500);
    }

    /**
     * Show loading state
     */
    function showLoadingState(isLoading) {
        const defaultText = submitButton.querySelector('.default-text');
        const loadingText = submitButton.querySelector('.loading-text');

        if (isLoading) {
            defaultText.classList.add('hidden');
            loadingText.classList.remove('hidden');
            submitButton.disabled = true;
        } else {
            defaultText.classList.remove('hidden');
            loadingText.classList.add('hidden');
            submitButton.disabled = false;
        }
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-2xl shadow-lg max-w-sm transform translate-x-full transition-transform duration-300 ${
            type === 'error' ? 'bg-red-500 text-white' : 
            type === 'success' ? 'bg-emerald-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="las ${type === 'error' ? 'la-exclamation-triangle' : type === 'success' ? 'la-check-circle' : 'la-info-circle'} text-xl mr-3"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Hide notification
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 4000);
    }

    /**
     * Close exist modal
     */
    window.closeExistModal = function() {
        const modal = document.getElementById('existModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();