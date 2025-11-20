<?php
// Include backend functions
require_once 'orderconfirm_backend.php';

session_start();

// Check if cart exists and has items
// if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
//     header("Location: ../cart/cart.php");
//     exit();
// }

// Get checkout data
$customer_id = $_SESSION['customer_id'] ?? 1; // Get from session
$shipping_method = $_POST['shipping_method'] ?? 'Standard';
$payment_method = $_POST['payment_method'] ?? 'Credit Card';
$shipping_address_id = $_POST['shipping_address_id'] ?? 1;
$billing_address_id = $_POST['billing_address_id'] ?? 1;
$cart_items = $_SESSION['cart'];

// Prepare items with product details
$order_items = [];
foreach ($cart_items as $item) {
    $order_items[] = [
        'product_id' => $item['product_id'],
        'product_name' => $item['product_name'],
        'product_sku' => $item['product_sku'],
        'price' => $item['price'],
        'quantity' => $item['quantity']
    ];
}

// Create order
$order_id = createOrder($customer_id, $order_items, $shipping_method, $payment_method, $shipping_address_id, $billing_address_id);

if ($order_id) {
    // Update product stock for each item
    foreach ($order_items as $item) {
        updateProductStock($item['product_id'], $item['quantity']);
    }
    
    // Set session variables for confirmation page
    $_SESSION['order_complete'] = true;
    $_SESSION['last_order_id'] = $order_id;
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Redirect to confirmation page
    header("Location: orderconfirm.php");
    exit();
} else {
    // Handle error
    header("Location: ../cart/checkout.php?error=order_failed");
    exit();
}
?>