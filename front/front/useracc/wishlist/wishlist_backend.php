<?php
// Include database connection
include '../../../databse/db_connection.php';

session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Add item to wishlist
function addToWishlist($user_id, $product_id) {
    global $conn;
    
    // Check if item already in wishlist
    $check_sql = "SELECT id FROM wishlist WHERE customer_id = $user_id AND product_id = $product_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        return ['success' => false, 'message' => 'Item already in wishlist'];
    }
    
    // Check if product exists and is active
    $product_sql = "SELECT id FROM products WHERE id = $product_id AND status = 'active'";
    $product_result = mysqli_query($conn, $product_sql);
    
    if (!$product_result || mysqli_num_rows($product_result) === 0) {
        return ['success' => false, 'message' => 'Product not available'];
    }
    
    // Add to wishlist
    $sql = "INSERT INTO wishlist (customer_id, product_id) VALUES ($user_id, $product_id)";
    
    if (mysqli_query($conn, $sql)) {
        return ['success' => true, 'message' => 'Item added to wishlist successfully!'];
    } else {
        return ['success' => false, 'message' => 'Error adding item to wishlist'];
    }
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] == 'add_to_wishlist') {
        if (!isLoggedIn()) {
            echo json_encode([
                'success' => false, 
                'message' => 'Please login to add items to wishlist',
                'redirect' => '../user/login/login.php'
            ]);
            exit();
        }
        
        if (isset($_POST['product_id'])) {
            $user_id = $_SESSION['user_id'];
            $product_id = intval($_POST['product_id']);
            
            $result = addToWishlist($user_id, $product_id);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        }
    }
}
?>