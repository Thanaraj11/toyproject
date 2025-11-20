<?php
// main/checkout/shipping/shipping.php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../../useracc/login/login.php");
    exit();
}
?>

<?php
// Start session and include database connection
session_start();
require_once '../../../../databse/db_connection.php';

// Include shipping functions
include 'shipping_backend.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../cart/cart.php');
    exit();
}

// Get previous form data if available
$previous_data = isset($_SESSION['shipping_info']) ? $_SESSION['shipping_info'] : [];
$shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : 'standard';

// Check for error message from backend
$error_message = isset($_SESSION['shipping_error']) ? $_SESSION['shipping_error'] : '';
unset($_SESSION['shipping_error']); // Clear error after displaying
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- <link rel="stylesheet" href="../../style1.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Checkout - Shipping Information</title>
  <style>
   /* Shipping Page Specific Styles */
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
  max-width: 800px;
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

/* Shipping Form */
#shipping-form {
  background: var(--primary-white);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Sections */
.section {
  margin-bottom: 2.5rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid var(--medium-gray);
}

.section:last-of-type {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.section h2 {
  color: var(--primary-black);
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
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
  min-width: 200px;
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



/* Radio Options for Shipping Method */
.radio-option {
  border: 2px solid var(--medium-gray);
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 0.75rem;
  cursor: pointer;
  transition: all 0.3s ease;
  background: var(--primary-white);
  display: inline-block;
}

.radio-option:hover {
  border-color: var(--medium-blue);
  background: var(--light-gray);
}

.radio-option.selected {
  border-color: var(--dark-blue);
  background: var(--light-blue);
}

.radio-option input[type="radio"] {
  margin-right: 0.75rem;
  transform: scale(1.2);
}

.radio-option label {
  cursor: pointer;
  margin: 0;
  display: block;
}

.radio-option strong {
  color: var(--primary-black);
  font-size: 1rem;
  display: block;
  margin-bottom: 0.25rem;
}

.radio-option small {
  color: var(--text-gray);
  font-size: 0.85rem;
}

/* Action Buttons */
#shipping-form > div[style*="display: flex"] {
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
  flex: 1;
  min-width: 200px;
  justify-content: center;
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
  flex: 1;
  min-width: 150px;
  justify-content: center;
  text-align: center;
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
  
  #shipping-form {
    padding: 1.5rem;
  }
  
  .form-row {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .form-group {
    min-width: auto;
  }
  
  #shipping-form > div[style*="display: flex"] {
    flex-direction: column;
  }
  
  .btn-primary,
  .btn-secondary {
    min-width: auto;
    width: 100%;
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
  
  #shipping-form {
    padding: 1rem;
  }
  
  .section h2 {
    font-size: 1.1rem;
  }
  
  .radio-option {
    padding: 0.75rem;
  }
  
  .radio-option strong {
    font-size: 0.9rem;
  }
  
  .radio-option small {
    font-size: 0.8rem;
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

/* Required Field Indicator */
.form-group label::after {
  content: " *";
  color: var(--error-red);
}

.form-group label:has(+ input:not([required]))::after {
  content: "";
}

/* Print Styles */
@media print {
  .breadcrumb,
  .checkout-steps,
  .btn-secondary {
    display: none !important;
  }
  
  #shipping-form {
    box-shadow: none;
    border: 1px solid var(--primary-black);
  }
  
  .btn-primary {
    display: none !important;
  }
  
  .radio-option {
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
        <li aria-current="page">Shipping Information</li>
      </ol>
    </nav>

    <div class="checkout-steps">
      <div class="step">
        <div class="step-number">1</div>
        <div class="step-text">Cart</div>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <div class="step-text">Review</div>
      </div>
      <div class="step active">
        <div class="step-number">3</div>
        <div class="step-text">Shipping</div>
      </div>
      <div class="step">
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
    
    <!-- FIX: Added action attribute to form -->
    <form id="shipping-form" method="POST" action="shipping.php">
      <input type="hidden" name="submit_shipping" value="1">
      
      <section class="section">
        <h2>Shipping Address</h2>
        
        <div class="form-row">
          <div class="form-group">
            <label for="fullname">Full Name *</label>
            <input type="text" id="fullname" name="fullname" required 
                   value="<?php echo isset($previous_data['fullname']) ? htmlspecialchars($previous_data['fullname']) : ''; ?>">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo isset($previous_data['email']) ? htmlspecialchars($previous_data['email']) : ''; ?>">
          </div>
          <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" required 
                   value="<?php echo isset($previous_data['phone']) ? htmlspecialchars($previous_data['phone']) : ''; ?>">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="address1">Address Line 1 *</label>
            <input type="text" id="address1" name="address1" required 
                   value="<?php echo isset($previous_data['address1']) ? htmlspecialchars($previous_data['address1']) : ''; ?>">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="address2">Address Line 2</label>
            <input type="text" id="address2" name="address2" 
                   value="<?php echo isset($previous_data['address2']) ? htmlspecialchars($previous_data['address2']) : ''; ?>">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="city">City *</label>
            <input type="text" id="city" name="city" required 
                   value="<?php echo isset($previous_data['city']) ? htmlspecialchars($previous_data['city']) : ''; ?>">
          </div>
          <div class="form-group">
            <label for="postal">Postal Code *</label>
            <input type="text" id="postal" name="postal" required 
                   value="<?php echo isset($previous_data['postal']) ? htmlspecialchars($previous_data['postal']) : ''; ?>">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="country">Country *</label>
            <select id="country" name="country" required>
              <option value="">Select Country</option>
              <option value="US" <?php echo (isset($previous_data['country']) && $previous_data['country'] == 'US') ? 'selected' : ''; ?>>United States</option>
              <option value="CA" <?php echo (isset($previous_data['country']) && $previous_data['country'] == 'CA') ? 'selected' : ''; ?>>Canada</option>
              <option value="UK" <?php echo (isset($previous_data['country']) && $previous_data['country'] == 'UK') ? 'selected' : ''; ?>>United Kingdom</option>
              <!-- Add more countries as needed -->
            </select>
          </div>
        </div>
      </section>

      <section class="section">
        <h2>Shipping Method</h2>
        
        <div class="radio-option <?php echo $shipping_method == 'standard' ? 'selected' : ''; ?>">
          <input type="radio" id="standard" name="shipping" value="standard" <?php echo $shipping_method == 'standard' ? 'checked' : ''; ?>>
          <label for="standard">
            <strong>Standard Shipping</strong><br>
            <small>5-7 business days - $5.00</small>
          </label>
        </div>
        
        <div class="radio-option <?php echo $shipping_method == 'express' ? 'selected' : ''; ?>">
          <input type="radio" id="express" name="shipping" value="express" <?php echo $shipping_method == 'express' ? 'checked' : ''; ?>>
          <label for="express">
            <strong>Express Shipping</strong><br>
            <small>2-3 business days - $15.00</small>
          </label>
        </div>
        
        <div class="radio-option <?php echo $shipping_method == 'overnight' ? 'selected' : ''; ?>">
          <input type="radio" id="overnight" name="shipping" value="overnight" <?php echo $shipping_method == 'overnight' ? 'checked' : ''; ?>>
          <label for="overnight">
            <strong>Overnight Shipping</strong><br>
            <small>1 business day - $25.00</small>
          </label>
        </div>
      </section>

      <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn-primary">
          <i class="fas fa-credit-card"></i> Continue to Payment
        </button>
        
        <a href="../review/review.php" class="btn-secondary">
          <i class="fas fa-arrow-left"></i> Back to Review
        </a>
      </div>
    </form>
  </main>

  

  <script>
    // Add selected class to radio options when clicked
    document.querySelectorAll('.radio-option input[type="radio"]').forEach(radio => {
      radio.addEventListener('change', function() {
        document.querySelectorAll('.radio-option').forEach(option => {
          option.classList.remove('selected');
        });
        if (this.checked) {
          this.closest('.radio-option').classList.add('selected');
        }
      });
    });
  </script>
</body>
</html>