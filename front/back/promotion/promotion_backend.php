<?php
// Include database connection
include '../../databse/db_connection.php';

// Function to get promotion statistics
function getPromotionStats($conn) {
    $stats = array();
    
    // Total discount codes
    $query = "SELECT COUNT(*) as total_codes FROM promotions WHERE type = 'discount-code'";
    $result = mysqli_query($conn, $query);
    $stats['total_codes'] = mysqli_fetch_assoc($result)['total_codes'];
    
    // Active promotions
    $query = "SELECT COUNT(*) as active_promotions FROM promotions WHERE status = 'active'";
    $result = mysqli_query($conn, $query);
    $stats['active_promotions'] = mysqli_fetch_assoc($result)['active_promotions'];
    
    // Total usage
    $query = "SELECT COUNT(*) as total_usage FROM promotion_usage";
    $result = mysqli_query($conn, $query);
    $stats['total_usage'] = mysqli_fetch_assoc($result)['total_usage'];
    
    // Revenue generated (sum of discount amounts)
    $query = "SELECT SUM(discount_amount) as revenue_generated FROM promotion_usage";
    $result = mysqli_query($conn, $query);
    $stats['revenue_generated'] = mysqli_fetch_assoc($result)['revenue_generated'] ?? 0;
    
    return $stats;
}

// Function to get all promotions with filters
function getAllPromotions($conn, $filters = array()) {
    $query = "SELECT p.*, 
                     COUNT(pu.id) as usage_count,
                     (p.usage_limit - COUNT(pu.id)) as remaining_uses
              FROM promotions p 
              LEFT JOIN promotion_usage pu ON p.id = pu.promotion_id";
    
    $where_conditions = array();
    $params = array();
    
    // Apply filters
    if (!empty($filters['status']) && $filters['status'] != 'all') {
        $where_conditions[] = "p.status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['type']) && $filters['type'] != 'all') {
        if ($filters['type'] == 'discount-code') {
            $where_conditions[] = "p.type = 'discount-code'";
        } else {
            $where_conditions[] = "p.type != 'discount-code'";
        }
    }
    
    if (!empty($filters['discount_type']) && $filters['discount_type'] != 'all') {
        $where_conditions[] = "p.discount_type = ?";
        $params[] = $filters['discount_type'];
    }
    
    if (!empty($filters['search'])) {
        $where_conditions[] = "(p.name LIKE ? OR p.code LIKE ?)";
        $search_term = "%" . $filters['search'] . "%";
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    $query .= " GROUP BY p.id ORDER BY p.created_at DESC";
    
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
    
    $promotions = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $promotions[] = $row;
    }
    
    return $promotions;
}

