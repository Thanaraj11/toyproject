<?php
// Include database connection
require_once '../../../databse/db_connection.php';

/**
 * Get product details by ID
 */
function getProductDetails($product_id) {
    global $conn;
    
    $product_id = mysqli_real_escape_string($conn, $product_id);
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = '$product_id' AND p.status = 'active'";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Get product images by product ID
 */
function getProductImages($product_id) {
    global $conn;
    
    $product_id = mysqli_real_escape_string($conn, $product_id);
    
    $sql = "SELECT * FROM product_images 
            WHERE product_id = '$product_id' 
            ORDER BY is_primary DESC, sort_order ASC";
    
    $result = mysqli_query($conn, $sql);
    $images = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $images[] = $row;
        }
    }
    
    return $images;
}

/**
 * Get product reviews by product ID
 */
function getProductReviews($product_id) {
    global $conn;
    
    $product_id = mysqli_real_escape_string($conn, $product_id);
    
    $sql = "SELECT pr.*, c.first_name, c.last_name 
            FROM product_reviews pr 
            LEFT JOIN customers c ON pr.customer_id = c.id 
            WHERE pr.product_id = '$product_id' AND pr.status = 'approved' 
            ORDER BY pr.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $reviews = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reviews[] = $row;
        }
    }
    
    return $reviews;
}

/**
 * Add a new review for a product
 */
function addProductReview($product_id, $customer_id, $rating, $comment, $title = '') {
    global $conn;
    
    $product_id = mysqli_real_escape_string($conn, $product_id);
    $customer_id = mysqli_real_escape_string($conn, $customer_id);
    $rating = mysqli_real_escape_string($conn, $rating);
    $comment = mysqli_real_escape_string($conn, $comment);
    $title = mysqli_real_escape_string($conn, $title);
    
    $sql = "INSERT INTO product_reviews (product_id, customer_id, rating, title, comment, status) 
            VALUES ('$product_id', '$customer_id', '$rating', '$title', '$comment', 'pending')";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    }
    
    return false;
}

/**
 * Format rating stars
 */
function getRatingStars($rating) {
    $stars = '';
    $fullStars = floor($rating);
    $emptyStars = 5 - $fullStars;
    
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '★';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '☆';
    }
    
    return $stars;
}
?>