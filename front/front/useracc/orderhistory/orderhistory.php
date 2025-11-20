<?php
// useracc/orderhistory/orderhistory.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../useracc/login/login.php");
    exit();
}

// Include order history functions - MOVED TO TOP so functions are available
include 'orderhistory_backend.php';
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- <link rel="stylesheet" href="../user.css">
  <link rel="stylesheet" href="orderhistory.css"> -->
  <title>Order History</title>
  <style>
    /* Order History Specific Styles */
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
  --warning-orange: #ff9800;
  --error-red: #f44336;
  --purple: #9c27b0;
}

/* Main Layout */
body {
  background-color: var(--light-gray);
  color: var(--primary-black);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  margin: 0;
  padding: 0;
}

/* Header Styles */
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
  flex-wrap: wrap;
}

nav a {
  color: var(--primary-white);
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: background-color 0.3s ease;
  font-size: 0.9rem;
}

nav a:hover {
  background-color: var(--dark-gray);
}

#logout {
  background-color: #d32f2f;
}

#logout:hover {
  background-color: #b71c1c;
}

/* Main Content */
main {
  max-width: 1400px;
  margin: 2rem auto;
  padding: 0 1rem;
}

/* Orders List Section */
#orders-list {
  background-color: var(--primary-white);
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#orders-list h2 {
  color: var(--primary-black);
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

/* No Orders State */
.no-orders {
  text-align: center;
  padding: 3rem 2rem;
  background-color: var(--light-gray);
  border-radius: 8px;
  border: 2px dashed var(--medium-gray);
}

.no-orders p {
  color: var(--text-gray);
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
}

/* Table Styles */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

thead {
  background-color: var(--light-blue);
  border-bottom: 2px solid var(--medium-blue);
}

