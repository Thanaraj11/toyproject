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
  <!-- <link rel="stylesheet" href="../user.css">
  <link rel="stylesheet" href="dashboard.css"> -->
  <title>User Dashboard</title>
  <style>
    /* Dashboard Specific Styles */
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
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* Welcome Section */
#welcome {
  background-color: var(--primary-white);
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  text-align: center;
}

#welcome h2 {
  color: var(--primary-black);
  margin-bottom: 0.5rem;
  font-size: 1.5rem;
}

#welcome p {
  color: var(--text-gray);
  font-size: 1rem;
}

#username {
  color: var(--dark-blue);
  font-weight: 600;
}

/* User Stats Section */
#user-stats {
  background-color: var(--primary-white);
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#user-stats h3 {
  color: var(--primary-black);
  margin-bottom: 1.5rem;
  font-size: 1.3rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background-color: var(--light-blue);
  border: 1px solid var(--medium-blue);
  border-radius: 8px;
  padding: 1.5rem;
  text-align: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-number {
  display: block;
  font-size: 2.5rem;
  font-weight: bold;
  color: var(--dark-blue);
  margin-bottom: 0.5rem;
}

.stat-label {
  color: var(--dark-gray);
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Recent Orders Section */
.recent-orders {
  background-color: var(--primary-white);
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.recent-orders h3 {
  color: var(--primary-black);
  margin-bottom: 1.5rem;
  font-size: 1.3rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1rem;
  border: 1px solid var(--medium-gray);
  border-radius: 6px;
  margin-bottom: 1rem;
  transition: background-color 0.3s ease;
}

.order-item:hover {
  background-color: var(--light-gray);
}

.order-item strong {
  color: var(--primary-black);
  font-size: 1.1rem;
}

.order-item small {
  color: var(--text-gray);
}

.order-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeaa7;
}

.status-processing {
  background-color: #cce7ff;
  color: #004085;
  border: 1px solid #b3d7ff;
}

.status-completed {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-shipped {
  background-color: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

.status-cancelled {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.recent-orders p {
  margin-top: 1rem;
  text-align: center;
}

.recent-orders a {
  color: var(--dark-blue);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.recent-orders a:hover {
  color: var(--primary-black);
  text-decoration: underline;
}

/* Quick Links Section */
#quick-links {
  background-color: var(--primary-white);
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#quick-links h3 {
  color: var(--primary-black);
  margin-bottom: 1.5rem;
  font-size: 1.3rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

.quick-links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.quick-link-card {
  background-color: var(--light-gray);
  border: 1px solid var(--medium-gray);
  border-radius: 8px;
  padding: 1.5rem;
  text-decoration: none;
  color: var(--primary-black);
  transition: all 0.3s ease;
  display: block;
}

.quick-link-card:hover {
  background-color: var(--light-blue);
  border-color: var(--medium-blue);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  text-decoration: none;
}

.quick-link-card strong {
  color: var(--dark-blue);
  font-size: 1.1rem;
  display: block;
  margin-bottom: 0.5rem;
}

.quick-link-card small {
  color: var(--text-gray);
  font-size: 0.9rem;
}

/* Error Message */
.error-message {
  background-color: #ffebee;
  color: #c62828;
  padding: 2rem;
  border-radius: 8px;
  text-align: center;
  border-left: 4px solid #f44336;
  margin: 2rem auto;
  max-width: 600px;
}

.error-message p {
  margin: 0.5rem 0;
}

.error-message a {
  color: var(--dark-blue);
  text-decoration: none;
  font-weight: 600;
}

.error-message a:hover {
  text-decoration: underline;
}

/* Responsive Design */
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
    gap: 1.5rem;
  }
  
  #welcome,
  #user-stats,
  .recent-orders,
  #quick-links {
    padding: 1.5rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .quick-links-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .order-item {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .order-status {
    align-self: flex-start;
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  nav {
    flex-direction: column;
    width: 100%;
  }
  
  nav a {
    text-align: center;
    width: 100%;
  }
  
  .stat-number {
    font-size: 2rem;
  }
  
  #welcome h2 {
    font-size: 1.3rem;
  }
}

/* Animation for stat cards */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stat-card {
  animation: fadeInUp 0.6s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Print Styles */
@media print {
  nav,
  .quick-link-card,
  .recent-orders a {
    display: none !important;
  }
  
  .stat-card,
  .order-item {
    break-inside: avoid;
  }
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