document.addEventListener('DOMContentLoaded', function() {
            // Get the button element
            const goHomeButton = document.getElementById('goHome');
            
            // Add click event to the button
            goHomeButton.addEventListener('click', function() {
                // In a real scenario, this would redirect to your homepage
                // For demonstration, we'll show a message
                alert('Redirecting to homepage...');
                // window.location.href = '/'; // Uncomment this in production
            });
            
            // Add some interactive effects to the 404 text
            const errorCode = document.querySelector('.error-code');
            
            errorCode.addEventListener('mouseover', function() {
                this.style.transform = 'rotate(0deg) scale(1.1)';
                this.style.transition = 'all 0.5s ease';
            });
            
            errorCode.addEventListener('mouseout', function() {
                this.style.transform = 'rotate(-5deg) scale(1)';
            });
            
            // Add a fun effect - typewriter style text for the error message
            const errorMessage = document.querySelector('.error-message');
            const originalText = errorMessage.textContent;
            errorMessage.textContent = '';
            
            let i = 0;
            const typeWriter = setInterval(function() {
                if (i < originalText.length) {
                    errorMessage.textContent += originalText.charAt(i);
                    i++;
                } else {
                    clearInterval(typeWriter);
                }
            }, 50);
        });