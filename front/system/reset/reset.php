<?php
// Include the handler file for functions
require_once 'reset_backend.php';

// Check for session messages
$request_message = '';
$request_message_type = '';
if (isset($_SESSION['reset_message']) && isset($_SESSION['message_type'])) {
    $request_message = $_SESSION['reset_message'];
    $request_message_type = $_SESSION['message_type'];
    
    // Clear the session messages
    unset($_SESSION['reset_message']);
    unset($_SESSION['message_type']);
}

// Check if token is provided in URL (for reset form)
$token = $_GET['token'] ?? '';
$show_reset_form = !empty($token);

// Validate token if showing reset form
if ($show_reset_form) {
    $valid_token = validateResetToken($token);
    if (!$valid_token) {
        $show_reset_form = false;
        $error_message = "Invalid or expired reset token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="reset.css">
</head>
<body>
    <div class="form-toggle">
        <button class="toggle-btn <?php echo !$show_reset_form ? 'active' : ''; ?>" id="request-toggle">Request Reset</button>
        <button class="toggle-btn <?php echo $show_reset_form ? 'active' : ''; ?>" id="reset-toggle">Reset Password</button>
    </div>
    
    <div class="container">
        <!-- Request Reset Form -->
        <div class="form-container <?php echo !$show_reset_form ? 'active' : ''; ?>" id="request-form">
            <div class="form-header">
                <i class="fas fa-key"></i>
                <h2>Forgot Your Password?</h2>
                <p>Enter your email address and we'll send you a reset link</p>
            </div>
            
            <div class="form-body">
                <div class="message info" id="request-info">
                    <i class="fas fa-info-circle"></i> Enter your account email address
                </div>
                
                <?php if ($request_message && $request_message_type == 'error'): ?>
                <div class="message error" id="request-error">
                    <i class="fas fa-exclamation-circle"></i> <span id="error-text"><?php echo $request_message; ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($request_message && $request_message_type == 'success'): ?>
                <div class="message success" id="request-success">
                    <i class="fas fa-check-circle"></i> <?php echo $request_message; ?>
                </div>
                <?php endif; ?>
                
                <form id="request-reset-form" action="reset_backend.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Send Reset Link</button>
                </form>
                
                <div class="back-to-login">
                    <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </div>
            </div>
        </div>
        
        <!-- Reset Password Form -->
        <div class="form-container <?php echo $show_reset_form ? 'active' : ''; ?>" id="reset-form">
            <div class="form-header">
                <i class="fas fa-lock"></i>
                <h2>Create New Password</h2>
                <p>Your new password must be different from previous passwords</p>
            </div>
            
            <div class="form-body">
                <?php if (isset($error_message)): ?>
                <div class="message error" id="reset-error">
                    <i class="fas fa-exclamation-circle"></i> <span id="reset-error-text"><?php echo $error_message; ?></span>
                </div>
                <?php else: ?>
                <div class="message info" id="reset-info">
                    <i class="fas fa-info-circle"></i> Enter your new password and confirm it
                </div>
                <?php endif; ?>
                
                <?php if ($show_reset_form): ?>
                <form id="reset-password-form" action="reset_backend.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="new-password">New Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="new-password" name="new_password" class="form-control" placeholder="Enter new password" required>
                            <span class="password-toggle" id="toggle-new-password">
                                <i class="far fa-eye"></i>
                            </span>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-fill" id="password-strength"></div>
                        </div>
                        <div class="strength-labels">
                            <span>Weak</span>
                            <span>Medium</span>
                            <span>Strong</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm-password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                            <span class="password-toggle" id="toggle-confirm-password">
                                <i class="far fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Reset Password</button>
                </form>
                <?php endif; ?>
                
                <div class="back-to-login">
                    <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2023 Your Company. All rights reserved.</p>
        <p>
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a> | 
            <a href="#">Help Center</a>
        </p>
    </footer>
    
    <script src="reset.js"></script>
</body>
</html>