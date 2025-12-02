<?php
// useracc/dashboard/dashboard.php
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
  <!-- <link rel="stylesheet" href="../user.css">-->
  <link rel="stylesheet" href="dashboard.css"> 
  <title>User Dashboard</title>
  <style>
    /* Dashboard Specific Styles */

  </style>
</head>
<body>
  <header>
    <div>
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <p>Overview of your account activity</p>
    </div>
    <?php include '../header3.php'; ?>
</header>

  <main>
    <?php include 'dashboard_backend.php'; ?>
    <?php if (!$user_info): ?>
      <div class="error-message">
        <p>Error loading user information. Please try logging in again.</p>
        <p><a href="../login/login.php">Go to Login</a></p>
      </div>
    <?php else: ?>
    
    <section id="welcome">
      <h2>Welcome, <span id="username">
        <?php 
        echo htmlspecialchars(
            $user_info['first_name'] . ' ' . $user_info['last_name']
        ); 
        ?>
      </span>!</h2>
      <p>Member since: <?php echo date('F Y', strtotime($user_info['created_at'])); ?></p>
    </section>

    <section id="user-stats">
      <h3>Your Statistics</h3>
      <div class="stats-grid">
        <div class="stat-card">
          <span class="stat-number"><?php echo $user_stats['total_orders']; ?></span>
          <span class="stat-label">Total Orders</span>
        </div>
        <div class="stat-card">
          <span class="stat-number"><?php echo $user_stats['pending_orders']; ?></span>
          <span class="stat-label">Pending Orders</span>
        </div>
        <div class="stat-card">
          <span class="stat-number"><?php echo $user_stats['wishlist_items']; ?></span>
          <span class="stat-label">Wishlist Items</span>
        </div>
        <div class="stat-card">
          <span class="stat-number"><?php echo $user_stats['saved_addresses']; ?></span>
          <span class="stat-label">Saved Addresses</span>
        </div>
      </div>
    </section>

    <?php if (!empty($recent_orders)): ?>
    <section class="recent-orders">
      <h3>Recent Orders</h3>
      <?php foreach ($recent_orders as $order): ?>
        <div class="order-item">
          <div>
            <strong>Order #<?php echo htmlspecialchars($order['order_number']); ?></strong>
            <br>
            <small>Date: <?php echo date('M j, Y', strtotime($order['created_at'])); ?></small>
            <br>
            Total: $<?php echo number_format($order['total_amount'], 2); ?>
            <?php if ($order['payment_status'] !== 'paid'): ?>
              <br>
              <small style="color: #dc3545;">Payment: <?php echo ucfirst($order['payment_status']); ?></small>
            <?php endif; ?>
          </div>
          <span class="order-status status-<?php echo htmlspecialchars($order['status']); ?>">
            <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
          </span>
        </div>
      <?php endforeach; ?>
      <p><a href="../orderhistory/orderhistory.php">View all orders →</a></p>
    </section>
    <?php else: ?>
    <section class="recent-orders">
      <h3>Recent Orders</h3>
      <p>You haven't placed any orders yet.</p>
      <p><a href="../../main/index/index.php">Start shopping →</a></p>
    </section>
    <?php endif; ?>

    <section id="quick-links">
      <h3>Quick Links</h3>
      <div class="quick-links-grid">
        <a href="../orderhistory/orderhistory.php" class="quick-link-card">
          <strong>Order History</strong>
          <br>
          <small>View all orders</small>
        </a>
        <a href="../wishlist/wishlist.php" class="quick-link-card">
          <strong>Wishlist</strong>
          <br>
          <small>Saved items (<?php echo $user_stats['wishlist_items']; ?>)</small>
        </a>
        <a href="../profile/profile.php" class="quick-link-card">
          <strong>Profile Settings</strong>
          <br>
          <small>Update information</small>
        </a>
        <a href="../adress/adress.php" class="quick-link-card">
          <strong>Address Book</strong>
          <br>
          <small>Manage addresses (<?php echo $user_stats['saved_addresses']; ?>)</small>
        </a>
      </div>
    </section>
    
    <?php endif; ?>
  </main>
</body>
</html>