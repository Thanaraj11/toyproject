<?php
// Include database connection
require_once '../../../databse/db_connection.php';

/**
 * Get order details by order ID
 */
function getOrderDetails($order_id) {
    global $conn;
    
    $order_id = mysqli_real_escape_string($conn, $order_id);
    
    $sql = "SELECT o.*, 
                   c.name as customer_name, 
                   c.email as customer_email,
                   sa.street as shipping_street,
                   sa.city as shipping_city,
                   sa.state as shipping_state,
                   sa.zip_code as shipping_zip,
                   sa.country as shipping_country,
                   ba.street as billing_street,
                   ba.city as billing_city,
                   ba.state as billing_state,
                   ba.zip_code as billing_zip,
                   ba.country as billing_country
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            LEFT JOIN addresses sa ON o.shipping_address_id = sa.id
            LEFT JOIN addresses ba ON o.billing_address_id = ba.id
            WHERE o.id = '$order_id'";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Get order items by order ID
 */
function getOrderItems($order_id) {
    global $conn;
    
    $order_id = mysqli_real_escape_string($conn, $order_id);
    
    $sql = "SELECT oi.*, p.image_url
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = '$order_id'";
    
    $result = mysqli_query($conn, $sql);
    $items = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
    }
    
    return $items;
}

/**
 * Get shipping method cost and delivery days
 */
function getShippingMethodDetails($shipping_method_name) {
    global $conn;
    
    $shipping_method_name = mysqli_real_escape_string($conn, $shipping_method_name);
    
    $sql = "SELECT * FROM shipping_methods 
            WHERE name = '$shipping_method_name' AND status = 'active'";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Calculate estimated delivery date based on shipping method
 */
function getEstimatedDeliveryDate($order_date, $shipping_method_name) {
    $shipping_method = getShippingMethodDetails($shipping_method_name);
    
    if (!$shipping_method) {
        // Default to standard shipping if method not found
        $delivery_days = 5;
    } else {
        $delivery_days = $shipping_method['delivery_days'];
    }
    
    $order_timestamp = strtotime($order_date);
    
    // Add business days (excluding weekends)
    $delivery_date = $order_timestamp;
    $days_added = 0;
    
    while ($days_added < $delivery_days) {
        $delivery_date = strtotime('+1 day', $delivery_date);
        // Check if it's a weekend (Saturday = 6, Sunday = 0)
        $day_of_week = date('w', $delivery_date);
        if ($day_of_week != 0 && $day_of_week != 6) {
            $days_added++;
        }
    }
    
    return date('F j, Y', $delivery_date);
}

/**
 * Create a new order
 */
function createOrder($user_id, $items, $shipping_method, $payment_method, $shipping_address_id, $billing_address_id) {
    global $conn;
    
    $customer_id = mysqli_real_escape_string($conn, $user_id);
    $shipping_method = mysqli_real_escape_string($conn, $shipping_method);
    $payment_method = mysqli_real_escape_string($conn, $payment_method);
    $shipping_address_id = mysqli_real_escape_string($conn, $shipping_address_id);
    $billing_address_id = mysqli_real_escape_string($conn, $billing_address_id);
    
    // Calculate totals
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['quantity'] * $item['price'];
    }
    
    $shipping_cost = getShippingMethodDetails($shipping_method)['cost'] ?? 0;
    $tax_amount = calculateTaxAmount($subtotal, $shipping_address_id);
    $total_amount = $subtotal + $shipping_cost + $tax_amount;
    
    // Generate order number
    $order_number = 'ORD' . date('YmdHis') . mt_rand(100, 999);
    
    $sql = "INSERT INTO orders (
                order_number, 
                customer_id, 
                total_amount, 
                subtotal_amount, 
                shipping_amount, 
                tax_amount,
                payment_method,
                shipping_address_id,
                billing_address_id,
                status
            ) VALUES (
                '$order_number',
                '$customer_id',
                '$total_amount',
                '$subtotal',
                '$shipping_cost',
                '$tax_amount',
                '$payment_method',
                '$shipping_address_id',
                '$billing_address_id',
                'confirmed'
            )";
    
    if (mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // Add order items
        foreach ($items as $item) {
            addOrderItem($order_id, $item);
        }
        
        return $order_id;
    }
    
    return false;
}

/**
 * Add order item
 */
function addOrderItem($order_id, $item) {
    global $conn;
    
    $order_id = mysqli_real_escape_string($conn, $order_id);
    $product_id = mysqli_real_escape_string($conn, $item['product_id']);
    $product_name = mysqli_real_escape_string($conn, $item['product_name']);
    $product_sku = mysqli_real_escape_string($conn, $item['product_sku']);
    $product_price = mysqli_real_escape_string($conn, $item['price']);
    $quantity = mysqli_real_escape_string($conn, $item['quantity']);
    $total_price = $quantity * $product_price;
    
    $sql = "INSERT INTO order_items (
                order_id, 
                product_id, 
                product_name, 
                product_sku, 
                product_price, 
                quantity, 
                total_price
            ) VALUES (
                '$order_id',
                '$product_id',
                '$product_name',
                '$product_sku',
                '$product_price',
                '$quantity',
                '$total_price'
            )";
    
    return mysqli_query($conn, $sql);
}

/**
 * Calculate tax amount (simplified - you might need more complex logic)
 */
function calculateTaxAmount($subtotal, $address_id) {
    // Simplified tax calculation - 8% tax rate
    return $subtotal * 0.08;
}

/**
 * Update product stock after order
 */
function updateProductStock($product_id, $quantity_sold) {
    global $conn;
    
    $product_id = mysqli_real_escape_string($conn, $product_id);
    $quantity_sold = mysqli_real_escape_string($conn, $quantity_sold);
    
    $sql = "UPDATE products SET current_stock = current_stock - $quantity_sold 
            WHERE id = '$product_id' AND current_stock >= $quantity_sold";
    
    return mysqli_query($conn, $sql);
}

/**
 * Format address for display
 */
function formatAddress($street, $city, $state, $zip_code, $country) {
    $address = htmlspecialchars($street);
    if ($city) $address .= '<br>' . htmlspecialchars($city);
    if ($state) $address .= ', ' . htmlspecialchars($state);
    if ($zip_code) $address .= ' ' . htmlspecialchars($zip_code);
    if ($country) $address .= '<br>' . htmlspecialchars($country);
    return $address;
}
?>