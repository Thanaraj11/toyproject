<?php
// main/checkout/review/review.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../../useracc/login/login.php");
    exit();
}
?>

<?php
// Start session and include database connection
// session_start();
require_once '../../../../databse/db_connection.php';

// Include review functions
include 'review_backend.php';

// Calculate order summary
$order_summary = calculateOrderSummary();
$cart_items = $order_summary['items'];

// Check if cart is empty
if (empty($cart_items)) {
    header('Location: ../cart/cart.php');
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="review.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Checkout - Review Order</title>
  <style>
    /* Review Order Specific Styles */

  </style>
</head>
<body>
  

  <main class="container">
    <nav aria-label="Breadcrumb" class="breadcrumb">
      <ol>
        <li><a href="../index/index.php">Home</a></li>
        <li><a href="../cart/cart.php">Shopping Cart</a></li>
        <li aria-current="page">Review Order</li>
      </ol>
    </nav>

    <div class="checkout-steps">
      <div class="step">
        <div class="step-number">1</div>
        <div class="step-text">Shopping Cart</div>
      </div>
      <div class="step active">
        <div class="step-number">2</div>
        <div class="step-text">Review Order</div>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <div class="step-text">Shipping</div>
      </div>
      <div class="step">
        <div class="step-number">4</div>
        <div class="step-text">Payment</div>
      </div>
      <div class="step">
        <div class="step-number">5</div>
        <div class="step-text">Confirmation</div>
      </div>
    </div>

    <section id="order-summary">
      <h2>Review Your Order</h2>
      
      <h3>Order Items</h3>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th style="text-align: center;">Quantity</th>
            <th style="text-align: right;">Price</th>
            <th style="text-align: right;">Total</th>
          </tr>
        </thead>
        <tbody id="summary-body">
          <?php foreach ($cart_items as $item): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
            <td style="text-align: right;">$<?php echo number_format($item['price'], 2); ?></td>
            <td style="text-align: right;">$<?php echo number_format($item['total'], 2); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <div style="float: right; width: 300px;">
        <div style="margin-bottom: 10px;">
          <strong>Subtotal:</strong> 
          <span style="float: right;">$<?php echo number_format($order_summary['subtotal'], 2); ?></span>
        </div>
        
        <div style="margin-bottom: 10px;">
          <strong>Shipping:</strong> 
          <span style="float: right;"><?php echo getShippingMethodName($order_summary['shipping_method']); ?></span>
        </div>
        
        <div class="summary-total">
          <strong>Order Total:</strong> 
          <span style="float: right;">$<?php echo number_format($order_summary['order_total'], 2); ?></span>
        </div>
      </div>
      
      <div style="clear: both; margin-top: 30px;">
        <!-- Updated: Redirect to shipping.php instead of payment.php -->
        <form method="POST" action="../shipping/shipping.php">
          <button type="submit" name="proceed_to_shipping" class="btn-primary">
            <i class="fas fa-shipping-fast"></i> Proceed to Shipping
          </button>
          
          <a href="../../cart/cart.php" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Cart
          </a>
        </form>
      </div>
    </section>
  </main>


</body>
</html>