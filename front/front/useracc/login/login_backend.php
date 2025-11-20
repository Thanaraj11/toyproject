<?php
// Include database connection
include '../../../databse/db_connection.php';

session_start();
// login.php
// if (isset($_SESSION['user_id'])) {
//     header("Location: ../../main/index/inde.php");
//     exit;
// }

function validateLogin($email, $password) {
    global $conn;
    
    // Escape user input for security
    $email = mysqli_real_escape_string($conn, $email);
    
    // Query using email (since we don't have username in customers table)
    $sql = "SELECT id, first_name, last_name, email, password_hash, status 
            FROM customers 
            WHERE email = '$email' AND status = 'active'";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        
        // Verify password (using password_verify for hashed passwords)
        if (password_verify($password, $customer['password_hash'])) {
            
            // Update last login timestamp
            $update_sql = "UPDATE customers SET last_login = NOW() WHERE id = " . $customer['id'];
            mysqli_query($conn, $update_sql);
            
            return [
                'success' => true,
                'user_id' => $customer['id'],
                'first_name' => $customer['first_name'],
                'last_name' => $customer['last_name'],
                'email' => $customer['email']
            ];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid email or password'];
}

function setUserSession($user_data) {
    $_SESSION['user_id'] = $user_data['user_id'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['first_name'] = $user_data['first_name'];
    $_SESSION['last_name'] = $user_data['last_name'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    $_SESSION['user_type'] = 'customer'; // Add user type for clarity
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function redirectIfLoggedIn($redirect_url = '../dashboard/dashboard.php') {
    if (isLoggedIn()) {
        header("Location: $redirect_url");
        exit();
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        $login_result = validateLogin($email, $password);
        
        if ($login_result['success']) {
            setUserSession($login_result);
            
            // Redirect to dashboard or intended page
            $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '../dashboard/dashboard.php';
            unset($_SESSION['redirect_url']);
            
            header("Location: $redirect_url");
            exit();
        } else {
            $error_message = $login_result['message'];
        }
    }
}

// Redirect if already logged in
redirectIfLoggedIn();
?>