<?php
// Include database connection
include '../../databse/db_connection.php';

// Function to get all orders with filters
function getAllOrders($conn, $filters = array()) {
    $query = "SELECT o.*, 
                     c.first_name, 
                     c.last_name, 
                     c.email,
                     COUNT(oi.id) as item_count
              FROM orders o 
              JOIN customers c ON o.customer_id = c.id 
              LEFT JOIN order_items oi ON o.id = oi.order_id";
    
    $where_conditions = array();
    $params = array();
    
    // Apply filters
    if (!empty($filters['status']) && $filters['status'] != 'all') {
        $where_conditions[] = "o.status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['date_range'])) {
        switch ($filters['date_range']) {
            case 'today':
                $where_conditions[] = "DATE(o.created_at) = CURDATE()";
                break;
            case 'week':
                $where_conditions[] = "YEARWEEK(o.created_at) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $where_conditions[] = "YEAR(o.created_at) = YEAR(CURDATE()) AND MONTH(o.created_at) = MONTH(CURDATE())";
                break;
        }
    }
    
    if (!empty($filters['search'])) {
        $where_conditions[] = "(o.order_number LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ?)";
        $search_term = "%" . $filters['search'] . "%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    $query .= " GROUP BY o.id ORDER BY o.created_at DESC";
    
    // Prepare and execute statement
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $query);
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, $query);
    }
    
    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    return $orders;
}

// Function to get order by ID
function getOrderById($conn, $id) {
    $query = "SELECT o.*, 
                     c.first_name, 
                     c.last_name, 
                     c.email,
                     c.phone,
                     sa.address_line1 as shipping_address1,
                     sa.address_line2 as shipping_address2,
                     sa.city as shipping_city,
                     sa.state as shipping_state,
                     sa.zip_code as shipping_zip,
                     sa.country as shipping_country
              FROM orders o 
              JOIN customers c ON o.customer_id = c.id 
              LEFT JOIN addresses sa ON o.shipping_address_id = sa.id 
              WHERE o.id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to get order items
function getOrderItems($conn, $order_id) {
    $query = "SELECT oi.*, p.image_url 
              FROM order_items oi 
              LEFT JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $items = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    return $items;
}

// Function to get order status history
function getOrderStatusHistory($conn, $order_id) {
    $query = "SELECT * FROM order_status_history 
              WHERE order_id = ? 
              ORDER BY created_at ASC";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $history = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $history[] = $row;
    }
    
    return $history;
}

// Function to update order status
function updateOrderStatus($conn, $order_id, $new_status, $notes = '') {
    // Get current order
    $order = getOrderById($conn, $order_id);
    if (!$order) {
        return false;
    }
    
    // Update order status
    $update_query = "UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, 'si', $new_status, $order_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        return false;
    }
    
    // Record status change in history
    $history_query = "INSERT INTO order_status_history (order_id, status, notes) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $history_query);
    mysqli_stmt_bind_param($stmt, 'iss', $order_id, $new_status, $notes);
    
    return mysqli_stmt_execute($stmt);
}

// Function to get order statistics
function getOrderStats($conn) {
    $stats = array();
    
    // Total orders
    $query = "SELECT COUNT(*) as total_orders FROM orders";
    $result = mysqli_query($conn, $query);
    $stats['total_orders'] = mysqli_fetch_assoc($result)['total_orders'];
    
    // Pending orders
    $query = "SELECT COUNT(*) as pending_orders FROM orders WHERE status = 'pending'";
    $result = mysqli_query($conn, $query);
    $stats['pending_orders'] = mysqli_fetch_assoc($result)['pending_orders'];
    
    // Processing orders
    $query = "SELECT COUNT(*) as processing_orders FROM orders WHERE status = 'processing'";
    $result = mysqli_query($conn, $query);
    $stats['processing_orders'] = mysqli_fetch_assoc($result)['processing_orders'];
    
    // Total revenue
    $query = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE status IN ('processing', 'shipped', 'delivered')";
    $result = mysqli_query($conn, $query);
    $stats['total_revenue'] = mysqli_fetch_assoc($result)['total_revenue'] ?? 0;
    
    return $stats;
}

// Function to get orders count by status
function getOrdersCountByStatus($conn) {
    $query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
    $result = mysqli_query($conn, $query);
    
    $counts = array(
        'pending' => 0,
        'processing' => 0,
        'shipped' => 0,
        'delivered' => 0,
        'cancelled' => 0
    );
    
    while ($row = mysqli_fetch_assoc($result)) {
        $counts[$row['status']] = $row['count'];
    }
    
    return $counts;
}

// Function to format order status for display
function formatOrderStatus($status) {
    $status_map = array(
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    );
    
    return $status_map[$status] ?? $status;
}

// Function to get status class for styling
function getStatusClass($status) {
    return 'status-' . $status;
}

// Function to generate timeline status
function getTimelineStatus($status_history, $current_status) {
    $timeline = array(
        'pending' => array('active' => false, 'completed' => false),
        'processing' => array('active' => false, 'completed' => false),
        'shipped' => array('active' => false, 'completed' => false),
        'delivered' => array('active' => false, 'completed' => false)
    );
    
    $status_flow = array('pending', 'processing', 'shipped', 'delivered');
    
    foreach ($status_history as $history) {
        if (in_array($history['status'], $status_flow)) {
            $timeline[$history['status']]['completed'] = true;
        }
    }
    
    // Set current active status
    if (in_array($current_status, $status_flow)) {
        $timeline[$current_status]['active'] = true;
    }
    
    return $timeline;
}
?>