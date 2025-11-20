<?php
session_start();
require_once '../../../databse/db_connection.php';

// Include profile functions
include 'profile_backend.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_to'] = 'profile.php';
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$user = getUserProfile($user_id);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Get user addresses
$addresses = getUserAddresses($user_id);

// Get user orders
$orders = getUserOrders($user_id);

// Check for messages
$success_message = isset($_SESSION['profile_success']) ? $_SESSION['profile_success'] : '';
$error_message = isset($_SESSION['profile_error']) ? $_SESSION['profile_error'] : '';
unset($_SESSION['profile_success']);
unset($_SESSION['profile_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ToyBox</title>
    <!-- <link rel="stylesheet" href="../../style1.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Profile Page Specific Styles */
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
  --sidebar-bg: #f8f9fa;
}

/* Main Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

/* Profile Header */
.profile-header {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 3rem;
  padding: 2rem;
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border-left: 4px solid var(--dark-blue);
}

.avatar {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, var(--dark-blue), var(--medium-blue));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
  color: var(--primary-white);
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
}

.profile-welcome h1 {
  margin: 0 0 0.5rem 0;
  color: var(--primary-black);
  font-size: 2rem;
  font-weight: 600;
}

.profile-welcome p {
  margin: 0;
  color: var(--text-gray);
  font-size: 1.1rem;
}

/* Messages */
.success-message,
.error-message {
  padding: 1rem 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 600;
}

.success-message {
  background-color: #e8f5e8;
  color: #2e7d32;
  border-left: 4px solid var(--success-green);
}

.error-message {
  background-color: #ffebee;
  color: #c62828;
  border-left: 4px solid var(--error-red);
}

.success-message i,
.error-message i {
  font-size: 1.2rem;
}

/* Profile Layout */
.profile-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 2rem;
  min-height: 600px;
}

