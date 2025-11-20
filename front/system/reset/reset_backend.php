<?php
session_start();
require_once '../../databse/db_connection.php';

// Function to validate reset token
function validateResetToken($token) {
    global $conn;
    
    // Clean the token
    $token = $conn->real_escape_string($token);
    
    // Check if token exists and is not expired or used
    $sql = "SELECT * FROM password_resets WHERE token = '$token' AND used = 0 AND expires_at > NOW()";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return true;
    }
    
    return false;
}

// Function to send password reset email
function sendResetEmail($email, $token) {
    // In a real application, you would send an actual email
    // For this example, we'll just simulate the process
    
    $reset_link = "http://yourdomain.com/reset.php?token=" . $token;
    
    // Simulate email sending (replace with actual email code)
    error_log("Password reset link for $email: $reset_link");
    
    return true;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Request reset form submission
    if (isset($_POST['email']) && !isset($_POST['token'])) {
        $email = $conn->real_escape_string($_POST['email']);
        
        // Check if email exists in customers table
        $sql = "SELECT id, email FROM customers WHERE email = '$email' AND status = 'active'";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            // Generate unique token
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing reset tokens for this email
            $conn->query("DELETE FROM password_resets WHERE email = '$email'");
            
            // Insert new reset token
            $insert_sql = "INSERT INTO password_resets (email, token, expires_at) VALUES ('$email', '$token', '$expires_at')";
            
            if ($conn->query($insert_sql)) {
                // Send reset email
                if (sendResetEmail($email, $token)) {
                    $_SESSION['reset_message'] = "Password reset email sent successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['reset_message'] = "Error sending reset email. Please try again.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                $_SESSION['reset_message'] = "Error generating reset token. Please try again.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['reset_message'] = "No account found with that email address.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: reset.php");
        exit();
    }
    
    // Reset password form submission
    if (isset($_POST['token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        $token = $conn->real_escape_string($_POST['token']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate passwords match
        if ($new_password !== $confirm_password) {
            $_SESSION['reset_message'] = "Passwords do not match.";
            $_SESSION['message_type'] = "error";
            header("Location: reset.php?token=" . $token);
            exit();
        }
        
        // Validate password strength
        if (strlen($new_password) < 8) {
            $_SESSION['reset_message'] = "Password must be at least 8 characters long.";
            $_SESSION['message_type'] = "error";
            header("Location: reset.php?token=" . $token);
            exit();
        }
        
        // Validate token
        if (!validateResetToken($token)) {
            $_SESSION['reset_message'] = "Invalid or expired reset token.";
            $_SESSION['message_type'] = "error";
            header("Location: reset.php");
            exit();
        }
        
        // Get email from token
        $token_sql = "SELECT email FROM password_resets WHERE token = '$token'";
        $token_result = $conn->query($token_sql);
        
        if ($token_result && $token_result->num_rows > 0) {
            $row = $token_result->fetch_assoc();
            $email = $row['email'];
            
            // Hash new password
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update customer password
            $update_sql = "UPDATE customers SET password_hash = '$password_hash', updated_at = NOW() WHERE email = '$email'";
            
            if ($conn->query($update_sql)) {
                // Mark token as used
                $conn->query("UPDATE password_resets SET used = 1 WHERE token = '$token'");
                
                $_SESSION['reset_message'] = "Password reset successfully! You can now login with your new password.";
                $_SESSION['message_type'] = "success";
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['reset_message'] = "Error updating password. Please try again.";
                $_SESSION['message_type'] = "error";
                header("Location: reset.php?token=" . $token);
                exit();
            }
        } else {
            $_SESSION['reset_message'] = "Invalid reset token.";
            $_SESSION['message_type'] = "error";
            header("Location: reset.php");
            exit();
        }
    }
}
?>