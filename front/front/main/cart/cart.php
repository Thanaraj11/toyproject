<?php
// main/cart/cart.php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../useracc/login/login.php");
    exit();
}
?>

<?php
// Start session to store cart data
// session_start();

// Include database connection
require_once '../../../databse/db_connection.php';
// Redirect to login if not logged in
// if (!isLoggedIn()) {
//     header("Location: ../../useracc/login/login.php");
//     exit();
// }

// Include cart functions
include 'cart_backend.php';

$cart_items = getCartItems($conn);
$cart_total = getCartTotal($conn);
$cart_count = getCartCount();

// Handle messages
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../style1.css">
  <link rel="stylesheet" href="cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Shopping Cart - ToyBox</title>
  <style>
    /* Remove button */
    .remove {
      background-color: #ff4757;
      color: white;
      border: none;
      padding: 0.5rem 0.8rem;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .remove:hover {
      background-color: #ff2e43;
    }
    
    .qty-decrease, .qty-increase {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 0.3rem 0.6rem;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .cart-qty {
      width: 50px;
      text-align: center;
      margin: 0 0.5rem;
    }
    
    .alert {
      padding: 10px;
      margin: 10px 0;
      border-radius: 4px;
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .stock-warning {
      color: #ff6b6b;
      font-size: 0.9em;
      margin-top: 5px;
    }
    
    .cart-item-image {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }
    
    .out-of-stock {
      opacity: 0.6;
      background-color: #f8f9fa;
    }
    
    .original-price {
      text-decoration: line-through;
      color: #6c757d;
      font-size: 0.9em;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
      
    }
    
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    
    th {
      background-color: #f8f9fa;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <?php include '../header1.php'; ?>

  <main class="container">
    <?php if ($message === 'added'): ?>
      <div class="alert alert-success">Product added to cart successfully!</div>
    <?php elseif ($message === 'removed'): ?>
      <div class="alert alert-success">Product removed from cart!</div>
    <?php elseif ($message === 'updated'): ?>
      <div class="alert alert-success">Cart updated successfully!</div>
    <?php elseif ($message === 'cleared'): ?>
      <div class="alert alert-success">Cart cleared successfully!</div>
    <?php elseif ($error === 'not_found'): ?>
      <div class="alert alert-error">Product not found!</div>
    <?php elseif ($error === 'out_of_stock'): ?>
      <div class="alert alert-error">Some products are out of stock!</div>
    <?php endif; ?>
    
    <nav aria-label="Breadcrumb" class="breadcrumb">
      <ol>
        <li><a href="../index/index.php">Home</a></li>
        <li aria-current="page">Shopping Cart</li>
      </ol>
    </nav>
    
    <section id="cart-items">
      <h2>Your Shopping Cart (<?php echo $cart_count; ?> items)</h2>
      <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
          <i class="fas fa-shopping-cart fa-3x"></i>
          <h3>Your cart is empty</h3>
          <p>Browse our products and add some items to your cart.</p>
          <a href="../index/index.php" class="btn-primary">Continue Shopping</a>
        </div>
      <?php else: ?>
        <div class="cart-table-container">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="cart-body">
              <?php foreach ($cart_items as $item): ?>
                <tr class="cart-item <?php echo $item['current_stock'] == 0 ? 'out-of-stock' : ''; ?>">
                  <td class="cart-product">
                    <div class="product-info">
                      <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                           alt="<?php echo htmlspecialchars($item['name']); ?>" 
                           class="cart-item-image">
                      <div class="product-details">
                        <h4 class="product-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p class="product-category"><?php echo htmlspecialchars($item['category_name']); ?></p>
                        <?php if ($item['current_stock'] == 0): ?>
                          <div class="stock-warning">
                            <i class="fas fa-exclamation-triangle"></i> Out of Stock
                          </div>
                        <?php elseif ($item['quantity'] > $item['current_stock']): ?>
                          <div class="stock-warning">
                            <i class="fas fa-exclamation-triangle"></i> Only <?php echo $item['current_stock']; ?> available
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td class="cart-price">
                    $<?php echo number_format($item['price'], 2); ?>
                    <?php if ($item['original_price'] && $item['original_price'] > $item['price']): ?>
                      <br><span class="original-price">$<?php echo number_format($item['original_price'], 2); ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="cart-quantity">
                    <form method="POST" action="cart_backend.php" class="quantity-form">
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                      <div class="quantity-controls">
                        <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" 
                                class="qty-decrease" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>-</button>
                        <input type="number" class="cart-qty" value="<?php echo $item['quantity']; ?>" 
                               min="1" max="<?php echo $item['current_stock']; ?>" readonly>
                        <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" 
                                class="qty-increase" <?php echo $item['quantity'] >= $item['current_stock'] ? 'disabled' : ''; ?>>+</button>
                      </div>
                    </form>
                  </td>
                  <td class="cart-total">
                    $<?php echo number_format($item['subtotal'], 2); ?>
                  </td>
                  <td class="cart-action">
                    <form method="POST" action="cart_backend.php">
                      <input type="hidden" name="action" value="remove">
                      <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                      <button type="submit" class="remove" title="Remove item">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        
        <div class="cart-actions">
          <form method="POST" action="cart_backend.php">
            <input type="hidden" name="action" value="clear">
            <button type="submit" class="btn-secondary" onclick="return confirm('Are you sure you want to clear your cart?')">
              <i class="fas fa-trash"></i> Clear Cart
            </button>
          </form>
          <a href="../index/index.php" class="btn-secondary">
            <i class="fas fa-shopping-bag"></i> Continue Shopping
          </a>
        </div>
      <?php endif; ?>
    </section>

    <?php if (!empty($cart_items)): ?>
    <section id="cart-summary">
      <div class="summary-card">
        <h2>Order Summary</h2>
        
        <div class="summary-row">
          <span>Subtotal (<?php echo $cart_count; ?> items):</span>
          <span>$<?php echo number_format($cart_total, 2); ?></span>
        </div>
        
        <div class="summary-row">
          <span>Shipping:</span>
          <span>
            <?php if ($cart_total > 50): ?>
              FREE
            <?php else: ?>
              $5.99
            <?php endif; ?>
          </span>
        </div>
        
        <div class="summary-row">
          <span>Tax:</span>
          <span>$<?php echo number_format($cart_total * 0.08, 2); ?></span>
        </div>
        
        <div class="summary-divider"></div>
        
        <div class="summary-row total">
          <span><strong>Total:</strong></span>
          <span><strong>
            $<?php 
            $shipping = $cart_total > 50 ? 0 : 5.99;
            $tax = $cart_total * 0.08;
            echo number_format($cart_total + $shipping + $tax, 2); 
            ?>
          </strong></span>
        </div>
        
        <?php if ($cart_total < 50): ?>
          <div class="shipping-notice">
            <i class="fas fa-shipping-fast"></i>
            Add $<?php echo number_format(50 - $cart_total, 2); ?> more for free shipping!
          </div>
        <?php else: ?>
          <div class="shipping-notice free">
            <i class="fas fa-check-circle"></i>
            You qualify for free shipping!
          </div>
        <?php endif; ?>
        
        <!-- Updated: Redirect to review.php instead of checkout.php -->
        <form action="../checkout/review/review.php" method="GET">
          <button type="submit" id="checkout" class="btn-primary checkout-btn">
            <i class="fas fa-lock"></i> Proceed to Checkout
          </button>
        </form>
        
        <div class="payment-methods">
          <p>We accept:</p>
          <div class="payment-icons">
            <i class="fab fa-cc-visa"></i>
            <i class="fab fa-cc-mastercard"></i>
            <i class="fab fa-cc-amex"></i>
            <i class="fab fa-cc-paypal"></i>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>
  </main>

  <?php include '../footer.php'; ?>
</body>
</html>