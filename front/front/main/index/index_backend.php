<?php
/**
 * Get all active categories
 */
function getCategories($conn) {
    $sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC";
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
 * Get promotional banners from the database
 */
function getBanners($conn) {
    // Prepare SQL query to fetch only active banners, ordered by sort_order
    $sql = "
        SELECT 
            id,
            title,
            description,
            image_url,
            target_url,
            button_text,
            button_color,
            position,
            sort_order,
            start_date,
            end_date
        FROM banners
        WHERE status = 'active'
          AND (start_date IS NULL OR start_date <= NOW())
          AND (end_date IS NULL OR end_date >= NOW())
        ORDER BY sort_order ASC, created_at DESC
    ";

    $result = mysqli_query($conn, $sql);

    $banners = array();

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $banners[] = array(
                'image_url'     => $row['image_url'],
                'title'         => $row['title'],
                'description'   => $row['description'],
                'button_text'   => $row['button_text'] ?: 'Learn More',
                'button_url'    => $row['target_url'] ?: '#',
                'button_color'  => $row['button_color'] ?: '#4361ee',
                'position'      => $row['position']
            );
        }
    } else {
        // Fallback if no active banners found
        $banners[] = array(
            'image_url' => '../images/default-banner.jpg',
            'title' => 'Welcome to Our Store',
            'description' => 'Discover amazing deals and new arrivals.',
            'button_text' => 'Shop Now',
            'button_url' => '../productlist/productlist.php',
            'button_color' => '#4361ee'
        );
    }

    return $banners;
}


/**
 * Get featured products with their images
 */
function getFeaturedProducts($conn, $limit = 8) {
    $limit = mysqli_real_escape_string($conn, $limit);
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'active' AND p.is_featured = TRUE 
            ORDER BY p.created_at DESC 
            LIMIT $limit";
    
    $result = mysqli_query($conn, $sql);
    $products = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Get all images for this product
            $product_id = $row['id'];
            $images_sql = "SELECT * FROM product_images 
                          WHERE product_id = $product_id 
                          ORDER BY is_primary DESC, sort_order ASC";
            $images_result = mysqli_query($conn, $images_sql);
            $images = array();
            
            if ($images_result && mysqli_num_rows($images_result) > 0) {
                while ($image_row = mysqli_fetch_assoc($images_result)) {
                    $images[] = $image_row;
                }
            }
            $row['images'] = $images;
            $products[] = $row;
        }
    }
    
    return $products;
}

/**
 * Get all products with pagination and images
 */
function getAllProducts($conn, $limit = 12) {
    $limit = mysqli_real_escape_string($conn, $limit);
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'active' 
            ORDER BY p.created_at DESC 
            LIMIT $limit";
    
    $result = mysqli_query($conn, $sql);
    $products = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Get all images for this product
            $product_id = $row['id'];
            $images_sql = "SELECT * FROM product_images 
                          WHERE product_id = $product_id 
                          ORDER BY is_primary DESC, sort_order ASC";
            $images_result = mysqli_query($conn, $images_sql);
            $images = array();
            
            if ($images_result && mysqli_num_rows($images_result) > 0) {
                while ($image_row = mysqli_fetch_assoc($images_result)) {
                    $images[] = $image_row;
                }
            }
            $row['images'] = $images;
            $products[] = $row;
        }
    }
    
    return $products;
}

/**
 * Search products with images
 */
function searchProducts($conn, $search_term) {
    $search_term = mysqli_real_escape_string($conn, $search_term);
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'active' 
            AND (p.name LIKE '%$search_term%' OR p.description LIKE '%$search_term%' OR c.name LIKE '%$search_term%')
            ORDER BY p.name ASC";
    
    $result = mysqli_query($conn, $sql);
    $products = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Get all images for this product
            $product_id = $row['id'];
            $images_sql = "SELECT * FROM product_images 
                          WHERE product_id = $product_id 
                          ORDER BY is_primary DESC, sort_order ASC";
            $images_result = mysqli_query($conn, $images_sql);
            $images = array();
            
            if ($images_result && mysqli_num_rows($images_result) > 0) {
                while ($image_row = mysqli_fetch_assoc($images_result)) {
                    $images[] = $image_row;
                }
            }
            $row['images'] = $images;
            $products[] = $row;
        }
    }
    
    return $products;
}

/**
 * Generate star rating HTML
 */
function generateStarRating($rating) {
    $stars = '';
    $fullStars = floor($rating);
    $emptyStars = 5 - $fullStars;
    
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '★';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '☆';
    }
    
    return $stars;
}

/**
 * Format price with discount if available
 */
function formatPrice($price, $original_price = null) {
    if ($original_price && $original_price > $price) {
        return '<span class="original-price">$' . number_format($original_price, 2) . '</span> 
                <span class="current-price">$' . number_format($price, 2) . '</span>';
    } else {
        return '<span class="current-price">$' . number_format($price, 2) . '</span>';
    }
}

// Function to get all product images grouped by product
function getProductImagesGrouped($conn) {
    $images = array();
    
    $sql = "SELECT pi.*, p.name as product_name 
            FROM product_images pi
            LEFT JOIN products p ON pi.product_id = p.id
            WHERE p.status = 'active'
            ORDER BY pi.product_id, pi.sort_order, pi.created_at";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            if (!isset($images[$product_id])) {
                $images[$product_id] = array();
            }
            $images[$product_id][] = $row;
        }
    }
    
    return $images;
}

?>