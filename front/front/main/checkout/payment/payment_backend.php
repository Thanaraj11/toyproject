<?php
// Start session and include database connection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../../databse/db_connection.php';

function validatePaymentData($payment_data) {
    // Basic validation for credit card
    if ($payment_data['payment_method'] === 'credit_card') {
        $required_fields = ['card_number', 'card_name', 'expiry_date', 'cvv'];
        foreach ($required_fields as $field) {
            if (empty($payment_data[$field])) {
                return false;
            }
        }
        
        // Basic format validation only
        $card_number = str_replace(' ', '', $payment_data['card_number']);
        if (!preg_match('/^\d{16}$/', $card_number)) {
            return false;
        }
        
        if (!preg_match('/^\d{3,4}$/', $payment_data['cvv'])) {
            return false;
        }
        
        if (!preg_match('/^\d{2}\/\d{2}$/', $payment_data['expiry_date'])) {
            return false;
        }
    }
    
    return true;
}

function processPayment($payment_data, $order_total) {
    // Store payment information in session
    $_SESSION['payment_info'] = [
        'payment_method' => $payment_data['payment_method'],
        'amount' => $order_total,
        'transaction_id' => 'DEMO_' . uniqid(),
        'status' => 'completed',
        'card_last_four' => $payment_data['payment_method'] === 'credit_card' ? substr(str_replace(' ', '', $payment_data['card_number']), -4) : null
    ];
    
    return true;
}

function calculateOrderSummary() {
    // Recalculate order summary to ensure accuracy
    $cart_items = [];
    $subtotal = 0;
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $ids_string = implode(',', $product_ids);
        
        $sql = "SELECT p.id as product_id, p.name as product_name, p.price, p.sku 
                FROM products p 
                WHERE p.id IN ($ids_string) AND p.status = 'active'";
        $result = mysqli_query($GLOBALS['conn'], $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($product = mysqli_fetch_assoc($result)) {
                $product_id = $product['product_id'];
                $item_total = floatval($product['price']) * $_SESSION['cart'][$product_id];
                $subtotal += $item_total;
                
                $cart_items[] = [
                    'product_id' => $product_id,
                    'name' => $product['product_name'],
                    'sku' => $product['sku'],
                    'price' => floatval($product['price']),
                    'quantity' => $_SESSION['cart'][$product_id],
                    'total' => $item_total
                ];
            }
        }
    }
    
    // Get shipping cost
    $shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : 'standard';
    $shipping_cost = getShippingCost($shipping_method);
    
    $order_total = $subtotal + $shipping_cost;
    
    return [
        'items' => $cart_items,
        'subtotal' => $subtotal,
        'shipping_method' => $shipping_method,
        'shipping_cost' => $shipping_cost,
        'order_total' => $order_total
    ];
}

function getShippingCost($method) {
    $shipping_costs = [
        'standard' => 5.00,
        'express' => 15.00,
        'overnight' => 25.00
    ];
    
    return isset($shipping_costs[$method]) ? $shipping_costs[$method] : 5.00;
}

function getShippingMethodName($method) {
    $method_names = [
        'standard' => 'Standard Shipping',
        'express' => 'Express Shipping',
        'overnight' => 'Overnight Shipping'
    ];
    
    return isset($method_names[$method]) ? $method_names[$method] : 'Standard Shipping';
}

// Handle payment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_payment'])) {
    $payment_data = [
        'payment_method' => mysqli_real_escape_string($conn, $_POST['payment_method']),
        'card_number' => isset($_POST['card_number']) ? mysqli_real_escape_string($conn, $_POST['card_number']) : '',
        'card_name' => isset($_POST['card_name']) ? mysqli_real_escape_string($conn, $_POST['card_name']) : '',
        'expiry_date' => isset($_POST['expiry_date']) ? mysqli_real_escape_string($conn, $_POST['expiry_date']) : '',
        'cvv' => isset($_POST['cvv']) ? mysqli_real_escape_string($conn, $_POST['cvv']) : ''
    ];
    
    $order_summary = calculateOrderSummary();
    
    if (validatePaymentData($payment_data)) {
        if (processPayment($payment_data, $order_summary['order_total'])) {
            // Store order summary in session for confirmation page
            $_SESSION['order_summary'] = $order_summary;
            
            // Set order complete flag in session
            $_SESSION['order_complete'] = true;
            
            // Redirect to order confirmation page
            header("Location: ../../orderconfirm/orderconfirm.php");
            exit();
        } else {
            $_SESSION['payment_error'] = "Payment processing failed. Please try again.";
            header("Location: payment.php");
            exit();
        }
    } else {
        $_SESSION['payment_error'] = "Please fill in all required payment information correctly.";
        header("Location: payment.php");
        exit();
    }
}
?>