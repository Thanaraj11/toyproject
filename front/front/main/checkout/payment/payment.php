<?php
// main/checkout/payment/payment.php
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

// Include payment functions
include 'payment_backend.php';

// Check if shipping info is completed
if (!isset($_SESSION['shipping_info']) || !isset($_SESSION['shipping_method'])) {
    header('Location: ../shipping/shipping.php');
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../cart/cart.php');
    exit();
}

// Calculate order summary
$order_summary = calculateOrderSummary();

// Check for error message from backend
$error_message = isset($_SESSION['payment_error']) ? $_SESSION['payment_error'] : '';
unset($_SESSION['payment_error']); // Clear error after displaying
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../../style1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Checkout - Payment</title>
  <style>
    .container {
      max-width: 1000px;
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
      margin: 0 10px;
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
      font-size: 0.9em;
    }
    
    .step.completed .step-number {
      background-color: #28a745;
    }
    
    .step.active .step-number {
      background-color: #007bff;
    }
    
    .step-text {
      font-weight: bold;
      font-size: 0.8em;
    }
    
    .checkout-layout {
      display: grid;
      grid-template-columns: 1fr 350px;
      gap: 2rem;
    }
    
    .section {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 1rem;
    }
    
    .form-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    
    .form-group {
      flex: 1;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }
    
    .form-group input, .form-group select {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 1em;
    }
    
    .payment-option {
      margin-bottom: 1rem;
      padding: 1rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .payment-option.selected {
      border-color: #007bff;
      background-color: #f8f9ff;
    }
    
    .btn-primary {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 15px 30px;
      font-size: 1.1em;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
      margin-top: 1rem;
    }
    
    .btn-primary:hover {
      background-color: #0056b3;
    }
    
    .btn-secondary {
      background-color: #6c757d;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 4px;
      display: inline-block;
      text-align: center;
    }
    
    .order-summary-item {
      display: flex;
      justify-content: between;
      margin-bottom: 0.5rem;
    }
    
    .order-total {
      font-size: 1.2em;
      font-weight: bold;
      border-top: 2px solid #dee2e6;
      padding-top: 1rem;
      margin-top: 1rem;
    }
    
    .error-message {
      color: #dc3545;
      background: #f8d7da;
      padding: 1rem;
      border: 1px solid #f5c6cb;
      border-radius: 4px;
      margin-bottom: 1rem;
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
        <li><a href="../review/review.php">Review Order</a></li>
        <li><a href="../shipping/shipping.php">Shipping</a></li>
        <li aria-current="page">Payment</li>
      </ol>
    </nav>

    <div class="checkout-steps">
      <div class="step completed">
        <div class="step-number"><i class="fas fa-check"></i></div>
        <div class="step-text">Cart</div>
      </div>
      <div class="step completed">
        <div class="step-number"><i class="fas fa-check"></i></div>
        <div class="step-text">Review</div>
      </div>
      <div class="step completed">
        <div class="step-number"><i class="fas fa-check"></i></div>
        <div class="step-text">Shipping</div>
      </div>
      <div class="step active">
        <div class="step-number">4</div>
        <div class="step-text">Payment</div>
      </div>
      <div class="step">
        <div class="step-number">5</div>
        <div class="step-text">Confirm</div>
      </div>
    </div>

    <?php if (!empty($error_message)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="checkout-layout">
      <div>
        <form id="payment-form" method="POST" action="payment_backend.php">
          <input type="hidden" name="submit_payment" value="1">
          
          <section class="section">
            <h2>Payment Method</h2>
            
            <div class="payment-option selected">
              <input type="radio" id="credit-card" name="payment_method" value="credit_card" checked>
              <label for="credit-card">
                <strong><i class="fas fa-credit-card"></i> Credit/Debit Card</strong>
              </label>
              
              <div id="credit-card-form" style="margin-top: 1rem;">
                <div class="form-row">
                  <div class="form-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="card_name">Name on Card</label>
                    <input type="text" id="card_name" name="card_name" required>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="expiry_date">Expiry Date</label>
                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" maxlength="5" required>
                  </div>
                  <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="payment-option">
              <input type="radio" id="paypal" name="payment_method" value="paypal">
              <label for="paypal">
                <strong><i class="fab fa-paypal"></i> PayPal</strong>
              </label>
            </div>
          </section>

          <section class="section">
            <h2>Billing Address</h2>
            <div class="form-group">
              <label>
                <input type="checkbox" id="same-as-shipping" name="same_as_shipping" checked>
                Same as shipping address
              </label>
            </div>
            
            <div id="billing-address" style="display: none;">
              <!-- Billing address fields would go here -->
            </div>
          </section>

          <button type="submit" class="btn-primary">
           <a href="../../orderconfirm/orderconfirm.php"> <i class="fas fa-lock"></i> Complete Order</a>
          </button>
          
          <a href="../shipping/shipping.php" class="btn-secondary" style="display: block; text-align: center; margin-top: 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Shipping
          </a>
        </form>
      </div>

      <div>
        <section class="section">
          <h2>Order Summary</h2>
          
          <div class="order-summary-item">
            <span>Subtotal (<?php echo count($order_summary['items']); ?> items):</span>
            <span>$<?php echo number_format($order_summary['subtotal'], 2); ?></span>
          </div>
          
          <div class="order-summary-item">
            <span>Shipping:</span>
            <span>$<?php echo number_format($order_summary['shipping_cost'], 2); ?></span>
          </div>
          
          <div class="order-total">
            <span>Total:</span>
            <span>$<?php echo number_format($order_summary['order_total'], 2); ?></span>
          </div>
        </section>

        <section class="section">
          <h2>Shipping Information</h2>
          <?php $shipping = $_SESSION['shipping_info']; ?>
          <p>
            <strong><?php echo htmlspecialchars($shipping['fullname']); ?></strong><br>
            <?php echo htmlspecialchars($shipping['address1']); ?><br>
            <?php if (!empty($shipping['address2'])): ?>
              <?php echo htmlspecialchars($shipping['address2']); ?><br>
            <?php endif; ?>
            <?php echo htmlspecialchars($shipping['city']); ?>, 
            <?php echo htmlspecialchars($shipping['postal']); ?><br>
            <?php echo htmlspecialchars($shipping['country']); ?>
          </p>
          <p>
            <strong>Shipping Method:</strong><br>
            <?php echo getShippingMethodName($_SESSION['shipping_method']); ?>
          </p>
        </section>
      </div>
    </div>
  </main>

  <?php include '../../footer.php'; ?>

  <script>
    // Handle same as shipping checkbox
    document.getElementById('same-as-shipping').addEventListener('change', function() {
      document.getElementById('billing-address').style.display = this.checked ? 'none' : 'block';
    });

    // Handle payment method selection
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
      radio.addEventListener('change', function() {
        document.querySelectorAll('.payment-option').forEach(option => {
          option.classList.remove('selected');
        });
        this.closest('.payment-option').classList.add('selected');
      });
    });

    // Format card number input
    document.getElementById('card_number').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
      let formattedValue = value.match(/.{1,4}/g)?.join(' ');
      if (formattedValue) {
        e.target.value = formattedValue;
      }
    });

    // Format expiry date input
    document.getElementById('expiry_date').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\//g, '').replace(/[^0-9]/gi, '');
      if (value.length >= 2) {
        e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
      }
    });

    // Restrict CVV to numbers only
    document.getElementById('cvv').addEventListener('input', function(e) {
      e.target.value = e.target.value.replace(/[^0-9]/gi, '');
    });
  </script>
</body>
</html>