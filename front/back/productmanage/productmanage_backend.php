<?php
// Include database connection
include '../../databse/db_connection.php';

// Function to get all products
function getAllProducts($conn, $filters = array()) {
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE 1=1";
    
    $params = array();
    
    // Apply filters
    if (!empty($filters['category']) && $filters['category'] != 'all') {
        $query .= " AND c.slug = ?";
        $params[] = $filters['category'];
    }
    
    if (!empty($filters['status']) && $filters['status'] != 'all') {
        $query .= " AND p.status = ?";
        $params[] = $filters['status'];
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

// Function to add new product
function addProduct($conn, $data) {
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
    
    $query = "INSERT INTO products (name, sku, description, category_id, supplier_id, price, cost_price, current_stock, min_stock_level, max_stock_level, image_url, status) 
              VALUES ('$name', '$sku', '$description', $category_id, $supplier_id, $price, $cost_price, $current_stock, $min_stock_level, $max_stock_level, '$image_url', '$status')";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

// Function to update product
function updateProduct($conn, $id, $data) {
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
    
    $query = "UPDATE products 
              SET name = '$name', 
                  sku = '$sku', 
                  description = '$description', 
                  category_id = $category_id, 
                  supplier_id = $supplier_id, 
                  price = $price, 
                  cost_price = $cost_price, 
                  current_stock = $current_stock, 
                  min_stock_level = $min_stock_level, 
                  max_stock_level = $max_stock_level, 
                  image_url = '$image_url', 
                  status = '$status',
                  updated_at = CURRENT_TIMESTAMP
              WHERE id = $id";
    
    return mysqli_query($conn, $query);
}

// Function to delete product
function deleteProduct($conn, $id) {
    $query = "DELETE FROM products WHERE id = $id";
    return mysqli_query($conn, $query);
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

// Function to get category by slug
function getCategoryBySlug($conn, $slug) {
    $query = "SELECT * FROM categories WHERE slug = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to handle image upload
function uploadProductImage($file) {
    $target_dir = "../uploads/products/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $file_name = "product_" . time() . "_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    
    // Check if image file is actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return array('success' => false, 'error' => 'File is not an image.');
    }
    
    // Check file size (5MB limit)
    if ($file["size"] > 5000000) {
        return array('success' => false, 'error' => 'File is too large. Maximum size is 5MB.');
    }
    
    // Allow certain file formats
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($file_extension, $allowed_extensions)) {
        return array('success' => false, 'error' => 'Only JPG, JPEG, PNG & GIF files are allowed.');
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('success' => true, 'file_path' => $target_file, 'file_url' => str_replace('../', '', $target_file));
    } else {
        return array('success' => false, 'error' => 'Sorry, there was an error uploading your file.');
    }
}

// Function to get product statistics
function getProductStats($conn) {
    $stats = array();
    
    // Total products
    $query = "SELECT COUNT(*) as total_products FROM products";
    $result = mysqli_query($conn, $query);
    $stats['total_products'] = mysqli_fetch_assoc($result)['total_products'];
    
    // Active products
    $query = "SELECT COUNT(*) as active_products FROM products WHERE status = 'active'";
    $result = mysqli_query($conn, $query);
    $stats['active_products'] = mysqli_fetch_assoc($result)['active_products'];
    
    // Out of stock products
    $query = "SELECT COUNT(*) as out_of_stock FROM products WHERE current_stock = 0";
    $result = mysqli_query($conn, $query);
    $stats['out_of_stock'] = mysqli_fetch_assoc($result)['out_of_stock'];
    
    // Low stock products
    $query = "SELECT COUNT(*) as low_stock FROM products WHERE current_stock <= min_stock_level AND current_stock > 0";
    $result = mysqli_query($conn, $query);
    $stats['low_stock'] = mysqli_fetch_assoc($result)['low_stock'];
    
    return $stats;
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

// Function to format stock status for display
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