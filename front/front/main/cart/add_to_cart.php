<?php
// Include cart functions
include 'cart_backend.php';
include '../index/index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if (addToCart($product_id, $quantity)) {
        // Redirect back to product page or cart
        $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : 'cart.php';
        header('Location: ' . $redirect_url);
        exit();
    } else {
        echo "Error adding product to cart.";
    }
} else {
    echo "Invalid request.";
}
?>