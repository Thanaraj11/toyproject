<?php
// Product Management Backend Functions

/**
 * Function to handle image upload
 */
function uploadProductImage($file) {
    $target_dir = "../uploads/products/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // Generate unique filename
    $file_name = "product_" . time() . "_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    
    // Check if file is actually an image
    if (!isset($file["tmp_name"]) || empty($file["tmp_name"])) {
        return array('success' => false, 'error' => 'No file uploaded.');
    }
    
    // Check if image file is actual image
    $check = @getimagesize($file["tmp_name"]);
    if ($check === false) {
        return array('success' => false, 'error' => 'File is not an image.');
    }
    
    // Check file size (5MB limit)
    if ($file["size"] > 5000000) {
        return array('success' => false, 'error' => 'File is too large. Maximum size is 5MB.');
    }
    
    // Allow certain file formats
    $allowed_extensions = array("jpg", "jpeg", "png", "gif", "webp");
    if (!in_array($file_extension, $allowed_extensions)) {
        return array('success' => false, 'error' => 'Only JPG, JPEG, PNG, GIF & WebP files are allowed.');
    }
    
    // Try to upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Return relative path for web access
        $web_path = str_replace('../', '', $target_file);
        return array(
            'success' => true, 
            'file_path' => $target_file, 
            'file_url' => $web_path
        );
    } else {
        return array('success' => false, 'error' => 'Sorry, there was an error uploading your file.');
    }
}

/**
 * Function to get all products with optional filters
 */
function getAllProducts($conn, $filters = array()) {
    $query = "SELECT p.*, c.name as category_name, c.slug as category_slug
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE 1=1";
    
    $params = array();
    $types = '';
    
    // Apply filters
    if (!empty($filters['category']) && $filters['category'] != 'all') {
        $query .= " AND c.slug = ?";
        $params[] = $filters['category'];
        $types .= 's';
    }
    
    if (!empty($filters['status']) && $filters['status'] != 'all') {
        $query .= " AND p.status = ?";
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    if (!empty($filters['stock_status'])) {
        switch ($filters['stock_status']) {
            case 'in-stock':
                $query .= " AND p.current_stock > 0";
                break;
            case 'out-of-stock':
                $query .= " AND p.current_stock = 0";
                break;
            case 'low-stock':
                $query .= " AND p.current_stock <= p.min_stock_level AND p.current_stock > 0";
                break;
        }
    }
    
    if (!empty($filters['search'])) {
        $query .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?)";
        $search_term = "%" . $filters['search'] . "%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'sss';
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    // Prepare and execute statement
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            // Fallback to simple query if prepared statement fails
            $result = mysqli_query($conn, str_replace('?', "'" . mysqli_real_escape_string($conn, $params[0]) . "'", $query));
        }
    } else {
        $result = mysqli_query($conn, $query);
    }
    
    $products = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

/**
 * Function to get product by ID
 */
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
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Function to get product by SKU
 */