th {
  color: var(--primary-black);
  font-weight: 600;
  text-align: left;
  padding: 1rem;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

tbody tr {
  border-bottom: 1px solid var(--medium-gray);
  transition: background-color 0.3s ease;
}

tbody tr:hover {
  background-color: var(--light-gray);
}

td {
  padding: 1rem;
  vertical-align: top;
}

/* Order Status Styles */
.order-status-container {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.status-badge,
.payment-badge {
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-block;
  width: fit-content;
}

/* Status Colors */
.status-pending {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeaa7;
}

.status-confirmed {
  background-color: #cce7ff;
  color: #004085;
  border: 1px solid #b3d7ff;
}

.status-processing {
  background-color: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

.status-shipped {
  background-color: #e8f5e8;
  color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.status-delivered {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-cancelled {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

/* Payment Status Colors */
.payment-pending {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeaa7;
}

.payment-paid {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.payment-failed {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.payment-refunded {
  background-color: #e2e3e5;
  color: #383d41;
  border: 1px solid #d6d8db;
}

/* Button Styles */
.btn {
  display: inline-block;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
}

.btn-view {
  background-color: var(--dark-blue);
  color: var(--primary-white);
}

.btn-view:hover {
  background-color: #1565c0;
  transform: translateY(-1px);
}

.btn-cancel {
  background-color: var(--error-red);
  color: var(--primary-white);
}

.btn-cancel:hover {
  background-color: #d32f2f;
  transform: translateY(-1px);
}

.btn-reorder {
  background-color: var(--success-green);
  color: var(--primary-white);
}

.btn-reorder:hover {
  background-color: #388e3c;
  transform: translateY(-1px);
}

.btn-view {
  background-color: var(--primary-black);
  color: var(--primary-white);
}

.btn-view:hover {
  background-color: var(--dark-gray);
  transform: translateY(-1px);
}

/* Order Actions */
.order-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  min-width: 120px;
}

.order-actions form {
  margin: 0;
}

.order-actions button {
  width: 100%;
  font-size: 0.8rem;
  padding: 0.4rem 0.8rem;
}

/* Table Responsive Design */
@media (max-width: 1024px) {
  table {
    font-size: 0.9rem;
  }
  
  th, td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 768px) {
  header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
    padding: 1rem;
  }
  
  nav {
    justify-content: center;
  }
  
  main {
    margin: 1rem auto;
    padding: 0 0.5rem;
  }
  
  #orders-list {
    padding: 1rem;
    overflow-x: auto;
  }
  
  table {
    min-width: 800px;
  }
  
  .order-actions {
    flex-direction: row;
    flex-wrap: wrap;
  }
  
  .order-actions .btn {
    flex: 1;
    min-width: 80px;
  }
}

@media (max-width: 480px) {
  nav {
    flex-direction: column;
    width: 100%;
  }
  
  nav a {
    text-align: center;
    width: 100%;
  }
  
  .no-orders {
    padding: 2rem 1rem;
  }
  
  .no-orders .btn {
    width: 100%;
    padding: 0.75rem 1rem;
  }
  
  .order-status-container {
    gap: 0.25rem;
  }
  
  .status-badge,
  .payment-badge {
    font-size: 0.7rem;
    padding: 0.3rem 0.6rem;
  }
}

/* Animation for table rows */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

tbody tr {
  animation: fadeIn 0.5s ease-out;
}

tbody tr:nth-child(1) { animation-delay: 0.1s; }
tbody tr:nth-child(2) { animation-delay: 0.2s; }
tbody tr:nth-child(3) { animation-delay: 0.3s; }
tbody tr:nth-child(4) { animation-delay: 0.4s; }
tbody tr:nth-child(5) { animation-delay: 0.5s; }

/* Print Styles */
@media print {
  nav,
  .order-actions {
    display: none !important;
  }
  
  table {
    border: 1px solid var(--primary-black);
  }
  
  th {
    background-color: var(--light-gray) !important;
    color: var(--primary-black) !important;
  }
}
  </style>
</head>
<body>
  <!-- <header>
    <h1>My Orders</h1>
    <nav>
      <a href="../../main/index/index.php">Home</a>
      <a href="../../main/cart/cart.php">Cart</a>
      <a href="../logout/logout.php" id="logout">Logout</a>
      <a href="../orderhistory/orderhistory.php">Order History</a>
      <a href="../register/register.php">Register</a>
      <a href="../adress/adress.php">Address Book</a>
      <a href="../wishlist/wishlist.php">Wishlist</a>
    </nav>
  </header> -->
  <header>
    <div>
        <h1><i class="fas fa-history"></i> Order History</h1>
        <p>View your past orders and track current ones</p>
    </div>
    <?php include '../header3.php'; ?>
</header>

  <main>
    <section id="orders-list">
      <h2>Past Orders</h2>
      
      <?php
      // Check if user_orders is set and not empty
      if (!isset($user_orders) || empty($user_orders)): 
      ?>
        <div class="no-orders">
          <p>You haven't placed any orders yet.</p>
          <a href="../../main/productlist/productlist.php" class="btn btn-view">Start Shopping</a>
        </div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Order #</th>
              <th>Date</th>
              <th>Status</th>
              <th>Total</th>
              <th>Items</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="orders-body">
            <?php foreach ($user_orders as $order): ?>
            <tr>
              <td>#<?php echo htmlspecialchars($order['order_number']); ?></td>
              <td><?php echo formatOrderDate($order['order_date']); ?></td>
              <td>
                <div class="order-status-container">
                  <span class="status-badge <?php echo getStatusClass($order['status']); ?>">
                    <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                  </span>
                  <?php if (isset($order['payment_status'])): ?>
                    <span class="payment-badge <?php echo getPaymentStatusClass($order['payment_status']); ?>">
                      Payment: <?php echo ucfirst(htmlspecialchars($order['payment_status'])); ?>
                    </span>
                  <?php endif; ?>
                </div>
              </td>
              <td><?php echo formatCurrency($order['order_total']); ?></td>
              <td><?php echo $order['item_count']; ?> item(s)</td>
              <td>
                <div class="order-actions">
                  <a href="orderdetails.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-view">View Details</a>
                  
                  <?php if ($order['status'] == 'pending' || $order['status'] == 'confirmed'): ?>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="action" value="cancel_order">
                      <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                      <button type="submit" class="btn btn-cancel" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</button>
                    </form>
                  <?php endif; ?>
                  
                  <?php if ($order['status'] == 'delivered' || $order['status'] == 'cancelled'): ?>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="action" value="reorder">
                      <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                      <button type="submit" class="btn btn-reorder">Reorder</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  </main>

  <script>
    // Add confirmation for cancel actions
    document.addEventListener('DOMContentLoaded', function() {
      const cancelForms = document.querySelectorAll('form[action="cancel_order"]');
      cancelForms.forEach(form => {
        form.addEventListener('submit', function(e) {
          if (!confirm('Are you sure you want to cancel this order?')) {
            e.preventDefault();
          }
        });
      });
    });
  </script>
</body>
</html>