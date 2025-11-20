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
  <!-- <link rel="stylesheet" href="../../style1.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Checkout - Review Order</title>
  <style>
    /* Review Order Specific Styles */
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
}

/* Main Container */
.container {
  max-width: 1000px;
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
  flex-wrap: wrap;
}

.breadcrumb a {
  color: var(--dark-blue);
  text-decoration: none;
  font-size: 0.9rem;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

.breadcrumb li[aria-current="page"] {
  color: var(--text-gray);
  font-size: 0.9rem;
}

/* Checkout Steps */
.checkout-steps {
  display: flex;
  justify-content: space-between;
  margin-bottom: 3rem;
  position: relative;
}

.checkout-steps::before {
  content: '';
  position: absolute;
  top: 20px;
  left: 0;
  right: 0;
  height: 2px;
  background: var(--medium-gray);
  z-index: 1;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  z-index: 2;
  flex: 1;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--light-gray);
  border: 2px solid var(--medium-gray);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: var(--text-gray);
  margin-bottom: 0.5rem;
  transition: all 0.3s ease;
}

.step.active .step-number {
  background: var(--dark-blue);
  border-color: var(--dark-blue);
  color: var(--primary-white);
}

.step-text {
  font-size: 0.8rem;
  color: var(--text-gray);
  text-align: center;
  font-weight: 500;
}

.step.active .step-text {
  color: var(--dark-blue);
  font-weight: 600;
}

/* Order Summary Section */
#order-summary {
  background: var(--primary-white);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#order-summary h2 {
  color: var(--primary-black);
  font-size: 1.8rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

#order-summary h3 {
  color: var(--dark-gray);
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

/* Order Items Table */
#order-summary table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
  background: var(--primary-white);
}

#order-summary th {
  background: var(--light-blue);
  color: var(--primary-black);
  font-weight: 600;
  padding: 1rem;
  text-align: left;
  border-bottom: 2px solid var(--medium-blue);
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

#order-summary th[style*="text-align: center"] {
  text-align: center;
}

#order-summary th[style*="text-align: right"] {
  text-align: right;
}

#order-summary td {
  padding: 1rem;
  border-bottom: 1px solid var(--medium-gray);
  color: var(--dark-gray);
}

#order-summary td[style*="text-align: center"] {
  text-align: center;
}

#order-summary td[style*="text-align: right"] {
  text-align: right;
  font-weight: 500;
}

#order-summary tr:last-child td {
  border-bottom: none;
}

#order-summary tr:hover {
  background: var(--light-gray);
}

/* Order Total Summary */
#order-summary > div[style*="float: right"] {
  background: var(--light-gray);
  padding: 1.5rem;
  border-radius: 8px;
  border: 1px solid var(--medium-gray);
  margin-bottom: 2rem;
}

#order-summary > div[style*="float: right"] > div {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  color: var(--dark-gray);
}

.summary-total {
  border-top: 2px solid var(--medium-gray);
  margin-top: 0.5rem;
  padding-top: 0.5rem !important;
  font-size: 1.1rem;
  color: var(--primary-black) !important;
}

/* Action Buttons */
#order-summary > div[style*="clear: both"] {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--medium-gray);
}

.btn-primary {
  background: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary:hover {
  background: #1565c0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
}

.btn-secondary {
  background: var(--light-gray);
  color: var(--dark-gray);
  border: 1px solid var(--medium-gray);
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-secondary:hover {
  background: var(--medium-gray);
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .container {
    padding: 0 0.5rem 1.5rem;
  }
  
  .checkout-steps {
    margin-bottom: 2rem;
  }
  
  .step-text {
    font-size: 0.7rem;
  }
  
  .step-number {
    width: 35px;
    height: 35px;
    font-size: 0.9rem;
  }
  
  #order-summary {
    padding: 1.5rem;
  }
  
  #order-summary h2 {
    font-size: 1.5rem;
  }
  
  #order-summary table {
    font-size: 0.9rem;
  }
  
  #order-summary th,
  #order-summary td {
    padding: 0.75rem 0.5rem;
  }
  
  #order-summary > div[style*="float: right"] {
    float: none !important;
    width: 100% !important;
    margin-bottom: 1.5rem;
  }
  
  #order-summary > div[style*="clear: both"] {
    flex-direction: column;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .checkout-steps {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .checkout-steps::before {
    display: none;
  }
  
  .step {
    flex-direction: row;
    gap: 1rem;
    width: 100%;
  }
  
  .step-text {
    text-align: left;
    font-size: 0.8rem;
  }
  
  #order-summary {
    padding: 1rem;
  }
  
  #order-summary table {
    font-size: 0.8rem;
  }
  
  #order-summary th,
  #order-summary td {
    padding: 0.5rem 0.25rem;
  }
  
  #order-summary th {
    font-size: 0.75rem;
  }
}

/* Focus Styles for Accessibility */
button:focus,
a:focus {
  outline: 2px solid var(--dark-blue);
  outline-offset: 2px;
}

/* Print Styles */
@media print {
  .breadcrumb,
  .checkout-steps,
  .btn-secondary {
    display: none !important;
  }
  
  #order-summary {
    box-shadow: none;
    border: 1px solid var(--primary-black);
  }
  
  .btn-primary {
    display: none !important;
  }
}
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