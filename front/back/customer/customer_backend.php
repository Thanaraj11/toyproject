<?php
// Include database connection
include '../../databse/db_connection.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// =============================================================================
// CUSTOMER CRUD OPERATIONS
// =============================================================================

/**
 * Add new customer
 */
function addCustomer($conn, $data) {
    $first_name = mysqli_real_escape_string($conn, $data['first_name']);
    $last_name = mysqli_real_escape_string($conn, $data['last_name']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $phone = mysqli_real_escape_string($conn, $data['phone'] ?? '');
    $status = mysqli_real_escape_string($conn, $data['status'] ?? 'active');
    $password_hash = mysqli_real_escape_string($conn, $data['password_hash'] ?? '');
    
    $query = "INSERT INTO customers (first_name, last_name, email, phone, password_hash, status) 
              VALUES ('$first_name', '$last_name', '$email', '$phone', '$password_hash', '$status')";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    } else {
        error_log("Add customer failed: " . mysqli_error($conn));
        return false;
    }
}

/**
 * Update customer
 */
function updateCustomer($conn, $id, $data) {
    $first_name = mysqli_real_escape_string($conn, $data['first_name']);
    $last_name = mysqli_real_escape_string($conn, $data['last_name']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $phone = mysqli_real_escape_string($conn, $data['phone'] ?? '');
    $status = mysqli_real_escape_string($conn, $data['status'] ?? 'active');
    
    // Build query dynamically to handle optional fields
    $query = "UPDATE customers 
              SET first_name = '$first_name', 
                  last_name = '$last_name', 
                  email = '$email', 
                  phone = '$phone', 
                  status = '$status'";
    
    // Add password update if provided
    if (!empty($data['password_hash'])) {
        $password_hash = mysqli_real_escape_string($conn, $data['password_hash']);
        $query .= ", password_hash = '$password_hash'";
    }
    
    $query .= " WHERE id = $id";
    
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("Update customer failed: " . mysqli_error($conn));
    }
    return $result;
}

/**
 * Delete customer with transaction safety
 */
function deleteCustomer($conn, $id) {
    mysqli_begin_transaction($conn);
    
    try {
        // Note: Due to CASCADE constraints in addresses table, we might not need to delete addresses manually
        // But we'll keep it for safety
        
        // Delete related addresses first
        $delete_addresses = "DELETE FROM addresses WHERE customer_id = $id";
        if (!mysqli_query($conn, $delete_addresses)) {
            throw new Exception("Delete addresses failed: " . mysqli_error($conn));
        }
        
        // Delete customer (this will automatically delete orders due to CASCADE)
        $query = "DELETE FROM customers WHERE id = $id";
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Delete customer failed: " . mysqli_error($conn));
        }
        
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log($e->getMessage());
        return false;
    }
}

/**
 * Update customer status
 */
function updateCustomerStatus($conn, $id, $status) {
    $status = mysqli_real_escape_string($conn, $status);
    $query = "UPDATE customers SET status = '$status' WHERE id = $id";
    
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("Update customer status failed: " . mysqli_error($conn));
    }
    return $result;
}

// =============================================================================
// CUSTOMER DATA RETRIEVAL FUNCTIONS
// =============================================================================

/**
 * Get all customers with filters and order statistics
 */
function getAllCustomers($conn, $filters = array()) {
    // Build base query - FIXED: Use created_at instead of order_date for orders
    $query = "SELECT c.*, 
                     COUNT(o.id) as order_count,
                     COALESCE(SUM(o.total_amount), 0) as total_spent,
                     MAX(o.created_at) as last_order_date
              FROM customers c 
              LEFT JOIN orders o ON c.id = o.customer_id";
    
    $where_conditions = array();
    $params = array();
    $types = '';
    
    // Apply filters
    if (!empty($filters['status']) && $filters['status'] != 'all') {
        $where_conditions[] = "c.status = ?";
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    if (!empty($filters['search'])) {
        $where_conditions[] = "(c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ?)";
        $search_term = "%" . $filters['search'] . "%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'sss';
    }
    
    // Add WHERE clause if conditions exist
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    $query .= " GROUP BY c.id";
    
    // Apply sorting
    $sort_options = array(
        'newest' => 'c.created_at DESC',
        'oldest' => 'c.created_at ASC',
        'name' => 'c.first_name ASC, c.last_name ASC',
        'orders' => 'order_count DESC'
    );
    
    $sort = !empty($filters['sort']) ? $filters['sort'] : 'newest';
    $query .= " ORDER BY " . ($sort_options[$sort] ?? 'c.created_at DESC');
    
    // Prepare and execute statement
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return array();
    }
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        return array();
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    $customers = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $customers;
}

