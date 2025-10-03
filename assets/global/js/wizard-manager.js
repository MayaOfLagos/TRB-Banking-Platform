/**
 * Generic Multi-Step Wizard JavaScript
 * Reusable wizard functionality for banking applications
 */
(function() {
    'use strict';

    class WizardManager {
        constructor(options = {}) {
            this.options = {
                formId: 'wizardForm',
                stepSelector: '.form-step',
                stepIndicatorSelector: '.step-indicator',
                progressLineSelector: '.progress-line',
                nextButtonSelector: '.btn-next',
                prevButtonSelector: '.btn-prev',
                submitButtonSelector: '#submit-btn',
                themeToggleSelector: '#theme-toggle',
                currentStep: 1,
                totalSteps: 3,
                validateOnNext: true,
                autoFocus: true,
                enableKeyboardNavigation: true,
                enableThemeToggle: true,
                animationDuration: 300,
                ...options
            };

            this.currentStep = this.options.currentStep;
            this.totalSteps = this.options.totalSteps;

            this.init();
        }

        init() {
            this.setupElements();
            this.setupEventListeners();
            if (this.options.enableThemeToggle) {
                this.setupThemeToggle();
            }
            this.setupPasswordToggles();
            this.setupPasswordStrength();
            this.setupFormValidation();
            this.resetSubmitButtonState();
            this.showStep(this.currentStep);
        }

        setupElements() {
            this.form = document.getElementById(this.options.formId);
            this.steps = document.querySelectorAll(this.options.stepSelector);
            this.stepIndicators = document.querySelectorAll(this.options.stepIndicatorSelector);
            this.progressLines = document.querySelectorAll(this.options.progressLineSelector);
            this.nextButtons = document.querySelectorAll(this.options.nextButtonSelector);
            this.prevButtons = document.querySelectorAll(this.options.prevButtonSelector);
            this.submitButton = document.querySelector(this.options.submitButtonSelector);
            this.themeToggle = document.querySelector(this.options.themeToggleSelector);
        }

        setupEventListeners() {
            // Navigation buttons
            this.nextButtons.forEach(btn => {
                btn.addEventListener('click', () => this.handleNext());
            });

            this.prevButtons.forEach(btn => {
                btn.addEventListener('click', () => this.handlePrevious());
            });

            // Form submission
            if (this.form) {
                this.form.addEventListener('submit', (e) => this.handleSubmit(e));
            }

            // Keyboard navigation
            if (this.options.enableKeyboardNavigation) {
                document.addEventListener('keydown', (e) => this.handleKeydown(e));
            }

            // Page visibility change to reset button state
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.resetSubmitButtonState();
                }
            });

            // Window focus to reset button state
            window.addEventListener('focus', () => {
                this.resetSubmitButtonState();
            });
        }

        setupThemeToggle() {
            if (!this.themeToggle) return;

            // Check for saved theme preference or default to 'light'
            const currentTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.classList.toggle('dark', currentTheme === 'dark');

            this.themeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                // Add animation effect
                this.themeToggle.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    this.themeToggle.style.transform = 'scale(1)';
                }, 150);
            });
        }

        setupPasswordToggles() {
            const toggleButtons = document.querySelectorAll('.toggle-password');

            toggleButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const targetId = button.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    const icon = button.querySelector('i');

                    if (!targetInput || !icon) return;

                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        icon.classList.remove('la-eye');
                        icon.classList.add('la-eye-slash');
                        button.setAttribute('title', 'Hide password');
                    } else {
                        targetInput.type = 'password';
                        icon.classList.remove('la-eye-slash');
                        icon.classList.add('la-eye');
                        button.setAttribute('title', 'Show password');
                    }
                });

                // Set initial title
                button.setAttribute('title', 'Show password');
            });
        }

        setupPasswordStrength() {
            const passwordInput = document.getElementById('password');
            const strengthContainer = document.getElementById('password-strength-container');
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('password-strength-text');

            if (!passwordInput || !strengthContainer) return;

            passwordInput.addEventListener('input', () => {
                const password = passwordInput.value;
                const strength = this.calculatePasswordStrength(password);

                if (password.length > 0) {
                    strengthContainer.classList.remove('hidden');
                    this.updateStrengthIndicator(strength, strengthBar, strengthText);
                } else {
                    strengthContainer.classList.add('hidden');
                }
            });
        }

        calculatePasswordStrength(password) {
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
                text: this.getStrengthText(score)
            };
        }

        getStrengthText(score) {
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

        updateStrengthIndicator(strength, bar, text) {
            if (!bar || !text) return;

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

        setupFormValidation() {
            if (!this.form) return;

            const inputs = this.form.querySelectorAll('input[required], select[required]');

            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });

                input.addEventListener('input', () => {
                    if (input.classList.contains('border-red-500')) {
                        this.validateField(input);
                    }
                });
            });
        }

        validateField(field) {
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

        handleNext() {
            if (this.options.validateOnNext && !this.validateCurrentStep()) {
                return;
            }

            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.showStep(this.currentStep);
                this.updateProgress();
            }
        }

        handlePrevious() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.showStep(this.currentStep);
                this.updateProgress();
            }
        }

        validateCurrentStep() {
            const currentStepElement = document.getElementById(`step-${this.currentStep}`);
            if (!currentStepElement) return true;

            const requiredFields = currentStepElement.querySelectorAll('input[required], select[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            // Additional validation for password confirmation
            if (this.currentStep === 2) {
                const password = document.getElementById('password');
                const passwordConfirm = document.getElementById('password_confirmation');

                if (password && passwordConfirm) {
                    if (password.value !== passwordConfirm.value) {
                        passwordConfirm.classList.add('border-red-500');
                        this.showNotification('Passwords do not match', 'error');
                        isValid = false;
                    } else {
                        passwordConfirm.classList.remove('border-red-500');
                        passwordConfirm.classList.add('border-emerald-500');
                    }
                }
            }

            if (!isValid) {
                this.showNotification('Please fill in all required fields correctly', 'error');
            }

            return isValid;
        }

        showStep(stepNumber) {
            // Hide all steps
            this.steps.forEach(step => {
                step.classList.remove('active');
            });

            // Show current step
            const currentStepElement = document.getElementById(`step-${stepNumber}`);
            if (currentStepElement) {
                currentStepElement.classList.add('active');

                // Focus first input if autoFocus is enabled
                if (this.options.autoFocus) {
                    const firstInput = currentStepElement.querySelector('input, select');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), this.options.animationDuration);
                    }
                }
            }

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Trigger custom event
            this.triggerEvent('stepChanged', { step: stepNumber });
        }

        updateProgress() {
            this.stepIndicators.forEach((indicator, index) => {
                const stepNum = index + 1;
                const circle = indicator.querySelector('.step-circle');
                const number = indicator.querySelector('.step-number');
                const check = indicator.querySelector('.step-check');

                if (stepNum < this.currentStep) {
                    // Completed step
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                    circle.classList.remove('bg-gray-300', 'dark:bg-gray-600', 'text-gray-600', 'dark:text-gray-400');
                    circle.classList.add('bg-emerald-600', 'text-white');
                    if (number) number.classList.add('hidden');
                    if (check) check.classList.remove('hidden');
                } else if (stepNum === this.currentStep) {
                    // Current step
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                    circle.classList.remove('bg-gray-300', 'dark:bg-gray-600', 'text-gray-600', 'dark:text-gray-400');
                    circle.classList.add('bg-emerald-600', 'text-white');
                    if (number) number.classList.remove('hidden');
                    if (check) check.classList.add('hidden');
                } else {
                    // Future step
                    indicator.classList.remove('active', 'completed');
                    circle.classList.add('bg-gray-300', 'dark:bg-gray-600', 'text-gray-600', 'dark:text-gray-400');
                    circle.classList.remove('bg-emerald-600', 'text-white');
                    if (number) number.classList.remove('hidden');
                    if (check) check.classList.add('hidden');
                }
            });

            // Update progress lines
            this.progressLines.forEach((line, index) => {
                const fill = line.querySelector('.progress-fill');
                const lineNum = index + 1;

                if (fill) {
                    if (lineNum < this.currentStep) {
                        fill.style.width = '100%';
                    } else {
                        fill.style.width = '0%';
                    }
                }
            });

            // Trigger custom event
            this.triggerEvent('progressUpdated', { step: this.currentStep });
        }

        handleSubmit(e) {
            e.preventDefault();

            if (!this.validateCurrentStep()) {
                return;
            }

            // Show loading state
            this.showLoadingState(true);

            // Add error recovery mechanism
            const submitTimeout = setTimeout(() => {
                this.showLoadingState(false);
                this.showNotification('Request timed out. Please try again.', 'error');
            }, 30000); // 30 seconds timeout

            // Handle form submission errors
            window.addEventListener('beforeunload', () => {
                clearTimeout(submitTimeout);
            });

            // Trigger custom event before submit
            this.triggerEvent('beforeSubmit', { form: this.form });

            // Submit form
            setTimeout(() => {
                this.form.submit();
            }, 500);
        }

        showLoadingState(isLoading) {
            if (!this.submitButton) return;

            const defaultText = this.submitButton.querySelector('.default-text');
            const loadingText = this.submitButton.querySelector('.loading-text');

            if (isLoading) {
                if (defaultText) defaultText.classList.add('hidden');
                if (loadingText) loadingText.classList.remove('hidden');
                this.submitButton.disabled = true;
            } else {
                if (defaultText) defaultText.classList.remove('hidden');
                if (loadingText) loadingText.classList.add('hidden');
                this.submitButton.disabled = false;
            }
        }

        resetSubmitButtonState() {
            if (this.submitButton) {
                const defaultText = this.submitButton.querySelector('.default-text');
                const loadingText = this.submitButton.querySelector('.loading-text');

                this.submitButton.disabled = false;

                if (defaultText) {
                    defaultText.classList.remove('hidden');
                }
                if (loadingText) {
                    loadingText.classList.add('hidden');
                }
            }
        }

        handleKeydown(e) {
            if (e.key === 'Enter' && !e.target.matches('textarea')) {
                e.preventDefault();
                if (this.currentStep < this.totalSteps) {
                    this.handleNext();
                } else {
                    this.handleSubmit(e);
                }
            }
        }

        showNotification(message, type = 'info') {
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
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 4000);
        }

        triggerEvent(eventName, data = {}) {
            const event = new CustomEvent(`wizard:${eventName}`, {
                detail: { wizard: this, ...data }
            });
            document.dispatchEvent(event);
        }

        // Public API methods
        goToStep(stepNumber) {
            if (stepNumber >= 1 && stepNumber <= this.totalSteps) {
                this.currentStep = stepNumber;
                this.showStep(stepNumber);
                this.updateProgress();
            }
        }

        getCurrentStep() {
            return this.currentStep;
        }

        getTotalSteps() {
            return this.totalSteps;
        }

        isValid() {
            return this.validateCurrentStep();
        }
    }

    // Global utility functions
    window.closeExistModal = function() {
        const modal = document.getElementById('existModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Auto-initialize wizard when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            // Check if there's a wizard form on the page
            if (document.querySelector('.form-step')) {
                window.wizardManager = new WizardManager();
            }
        });
    } else {
        // Check if there's a wizard form on the page
        if (document.querySelector('.form-step')) {
            window.wizardManager = new WizardManager();
        }
    }

    // Export for use as module
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = WizardManager;
    }

    // Export for use as AMD module
    if (typeof define === 'function' && define.amd) {
        define(() => WizardManager);
    }

    // Export to global scope
    window.WizardManager = WizardManager;

})();