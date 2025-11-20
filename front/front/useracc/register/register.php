
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Create Your Account</title>
  <style>
    :root {
      --primary: #7c3aed;
      --primary-light: #8b5cf6;
      --primary-dark: #6d28d9;
      --secondary: #06d6a0;
      --dark: #1e293b;
      --light: #f8fafc;
      --gray: #64748b;
      --gray-light: #e2e8f0;
      --error: #ef4444;
      --success: #10b981;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: var(--light);
      color: var(--dark);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    .container {
      display: flex;
      min-height: 100vh;
    }
    
    .left-panel {
      flex: 1;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
      position: relative;
      overflow: hidden;
    }
    
    .left-panel::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.05)"/></svg>');
      background-size: cover;
    }
    
    .logo {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 2rem;
      font-size: 1.5rem;
      font-weight: 700;
      z-index: 1;
      position: relative;
    }
    
    .content {
      max-width: 500px;
      z-index: 1;
      position: relative;
    }
    
    .content h1 {
      font-size: 2.5rem;
      margin-bottom: 1.5rem;
      line-height: 1.2;
    }
    
    .content p {
      font-size: 1.125rem;
      opacity: 0.9;
      margin-bottom: 2rem;
    }
    
    .features {
      list-style: none;
      margin-top: 2rem;
    }
    
    .features li {
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .features i {
      background: rgba(255, 255, 255, 0.2);
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .right-panel {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem;
      background-color: white;
    }
    
    .form-container {
      max-width: 450px;
      width: 100%;
      margin: 0 auto;
    }
    
    .form-header {
      margin-bottom: 2rem;
    }
    
    .form-header h2 {
      font-size: 1.75rem;
      color: var(--dark);
      margin-bottom: 0.5rem;
    }
    
    .form-header p {
      color: var(--gray);
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-row {
      display: flex;
      gap: 1rem;
    }
    
    .form-row .form-group {
      flex: 1;
    }
    
    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--dark);
    }
    
    .input-wrapper {
      position: relative;
    }
    
    .input-wrapper i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray);
    }
    
    input {
      width: 100%;
      padding: 0.875rem 1rem 0.875rem 3rem;
      border: 1px solid var(--gray-light);
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s;
    }
    
    input:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }
    
    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--gray);
      cursor: pointer;
    }
    
    .terms-container {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      margin-bottom: 2rem;
      padding: 1rem;
      background-color: #f8fafc;
      border-radius: 8px;
    }
    
    .terms-container input {
      width: auto;
      margin-top: 0.25rem;
    }
    
    .terms-container label {
      font-size: 0.9rem;
      color: var(--gray);
      line-height: 1.4;
    }
    
    .terms-container a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }
    
    .terms-container a:hover {
      text-decoration: underline;
    }
    
    .btn-register {
      width: 100%;
      background: var(--primary);
      color: white;
      border: none;
      padding: 1rem;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
    }
    
    .btn-register:hover {
      background: var(--primary-dark);
    }
    
    .divider {
      display: flex;
      align-items: center;
      margin: 2rem 0;
      color: var(--gray);
    }
    
    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background-color: var(--gray-light);
    }
    
    .divider span {
      padding: 0 1rem;
      font-size: 0.875rem;
    }
    
    .social-login {
      display: flex;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .social-btn {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem;
      border: 1px solid var(--gray-light);
      border-radius: 8px;
      background: white;
      color: var(--dark);
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .social-btn:hover {
      border-color: var(--primary);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .login-link {
      text-align: center;
      color: var(--gray);
    }
    
    .login-link a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }
    
    .login-link a:hover {
      text-decoration: underline;
    }
    
    .alert {
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
    }
    
    .alert-error {
      background-color: #fef2f2;
      color: var(--error);
      border-left: 4px solid var(--error);
    }
    
    .alert-success {
      background-color: #f0fdf4;
      color: var(--success);
      border-left: 4px solid var(--success);
    }
    
    .alert i {
      margin-top: 0.125rem;
    }
    
    .password-strength {
      margin-top: 0.5rem;
    }
    
    .strength-bar {
      height: 4px;
      background-color: var(--gray-light);
      border-radius: 2px;
      margin-bottom: 0.5rem;
      overflow: hidden;
    }
    
    .strength-fill {
      height: 100%;
      width: 0%;
      border-radius: 2px;
      transition: all 0.3s;
    }
    
    .strength-text {
      font-size: 0.75rem;
      color: var(--gray);
    }
    
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }
      
      .left-panel {
        padding: 2rem;
      }
      
      .right-panel {
        padding: 2rem;
      }
      
      .form-row {
        flex-direction: column;
        gap: 0;
      }
      
      .social-login {
        flex-direction: column;
      }
    }
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