<?php
// main/checkout/review/review.php
session_start();
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
  <link rel="stylesheet" href="../../style1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Checkout - Review Order</title>
  <style>
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .checkout-steps {
      display: flex;
      justify-content: center;
      margin-bottom: 30px;
    }
    
    .step {
      display: flex;
      align-items: center;
      margin: 0 20px;
    }
    
    .step-number {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: #007bff;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
    }
    
    .step.active .step-number {
      background-color: #28a745;
    }
    
    .step-text {
      font-weight: bold;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
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
    
    .btn-primary {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px 24px;
      font-size: 1.1em;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }
    
    .btn-secondary {
      background-color: #6c757d;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 4px;
      display: inline-block;
      margin-left: 10px;
    }
    
    .summary-total {
      font-size: 1.2em;
      font-weight: bold;
      margin: 1rem 0;
      padding-top: 10px;
      border-top: 2px solid #dee2e6;
    }
  </style>
</head>
<body>
  <?php include '../../header1.php'; ?>

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

  <?php include '../../footer.php'; ?>
</body>
</html>