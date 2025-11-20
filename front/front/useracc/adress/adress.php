<?php
// useracc/address/address.php
session_start();
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
  <!-- <link rel="stylesheet" href="../user.css">
  <link rel="stylesheet" href="address.css"> -->
  <style>
    /* Address Book Specific Styles */
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
}

/* Main Layout */
body {
  background-color: var(--light-gray);
  color: var(--primary-black);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
}

header {
  background-color: var(--primary-black);
  color: var(--primary-white);
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

header h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 600;
}

nav {
  display: flex;
  gap: 1rem;
}

nav a {
  color: var(--primary-white);
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

nav a:hover {
  background-color: var(--dark-gray);
}

main {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}

/* Address List Section */
#address-list {
  background-color: var(--primary-white);
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#address-list h2 {
  color: var(--primary-black);
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

/* Address Items */
#addresses-ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.address-item {
  background-color: var(--light-gray);
  border: 1px solid var(--medium-gray);
  border-radius: 6px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.address-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.address-item strong {
  color: var(--primary-black);
  font-size: 1.1rem;
  display: block;
  margin-bottom: 0.5rem;
}

.address-type {
  color: var(--text-gray);
  font-style: italic;
  margin-left: 0.5rem;
}

.default-badge {
  background-color: var(--light-blue);
  color: var(--dark-blue);
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  margin-left: 1rem;
}

.address-item p {
  color: var(--dark-gray);
  margin: 1rem 0;
  line-height: 1.8;
}

/* Address Actions */
.address-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.address-actions form {
  margin: 0;
}

.address-actions button {
  background-color: var(--primary-black);
  color: var(--primary-white);
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
}

.address-actions button:hover {
  background-color: var(--dark-gray);
}

.delete-btn {
  background-color: #d32f2f !important;
}

.delete-btn:hover {
  background-color: #b71c1c !important;
}

/* Add Address Button */
#add-address {
  background-color: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 1rem;
}

#add-address:hover {
  background-color: #1565c0;
}

/* Address Form Section */
#address-form-section {
  background-color: var(--primary-white);
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  display: none;
}

#form-title {
  color: var(--primary-black);
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

/* Form Styles */
#address-form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

#address-form div {
  display: flex;
  flex-direction: column;
}

.full-width {
  grid-column: 1 / -1;
}

#address-form label {
  color: var(--dark-gray);
  font-weight: 600;
  margin-bottom: 0.5rem;
}

#address-form input[type="text"],
#address-form input[type="tel"],
#address-form select {
  padding: 0.75rem;
  border: 1px solid var(--medium-gray);
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#address-form input[type="text"]:focus,
#address-form input[type="tel"]:focus,
#address-form select:focus {
  outline: none;
  border-color: var(--dark-blue);
  box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.2);
}

#address-form input[type="checkbox"] {
  margin-right: 0.5rem;
}

/* Form Buttons */
.form-buttons {
  grid-column: 1 / -1;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

.form-buttons button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.form-buttons button[type="submit"] {
  background-color: var(--dark-blue);
  color: var(--primary-white);
}

.form-buttons button[type="submit"]:hover {
  background-color: #1565c0;
}

.form-buttons button[type="button"] {
  background-color: var(--medium-gray);
  color: var(--dark-gray);
}

.form-buttons button[type="button"]:hover {
  background-color: #bdbdbd;
}

/* Messages */
.success-message {
  background-color: #e8f5e8;
  color: #2e7d32;
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
  border-left: 4px solid #4caf50;
}

.error-message {
  background-color: #ffebee;
  color: #c62828;
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
  border-left: 4px solid #f44336;
}

/* No Addresses Message */
#address-list p {
  color: var(--text-gray);
  font-style: italic;
  text-align: center;
  padding: 2rem;
  background-color: var(--light-gray);
  border-radius: 6px;
}

/* Responsive Design */
@media (max-width: 768px) {
  header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  nav {
    justify-content: center;
  }
  
  #address-form {
    grid-template-columns: 1fr;
  }
  
  .address-actions {
    flex-direction: column;
  }
  
  .address-actions button {
    width: 100%;
  }
  
  .form-buttons {
    flex-direction: column;
  }
  
  .form-buttons button {
    width: 100%;
  }
}

@media (max-width: 480px) {
  main {
    margin: 1rem auto;
    padding: 0 0.5rem;
  }
  
  #address-list,
  #address-form-section {
    padding: 1rem;
  }
  
  .address-item {
    padding: 1rem;
  }
}
  </style>
</head>
<body>
  <header>
    <h1>Saved Addresses</h1>
    <nav>
      <a href="../dashboard/dashboard.php">Dashboard</a>
      <a href="../../main/index/index.php">Home</a>
    </nav>
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