/**
 * Get customer by ID with order statistics
 */
function getCustomerById($conn, $id) {
    $query = "SELECT c.*, 
                     COUNT(o.id) as order_count,
                     COALESCE(SUM(o.total_amount), 0) as total_spent,
                     MAX(o.created_at) as last_order_date
              FROM customers c 
              LEFT JOIN orders o ON c.id = o.customer_id
              WHERE c.id = ?
              GROUP BY c.id";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Prepare failed in getCustomerById: " . mysqli_error($conn));
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed in getCustomerById: " . mysqli_stmt_error($stmt));
        return null;
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $customer;
    }
    
    mysqli_stmt_close($stmt);
    return null;
}

// =============================================================================
// RELATED DATA FUNCTIONS
// =============================================================================

/**
 * Get customer addresses
 */
function getCustomerAddresses($conn, $customer_id) {
    $query = "SELECT * FROM addresses WHERE customer_id = ? ORDER BY is_default DESC, type ASC";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        error_log("Prepare failed in getCustomerAddresses: " . mysqli_error($conn));
        return array();
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed in getCustomerAddresses: " . mysqli_stmt_error($stmt));
        return array();
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    $addresses = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $addresses[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $addresses;
}

/**
 * Get customer orders - FIXED: Use correct column names
 */
function getCustomerOrders($conn, $customer_id, $limit = 10) {
    $query = "SELECT o.*, 
                     sa.address_line1, sa.city, sa.state,
                     ba.address_line1 as billing_address_line1, ba.city as billing_city, ba.state as billing_state
              FROM orders o 
              LEFT JOIN addresses sa ON o.shipping_address_id = sa.id 
              LEFT JOIN addresses ba ON o.billing_address_id = ba.id
              WHERE o.customer_id = ? 
              ORDER BY o.created_at DESC 
              LIMIT ?";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Prepare failed in getCustomerOrders: " . mysqli_error($conn));
        return array();
    }
    
    mysqli_stmt_bind_param($stmt, 'ii', $customer_id, $limit);
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed in getCustomerOrders: " . mysqli_stmt_error($stmt));
        return array();
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $orders;
}

// =============================================================================
// STATISTICS AND ANALYTICS FUNCTIONS
// =============================================================================

/**
 * Get customer statistics - FIXED: Use correct status values
 */
function getCustomerStats($conn, $customer_id) {
    $customer = getCustomerById($conn, $customer_id);
    
    if (!$customer) {
        return null;
    }
    
    // Calculate average order value - FIXED: Use correct cancelled status
    $avg_query = "SELECT AVG(total_amount) as avg_order_value 
                  FROM orders 
                  WHERE customer_id = ? AND status != 'cancelled'";
    $stmt = mysqli_prepare($conn, $avg_query);
    
    if (!$stmt) {
        error_log("Prepare failed in getCustomerStats: " . mysqli_error($conn));
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Execute failed in getCustomerStats: " . mysqli_stmt_error($stmt));
        return null;
    }
    
    $avg_result = mysqli_stmt_get_result($stmt);
    $avg_row = mysqli_fetch_assoc($avg_result);
    mysqli_stmt_close($stmt);
    
    $stats = array(
        'total_orders' => $customer['order_count'] ?? 0,
        'total_spent' => $customer['total_spent'] ?? 0,
        'avg_order_value' => $avg_row['avg_order_value'] ? round($avg_row['avg_order_value'], 2) : 0,
        'last_order' => $customer['last_order_date'] ?? null
    );
    
    return $stats;
}

/**
 * Get customer counts by status
 */
function getCustomerCounts($conn) {
    $query = "SELECT status, COUNT(*) as count FROM customers GROUP BY status";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Customer counts query failed: " . mysqli_error($conn));
        return array('active' => 0, 'inactive' => 0, 'total' => 0);
    }
    
    $counts = array('active' => 0, 'inactive' => 0, 'total' => 0);
    while ($row = mysqli_fetch_assoc($result)) {
        $counts[$row['status']] = $row['count'];
        $counts['total'] += $row['count'];
    }
    
    return $counts;
}

// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

/**
 * Generate avatar initials from first and last name
 */
function getAvatarInitials($first_name, $last_name) {
    return strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
}

/**
 * Check if required tables exist
 */
function checkRequiredTables($conn) {
    $required_tables = ['customers', 'orders', 'addresses'];
    $missing_tables = [];
    
    foreach ($required_tables as $table) {
        $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
        if (mysqli_num_rows($check) == 0) {
            $missing_tables[] = $table;
        }
    }
    
    return $missing_tables;
}