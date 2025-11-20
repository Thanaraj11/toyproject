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
  <!-- <link rel="stylesheet" href="../style1.css">
  <link rel="stylesheet" href="cart.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Shopping Cart - ToyBox</title>
  <style>
    /* Cart Specific Styles */
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
  --error-red: #f44336;
  --warning-orange: #ff9800;
}

/* Main Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem 2rem;
}

/* Breadcrumb */
.breadcrumb {
  margin: 1rem 0 2rem;
}

.breadcrumb ol {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  gap: 0.5rem;
}

.breadcrumb a {
  color: var(--dark-blue);
  text-decoration: none;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

.breadcrumb li[aria-current="page"] {
  color: var(--text-gray);
}

/* Alert Messages */
.alert {
  padding: 1rem;
  border-radius: 6px;
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.alert-success {
  background: #e8f5e8;
  color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.alert-error {
  background: #ffebee;
  color: #c62828;
  border: 1px solid #ffcdd2;
}

/* Cart Items Section */
#cart-items {
  background: var(--primary-white);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 2rem;
}

#cart-items h2 {
  color: var(--primary-black);
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

/* Empty Cart */
.empty-cart {
  text-align: center;
  padding: 3rem 2rem;
  color: var(--text-gray);
}

.empty-cart i {
  margin-bottom: 1rem;
  color: var(--medium-gray);
}

.empty-cart h3 {
  color: var(--dark-gray);
  margin-bottom: 0.5rem;
}

.empty-cart p {
  margin-bottom: 1.5rem;
}

/* Cart Table */
.cart-table-container {
  overflow-x: auto;
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1.5rem;
}

.cart-table th {
  background: var(--light-blue);
  color: var(--primary-black);
  font-weight: 600;
  text-align: left;
  padding: 1rem;
  border-bottom: 2px solid var(--medium-blue);
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

.cart-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--medium-gray);
  vertical-align: top;
}

.cart-item:hover {
  background: var(--light-gray);
}

.cart-item.out-of-stock {
  background: #fff8f8;
  opacity: 0.7;
}

/* Cart Product */
.cart-product {
  min-width: 300px;
}

.product-info {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.cart-item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid var(--medium-gray);
}

.product-details {
  flex: 1;
}

.product-name {
  color: var(--primary-black);
  font-weight: 600;
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
}

.product-category {
  color: var(--text-gray);
  font-size: 0.8rem;
  margin: 0 0 0.5rem 0;
  text-transform: uppercase;
}

.stock-warning {
  color: var(--error-red);
  font-size: 0.8rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

/* Cart Price */
.cart-price {
  color: var(--dark-blue);
  font-weight: 600;
  font-size: 1.1rem;
  white-space: nowrap;
}

.original-price {
  color: var(--text-gray);
  text-decoration: line-through;
  font-size: 0.9rem;
  font-weight: 400;
}

/* Cart Quantity */
.cart-quantity {
  min-width: 120px;
}

.quantity-form {
  margin: 0;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.qty-decrease,
.qty-increase {
  background: var(--light-gray);
  border: 1px solid var(--medium-gray);
  width: 32px;
  height: 32px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.qty-decrease:hover:not(:disabled),
.qty-increase:hover:not(:disabled) {
  background: var(--medium-gray);
}

.qty-decrease:disabled,
.qty-increase:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.cart-qty {
  width: 50px;
  height: 32px;
  border: 1px solid var(--medium-gray);
  border-radius: 4px;
  text-align: center;
  background: var(--primary-white);
  font-size: 0.9rem;
}

/* Cart Total */
.cart-total {
  color: var(--primary-black);
  font-weight: 600;
  font-size: 1.1rem;
  white-space: nowrap;
}

/* Cart Action */
.cart-action {
  text-align: center;
}

.remove {
  background: var(--error-red);
  color: var(--primary-white);
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.remove:hover {
  background: #d32f2f;
  transform: scale(1.1);
}

/* Cart Actions */
.cart-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  padding-top: 1rem;
  border-top: 1px solid var(--medium-gray);
}

.btn-secondary {
  background: var(--light-gray);
  color: var(--dark-gray);
  border: 1px solid var(--medium-gray);
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.btn-secondary:hover {
  background: var(--medium-gray);
  transform: translateY(-1px);
}

/* Cart Summary */
#cart-summary {
  background: var(--primary-white);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.summary-card {
  max-width: 400px;
  margin-left: auto;
}

.summary-card h2 {
  color: var(--primary-black);
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  color: var(--dark-gray);
}

.summary-row.total {
  font-size: 1.2rem;
  color: var(--primary-black);
  border-top: 1px solid var(--medium-gray);
  margin-top: 0.5rem;
}

.summary-divider {
  height: 1px;
  background: var(--medium-gray);
  margin: 1rem 0;
}

/* Shipping Notice */
.shipping-notice {
  background: var(--light-blue);
  color: var(--dark-blue);
  padding: 0.75rem;
  border-radius: 6px;
  margin: 1rem 0;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.shipping-notice.free {
  background: #e8f5e8;
  color: #2e7d32;
}

/* Checkout Button */
.checkout-btn {
  width: 100%;
  background: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin: 1.5rem 0 1rem;
}

.checkout-btn:hover {
  background: #1565c0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
}

/* Payment Methods */
.payment-methods {
  text-align: center;
  padding-top: 1rem;
  border-top: 1px solid var(--medium-gray);
}

.payment-methods p {
  color: var(--text-gray);
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.payment-icons {
  display: flex;
  justify-content: center;
  gap: 1rem;
  font-size: 1.5rem;
  color: var(--dark-gray);
}

/* Primary Button */
.btn-primary {
  background: var(--dark-blue);
  color: var(--primary-white);
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-block;
  border: none;
  cursor: pointer;
}

.btn-primary:hover {
  background: #1565c0;
  transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .container {
    padding: 0 1rem 1.5rem;
  }
  
  #cart-items,
  #cart-summary {
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .product-info {
    flex-direction: column;
    text-align: center;
    gap: 0.5rem;
  }
  
  .cart-item-image {
    width: 60px;
    height: 60px;
  }
  
  .cart-table th,
  .cart-table td {
    padding: 0.75rem 0.5rem;
    font-size: 0.9rem;
  }
  
  .summary-card {
    max-width: none;
    margin-left: 0;
  }
  
  .cart-actions {
    flex-direction: column;
  }
  
  .btn-secondary {
    text-align: center;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 0.5rem 1rem;
  }
  
  #cart-items,
  #cart-summary {
    padding: 1rem;
  }
  
  .cart-table {
    font-size: 0.8rem;
  }
  
  .cart-table th {
    padding: 0.5rem 0.25rem;
    font-size: 0.75rem;
  }
  
  .quantity-controls {
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .qty-decrease,
  .qty-increase {
    width: 28px;
    height: 28px;
  }
  
  .cart-qty {
    width: 40px;
    height: 28px;
  }
  
  .remove {
    width: 32px;
    height: 32px;
  }
}

/* Focus Styles */
button:focus,
input:focus,
select:focus,
a:focus {
  outline: 2px solid var(--dark-blue);
  outline-offset: 2px;
}

/* Print Styles */
@media print {
  .cart-actions,
  #cart-summary,
  .breadcrumb {
    display: none !important;
  }
  
  #cart-items {
    box-shadow: none;
    border: 1px solid var(--primary-black);
  }
  
  .cart-table th {
    background: var(--light-gray) !important;
  }
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