<?php
// useracc/login/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: ../dashboard/dashboard.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="login.css">
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }
    
    header {
      background: transparent;
      padding: 2%;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin: 2%;

    }
    
    header h1 {
      margin: 0;
      color: #333;
    }
    
    nav a {
      color: #007bff;
      text-decoration: none;
    }
    
    nav a:hover {
      text-decoration: underline;
    }
    
    main {
      max-width: 400px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    
    #login-form {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    #login-form h2 {
      margin-top: 0;
      text-align: center;
      color: #333;
    }
    
    label {
      display: block;
      margin-bottom: 1rem;
      font-weight: 600;
    }
    
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
      margin-top: 0.25rem;
    }
    
    button[type="submit"] {
      width: 100%;
      background: #007bff;
      color: white;
      border: none;
      padding: 0.75rem;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      margin-bottom: 1rem;
    }
    
    button[type="submit"]:hover {
      background: #0056b3;
    }
    
    #forgot-password {
      display: block;
      text-align: center;
      color: #6c757d;
      text-decoration: none;
      margin-bottom: 1rem;
    }
    
    #forgot-password:hover {
      color: #0056b3;
    }
    
    .divider {
      text-align: center;
      margin: 1rem 0;
      color: #6c757d;
      position: relative;
    }
    
    .divider::before,
    .divider::after {
      content: "";
      position: absolute;
      top: 50%;
      width: 45%;
      height: 1px;
      background: #ddd;
    }
    
    .divider::before {
      left: 0;
    }
    
    .divider::after {
      right: 0;
    }
    
    .social-login {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    
    .social-btn {
      background: white;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 0.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .social-btn:hover {
      background: #f8f9fa;
    }
    
    .social-btn svg {
      width: 24px;
      height: 24px;
    }
    
    #create-account {
      display: block;
      text-align: center;
      color: #007bff;
      text-decoration: none;
    }
    
    #create-account:hover {
      text-decoration: underline;
    }
    
    .error-message {
      background: #f8d7da;
      color: #721c24;
      padding: 0.75rem;
      border: 1px solid #f5c6cb;
      border-radius: 4px;
      margin-bottom: 1rem;
    }
    
    .success-message {
      background: #d4edda;
      color: #155724;
      padding: 0.75rem;
      border: 1px solid #c3e6cb;
      border-radius: 4px;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <header>
    <h1>Customer Login</h1>
    <nav>
      <a href="../../main/index/index.php">Home</a>
    </nav>
  </header>

  <main>
    <?php
    // Include login functions
    include 'login_backend.php';
    
    // Display messages
    if (isset($_GET['registered']) && $_GET['registered'] == 1) {
        echo '<div class="success-message">Registration successful! Please login.</div>';
    }
    
    if (isset($_GET['logout']) && $_GET['logout'] == 1) {
        echo '<div class="success-message">You have been logged out successfully.</div>';
    }
    
    if (isset($_GET['expired']) && $_GET['expired'] == 1) {
        echo '<div class="error-message">Your session has expired. Please login again.</div>';
    }
    
    if (isset($_GET['access_denied']) && $_GET['access_denied'] == 1) {
        echo '<div class="error-message">Please login to access that page.</div>';
    }
    
    if (isset($error_message)) {
        echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
    }
    ?>
    
    <form id="login-form" method="POST">
      <input type="hidden" name="login" value="1">
      
      <h2>Welcome Back</h2>
      
      <label>
        Email:
        <input type="email" name="email" required placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      </label>
      
      <label>
        Password:
        <input type="password" name="password" required placeholder="Enter your password">
      </label>
      
      <button type="submit">Login</button>
      
      <a href="../../../system/reset/reset.php" id="forgot-password">Forgot password?</a>
      
      <div class="divider">or</div>
      
      <div class="social-login">
        <button type="button" class="social-btn">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4285F4">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
        </button>
        <button type="button" class="social-btn">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#1877F2">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
          </svg>
        </button>
        <button type="button" class="social-btn">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#1DA1F2">
            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
          </svg>
        </button>
      </div>
      
      <a href="../register/register.php" id="create-account">Create account</a>
    </form>
  </main>
</body>
</html>