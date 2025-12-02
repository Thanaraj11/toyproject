
<?php
// useracc/register/register.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: ../dashboard/dashboard.php");
    exit();
}
?>

<?php include 'register_backend.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="register.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Create Your Account</title>
  <style>
    
  </style>
</head>
<body>
  
  <div class="container">
    <div class="left-panel">
      <div class="logo">
        <i class="fas fa-store"></i>
        <span>BrandName</span>
      </div>
      <div class="content">
        <h1>Join thousands of satisfied customers</h1>
        <p>Create an account to access exclusive deals, faster checkout, and personalized recommendations.</p>
        <ul class="features">
          <li><i class="fas fa-shipping-fast"></i> Free shipping on orders over $50</li>
          <li><i class="fas fa-gift"></i> Exclusive member-only discounts</li>
          <li><i class="fas fa-heart"></i> Personalized product recommendations</li>
          <li><i class="fas fa-clock"></i> Faster checkout process</li>
        </ul>
      </div>
    </div>
    
    <div class="right-panel">
      <div class="form-container">
        <div class="form-header">
          <h2>Create Account</h2>
          <p>Join our community today</p>
        </div>
        
        <?php
        // Display error message if exists
        if (isset($error_message)) {
            echo '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><div>' . $error_message . '</div></div>';
        }
        
        // Display success message if redirected from login
        if (isset($_GET['redirect']) && $_GET['redirect'] == 'login') {
            echo '<div class="alert alert-success"><i class="fas fa-info-circle"></i><div>Please register to continue.</div></div>';
        }
        ?>
        
        <form id="registration-form" method="POST">
          <input type="hidden" name="register" value="1">
          
          <div class="form-group">
            <label for="fullname">Full Name</label>
            <div class="input-wrapper">
              <i class="fas fa-user"></i>
              <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name" 
                     value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
            </div>
          </div>
          
          <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-wrapper">
              <i class="fas fa-envelope"></i>
              <input type="email" id="email" name="email" required placeholder="Enter your email address"
                     value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
          </div>
          
          <div class="form-group">
            <label for="phone">Phone Number (Optional)</label>
            <div class="input-wrapper">
              <i class="fas fa-phone"></i>
              <input type="tel" id="phone" name="phone" placeholder="Enter your phone number"
                     value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
          </div>
          
          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <i class="fas fa-lock"></i>
              <input type="password" id="password" name="password" required placeholder="Create a secure password">
              <button type="button" class="password-toggle" id="togglePassword">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <div class="password-strength">
              <div class="strength-bar">
                <div class="strength-fill" id="strengthFill"></div>
              </div>
              <div class="strength-text" id="strengthText">Password strength</div>
            </div>
          </div>
          
          <div class="terms-container">
            <input type="checkbox" id="terms" name="terms" required <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>> 
            <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
          </div>
          
          <button type="submit" class="btn-register">
            <i class="fas fa-user-plus"></i> Create Account
          </button>
          
          <div class="divider">
            <span>Or continue with</span>
          </div>
          
          <div class="social-login">
            <button type="button" class="social-btn">
              <i class="fab fa-google"></i> Google
            </button>
            <button type="button" class="social-btn">
              <i class="fab fa-facebook-f"></i> Facebook
            </button>
          </div>
          
          <div class="login-link">
            Already have an account? <a href="../login/login.php">Sign in</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Password visibility toggle
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon = togglePassword.querySelector('i');
    
    togglePassword.addEventListener('click', function() {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      eyeIcon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });
    
    // Password strength indicator
    password.addEventListener('input', function() {
      const value = password.value;
      const strengthFill = document.getElementById('strengthFill');
      const strengthText = document.getElementById('strengthText');
      
      let strength = 0;
      let text = 'Password strength';
      let color = '#64748b';
      
      if (value.length >= 6) {
        strength += 25;
      }
      
      if (value.length >= 8) {
        strength += 25;
      }
      
      if (/[a-z]/.test(value) && /[A-Z]/.test(value)) {
        strength += 25;
      }
      
      if (/[0-9]/.test(value) && /[^a-zA-Z0-9]/.test(value)) {
        strength += 25;
      }
      
      strengthFill.style.width = `${strength}%`;
      
      if (strength < 50) {
        color = '#ef4444';
        text = 'Weak password';
      } else if (strength < 75) {
        color = '#f59e0b';
        text = 'Medium password';
      } else {
        color = '#10b981';
        text = 'Strong password';
      }
      
      strengthFill.style.backgroundColor = color;
      strengthText.style.color = color;
      strengthText.textContent = text;
    });
    
    // Form validation
    document.getElementById('registration-form').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const terms = document.getElementById('terms').checked;
      
      if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long.');
        return;
      }
      
      if (!terms) {
        e.preventDefault();
        alert('You must accept the Terms and Conditions.');
        return;
      }
    });
  </script>
</body>
</html>