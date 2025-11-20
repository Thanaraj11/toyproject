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
  <link rel="stylesheet" href="../../style1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Checkout - Shipping Information</title>
  <style>
    .container {
      max-width: 800px;
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
      margin: 0 15px;
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
      font-size: 0.9em;
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
    
    .form-group input:focus, .form-group select:focus {
      border-color: #007bff;
      outline: none;
      box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }
    
    .radio-option {
      margin-bottom: 0.5rem;
      padding: 1rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      cursor: pointer;
      transition: border-color 0.2s;
    }
    
    .radio-option:hover {
      border-color: #007bff;
    }
    
    .radio-option.selected {
      border-color: #007bff;
      background-color: #f8f9ff;
    }
    
    .radio-option input[type="radio"] {
      margin-right: 0.5rem;
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
      margin-left: 10px;
    }
    
    .btn-secondary:hover {
      background-color: #545b62;
    }
    
    .error-message {
      color: #dc3545;
      background: #f8d7da;
      padding: 1rem;
      border: 1px solid #f5c6cb;
      border-radius: 4px;
      margin-bottom: 1rem;
    }
    
    .success-message {
      color: #155724;
      background: #d4edda;
      padding: 1rem;
      border: 1px solid #c3e6cb;
      border-radius: 4px;
      margin-bottom: 1rem;
    }
    
    .section {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 2rem;
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

  <?php include '../../footer.php'; ?>

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