/* Profile Sidebar */
.profile-sidebar {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 1.5rem 0;
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.profile-nav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.profile-nav li {
  margin: 0;
}

.profile-nav a {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  color: var(--dark-gray);
  text-decoration: none;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
  font-weight: 500;
}

.profile-nav a:hover {
  background-color: var(--light-gray);
  color: var(--primary-black);
  border-left-color: var(--medium-gray);
}

.profile-nav a.active {
  background-color: var(--light-blue);
  color: var(--dark-blue);
  border-left-color: var(--dark-blue);
  font-weight: 600;
}

.profile-nav i {
  width: 20px;
  text-align: center;
  font-size: 1.1rem;
}

/* Profile Content */
.profile-content {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 2rem;
}

.section {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.section h2 {
  color: var(--primary-black);
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  font-weight: 600;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.info-item {
  background: var(--light-gray);
  padding: 1.5rem;
  border-radius: 8px;
  border-left: 4px solid var(--medium-blue);
}

.info-label {
  color: var(--text-gray);
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

.info-value {
  color: var(--primary-black);
  font-size: 1.1rem;
  font-weight: 600;
}

.status-confirmed {
  color: var(--success-green);
  font-weight: 600;
}

/* Buttons */
.btn-primary,
.btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.btn-primary {
  background: var(--dark-blue);
  color: var(--primary-white);
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
}

.btn-secondary:hover {
  background: var(--medium-gray);
  transform: translateY(-1px);
}

/* Address Grid */
.address-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.address-card {
  background: var(--light-gray);
  border: 2px solid var(--medium-gray);
  border-radius: 8px;
  padding: 1.5rem;
  transition: all 0.3s ease;
}

.address-card.default {
  border-color: var(--dark-blue);
  background: var(--light-blue);
}

.address-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--medium-gray);
}

.address-type {
  color: var(--dark-gray);
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

.default-badge {
  background: var(--dark-blue);
  color: var(--primary-white);
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.address-details {
  margin-bottom: 1.5rem;
}

.address-details p {
  margin: 0.25rem 0;
  color: var(--dark-gray);
}

.address-details strong {
  color: var(--primary-black);
  font-size: 1.1rem;
}

.address-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.address-actions .btn-secondary {
  padding: 0.5rem 1rem;
  font-size: 0.8rem;
}

/* Order Table */
.order-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.order-table th {
  background: var(--light-blue);
  color: var(--primary-black);
  font-weight: 600;
  text-align: left;
  padding: 1rem;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
  border-bottom: 2px solid var(--medium-blue);
}

.order-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--medium-gray);
  color: var(--dark-gray);
}

.order-table tr:hover {
  background: var(--light-gray);
}

/* Status Badges */
.status-pending { color: var(--warning-orange); font-weight: 600; }
.status-confirmed { color: var(--success-green); font-weight: 600; }
-status-processing { color: var(--dark-blue); font-weight: 600; }
.status-shipped { color: #9c27b0; font-weight: 600; }
.status-delivered { color: var(--success-green); font-weight: 600; }
.status-cancelled { color: var(--error-red); font-weight: 600; }

/* No Data States */
.no-data {
  text-align: center;
  padding: 3rem 2rem;
  color: var(--text-gray);
}

.no-data i {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: var(--medium-gray);
}

.no-data h3 {
  color: var(--dark-gray);
  margin-bottom: 0.5rem;
}

.no-data p {
  margin: 0;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .profile-layout {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .profile-sidebar {
    position: static;
  }
  
  .profile-nav {
    display: flex;
    overflow-x: auto;
    padding: 0 1rem;
  }
  
  .profile-nav li {
    flex-shrink: 0;
  }
  
  .profile-nav a {
    border-left: none;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
  }
  
  .profile-nav a.active {
    border-left: none;
    border-bottom-color: var(--dark-blue);
  }
}

@media (max-width: 768px) {
  .container {
    padding: 1rem 0.5rem;
  }
  
  .profile-header {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
    padding: 1.5rem;
  }
  
  .profile-content {
    padding: 1.5rem;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .address-grid {
    grid-template-columns: 1fr;
  }
  
  .order-table {
    font-size: 0.9rem;
    display: block;
    overflow-x: auto;
  }
  
  .address-actions {
    flex-direction: column;
  }
  
  .address-actions .btn-secondary {
    text-align: center;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .avatar {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
  
  .profile-welcome h1 {
    font-size: 1.5rem;
  }
  
  .profile-nav {
    flex-direction: column;
  }
  
  .profile-nav a {
    justify-content: center;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
    justify-content: center;
  }
}

/* Print Styles */
@media print {
  .profile-sidebar,
  .btn-primary,
  .btn-secondary {
    display: none !important;
  }
  
  .profile-layout {
    grid-template-columns: 1fr;
  }
  
  .profile-header {
    box-shadow: none;
    border: 1px solid var(--primary-black);
  }
  
  .address-card,
  .info-item {
    border: 1px solid var(--primary-black);
    break-inside: avoid;
  }
}
    </style>
</head>
<body>
    <?php include '../header3.php'; ?>

    <main class="container">
        <div class="profile-header">
            <div class="avatar">
                <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
            </div>
            <div class="profile-welcome">
                <h1>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                <p>Manage your account and view your orders</p>
            </div>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="profile-layout">
            <aside class="profile-sidebar">
                <nav>
                    <ul class="profile-nav">
                        <li><a href="#dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="#personal-info"><i class="fas fa-user"></i> Personal Information</a></li>
                        <li><a href="#addresses"><i class="fas fa-address-book"></i> Address Book</a></li>
                        <li><a href="#order-history"><i class="fas fa-shopping-bag"></i> Order History</a></li>
                        <li><a href="change_password.php"><i class="fas fa-lock"></i> Change Password</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
                    </ul>
                </nav>
            </aside>

            <div class="profile-content">
                <!-- Dashboard Section -->
                <section id="dashboard" class="section">
                    <h2>Account Overview</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Member Since</div>
                            <div class="info-value"><?php echo date('F Y', strtotime($user['created_at'])); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Total Orders</div>
                            <div class="info-value"><?php echo count($orders); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Login</div>
                            <div class="info-value">
                                <?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'First login'; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Account Status</div>
                            <div class="info-value status-confirmed"><?php echo ucfirst($user['status']); ?></div>
                        </div>
                    </div>
                </section>

                <!-- Personal Information Section -->
                <section id="personal-info" class="section">
                    <h2>Personal Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">First Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['first_name']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['last_name']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value"><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Not provided'; ?></div>
                        </div>
                    </div>
                    <a href="edit_profile.php" class="btn-primary">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </section>

                <!-- Address Book Section -->
                <section id="addresses" class="section">
                    <h2>Address Book</h2>
                    <?php if (!empty($addresses)): ?>
                        <div class="address-grid">
                            <?php foreach ($addresses as $address): ?>
                                <div class="address-card <?php echo $address['is_default'] ? 'default' : ''; ?>">
                                    <div class="address-header">
                                        <span class="address-type"><?php echo ucfirst($address['type']); ?></span>
                                        <?php if ($address['is_default']): ?>
                                            <span class="default-badge">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="address-details">
                                        <p><strong><?php echo htmlspecialchars($address['full_name']); ?></strong></p>
                                        <p><?php echo htmlspecialchars($address['phone']); ?></p>
                                        <p><?php echo htmlspecialchars($address['address_line1']); ?></p>
                                        <?php if (!empty($address['address_line2'])): ?>
                                            <p><?php echo htmlspecialchars($address['address_line2']); ?></p>
                                        <?php endif; ?>
                                        <p><?php echo htmlspecialchars($address['city'] . ', ' . $address['state'] . ' ' . $address['zip_code']); ?></p>
                                        <p><?php echo htmlspecialchars($address['country']); ?></p>
                                    </div>
                                    <div class="address-actions">
                                        <a href="edit_address.php?id=<?php echo $address['id']; ?>" class="btn-secondary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <?php if (!$address['is_default']): ?>
                                            <a href="set_default_address.php?id=<?php echo $address['id']; ?>&type=<?php echo $address['type']; ?>" class="btn-secondary">
                                                <i class="fas fa-star"></i> Set Default
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-address-book"></i>
                            <h3>No addresses saved</h3>
                            <p>Add your first address to make checkout faster</p>
                        </div>
                    <?php endif; ?>
                    <a href="add_address.php" class="btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Add New Address
                    </a>
                </section>

                <!-- Order History Section -->
                <section id="order-history" class="section">
                    <h2>Recent Orders</h2>
                    <?php if (!empty($orders)): ?>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td class="status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </td>
                                        <td>
                                            <a href="../order_details.php?id=<?php echo $order['id']; ?>" class="btn-secondary">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-shopping-bag"></i>
                            <h3>No orders yet</h3>
                            <p>Start shopping to see your orders here</p>
                            <a href="../../productlist/productlist.php" class="btn-primary" style="margin-top: 1rem;">
                                <i class="fas fa-shopping-cart"></i> Start Shopping
                            </a>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>

    

    <script>
        // Simple tab navigation
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.profile-nav a');
            const sections = document.querySelectorAll('.section');
            
            // Hide all sections except dashboard initially
            sections.forEach(section => {
                if (section.id !== 'dashboard') {
                    section.style.display = 'none';
                }
            });
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        
                        // Remove active class from all links
                        navLinks.forEach(l => l.classList.remove('active'));
                        // Add active class to clicked link
                        this.classList.add('active');
                        
                        // Hide all sections
                        sections.forEach(section => section.style.display = 'none');
                        
                        // Show target section
                        const targetId = this.getAttribute('href').substring(1);
                        const targetSection = document.getElementById(targetId);
                        if (targetSection) {
                            targetSection.style.display = 'block';
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>