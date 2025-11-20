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
    <link rel="stylesheet" href="../../style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: <?php echo $user['avatar_color'] ?: '#4361ee'; ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: white;
        }
        
        .profile-welcome h1 {
            margin: 0;
            font-size: 2rem;
        }
        
        .profile-welcome p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        
        .profile-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
        }
        
        .profile-sidebar {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .profile-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .profile-nav li {
            margin-bottom: 0.5rem;
        }
        
        .profile-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .profile-nav a:hover,
        .profile-nav a.active {
            background-color: #7c3aed;
            color: white;
        }
        
        .profile-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section {
            margin-bottom: 2rem;
        }
        
        .section h2 {
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #7c3aed;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            color: #333;
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background-color: #7c3aed;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #6d28d9;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .order-table th,
        .order-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .order-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .status-confirmed { color: #28a745; }
        .status-pending { color: #ffc107; }
        .status-cancelled { color: #dc3545; }
        
        .address-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .address-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            background: #f8fafc;
        }
        
        .address-card.default {
            border-color: #7c3aed;
            background: #f3f4f6;
        }
        
        .address-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .address-type {
            background: #7c3aed;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .default-badge {
            background: #10b981;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .address-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .no-data {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        
        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e0;
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

    <?php include '../footer2.php'; ?>

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