<?php
// useracc/whishlist/wishlist.php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../useracc/login/login.php");
    exit();
}
?>

<?php
// Include database connection
include '../../../databse/db_connection.php';

session_start();

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
  <link rel="stylesheet" href="../user.css">
  <link rel="stylesheet" href="wishlist.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
    }
    
    header {
      background: white;
      padding: 1rem 2rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    header h1 {
      margin: 0;
      color: #e91e63;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    header p {
      margin: 0.25rem 0 0 0;
      color: #6c757d;
    }
    
    nav {
      display: flex;
      gap: 1rem;
      margin: 1rem 0;
    }
    
    nav a {
      text-decoration: none;
      color: #007bff;
      padding: 0.5rem 1rem;
      border: 1px solid #007bff;
      border-radius: 4px;
      transition: all 0.3s ease;
    }
    
    nav a:hover {
      background: #007bff;
      color: white;
    }
    
    #logout {
      background: #dc3545;
      color: white;
      border-color: #dc3545;
    }
    
    #logout:hover {
      background: #c82333;
      border-color: #c82333;
    }
    
    main {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    
    #wishlist-items {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    
    #wishlist-items h2 {
      background: #f8f9fa;
      margin: 0;
      padding: 1.5rem;
      border-bottom: 1px solid #dee2e6;
      color: #495057;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #dee2e6;
    }
    
    th {
      background: #f8f9fa;
      font-weight: bold;
      color: #495057;
    }
    
    .wishlist-item {
      transition: background-color 0.3s;
    }
    
    .wishlist-item:hover {
      background: #f8f9fa;
    }
    
    .product-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .product-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid #dee2e6;
    }
    
    .product-name {
      font-weight: 600;
      color: #495057;
      margin-bottom: 0.25rem;
    }
    
    .product-description {
      color: #6c757d;
      font-size: 0.9em;
      margin: 0;
    }
    
    .price {
      font-weight: bold;
      color: #e91e63;
      font-size: 1.1em;
    }
    
    .actions {
      display: flex;
      gap: 0.5rem;
    }
    
    .btn {
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      font-size: 0.9em;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .btn-cart {
      background: #28a745;
      color: white;
    }
    
    .btn-cart:hover {
      background: #218838;
    }
    
    .btn-remove {
      background: #dc3545;
      color: white;
    }
    
    .btn-remove:hover {
      background: #c82333;
    }
    
    .empty-wishlist {
      text-align: center;
      padding: 3rem;
      color: #6c757d;
    }
    
    .empty-wishlist i {
      font-size: 4rem;
      color: #e91e63;
      margin-bottom: 1rem;
    }
    
    .empty-wishlist p {
      margin: 0.5rem 0;
      font-size: 1.1em;
    }
    
    .continue-shopping {
      display: inline-block;
      margin-top: 1rem;
      padding: 0.75rem 1.5rem;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.3s;
    }
    
    .continue-shopping:hover {
      background: #0056b3;
    }
    
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1rem 1.5rem;
      border-radius: 4px;
      color: white;
      font-weight: 600;
      z-index: 1000;
      opacity: 0;
      transform: translateX(100%);
      transition: all 0.3s;
    }
    
    .notification.show {
      opacity: 1;
      transform: translateX(0);
    }
    
    .notification.success {
      background: #28a745;
    }
    
    .notification.error {
      background: #dc3545;
    }
    
    .added-date {
      color: #6c757d;
      font-size: 0.8em;
      margin-top: 0.25rem;
    }
  </style>
</head>
<body>
  <header>
    <div>
      <h1><i class="fas fa-heart"></i> My Wishlist</h1>
      <p>Your favorite items all in one place</p>
    </div>
    <nav>
      <a href="../../main/index/index.php">Home</a>
      <a href="../../main/cart/cart.php">Cart</a>
      <a href="../dashboard/dashboard.php">Dashboard</a>
      <a href="?logout=1" id="logout">Logout</a>
      <a href="../orderhistory/orderhistory.php">Order History</a>
      <a href="../adress/adress.php">Address Book</a>
      <a href="../wishlist/wishlist.php">Wishlist</a>
    </nav>
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