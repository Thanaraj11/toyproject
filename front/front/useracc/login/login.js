 document.getElementById('login-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const username = this.username.value;
      const password = this.password.value;
      console.log('Login attempt:', username, password);
      alert('Login submitted for ' + username);
    });

    document.getElementById('forgot-password').addEventListener('click', function(e) {
      e.preventDefault();
      alert('Password reset link sent to your email (placeholder)');
    });