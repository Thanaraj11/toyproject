<?php
// Include database connection
include '../../../databse/db_connection.php'; // Fixed typo

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function validateRegistrationData($user_data) {
    $errors = [];
    
    // Validate required fields
    $required_fields = ['fullname', 'email', 'password', 'terms'];
    foreach ($required_fields as $field) {
        if (empty($user_data[$field])) {
            $errors[] = "All fields are required.";
            break;
        }
    }
    
    // Validate email format
    if (!filter_var($user_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    // Validate password strength
    if (strlen($user_data['password']) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    // Validate terms acceptance
    if ($user_data['terms'] !== 'on') {
        $errors[] = "You must accept the Terms and Conditions.";
    }
    
    return $errors;
}

function checkExistingCustomer($email) {
    global $conn;
    $errors = [];
    
    // Check if email already exists
    $email_sql = "SELECT id FROM customers WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
    $email_result = mysqli_query($conn, $email_sql);
    
    if ($email_result && mysqli_num_rows($email_result) > 0) {
        $errors[] = "Email address is already registered.";
    }
    
    return $errors;
}

function registerCustomer($user_data) {
    global $conn;
    
    // Extract user data
    $fullname = mysqli_real_escape_string($conn, $user_data['fullname']);
    $email = mysqli_real_escape_string($conn, $user_data['email']);
    $phone = isset($user_data['phone']) ? mysqli_real_escape_string($conn, $user_data['phone']) : '';
    $password = $user_data['password'];
    
    // Split full name into first and last name
    $name_parts = explode(' ', $fullname, 2);
    $first_name = mysqli_real_escape_string($conn, $name_parts[0]);
    $last_name = isset($name_parts[1]) ? mysqli_real_escape_string($conn, $name_parts[1]) : '';
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Generate random avatar color
    $colors = ['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0', '#560bad', '#b5179e'];
    $avatar_color = $colors[array_rand($colors)];
    
    // Insert customer into database - matching the table structure
    $sql = "INSERT INTO customers (first_name, last_name, email, phone, password_hash, avatar_color) 
            VALUES ('$first_name', '$last_name', '$email', '$phone', '$password_hash', '$avatar_color')";
    
    if (mysqli_query($conn, $sql)) {
        return [
            'success' => true,
            'customer_id' => mysqli_insert_id($conn),
            'message' => 'Registration successful!'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Registration failed: ' . mysqli_error($conn)
        ];
    }
}

function autoLoginCustomer($customer_id) {
    global $conn;
    
    // Get customer information for session
    $sql = "SELECT id, first_name, last_name, email, phone, avatar_color FROM customers WHERE id = $customer_id";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        
        // Set session variables
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['email'] = $customer['email'];
        $_SESSION['first_name'] = $customer['first_name'];
        $_SESSION['last_name'] = $customer['last_name'];
        $_SESSION['phone'] = $customer['phone'];
        $_SESSION['avatar_color'] = $customer['avatar_color'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Update last login
        $update_sql = "UPDATE customers SET last_login = CURRENT_TIMESTAMP WHERE id = $customer_id";
        mysqli_query($conn, $update_sql);
        
        return true;
    }
    
    return false;
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $user_data = [
        'fullname' => trim($_POST['fullname']),
        'email' => trim($_POST['email']),
        'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
        'password' => $_POST['password'],
        'terms' => isset($_POST['terms']) ? $_POST['terms'] : ''
    ];
    
    // Validate input data
    $validation_errors = validateRegistrationData($user_data);
    
    if (empty($validation_errors)) {
        // Check for existing customer
        $existing_errors = checkExistingCustomer($user_data['email']);
        
        if (empty($existing_errors)) {
            // Register customer
            $registration_result = registerCustomer($user_data);
            
            if ($registration_result['success']) {
                // Auto-login the customer
                autoLoginCustomer($registration_result['customer_id']);
                
                // Redirect to dashboard
                header("Location: ../dashboard/dashboard.php?registered=1");
                exit();
            } else {
                $error_message = $registration_result['message'];
            }
        } else {
            $error_message = implode('<br>', $existing_errors);
        }
    } else {
        $error_message = implode('<br>', $validation_errors);
    }
}
?>