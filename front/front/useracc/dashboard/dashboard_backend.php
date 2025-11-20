<?php
// Include database connection
include '../../../databse/db_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: ../login/login.php");
    exit();
}

// Get user information
function getUserInfo($user_id) {
    global $conn;
    
    $sql = "SELECT id, first_name, last_name, email, status, avatar_color, email_verified, last_login, created_at 
            FROM customers 
            WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Get user statistics
function getUserStats($user_id) {
    global $conn;
    
    $stats = [
        'total_orders' => 0,
        'pending_orders' => 0,
        'wishlist_items' => 0,
        'saved_addresses' => 0
    ];
    
    // Get total orders count
    $orders_sql = "SELECT COUNT(*) as total FROM orders WHERE customer_id = $user_id";
    $orders_result = mysqli_query($conn, $orders_sql);
    if ($orders_result && mysqli_num_rows($orders_result) > 0) {
        $row = mysqli_fetch_assoc($orders_result);
        $stats['total_orders'] = $row['total'];
    }
    
    // Get pending orders count
    $pending_sql = "SELECT COUNT(*) as total FROM orders WHERE customer_id = $user_id AND status = 'pending'";
    $pending_result = mysqli_query($conn, $pending_sql);
    if ($pending_result && mysqli_num_rows($pending_result) > 0) {
        $row = mysqli_fetch_assoc($pending_result);
        $stats['pending_orders'] = $row['total'];
    }
    
    // Get wishlist items count
    $wishlist_sql = "SELECT COUNT(*) as total FROM wishlist WHERE customer_id = $user_id";
    $wishlist_result = mysqli_query($conn, $wishlist_sql);
    if ($wishlist_result && mysqli_num_rows($wishlist_result) > 0) {
        $row = mysqli_fetch_assoc($wishlist_result);
        $stats['wishlist_items'] = $row['total'];
    }
    
    // Get saved addresses count
    $address_sql = "SELECT COUNT(*) as total FROM addresses WHERE customer_id = $user_id";
    $address_result = mysqli_query($conn, $address_sql);
    if ($address_result && mysqli_num_rows($address_result) > 0) {
        $row = mysqli_fetch_assoc($address_result);
        $stats['saved_addresses'] = $row['total'];
    }
    
    return $stats;
}

// Get recent orders
function getRecentOrders($user_id, $limit = 3) {
    global $conn;
    $orders = [];
    
    $sql = "SELECT id, order_number, total_amount, status, payment_status, created_at 
            FROM orders 
            WHERE customer_id = $user_id 
            ORDER BY created_at DESC 
            LIMIT $limit";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
    
    return $orders;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login/login.php");
    exit();
}

// Get current user data
$user_id = $_SESSION['user_id'];
$user_info = getUserInfo($user_id);
$user_stats = getUserStats($user_id);
$recent_orders = getRecentOrders($user_id);

// Check if user info was retrieved successfully
if (!$user_info) {
    die("Error: Unable to load user information. Please try logging in again.");
}
?>