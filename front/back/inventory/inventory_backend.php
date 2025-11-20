<?php
// Include database connection
include '../../databse/db_connection.php';

// Function to get inventory statistics
function getInventoryStats($conn) {
    $stats = array();
    
    // Total products
    $query = "SELECT COUNT(*) as total_products FROM products WHERE status = 'active'";
    $result = mysqli_query($conn, $query);
    $stats['total_products'] = mysqli_fetch_assoc($result)['total_products'];
    
    // Low stock items
    $query = "SELECT COUNT(*) as low_stock_items 
              FROM products 
              WHERE current_stock <= min_stock_level AND status = 'active'";
    $result = mysqli_query($conn, $query);
    $stats['low_stock_items'] = mysqli_fetch_assoc($result)['low_stock_items'];
    
    // Inventory value
    $query = "SELECT SUM(cost_price * current_stock) as inventory_value 
              FROM products 
              WHERE status = 'active'";
    $result = mysqli_query($conn, $query);
    $stats['inventory_value'] = mysqli_fetch_assoc($result)['inventory_value'] ?? 0;
    
    // Total categories
    $query = "SELECT COUNT(*) as total_categories FROM categories";
    $result = mysqli_query($conn, $query);
    $stats['total_categories'] = mysqli_fetch_assoc($result)['total_categories'];
    
    return $stats;
}

// Function to get all products with filters
function getProducts($conn, $filters = array()) {
    $query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN suppliers s ON p.supplier_id = s.id 
              WHERE p.status = 'active'";
    
    $params = array();
    
    // Apply filters
    if (!empty($filters['category']) && $filters['category'] != 'all') {
        $query .= " AND c.slug = ?";
        $params[] = $filters['category'];
    }
    
    if (!empty($filters['stock_status'])) {
        switch ($filters['stock_status']) {
            case 'low-stock':
                $query .= " AND p.current_stock <= p.min_stock_level";
                break;
            case 'out-of-stock':
                $query .= " AND p.current_stock = 0";
                break;
            case 'in-stock':
                $query .= " AND p.current_stock > p.min_stock_level";
                break;
        }
    }
    
    if (!empty($filters['search'])) {
        $query .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
        $search_term = "%" . $filters['search'] . "%";
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $query .= " ORDER BY p.name ASC";
    
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
    
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    return $products;
}

// Function to get product by ID
function getProductById($conn, $id) {
    $query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN suppliers s ON p.supplier_id = s.id 
              WHERE p.id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to get product by SKU
function getProductBySku($conn, $sku) {
    $query = "SELECT * FROM products WHERE sku = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $sku);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to get all categories
function getCategories($conn) {
    $query = "SELECT * FROM categories ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    
    $categories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

// Function to get all suppliers
function getSuppliers($conn) {
    $query = "SELECT * FROM suppliers ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    
    $suppliers = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = $row;
    }
    
    return $suppliers;
}

// Function to update product stock
function updateProductStock($conn, $product_id, $quantity, $type, $reason = '', $reference = '') {
    // Get current product
    $product = getProductById($conn, $product_id);
    if (!$product) {
        return false;
    }
    
    $previous_stock = $product['current_stock'];
    
    // Calculate new stock based on type
    switch ($type) {
        case 'in':
            $new_stock = $previous_stock + $quantity;
            break;
        case 'out':
            $new_stock = $previous_stock - $quantity;
            break;
        case 'adjustment':
            $new_stock = $quantity;
            break;
        default:
            return false;
    }
    
    // Ensure stock doesn't go negative
    if ($new_stock < 0) {
        return false;
    }
    
    // Update product stock
    $update_query = "UPDATE products SET current_stock = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, 'ii', $new_stock, $product_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        return false;
    }
    
    // Record stock movement
    $movement_query = "INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, reason, reference) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $movement_query);
    mysqli_stmt_bind_param($stmt, 'isiiiss', $product_id, $type, $quantity, $previous_stock, $new_stock, $reason, $reference);
    
    if (!mysqli_stmt_execute($stmt)) {
        return false;
    }
    
    // Check if we need to create or resolve low stock alerts
    checkLowStockAlerts($conn, $product_id, $new_stock, $product['min_stock_level']);
    
    return true;
}

// Function to check and manage low stock alerts
function checkLowStockAlerts($conn, $product_id, $current_stock, $min_stock_level) {
    // Determine alert level
    if ($current_stock == 0) {
        $alert_level = 'critical';
    } elseif ($current_stock <= $min_stock_level) {
        $alert_level = 'low';
    } else {
        // Stock is sufficient, resolve any existing alerts
        $resolve_query = "UPDATE low_stock_alerts SET is_resolved = TRUE, resolved_at = CURRENT_TIMESTAMP 
                          WHERE product_id = ? AND is_resolved = FALSE";
        $stmt = mysqli_prepare($conn, $resolve_query);
        mysqli_stmt_bind_param($stmt, 'i', $product_id);
        mysqli_stmt_execute($stmt);
        return;
    }
    
    // Check if there's already an unresolved alert
    $check_query = "SELECT id FROM low_stock_alerts WHERE product_id = ? AND is_resolved = FALSE";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        // Create new alert
        $alert_query = "INSERT INTO low_stock_alerts (product_id, current_stock, min_stock_level, alert_level) 
                        VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $alert_query);
        mysqli_stmt_bind_param($stmt, 'iiis', $product_id, $current_stock, $min_stock_level, $alert_level);
        mysqli_stmt_execute($stmt);
    } else {
        // Update existing alert
        $update_query = "UPDATE low_stock_alerts SET current_stock = ?, alert_level = ? 
                         WHERE product_id = ? AND is_resolved = FALSE";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, 'isi', $current_stock, $alert_level, $product_id);
        mysqli_stmt_execute($stmt);
    }
}

// Function to get low stock alerts
function getLowStockAlerts($conn, $resolved = false) {
    // Check if low_stock_alerts table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'low_stock_alerts'");
    if (mysqli_num_rows($check_table) == 0) {
        return array();
    }
    
    $query = "SELECT la.*, p.name as product_name, p.sku, p.min_stock_level, p.max_stock_level 
              FROM low_stock_alerts la 
              JOIN products p ON la.product_id = p.id 
              WHERE la.is_resolved = ? 
              ORDER BY la.alert_level DESC, la.created_at DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return array();
    }
    
    // Convert boolean to integer for MySQL
    $resolved_int = $resolved ? 1 : 0;
    mysqli_stmt_bind_param($stmt, 'i', $resolved_int);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $alerts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $alerts[] = $row;
    }
    
    return $alerts;
}


// Function to get stock movements for a product
function getProductStockMovements($conn, $product_id, $limit = 10) {
    $query = "SELECT * FROM stock_movements 
              WHERE product_id = ? 
              ORDER BY created_at DESC 
              LIMIT ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $product_id, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $movements = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $movements[] = $row;
    }
    
    return $movements;
}

// Function to get stock status
function getStockStatus($current_stock, $min_stock_level) {
    if ($current_stock == 0) {
        return 'out-of-stock';
    } elseif ($current_stock <= $min_stock_level) {
        return 'low-stock';
    } else {
        return 'in-stock';
    }
}

// Function to get stock percentage
function getStockPercentage($current_stock, $max_stock_level) {
    if ($max_stock_level == 0) return 0;
    return min(100, ($current_stock / $max_stock_level) * 100);
}

// Function to get stock level class
function getStockLevelClass($percentage) {
    if ($percentage >= 70) return 'stock-high';
    if ($percentage >= 30) return 'stock-medium';
    return 'stock-low';
}
?>