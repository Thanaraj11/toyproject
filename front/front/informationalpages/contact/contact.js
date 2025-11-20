

document.getElementById('contact-form').addEventListener('submit', function(e){
      e.preventDefault();
      const formData = new FormData(this);
      
      // Simulate form submission
      console.log('Contact submission:', {
        name: formData.get('name'),
        email: formData.get('email'),
        subject: formData.get('subject'),
        message: formData.get('message')
      });
      
      // Show success notification
      const notification = document.getElementById('notification');
      notification.textContent = 'Thank you for your message! We will get back to you shortly.';
      notification.style.display = 'block';
      notification.style.background = '#4CAF50';
      
      // Hide notification after 5 seconds
      setTimeout(() => {
        notification.style.display = 'none';
      }, 5000);
      
      // Reset the form
      this.reset();
    });