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
  <!-- <link rel="stylesheet" href="../user.css">-->
  <link rel="stylesheet" href="orderhistory.css"> 
  <title>Order History</title>
  <style>
    /* Order History Specific Styles */

  </style>
</head>
<body>
  
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