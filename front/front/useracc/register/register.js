document.getElementById('registration-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const user = {
        fullname: formData.get('fullname'),
        email: formData.get('email'),
        username: formData.get('username'),
        password: formData.get('password'),
        acceptedTerms: formData.get('terms') === 'on'
      };
      console.log('New registration:', user);
      alert('Registration successful for ' + user.username);
      window.location.href = 'login.html';
    });