function getProductBySku($conn, $sku) {
    $query = "SELECT * FROM products WHERE sku = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $sku);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Function to add new product
 */
function addProduct($conn, $data) {
    // Check if SKU already exists
    $existing = getProductBySku($conn, $data['sku']);
    if ($existing) {
        return false; // SKU already exists
    }
    
    // Escape all data
    $name = mysqli_real_escape_string($conn, $data['name']);
    $sku = mysqli_real_escape_string($conn, $data['sku']);
    $description = mysqli_real_escape_string($conn, $data['description']);
    $category_id = intval($data['category_id']);
    $supplier_id = intval($data['supplier_id']);
    $price = floatval($data['price']);
    $cost_price = floatval($data['cost_price']);
    $current_stock = intval($data['current_stock']);
    $min_stock_level = intval($data['min_stock_level']);
    $max_stock_level = intval($data['max_stock_level']);
    $image_url = mysqli_real_escape_string($conn, $data['image_url']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    
    // Create slug from name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    // Handle duplicate slugs
    $slug_counter = 1;
    $original_slug = $slug;
    while (slugExists($conn, $slug)) {
        $slug = $original_slug . '-' . $slug_counter;
        $slug_counter++;
    }
    
    $query = "INSERT INTO products (name, slug, sku, description, category_id, supplier_id, price, cost_price, current_stock, min_stock_level, max_stock_level, image_url, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssssiiddiiiss', 
        $name, $slug, $sku, $description, $category_id, $supplier_id, 
        $price, $cost_price, $current_stock, $min_stock_level, $max_stock_level, 
        $image_url, $status
    );
    
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

/**
 * Helper function to check if slug exists
 */
function slugExists($conn, $slug) {
    $query = "SELECT id FROM products WHERE slug = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_num_rows($result) > 0;
}

/**
 * Function to update product
 */
function updateProduct($conn, $id, $data) {
    // Escape all data
    $name = mysqli_real_escape_string($conn, $data['name']);
    $sku = mysqli_real_escape_string($conn, $data['sku']);
    $description = mysqli_real_escape_string($conn, $data['description']);
    $category_id = intval($data['category_id']);
    $supplier_id = intval($data['supplier_id']);
    $price = floatval($data['price']);
    $cost_price = floatval($data['cost_price']);
    $current_stock = intval($data['current_stock']);
    $min_stock_level = intval($data['min_stock_level']);
    $max_stock_level = intval($data['max_stock_level']);
    $image_url = mysqli_real_escape_string($conn, $data['image_url']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    
    // Create slug from name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    $query = "UPDATE products 
              SET name = ?, 
                  slug = ?,
                  sku = ?, 
                  description = ?, 
                  category_id = ?, 
                  supplier_id = ?, 
                  price = ?, 
                  cost_price = ?, 
                  current_stock = ?, 
                  min_stock_level = ?, 
                  max_stock_level = ?, 
                  image_url = ?, 
                  status = ?,
                  updated_at = CURRENT_TIMESTAMP
              WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssiiddiiiissi', 
        $name, $slug, $sku, $description, $category_id, $supplier_id, 
        $price, $cost_price, $current_stock, $min_stock_level, $max_stock_level, 
        $image_url, $status, $id
    );
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Function to delete product
 */
function deleteProduct($conn, $id) {
    // First, get the product to check for image
    $product = getProductById($conn, $id);
    if ($product && !empty($product['image_url'])) {
        // Delete the image file if it exists
        $image_path = "../" . $product['image_url'];
        if (file_exists($image_path) && is_file($image_path)) {
            @unlink($image_path);
        }
    }
    
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Function to get all categories
 */
function getCategories($conn) {
    $query = "SELECT * FROM categories ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    
    $categories = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

/**
 * Function to get all suppliers
 */
function getSuppliers($conn) {
    $query = "SELECT * FROM suppliers ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    
    $suppliers = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $suppliers[] = $row;
        }
    }
    
    return $suppliers;
}

/**
 * Function to get category by slug
 */
function getCategoryBySlug($conn, $slug) {
    $query = "SELECT * FROM categories WHERE slug = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Function to get product statistics
 */
function getProductStats($conn) {
    $stats = array(
        'total_products' => 0,
        'active_products' => 0,
        'out_of_stock' => 0,
        'low_stock' => 0
    );
    
    // Total products
    $query = "SELECT COUNT(*) as total_products FROM products";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $stats['total_products'] = mysqli_fetch_assoc($result)['total_products'];
    }
    
    // Active products
    $query = "SELECT COUNT(*) as active_products FROM products WHERE status = 'active'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $stats['active_products'] = mysqli_fetch_assoc($result)['active_products'];
    }
    
    // Out of stock products
    $query = "SELECT COUNT(*) as out_of_stock FROM products WHERE current_stock = 0";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $stats['out_of_stock'] = mysqli_fetch_assoc($result)['out_of_stock'];
    }
    
    // Low stock products
    $query = "SELECT COUNT(*) as low_stock FROM products WHERE current_stock <= min_stock_level AND current_stock > 0";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $stats['low_stock'] = mysqli_fetch_assoc($result)['low_stock'];
    }
    
    return $stats;
}

/**
 * Function to get stock status
 */
function getStockStatus($current_stock, $min_stock_level) {
    if ($current_stock == 0) {
        return 'out-of-stock';
    } elseif ($current_stock <= $min_stock_level) {
        return 'low-stock';
    } else {
        return 'in-stock';
    }
}

/**
 * Function to format stock status for display
 */
function formatStockStatus($current_stock, $min_stock_level) {
    $status = getStockStatus($current_stock, $min_stock_level);
    
    switch ($status) {
        case 'out-of-stock':
            return 'Out of Stock';
        case 'low-stock':
            return 'Low Stock';
        case 'in-stock':
            return 'In Stock';
        default:
            return 'Unknown';
    }
}
?>