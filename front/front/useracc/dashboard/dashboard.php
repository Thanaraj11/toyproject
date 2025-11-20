<?php
// useracc/dashboard/dashboard.php
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
  <link rel="stylesheet" href="dashboard.css">
  <title>User Dashboard</title>
  <style>
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin: 1rem 0;
    }
    
    .stat-card {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 1.5rem;
      text-align: center;
    }
    
    .stat-number {
      font-size: 2em;
      font-weight: bold;
      color: #007bff;
      display: block;
    }
    
    .stat-label {
      color: #6c757d;
      font-size: 0.9em;
    }
    
    .recent-orders {
      margin: 2rem 0;
    }
    
    .order-item {
      border: 1px solid #dee2e6;
      border-radius: 4px;
      padding: 1rem;
      margin-bottom: 0.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .order-status {
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
      font-size: 0.8em;
      font-weight: bold;
    }
    
    .status-pending {
      background: #fff3cd;
      color: #856404;
    }
    
    .status-confirmed {
      background: #d1edff;
      color: #004085;
    }
    
    .status-processing {
      background: #e2e3e5;
      color: #383d41;
    }
    
    .status-shipped {
      background: #d1ecf1;
      color: #0c5460;
    }
    
    .status-delivered {
      background: #d4edda;
      color: #155724;
    }
    
    .status-cancelled {
      background: #f8d7da;
      color: #721c24;
    }
    
    .quick-links-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 1rem;
      margin: 1rem 0;
    }
    
    .quick-link-card {
      background: white;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 1.5rem;
      text-align: center;
      text-decoration: none;
      color: inherit;
      transition: all 0.3s ease;
    }
    
    .quick-link-card:hover {
      background: #007bff;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
    
    .error-message {
      background: #f8d7da;
      color: #721c24;
      padding: 1rem;
      border-radius: 4px;
      margin: 1rem 0;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <header>
    <h1>User Dashboard</h1>
    
    <nav>
      <a href="../../main/index/index.php">Home</a>
      <a href="../../main/cart/cart.php">Cart</a>
      <a href="?logout=1" id="logout">Logout</a>
      <a href="../orderhistory/orderhistory.php">Order History</a>
      <a href="../adress/adress.php">Address Book</a>
      <a href="../wishlist/wishlist.php">Wishlist</a>
    </nav>
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