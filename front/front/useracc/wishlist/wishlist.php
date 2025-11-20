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
  <!-- <link rel="stylesheet" href="../user.css">
  <link rel="stylesheet" href="wishlist.css"> -->
  <style>
    /* Wishlist Specific Styles */
:root {
  --primary-black: #000000;
  --primary-white: #ffffff;
  --light-blue: #e3f2fd;
  --medium-blue: #90caf9;
  --dark-blue: #1976d2;
  --light-gray: #f5f5f5;
  --medium-gray: #e0e0e0;
  --dark-gray: #424242;
  --text-gray: #757575;
  --success-green: #4caf50;
  --warning-orange: #ff9800;
  --error-red: #f44336;
  --heart-red: #e91e63;
}

/* Main Layout */
body {
  background-color: var(--light-gray);
  color: var(--primary-black);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  margin: 0;
  padding: 0;
}

/* Header Styles */
header {
  background-color: var(--primary-black);
  color: var(--primary-white);
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

header > div h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

header > div h1 i {
  color: var(--heart-red);
}

header > div p {
  margin: 0.25rem 0 0 0;
  color: var(--medium-gray);
  font-size: 0.9rem;
}

nav {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

nav a {
  color: var(--primary-white);
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: background-color 0.3s ease;
  font-size: 0.9rem;
}

nav a:hover {
  background-color: var(--dark-gray);
}

#logout {
  background-color: #d32f2f;
}

#logout:hover {
  background-color: #b71c1c;
}

/* Main Content */
main {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}

/* Notifications */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 1rem 1.5rem;
  border-radius: 6px;
  font-weight: 600;
  z-index: 1000;
  transform: translateX(400px);
  opacity: 0;
  transition: all 0.3s ease;
  max-width: 400px;
}

.notification.show {
  transform: translateX(0);
  opacity: 1;
}

.notification.success {
  background-color: var(--success-green);
  color: var(--primary-white);
  border-left: 4px solid #2e7d32;
}

.notification.error {
  background-color: var(--error-red);
  color: var(--primary-white);
  border-left: 4px solid #c62828;
}

/* Wishlist Items Section */
#wishlist-items {
  background-color: var(--primary-white);
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#wishlist-items h2 {
  color: var(--primary-black);
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

#wishlist-items h2 i {
  color: var(--heart-red);
}

/* Empty Wishlist State */
.empty-wishlist {
  text-align: center;
  padding: 4rem 2rem;
  background-color: var(--light-gray);
  border-radius: 8px;
  border: 2px dashed var(--medium-gray);
}

.empty-wishlist i {
  font-size: 4rem;
  color: var(--medium-gray);
  margin-bottom: 1rem;
}

.empty-wishlist p {
  color: var(--text-gray);
  font-size: 1.1rem;
  margin: 0.5rem 0;
}

.empty-wishlist p:first-of-type {
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--dark-gray);
}

.continue-shopping {
  display: inline-block;
  margin-top: 1.5rem;
  padding: 0.75rem 1.5rem;
  background-color: var(--dark-blue);
  color: var(--primary-white);
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.continue-shopping:hover {
  background-color: #1565c0;
  transform: translateY(-2px);
}

/* Table Styles */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

thead {
  background-color: var(--light-blue);
  border-bottom: 2px solid var(--medium-blue);
}

th {
  color: var(--primary-black);
  font-weight: 600;
  text-align: left;
  padding: 1rem;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

tbody tr {
  border-bottom: 1px solid var(--medium-gray);
  transition: background-color 0.3s ease;
}

tbody tr:hover {
  background-color: var(--light-gray);
}

td {
  padding: 1.5rem 1rem;
  vertical-align: top;
}

/* Product Info */
.product-info {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.product-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid var(--medium-gray);
}

.product-image i {
  font-size: 2rem;
}

.product-name {
  font-weight: 600;
  color: var(--primary-black);
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.product-description {
  color: var(--text-gray);
  font-size: 0.9rem;
  margin: 0.25rem 0;
  line-height: 1.4;
}

.added-date {
  color: var(--text-gray);
  font-size: 0.8rem;
  font-style: italic;
  margin-top: 0.5rem;
}

/* Price */
.price {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--dark-blue);
  text-align: center;
}

/* Actions */
.actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  min-width: 140px;
}

.actions form {
  margin: 0;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1rem;
  border: none;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  width: 100%;
  justify-content: center;
}

.btn-cart {
  background-color: var(--dark-blue);
  color: var(--primary-white);
}

.btn-cart:hover {
  background-color: #1565c0;
  transform: translateY(-1px);
}

.btn-remove {
  background-color: var(--light-gray);
  color: var(--dark-gray);
  border: 1px solid var(--medium-gray);
}

.btn-remove:hover {
  background-color: var(--error-red);
  color: var(--primary-white);
  border-color: var(--error-red);
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  table {
    font-size: 0.9rem;
  }
  
  .product-image {
    width: 60px;
    height: 60px;
  }
  
  .actions {
    min-width: 120px;
  }
}

@media (max-width: 768px) {
  header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
    padding: 1rem;
  }
  
  nav {
    justify-content: center;
  }
  
  main {
    margin: 1rem auto;
    padding: 0 0.5rem;
  }
  
  #wishlist-items {
    padding: 1rem;
    overflow-x: auto;
  }
  
  table {
    min-width: 700px;
  }
  
  .product-info {
    gap: 0.75rem;
  }
  
  .btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
  }
}

@media (max-width: 480px) {
  nav {
    flex-direction: column;
    width: 100%;
  }
  
  nav a {
    text-align: center;
    width: 100%;
  }
  
  .empty-wishlist {
    padding: 3rem 1rem;
  }
  
  .empty-wishlist i {
    font-size: 3rem;
  }
  
  .continue-shopping {
    width: 100%;
    text-align: center;
  }
  
  .notification {
    right: 10px;
    left: 10px;
    max-width: none;
  }
}

/* Animation for wishlist items */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.wishlist-item {
  animation: fadeInUp 0.5s ease-out;
}

.wishlist-item:nth-child(1) { animation-delay: 0.1s; }
.wishlist-item:nth-child(2) { animation-delay: 0.2s; }
.wishlist-item:nth-child(3) { animation-delay: 0.3s; }
.wishlist-item:nth-child(4) { animation-delay: 0.4s; }
.wishlist-item:nth-child(5) { animation-delay: 0.5s; }

/* Heart icon animation */
@keyframes heartBeat {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

header h1 i {
  animation: heartBeat 2s ease-in-out infinite;
}

/* Print Styles */
@media print {
  nav,
  .actions {
    display: none !important;
  }
  
  .empty-wishlist {
    border: 1px solid var(--primary-black);
  }
  
  .product-image {
    border: 1px solid var(--primary-black);
  }
}
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