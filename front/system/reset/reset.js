document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const requestToggle = document.getElementById('request-toggle');
            const resetToggle = document.getElementById('reset-toggle');
            const requestForm = document.getElementById('request-form');
            const resetForm = document.getElementById('reset-form');
            const requestResetForm = document.getElementById('request-reset-form');
            const resetPasswordForm = document.getElementById('reset-password-form');
            const requestError = document.getElementById('request-error');
            const requestSuccess = document.getElementById('request-success');
            const resetError = document.getElementById('reset-error');
            const resetSuccess = document.getElementById('reset-success');
            const errorText = document.getElementById('error-text');
            const resetErrorText = document.getElementById('reset-error-text');
            const passwordStrength = document.getElementById('password-strength');
            const newPassword = document.getElementById('new-password');
            const toggleNewPassword = document.getElementById('toggle-new-password');
            const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
            const confirmPassword = document.getElementById('confirm-password');
            
            // Toggle between forms
            requestToggle.addEventListener('click', function() {
                if (!this.classList.contains('active')) {
                    this.classList.add('active');
                    resetToggle.classList.remove('active');
                    requestForm.classList.add('active');
                    resetForm.classList.remove('active');
                }
            });
            
            resetToggle.addEventListener('click', function() {
                if (!this.classList.contains('active')) {
                    this.classList.add('active');
                    requestToggle.classList.remove('active');
                    resetForm.classList.add('active');
                    requestForm.classList.remove('active');
                }
            });
            
            // Request reset form submission
            requestResetForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('email').value;
                
                // Simple validation
                if (!validateEmail(email)) {
                    showError(requestError, errorText, 'Please enter a valid email address');
                    return;
                }
                
                // Simulate API call
                simulateAPICall()
                    .then(() => {
                        requestError.style.display = 'none';
                        requestSuccess.style.display = 'block';
                        requestResetForm.reset();
                    })
                    .catch(() => {
                        showError(requestError, errorText, 'Email not found in our system');
                    });
            });
            
            // Reset password form submission
            resetPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const newPwd = newPassword.value;
                const confirmPwd = confirmPassword.value;
                
                // Validation
                if (newPwd.length < 8) {
                    showError(resetError, resetErrorText, 'Password must be at least 8 characters long');
                    return;
                }
                
                if (newPwd !== confirmPwd) {
                    showError(resetError, resetErrorText, 'Passwords do not match');
                    return;
                }
                
                // Simulate API call
                simulateAPICall()
                    .then(() => {
                        resetError.style.display = 'none';
                        resetSuccess.style.display = 'block';
                        resetPasswordForm.reset();
                        passwordStrength.style.width = '0%';
                    })
                    .catch(() => {
                        showError(resetError, resetErrorText, 'Failed to reset password. Please try again.');
                    });
            });
            
            // Password strength meter
            newPassword.addEventListener('input', function() {
                const strength = calculatePasswordStrength(this.value);
                updateStrengthMeter(strength);
            });
            
            // Toggle password visibility
            toggleNewPassword.addEventListener('click', function() {
                togglePasswordVisibility(newPassword, this);
            });
            
            toggleConfirmPassword.addEventListener('click', function() {
                togglePasswordVisibility(confirmPassword, this);
            });
            
            // Helper functions
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            function showError(errorElement, textElement, message) {
                errorElement.style.display = 'block';
                textElement.textContent = message;
                resetSuccess.style.display = 'none';
                requestSuccess.style.display = 'none';
            }
            
            function simulateAPICall() {
                return new Promise((resolve, reject) => {
                    // Simulate network delay
                    setTimeout(() => {
                        // Simulate 80% success rate
                        Math.random() > 0.2 ? resolve() : reject();
                    }, 1000);
                });
            }
            
            function calculatePasswordStrength(password) {
                let strength = 0;
                
                if (password.length >= 8) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 25;
                if (/[^A-Za-z0-9]/.test(password)) strength += 25;
                
                return strength;
            }
            
            function updateStrengthMeter(strength) {
                passwordStrength.style.width = strength + '%';
                
                if (strength < 50) {
                    passwordStrength.style.background = '#ef4444';
                } else if (strength < 75) {
                    passwordStrength.style.background = '#f59e0b';
                } else {
                    passwordStrength.style.background = '#10b981';
                }
            }
            
            function togglePasswordVisibility(input, toggle) {
                if (input.type === 'password') {
                    input.type = 'text';
                    toggle.innerHTML = '<i class="far fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    toggle.innerHTML = '<i class="far fa-eye"></i>';
                }
            }
        });