// Function to get promotion by ID
function getPromotionById($conn, $id) {
    $query = "SELECT p.*, 
                     COUNT(pu.id) as usage_count,
                     (p.usage_limit - COUNT(pu.id)) as remaining_uses
              FROM promotions p 
              LEFT JOIN promotion_usage pu ON p.id = pu.promotion_id 
              WHERE p.id = ? 
              GROUP BY p.id";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to get promotion by code
function getPromotionByCode($conn, $code) {
    $query = "SELECT * FROM promotions WHERE code = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to add new promotion
function addPromotion($conn, $data) {
    $name = mysqli_real_escape_string($conn, $data['name']);
    $code = mysqli_real_escape_string($conn, $data['code']);
    $type = mysqli_real_escape_string($conn, $data['type']);
    $discount_type = mysqli_real_escape_string($conn, $data['discount_type']);
    $discount_value = floatval($data['discount_value']);
    $min_order_amount = floatval($data['min_order_amount']);
    $max_discount_amount = !empty($data['max_discount_amount']) ? floatval($data['max_discount_amount']) : NULL;
    $usage_limit = !empty($data['usage_limit']) ? intval($data['usage_limit']) : NULL;
    $start_date = mysqli_real_escape_string($conn, $data['start_date']);
    $end_date = !empty($data['end_date']) ? mysqli_real_escape_string($conn, $data['end_date']) : NULL;
    $status = mysqli_real_escape_string($conn, $data['status']);
    $applicable_categories = mysqli_real_escape_string($conn, $data['applicable_categories']);
    $applicable_products = mysqli_real_escape_string($conn, $data['applicable_products']);
    
    $query = "INSERT INTO promotions (name, code, type, discount_type, discount_value, min_order_amount, max_discount_amount, usage_limit, start_date, end_date, status, applicable_categories, applicable_products) 
              VALUES ('$name', '$code', '$type', '$discount_type', $discount_value, $min_order_amount, " . 
              ($max_discount_amount ? "$max_discount_amount" : "NULL") . ", " . 
              ($usage_limit ? "$usage_limit" : "NULL") . ", '$start_date', " . 
              ($end_date ? "'$end_date'" : "NULL") . ", '$status', '$applicable_categories', '$applicable_products')";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

// Function to update promotion
function updatePromotion($conn, $id, $data) {
    $name = mysqli_real_escape_string($conn, $data['name']);
    $code = mysqli_real_escape_string($conn, $data['code']);
    $type = mysqli_real_escape_string($conn, $data['type']);
    $discount_type = mysqli_real_escape_string($conn, $data['discount_type']);
    $discount_value = floatval($data['discount_value']);
    $min_order_amount = floatval($data['min_order_amount']);
    $max_discount_amount = !empty($data['max_discount_amount']) ? floatval($data['max_discount_amount']) : NULL;
    $usage_limit = !empty($data['usage_limit']) ? intval($data['usage_limit']) : NULL;
    $start_date = mysqli_real_escape_string($conn, $data['start_date']);
    $end_date = !empty($data['end_date']) ? mysqli_real_escape_string($conn, $data['end_date']) : NULL;
    $status = mysqli_real_escape_string($conn, $data['status']);
    $applicable_categories = mysqli_real_escape_string($conn, $data['applicable_categories']);
    $applicable_products = mysqli_real_escape_string($conn, $data['applicable_products']);
    
    $query = "UPDATE promotions 
              SET name = '$name', 
                  code = '$code', 
                  type = '$type', 
                  discount_type = '$discount_type', 
                  discount_value = $discount_value, 
                  min_order_amount = $min_order_amount, 
                  max_discount_amount = " . ($max_discount_amount ? "$max_discount_amount" : "NULL") . ", 
                  usage_limit = " . ($usage_limit ? "$usage_limit" : "NULL") . ", 
                  start_date = '$start_date', 
                  end_date = " . ($end_date ? "'$end_date'" : "NULL") . ", 
                  status = '$status', 
                  applicable_categories = '$applicable_categories', 
                  applicable_products = '$applicable_products',
                  updated_at = CURRENT_TIMESTAMP
              WHERE id = $id";
    
    return mysqli_query($conn, $query);
}

// Function to delete promotion
function deletePromotion($conn, $id) {
    $query = "DELETE FROM promotions WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Function to update promotion status based on dates
function updatePromotionStatus($conn, $id) {
    $promotion = getPromotionById($conn, $id);
    if (!$promotion) return false;
    
    $now = date('Y-m-d H:i:s');
    $new_status = $promotion['status'];
    
    if ($promotion['start_date'] > $now) {
        $new_status = 'upcoming';
    } elseif ($promotion['end_date'] && $promotion['end_date'] < $now) {
        $new_status = 'expired';
    } elseif ($promotion['start_date'] <= $now && (!$promotion['end_date'] || $promotion['end_date'] >= $now)) {
        $new_status = 'active';
    }
    
    if ($new_status != $promotion['status']) {
        $query = "UPDATE promotions SET status = '$new_status' WHERE id = $id";
        return mysqli_query($conn, $query);
    }
    
    return true;
}

// Function to generate unique promo code
function generatePromoCode($conn, $length = 8) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $max_attempts = 10;
    $attempt = 0;
    
    do {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        $existing = getPromotionByCode($conn, $code);
        $attempt++;
    } while ($existing && $attempt < $max_attempts);
    
    return $code;
}

// Function to get discount display text
function getDiscountDisplay($discount_type, $discount_value) {
    switch ($discount_type) {
        case 'percentage':
            return $discount_value . '% OFF';
        case 'fixed':
            return '$' . $discount_value . ' OFF';
        case 'shipping':
            return 'Free Shipping';
        default:
            return 'Unknown';
    }
}

// Function to get promotion status based on dates
function getPromotionStatus($start_date, $end_date) {
    $now = date('Y-m-d H:i:s');
    
    if ($start_date > $now) {
        return 'upcoming';
    } elseif ($end_date && $end_date < $now) {
        return 'expired';
    } else {
        return 'active';
    }
}

// Function to get categories for dropdown
function getCategories($conn) {
    $query = "SELECT * FROM categories ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    
    $categories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

// Function to record promotion usage
function recordPromotionUsage($conn, $promotion_id, $order_id, $customer_id, $discount_amount) {
    $query = "INSERT INTO promotion_usage (promotion_id, order_id, customer_id, discount_amount) 
              VALUES (?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiid', $promotion_id, $order_id, $customer_id, $discount_amount);
    
    if (mysqli_stmt_execute($stmt)) {
        // Update used count in promotions table
        $update_query = "UPDATE promotions SET used_count = used_count + 1 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, 'i', $promotion_id);
        mysqli_stmt_execute($stmt);
        
        return true;
    }
    
    return false;
}
?>