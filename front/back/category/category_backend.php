<?php
// Include database connection
include '../../databse/db_connection.php';

// Function to get all categories
function getAllCategories($conn, $parent_id = null) {
    $query = "SELECT c.*, 
                     p.name as parent_name,
                     (SELECT COUNT(*) FROM categories AS sub WHERE sub.parent_id = c.id) as subcategory_count
              FROM categories c 
              LEFT JOIN categories p ON c.parent_id = p.id";
    
    if ($parent_id !== null) {
        $query .= " WHERE c.parent_id = $parent_id";
    } else {
        $query .= " WHERE c.parent_id IS NULL";
    }
    
    $query .= " ORDER BY c.name ASC";
    
    $result = mysqli_query($conn, $query);
    $categories = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

// Function to get category by ID
function getCategoryById($conn, $id) {
    $query = "SELECT c.*, p.name as parent_name 
              FROM categories c 
              LEFT JOIN categories p ON c.parent_id = p.id 
              WHERE c.id = $id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to add new category
function addCategory($conn, $data) {
    $name = mysqli_real_escape_string($conn, $data['name']);
    $slug = mysqli_real_escape_string($conn, $data['slug']);
    $description = mysqli_real_escape_string($conn, $data['description']);
    $icon = mysqli_real_escape_string($conn, $data['icon']);
    $color = mysqli_real_escape_string($conn, $data['color']);
    $parent_id = !empty($data['parent_id']) ? intval($data['parent_id']) : 'NULL';
    $status = mysqli_real_escape_string($conn, $data['status']);
    
    $query = "INSERT INTO categories (name, slug, description, icon, color, parent_id, status) 
              VALUES ('$name', '$slug', '$description', '$icon', '$color', $parent_id, '$status')";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

// Function to update category
function updateCategory($conn, $id, $data) {
    $name = mysqli_real_escape_string($conn, $data['name']);
    $slug = mysqli_real_escape_string($conn, $data['slug']);
    $description = mysqli_real_escape_string($conn, $data['description']);
    $icon = mysqli_real_escape_string($conn, $data['icon']);
    $color = mysqli_real_escape_string($conn, $data['color']);
    $parent_id = !empty($data['parent_id']) ? intval($data['parent_id']) : 'NULL';
    $status = mysqli_real_escape_string($conn, $data['status']);
    
    $query = "UPDATE categories 
              SET name = '$name', 
                  slug = '$slug', 
                  description = '$description', 
                  icon = '$icon', 
                  color = '$color', 
                  parent_id = $parent_id, 
                  status = '$status',
                  updated_at = CURRENT_TIMESTAMP
              WHERE id = $id";
    
    return mysqli_query($conn, $query);
}

// Function to delete category
function deleteCategory($conn, $id) {
    // First, set parent_id to NULL for subcategories
    $update_query = "UPDATE categories SET parent_id = NULL WHERE parent_id = $id";
    mysqli_query($conn, $update_query);
    
    // Then delete the category
    $delete_query = "DELETE FROM categories WHERE id = $id";
    return mysqli_query($conn, $delete_query);
}

// Function to get parent categories (categories without parent)
function getParentCategories($conn) {
    $query = "SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    $categories = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

// Function to search categories
function searchCategories($conn, $search_term) {
    $search_term = mysqli_real_escape_string($conn, $search_term);
    $query = "SELECT c.*, p.name as parent_name 
              FROM categories c 
              LEFT JOIN categories p ON c.parent_id = p.id 
              WHERE c.name LIKE '%$search_term%' OR c.description LIKE '%$search_term%'
              ORDER BY c.name ASC";
    
    $result = mysqli_query($conn, $query);
    $categories = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

// Function to update product count for a category
function updateProductCount($conn, $category_id) {
    // This would typically be called when products are added/removed
    $count_query = "SELECT COUNT(*) as product_count FROM products WHERE category_id = $category_id";
    $result = mysqli_query($conn, $count_query);
    $row = mysqli_fetch_assoc($result);
    
    $update_query = "UPDATE categories SET product_count = {$row['product_count']} WHERE id = $category_id";
    return mysqli_query($conn, $update_query);
}
?>