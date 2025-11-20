<?php
/**
 * Get categories for filter dropdown
 */
function getCategoriesForFilter($conn) {
    $sql = "SELECT id, name, slug FROM categories WHERE status = 'active' ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $categories = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

/**
 * Get price range for products
 */
function getPriceRange($conn, $filters = array()) {
    $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE status = 'active'";
    
    // Apply category filter if specified
    if (!empty($filters['category']) && $filters['category'] != 'all') {
        $category_slug = mysqli_real_escape_string($conn, $filters['category']);
        $sql = "SELECT MIN(p.price) as min_price, MAX(p.price) as max_price 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active' AND c.slug = '$category_slug'";
    }
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return array(
            'min_price' => $row['min_price'] ? floatval($row['min_price']) : 0,
            'max_price' => $row['max_price'] ? floatval($row['max_price']) : 1000
        );
    }
    
    return array('min_price' => 0, 'max_price' => 1000);
}

/**
 * Get filtered products with pagination
 */
function getFilteredProducts($conn, $filters, $page = 1, $per_page = 12) {
    $offset = ($page - 1) * $per_page;
    
    // Build WHERE conditions
    $where_conditions = array("p.status = 'active'");
    
    // Category filter
    if (!empty($filters['category']) && $filters['category'] != 'all') {
        $category_slug = mysqli_real_escape_string($conn, $filters['category']);
        $where_conditions[] = "c.slug = '$category_slug'";
    }
    
    // Price filters
    if (!empty($filters['min_price'])) {
        $min_price = floatval($filters['min_price']);
        $where_conditions[] = "p.price >= $min_price";
    }
    
    if (!empty($filters['max_price'])) {
        $max_price = floatval($filters['max_price']);
        $where_conditions[] = "p.price <= $max_price";
    }
    
    // Search filter
    if (!empty($filters['search'])) {
        $search_term = mysqli_real_escape_string($conn, $filters['search']);
        $where_conditions[] = "(p.name LIKE '%$search_term%' OR p.description LIKE '%$search_term%')";
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Build ORDER BY
    $order_by = "p.name ASC";
    switch ($filters['sort']) {
        case 'price-asc':
            $order_by = "p.price ASC";
            break;
        case 'price-desc':
            $order_by = "p.price DESC";
            break;
        case 'rating':
            $order_by = "p.rating DESC, p.review_count DESC";
            break;
        case 'newest':
            $order_by = "p.created_at DESC";
            break;
    }
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE $where_clause";
    $count_result = mysqli_query($conn, $count_sql);
    $total_count = 0;
    
    if ($count_result && mysqli_num_rows($count_result) > 0) {
        $row = mysqli_fetch_assoc($count_result);
        $total_count = $row['total'];
    }
    
    // Get products
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE $where_clause 
            ORDER BY $order_by 
            LIMIT $offset, $per_page";
    
    $result = mysqli_query($conn, $sql);
    $products = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    $total_pages = ceil($total_count / $per_page);
    
    return array(
        'products' => $products,
        'total_count' => $total_count,
        'total_pages' => $total_pages
    );
}

/**
 * Format price for display
 */
function formatPriceDisplay($price, $original_price = null) {
    if ($original_price && $original_price > $price) {
        return '<span class="original-price">$' . number_format($original_price, 2) . '</span> 
                <span class="current-price">$' . number_format($price, 2) . '</span>';
    } else {
        return '<span class="current-price">$' . number_format($price, 2) . '</span>';
    }
}

/**
 * Generate pagination links
 */
function generatePagination($current_page, $total_pages, $base_url) {
    $pagination = '';
    
    if ($total_pages <= 1) {
        return $pagination;
    }
    
    // Previous button
    if ($current_page > 1) {
        $prev_url = $base_url . '&page=' . ($current_page - 1);
        $pagination .= '<a href="' . $prev_url . '" class="page-link prev">Previous</a>';
    }
    
    // Page numbers
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);
    
    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $current_page) {
            $pagination .= '<span class="page-link current">' . $i . '</span>';
        } else {
            $page_url = $base_url . '&page=' . $i;
            $pagination .= '<a href="' . $page_url . '" class="page-link">' . $i . '</a>';
        }
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $next_url = $base_url . '&page=' . ($current_page + 1);
        $pagination .= '<a href="' . $next_url . '" class="page-link next">Next</a>';
    }
    
    return $pagination;
}
?>