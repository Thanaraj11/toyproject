<?php
// Include database connection
require_once '../../../../databse/db_connection.php';

function getCartItemsWithDetails() {
    global $conn;
    $cart_items = [];
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $ids_string = implode(',', $product_ids);
        
        $sql = "SELECT p.id as product_id, p.name as product_name, p.price, p.image_url 
                FROM products p 
                WHERE p.id IN ($ids_string) AND p.status = 'active'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($product = mysqli_fetch_assoc($result)) {
                $product_id = $product['product_id'];
                $cart_items[] = [
                    'product_id' => $product_id,
                    'name' => $product['product_name'],
                    'price' => floatval($product['price']),
                    'image_url' => $product['image_url'],
                    'quantity' => $_SESSION['cart'][$product_id],
                    'total' => floatval($product['price']) * $_SESSION['cart'][$product_id]
                ];
            }
        }
    }
    
    return $cart_items;
}

function calculateOrderSummary() {
    $cart_items = getCartItemsWithDetails();
    $subtotal = 0;
    
    foreach ($cart_items as $item) {
        $subtotal += $item['total'];
    }
    
    // Shipping cost calculation
    $shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : 'standard';
    $shipping_cost = calculateShippingCost($shipping_method);
    
    $order_total = $subtotal + $shipping_cost;
    
    return [
        'items' => $cart_items,
        'subtotal' => $subtotal,
        'shipping_method' => $shipping_method,
        'shipping_cost' => $shipping_cost,
        'order_total' => $order_total
    ];
}

function calculateShippingCost($method) {
    $shipping_costs = [
        'standard' => 5.00,
        'express' => 12.00,
        'overnight' => 25.00
    ];
    
    return isset($shipping_costs[$method]) ? $shipping_costs[$method] : 5.00;
}

function getShippingMethodName($method) {
    $method_names = [
        'standard' => 'Standard Shipping ($5.00)',
        'express' => 'Express Shipping ($12.00)',
        'overnight' => 'Overnight Shipping ($25.00)'
    ];
    
    return isset($method_names[$method]) ? $method_names[$method] : 'Standard Shipping ($5.00)';
}

// Initialize shipping method if not set
if (!isset($_SESSION['shipping_method'])) {
    $_SESSION['shipping_method'] = 'standard';
}
?>