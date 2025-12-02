<?php
// useracc/address/address.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../useracc/login/login.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Address Book</title>
  <link rel="stylesheet" href="adress.css">
  <style>
    /* Address Book Specific Styles */

  </style>
</head>
<body>
  <header>
    <div>
        <h1><i class="fas fa-map-marker-alt"></i> My Addresses</h1>
        <p>Manage your delivery addresses</p>
    </div>
    <?php include '../header3.php'; ?>
</header>
  <main>
    <?php
    // Include address functions - fix the filename
    include 'adress_backend.php';
    
    // Display success/error messages
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo '<div class="success-message">Operation completed successfully!</div>';
    }
    
    if (isset($error_message)) {
        echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
    }
    
    // Get addresses for current user
    $addresses = getAddresses($customer_id);
    ?>
    
    <section id="address-list">
      <h2>Your Addresses</h2>
      
      <?php if (empty($addresses)): ?>
        <p>No addresses saved yet.</p>
      <?php else: ?>
        <ul id="addresses-ul">
          <?php foreach ($addresses as $address): ?>
          <li class="address-item">
            <strong><?php echo htmlspecialchars($address['full_name']); ?></strong>
            <span class="address-type">(<?php echo htmlspecialchars($address['type']); ?>)</span>
            <?php if ($address['is_default']): ?>
              <span class="default-badge">Default</span>
            <?php endif; ?>
            <p>
              <?php echo htmlspecialchars($address['address_line1']); ?><br>
              <?php if (!empty($address['address_line2'])): ?>
                <?php echo htmlspecialchars($address['address_line2']); ?><br>
              <?php endif; ?>
              <?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> <?php echo htmlspecialchars($address['zip_code']); ?><br>
              <?php echo htmlspecialchars($address['country']); ?><br>
              Phone: <?php echo htmlspecialchars($address['phone']); ?>
            </p>
            <div class="address-actions">
              <?php if (!$address['is_default']): ?>
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="action" value="set_default">
                  <input type="hidden" name="id" value="<?php echo $address['id']; ?>">
                  <button type="submit">Set as Default</button>
                </form>
              <?php endif; ?>
              
              <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="delete_address">
                <input type="hidden" name="id" value="<?php echo $address['id']; ?>">
                <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this address?')">Delete</button>
              </form>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      
      <button id="add-address">Add New Address</button>
    </section>

    <section id="address-form-section">
      <h3 id="form-title">Add Address</h3>
      <form id="address-form" method="POST">
        <input type="hidden" name="action" value="add_address">
        
        <div>
          <label>Address Type:</label>
          <select name="type" required>
            <option value="shipping">Shipping</option>
            <option value="billing">Billing</option>
          </select>
        </div>
        
        <div class="full-width">
          <label>Full Name:</label>
          <input type="text" name="full_name" required>
        </div>
        
        <div class="full-width">
          <label>Phone:</label>
          <input type="tel" name="phone" required>
        </div>
        
        <div class="full-width">
          <label>Address Line 1:</label>
          <input type="text" name="address_line1" required>
        </div>
        
        <div class="full-width">
          <label>Address Line 2:</label>
          <input type="text" name="address_line2">
        </div>
        
        <div>
          <label>City:</label>
          <input type="text" name="city" required>
        </div>
        
        <div>
          <label>State:</label>
          <input type="text" name="state" required>
        </div>
        
        <div>
          <label>Zip Code:</label>
          <input type="text" name="zip_code" required>
        </div>
        
        <div class="full-width">
          <label>Country:</label>
          <input type="text" name="country" required>
        </div>
        
        <div class="full-width">
          <label>
            <input type="checkbox" name="is_default" value="1">
            Set as default address for this type
          </label>
        </div>
        
        <div class="form-buttons">
          <button type="submit">Save Address</button>
          <button type="button" id="cancel-address">Cancel</button>
        </div>
      </form>
    </section>
  </main>

  <script>
    // Simple JavaScript to show/hide form
    document.getElementById('add-address').addEventListener('click', function() {
      document.getElementById('address-form-section').style.display = 'block';
      document.getElementById('address-list').style.display = 'none';
    });
    
    document.getElementById('cancel-address').addEventListener('click', function() {
      document.getElementById('address-form-section').style.display = 'none';
      document.getElementById('address-list').style.display = 'block';
      document.getElementById('address-form').reset();
    });
  </script>
</body>
</html>