<?php
// Include database connection
require_once '../../../../databse/db_connection.php';

function validateShippingData($shipping_data) {
    // Validate required fields
    $required_fields = ['fullname', 'email', 'phone', 'address1', 'city', 'postal', 'country', 'shipping'];
    foreach ($required_fields as $field) {
        if (empty($shipping_data[$field])) {
            return false;
        }
    }
    
    // Validate email format
    if (!filter_var($shipping_data['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Validate phone (basic check for numbers)
    if (!preg_match('/^[\d\s\-\+\(\)]+$/', $shipping_data['phone'])) {
        return false;
    }
    
    return true;
}

function saveShippingInfo($shipping_data) {
    // Store shipping information in session
    $_SESSION['shipping_info'] = [
        'fullname' => $shipping_data['fullname'],
        'email' => $shipping_data['email'],
        'phone' => $shipping_data['phone'],
        'address1' => $shipping_data['address1'],
        'address2' => $shipping_data['address2'] ?? '',
        'city' => $shipping_data['city'],
        'postal' => $shipping_data['postal'],
        'country' => $shipping_data['country']
    ];
    
    // Store shipping method
    $_SESSION['shipping_method'] = $shipping_data['shipping'];
    
    return true;
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

// Handle shipping form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_shipping'])) {
    $shipping_data = [
        'fullname' => mysqli_real_escape_string($conn, $_POST['fullname']),
        'email' => mysqli_real_escape_string($conn, $_POST['email']),
        'phone' => mysqli_real_escape_string($conn, $_POST['phone']),
        'address1' => mysqli_real_escape_string($conn, $_POST['address1']),
        'address2' => mysqli_real_escape_string($conn, $_POST['address2'] ?? ''),
        'city' => mysqli_real_escape_string($conn, $_POST['city']),
        'postal' => mysqli_real_escape_string($conn, $_POST['postal']),
        'country' => mysqli_real_escape_string($conn, $_POST['country']),
        'shipping' => mysqli_real_escape_string($conn, $_POST['shipping'])
    ];
    
    if (validateShippingData($shipping_data)) {
        if (saveShippingInfo($shipping_data)) {
            // Redirect to payment page
            header("Location: ../payment/payment.php");
            exit();
        } else {
            $_SESSION['shipping_error'] = "Error saving shipping information. Please try again.";
            header("Location: shipping.php");
            exit();
        }
    } else {
        $_SESSION['shipping_error'] = "Please fill in all required fields correctly.";
        header("Location: shipping.php");
        exit();
    }
}
?>