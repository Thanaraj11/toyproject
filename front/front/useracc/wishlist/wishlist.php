<?php
// useracc/whishlist/wishlist.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../useracc/login/login.php");
    exit();
}
?>

<?php
// Include database connection
include '../../../databse/db_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect to login if not logged in
// if (!isLoggedIn()) {
//     header("Location: ../login/login.php");
//     exit();
// }

// Get user wishlist items
function getWishlistItems($user_id) {
    global $conn;
    $wishlist_items = [];
    
    $sql = "SELECT w.id, w.product_id, p.name as product_name, p.price, p.description, 
                   p.image_url, w.added_at
            FROM wishlist w
            JOIN products p ON w.product_id = p.id
            WHERE w.customer_id = $user_id
            ORDER BY w.added_at DESC";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $wishlist_items[] = $row;
        }
    }
    
    return $wishlist_items;
}

// Remove item from wishlist
function removeFromWishlist($wishlist_id, $user_id) {
    global $conn;
    
    $sql = "DELETE FROM wishlist WHERE id = $wishlist_id AND customer_id = $user_id";
    
    if (mysqli_query($conn, $sql)) {
        return ['success' => true, 'message' => 'Item removed from wishlist'];
    } else {
        return ['success' => false, 'message' => 'Error removing item from wishlist'];
    }
}

// Move item to cart
function moveToCart($wishlist_id, $user_id) {
    global $conn;
    
    // Get product details from wishlist
    $sql = "SELECT w.product_id, p.price, p.current_stock 
            FROM wishlist w 
            JOIN products p ON w.product_id = p.id 
            WHERE w.id = $wishlist_id AND w.customer_id = $user_id";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
        $product_id = $item['product_id'];
        
        // Check stock availability
        if ($item['current_stock'] <= 0) {
            return ['success' => false, 'message' => 'Product is out of stock'];
        }
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add to cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            // Get product details
            $product_sql = "SELECT name, price, image_url FROM products WHERE id = $product_id";
            $product_result = mysqli_query($conn, $product_sql);
            
            if ($product_result && mysqli_num_rows($product_result) > 0) {
                $product = mysqli_fetch_assoc($product_result);
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image_url' => $product['image_url'],
                    'quantity' => 1
                ];
            }
        }
        
        // Remove from wishlist
        removeFromWishlist($wishlist_id, $user_id);
        
        return ['success' => true, 'message' => 'Item moved to cart successfully!'];
    }
    
    return ['success' => false, 'message' => 'Error moving item to cart'];
}

// Handle wishlist actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $user_id = $_SESSION['user_id'];
    
    switch ($_POST['action']) {
        case 'remove_from_wishlist':
            if (isset($_POST['wishlist_id'])) {
                $result = removeFromWishlist($_POST['wishlist_id'], $user_id);
                if ($result['success']) {
                    $_SESSION['success_message'] = $result['message'];
                } else {
                    $_SESSION['error_message'] = $result['message'];
                }
            }
            break;
            
        case 'move_to_cart':
            if (isset($_POST['wishlist_id'])) {
                $result = moveToCart($_POST['wishlist_id'], $user_id);
                if ($result['success']) {
                    $_SESSION['success_message'] = $result['message'];
                } else {
                    $_SESSION['error_message'] = $result['message'];
                }
            }
            break;
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Get current user wishlist items
$user_id = $_SESSION['user_id'];
$wishlist_items = getWishlistItems($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Wishlist</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- <link rel="stylesheet" href="../user.css">-->
  <link rel="stylesheet" href="wishlist.css"> 
  <style>
    /* Wishlist Specific Styles */

  </style>
</head>
<body>
  <header>
    <div>
      <h1><i class="fas fa-heart"></i> My Wishlist</h1>
      <p>Your favorite items all in one place</p>
    </div>
    <?php include '../header3.php'; ?>
  </header>

  <main>
    <?php
    // Display success/error messages
    if (isset($_SESSION['success_message'])) {
        echo '<div id="notification" class="notification success show">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    
    if (isset($_SESSION['error_message'])) {
        echo '<div id="notification" class="notification error show">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>
    
    <section id="wishlist-items">
      <h2><i class="fas fa-bookmark"></i> Saved Products</h2>
      
      <div id="wishlist-container">
        <?php if (empty($wishlist_items)): ?>
          <div id="empty-wishlist-message" class="empty-wishlist">
            <i class="fas fa-heart-broken"></i>
            <p>Your wishlist is empty</p>
            <p>Start adding items you love!</p>
            <a href="../../main/index/index.php" class="continue-shopping">Continue Shopping</a>
          </div>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="wishlist-body">
              <?php foreach ($wishlist_items as $item): ?>
              <tr class="wishlist-item">
                <td>
                  <div class="product-info">
                    <?php if (!empty($item['image_url'])): ?>
                      <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-image">
                    <?php else: ?>
                      <div class="product-image" style="background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image" style="color: #6c757d;"></i>
                      </div>
                    <?php endif; ?>
                    <div>
                      <div class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                      <?php if (!empty($item['description'])): ?>
                        <p class="product-description"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?></p>
                      <?php endif; ?>
                      <div class="added-date">Added on <?php echo date('M j, Y', strtotime($item['added_at'])); ?></div>
                    </div>
                  </div>
                </td>
                <td class="price">$<?php echo number_format($item['price'], 2); ?></td>
                <td>
                  <div class="actions">
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="action" value="move_to_cart">
                      <input type="hidden" name="wishlist_id" value="<?php echo $item['id']; ?>">
                      <button type="submit" class="btn btn-cart">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                      </button>
                    </form>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="action" value="remove_from_wishlist">
                      <input type="hidden" name="wishlist_id" value="<?php echo $item['id']; ?>">
                      <button type="submit" class="btn btn-remove" onclick="return confirm('Are you sure you want to remove this item from your wishlist?')">
                        <i class="fas fa-trash"></i> Remove
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </section>
  </main>
  
  <script>
    // Auto-hide notifications after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
      const notifications = document.querySelectorAll('.notification.show');
      notifications.forEach(notification => {
        setTimeout(() => {
          notification.classList.remove('show');
          setTimeout(() => notification.remove(), 300);
        }, 5000);
      });
    });
  </script>
</body>
</html>