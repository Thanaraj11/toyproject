<?php
session_start();
require_once '../../../databse/db_connection.php';

/**
 * Get product details by ID
 */
function getProduct($conn, $product_id) {
    $product_id = mysqli_real_escape_string($conn, $product_id);
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = '$product_id' AND p.status = 'active'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Add product to cart
 */
function addToCart($product_id, $quantity = 1) {
    global $conn;
    $product = getProduct($conn, $product_id);
    
    if (!$product) {
        return false;
    }
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    return true;
}

/**
 * Update cart item quantity
 */
function updateCartQuantity($product_id, $quantity) {
    $product_id = intval($product_id);
    $quantity = intval($quantity);
    
    if ($quantity <= 0) {
        return removeFromCart($product_id);
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = $quantity;
        return true;
    }
    
    return false;
}

/**
 * Remove product from cart
 */
function removeFromCart($product_id) {
    $product_id = intval($product_id);
    
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        return true;
    }
    
    return false;
}

/**
 * Clear entire cart
 */
function clearCart() {
    $_SESSION['cart'] = array();
    return true;
}

/**
 * Get cart items with product details
 */
function getCartItems($conn) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return array();
    }
    
    $cart_items = array();
    $product_ids = array_keys($_SESSION['cart']);
    
    if (empty($product_ids)) {
        return array();
    }
    
    $ids_string = implode(',', $product_ids);
    
    $query = "SELECT p.id, p.name, p.price, p.original_price, p.image_url, p.current_stock, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.id IN ($ids_string) AND p.status = 'active'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Ensure price is a numeric value
            $price = floatval($row['price']);
            $quantity = intval($_SESSION['cart'][$row['id']]);
            
            $cart_items[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $price,
                'original_price' => $row['original_price'] ? floatval($row['original_price']) : null,
                'image_url' => $row['image_url'],
                'current_stock' => intval($row['current_stock']),
                'category_name' => $row['category_name'],
                'quantity' => $quantity,
                'subtotal' => $price * $quantity
            );
        }
    }
    
    return $cart_items;
}

/**
 * Get cart total
 */
function getCartTotal($conn) {
    $cart_items = getCartItems($conn);
    $total = 0;
    
    foreach ($cart_items as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

/**
 * Get cart items count
 */
function getCartCount() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    
    return array_sum($_SESSION['cart']);
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;
    
    switch ($action) {
        case 'add':
            $quantity = $_POST['quantity'] ?? 1;
            if (addToCart($product_id, $quantity)) {
                header('Location: cart.php?message=added');
            } else {
                header('Location: cart.php?error=not_found');
            }
            exit;
            
        case 'update':
            $quantity = $_POST['quantity'] ?? 1;
            updateCartQuantity($product_id, $quantity);
            header('Location: cart.php?message=updated');
            exit;
            
        case 'remove':
            removeFromCart($product_id);
            header('Location: cart.php?message=removed');
            exit;
            
        case 'clear':
            clearCart();
            header('Location: cart.php?message=cleared');
            exit;
    }
}
?>