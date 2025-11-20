<?php
// Include database connection
include '../../../databse/db_connection.php';

session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

//Redirect to login if not logged in
// if (!isLoggedIn()) {
//     header("Location: ../login/login.php");
//     exit();
// }

// Get user orders
function getUserOrders($user_id) {
    global $conn;
    $orders = [];
    
    $sql = "SELECT o.id as order_id, o.order_number, o.total_amount as order_total, 
                   o.status, o.created_at as order_date, o.payment_status,
                   COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.customer_id = $user_id
            GROUP BY o.id
            ORDER BY o.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    }
    
    return $orders;
}

// Get order details
function getOrderDetails($order_id, $user_id) {
    global $conn;
    
    // Verify order belongs to user
    $verify_sql = "SELECT id FROM orders WHERE id = $order_id AND customer_id = $user_id";
    $verify_result = mysqli_query($conn, $verify_sql);
    
    if (!$verify_result || mysqli_num_rows($verify_result) == 0) {
        return null;
    }
    
    $order_details = [];
    
    // Get order header
    $order_sql = "SELECT o.*, 
                         sa.full_name as shipping_name, sa.address_line1 as shipping_address1, 
                         sa.address_line2 as shipping_address2, sa.city as shipping_city,
                         sa.state as shipping_state, sa.zip_code as shipping_zip, sa.country as shipping_country,
                         ba.full_name as billing_name, ba.address_line1 as billing_address1,
                         ba.address_line2 as billing_address2, ba.city as billing_city,
                         ba.state as billing_state, ba.zip_code as billing_zip, ba.country as billing_country
                  FROM orders o
                  LEFT JOIN addresses sa ON o.shipping_address_id = sa.id
                  LEFT JOIN addresses ba ON o.billing_address_id = ba.id
                  WHERE o.id = $order_id";
    $order_result = mysqli_query($conn, $order_sql);
    $order_details['header'] = mysqli_fetch_assoc($order_result);
    
    // Get order items
    $items_sql = "SELECT oi.*, p.name as product_name, p.slug as product_slug
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = $order_id";
    $items_result = mysqli_query($conn, $items_sql);
    $order_details['items'] = [];
    
    while ($row = mysqli_fetch_assoc($items_result)) {
        $order_details['items'][] = $row;
    }
    
    return $order_details;
}

// Get order status class for styling
function getStatusClass($status) {
    $status_classes = [
        'pending' => 'status-pending',
        'confirmed' => 'status-confirmed',
        'processing' => 'status-processing',
        'shipped' => 'status-shipped',
        'delivered' => 'status-delivered',
        'cancelled' => 'status-cancelled'
    ];
    
    return isset($status_classes[$status]) ? $status_classes[$status] : 'status-pending';
}

// Get payment status class for styling
function getPaymentStatusClass($status) {
    $status_classes = [
        'pending' => 'payment-pending',
        'paid' => 'payment-paid',
        'failed' => 'payment-failed',
        'refunded' => 'payment-refunded'
    ];
    
    return isset($status_classes[$status]) ? $status_classes[$status] : 'payment-pending';
}

// Get formatted order date
function formatOrderDate($date) {
    return date('M j, Y', strtotime($date));
}

// Format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Handle order actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $user_id = $_SESSION['user_id'];
    
    switch ($_POST['action']) {
        case 'cancel_order':
            if (isset($_POST['order_id'])) {
                $order_id = intval($_POST['order_id']);
                cancelOrder($order_id, $user_id);
            }
            break;
            
        case 'reorder':
            if (isset($_POST['order_id'])) {
                $order_id = intval($_POST['order_id']);
                reorderItems($order_id, $user_id);
            }
            break;
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

function cancelOrder($order_id, $user_id) {
    global $conn;
    
    // Only allow cancellation for pending orders
    $sql = "UPDATE orders SET status = 'cancelled' 
            WHERE id = $order_id AND customer_id = $user_id AND status = 'pending'";
    
    return mysqli_query($conn, $sql);
}

function reorderItems($order_id, $user_id) {
    global $conn;
    
    // Verify order belongs to user
    $verify_sql = "SELECT id FROM orders WHERE id = $order_id AND customer_id = $user_id";
    $verify_result = mysqli_query($conn, $verify_sql);
    
    if (!$verify_result || mysqli_num_rows($verify_result) == 0) {
        return false;
    }
    
    // Get order items
    $items_sql = "SELECT product_id, quantity FROM order_items WHERE order_id = $order_id";
    $items_result = mysqli_query($conn, $items_sql);
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add items to cart
    while ($row = mysqli_fetch_assoc($items_result)) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Get product details
            $product_sql = "SELECT name, price FROM products WHERE id = $product_id";
            $product_result = mysqli_query($conn, $product_sql);
            
            if ($product_result && mysqli_num_rows($product_result) > 0) {
                $product = mysqli_fetch_assoc($product_result);
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
        }
    }
    
    return true;
}

// Get current user orders
$user_id = $_SESSION['user_id'];
$user_orders = getUserOrders($user_id);
?>