<?php
// main/checkout/payment/payment.php
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
  <!-- <link rel="stylesheet" href="../../style1.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Checkout - Payment</title>
  <style>
    /* Payment Page Specific Styles */
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

.step.completed .step-number {
  background: var(--success-green);
  border-color: var(--success-green);
  color: var(--primary-white);
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

.step.completed .step-text,
.step.active .step-text {
  color: var(--dark-blue);
  font-weight: 600;
}

/* Error Message */
.error-message {
  background: #ffebee;
  color: #c62828;
  padding: 1rem;
  border-radius: 6px;
  border-left: 4px solid var(--error-red);
  margin-bottom: 2rem;
  font-weight: 600;
}

/* Checkout Layout */
.checkout-layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

/* Sections */
.section {
  background: var(--primary-white);
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 1.5rem;
}

.section h2 {
  color: var(--primary-black);
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

/* Payment Form */
#payment-form {
  margin-bottom: 2rem;
}

/* Payment Options */
.payment-option {
  border: 2px solid var(--medium-gray);
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  background: var(--primary-white);
}

.payment-option:hover {
  border-color: var(--medium-blue);
  background: var(--light-gray);
}

.payment-option.selected {
  border-color: var(--dark-blue);
  background: var(--light-blue);
}

.payment-option input[type="radio"] {
  margin-right: 0.75rem;
  transform: scale(1.2);
}

.payment-option label {
  cursor: pointer;
  margin: 0;
  display: block;
  font-size: 1rem;
  color: var(--primary-black);
}

.payment-option label strong {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Form Layout */
.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.form-group {
  flex: 1;
  min-width: 150px;
}

.form-group label {
  display: block;
  color: var(--dark-gray);
  font-weight: 600;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--medium-gray);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--primary-white);
  color: var(--primary-black);
  transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: var(--dark-blue);
}

.form-group input::placeholder {
  color: var(--text-gray);
}

/* Checkbox Styling */
.form-group label input[type="checkbox"] {
  margin-right: 0.5rem;
  transform: scale(1.1);
}

/* Order Summary */
.order-summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  color: var(--dark-gray);
  border-bottom: 1px solid var(--medium-gray);
}

.order-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--primary-black);
  border-top: 2px solid var(--medium-gray);
  margin-top: 0.5rem;
}

/* Shipping Information */
.section p {
  color: var(--dark-gray);
  line-height: 1.5;
  margin-bottom: 1rem;
}

.section p strong {
  color: var(--primary-black);
}

/* Action Buttons */
.btn-primary {
  background: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 1rem 1.5rem;
  border-radius: 6px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  /* text-decoration: none; */
  display: block;
  width: 100%;
  text-align: center;
}

.btn-primary:hover {
  background: #1565c0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
}

.btn-primary a {
  color: var(--primary-white);
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
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
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
}

.btn-secondary:hover {
  background: var(--medium-gray);
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .checkout-layout {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

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
  
  .section {
    padding: 1rem;
  }
  
  .form-row {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .form-group {
    min-width: auto;
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
  
  .payment-option {
    padding: 0.75rem;
  }
  
  .payment-option label {
    font-size: 0.9rem;
  }
  
  .btn-primary {
    padding: 0.75rem 1rem;
    font-size: 1rem;
  }
}

/* Focus Styles for Accessibility */
button:focus,
input:focus,
select:focus,
a:focus {
  outline: 2px solid var(--dark-blue);
  outline-offset: 2px;
}

/* Card Input Specific Styling */
#card_number,
#expiry_date,
#cvv {
  font-family: monospace;
  letter-spacing: 1px;
}

/* Payment Icons */
.payment-option .fa-credit-card,
.payment-option .fa-paypal {
  color: var(--dark-blue);
}

.payment-option.selected .fa-credit-card,
.payment-option.selected .fa-paypal {
  color: var(--primary-white);
}

/* Print Styles */
@media print {
  .breadcrumb,
  .checkout-steps,
  .btn-primary,
  .btn-secondary {
    display: none !important;
  }
  
  .checkout-layout {
    grid-template-columns: 1fr;
  }
  
  .section {
    box-shadow: none;
    border: 1px solid var(--primary-black);
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
             <i class="fas fa-lock"></i> Complete Order
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