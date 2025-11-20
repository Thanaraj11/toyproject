document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('subscription-form');
            const successMessage = document.getElementById('success-message');
            const mainContent = document.querySelector('.main-content');
            const returnBtn = document.getElementById('return-btn');
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form values
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                
                // Simple validation
                if (name && email) {
                    // In a real application, you would send this data to a server
                    console.log('Subscription details:', { name, email });
                    
                    // Show success message
                    mainContent.style.display = 'none';
                    successMessage.style.display = 'block';
                    
                    // You could also send this data to a server here
                }
            });
            
            // Return button functionality
            returnBtn.addEventListener('click', function() {
                successMessage.style.display = 'none';
                mainContent.style.display = 'flex';
                form.reset();
            });
            
            // Social login buttons
            const socialButtons = document.querySelectorAll('.social-btn');
            
            socialButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const platform = this.classList.contains('google') ? 'Google' : 'Facebook';
                    alert(`You chose to continue with ${platform}. This would typically redirect to authentication.`);
                });
            });
            
            // Animate elements on page load
            const animatedElements = document.querySelectorAll('.illustration, .subscription');
            
            animatedElements.forEach(element => {
                element.style.opacity = 0;
                element.style.transform = 'translateY(20px)';
            });
            
            let delay = 0;
            animatedElements.forEach(element => {
                setTimeout(() => {
                    element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    element.style.opacity = 1;
                    element.style.transform = 'translateY(0)';
                }, delay);
                delay += 200;
            });
        });