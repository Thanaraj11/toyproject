<?php
// useracc/orderhistory/orderhistory.php
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
  <link rel="stylesheet" href="../user.css">
  <link rel="stylesheet" href="orderhistory.css">
  <title>Order History</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 1rem 0;
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
    
    .status-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8em;
      font-weight: bold;
    }
    
    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .status-confirmed {
      background-color: #d1edff;
      color: #004085;
    }
    
    .status-processing {
      background-color: #cce7ff;
      color: #004085;
    }
    
    .status-shipped {
      background-color: #d4edda;
      color: #155724;
    }
    
    .status-delivered {
      background-color: #d1e7dd;
      color: #0f5132;
    }
    
    .status-cancelled {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .payment-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.7em;
      margin-left: 5px;
    }
    
    .payment-pending {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .payment-paid {
      background-color: #d4edda;
      color: #155724;
    }
    
    .payment-failed {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .payment-refunded {
      background-color: #e2e3e5;
      color: #383d41;
    }
    
    .order-actions {
      display: flex;
      gap: 0.5rem;
    }
    
    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      font-size: 0.9em;
      display: inline-block;
    }
    
    .btn-view {
      background-color: #007bff;
      color: white;
    }
    
    .btn-reorder {
      background-color: #28a745;
      color: white;
    }
    
    .btn-cancel {
      background-color: #dc3545;
      color: white;
    }
    
    .btn:hover {
      opacity: 0.8;
    }
    
    .no-orders {
      text-align: center;
      padding: 2rem;
      color: #6c757d;
    }
    
    nav {
      display: flex;
      gap: 1rem;
      margin: 1rem 0;
    }
    
    nav a {
      text-decoration: none;
      color: #007bff;
      padding: 0.5rem 1rem;
      border: 1px solid #007bff;
      border-radius: 4px;
      transition: all 0.3s ease;
    }
    
    nav a:hover {
      background: #007bff;
      color: white;
    }
    
    #logout {
      background: #dc3545;
      color: white;
      border-color: #dc3545;
    }
    
    #logout:hover {
      background: #c82333;
      border-color: #c82333;
    }

    .order-status-container {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
  </style>
</head>
<body>
  <header>
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
  </header>

  <main>
    <section id="orders-list">
      <h2>Past Orders</h2>
      
      <?php
      // Include order history functions
      include 'orderhistory_backend.php';
      
      if (empty($user_orders)): 
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
</body>
</html>