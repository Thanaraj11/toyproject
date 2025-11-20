<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include necessary files
include '../../databse/db_connection.php';
include 'customer_backend.php';

// Check if database connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables
$message = '';
$message_type = ''; // success or error
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'customer-list';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_customer'])) {
        // Add new customer - FIXED: Include password_hash field
        $data = array(
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'status' => $_POST['status'],
            'password_hash' => password_hash('temp_password123', PASSWORD_DEFAULT) // Default temporary password
        );
        
        if (addCustomer($conn, $data)) {
            $message = 'Customer added successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error adding customer. Please try again.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['update_customer'])) {
        // Update customer
        $id = intval($_POST['customer_id']);
        $data = array(
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'status' => $_POST['status']
        );
        
        // Add password update if provided
        if (!empty($_POST['password'])) {
            $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        
        if (updateCustomer($conn, $id, $data)) {
            $message = 'Customer updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error updating customer. Please try again.';
            $message_type = 'error';
        }
    }
}

// Handle delete requests
if (isset($_GET['delete_customer'])) {
    $customer_id = intval($_GET['delete_customer']);
    if (deleteCustomer($conn, $customer_id)) {
        $message = 'Customer deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error deleting customer. Please try again.';
        $message_type = 'error';
    }
}

// Handle status toggle
if (isset($_GET['toggle_status'])) {
    $customer_id = intval($_GET['toggle_status']);
    $customer = getCustomerById($conn, $customer_id);
    if ($customer) {
        $new_status = $customer['status'] == 'active' ? 'inactive' : 'active';
        if (updateCustomerStatus($conn, $customer_id, $new_status)) {
            $message = 'Customer status updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error updating customer status. Please try again.';
            $message_type = 'error';
        }
    }
}

// Apply filters
$filters = array();
if (isset($_GET['status']) && $_GET['status'] != 'all') {
    $filters['status'] = $_GET['status'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}
if (isset($_GET['sort'])) {
    $filters['sort'] = $_GET['sort'];
}

// Get customers for display
$customers = getAllCustomers($conn, $filters);

// Get customer for details view
$customer_details = null;
$customer_addresses = array();
$customer_orders = array();
$customer_stats = array();

if (isset($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);
    $customer_details = getCustomerById($conn, $customer_id);
    if ($customer_details) {
        $customer_addresses = getCustomerAddresses($conn, $customer_id);
        $customer_orders = getCustomerOrders($conn, $customer_id);
        $customer_stats = getCustomerStats($conn, $customer_id);
        $current_tab = 'customer-details';
    }
}

// Get customer for editing
$edit_customer = null;
if (isset($_GET['edit_customer'])) {
    $edit_customer_id = intval($_GET['edit_customer']);
    $edit_customer = getCustomerById($conn, $edit_customer_id);
    $current_tab = 'customer-list';
}

// Get customer counts for filter
$customer_counts = getCustomerCounts($conn);

// Function to generate random avatar color (since it's not in database)
function getRandomAvatarColor() {
    $colors = ['#4a90e2', '#50c878', '#ff6b6b', '#ffa500', '#9b59b6', '#1abc9c', '#e74c3c', '#3498db'];
    return $colors[array_rand($colors)];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../back.css">
    <link rel="stylesheet" href="../back1.css">
    <style>
        /* Fallback styles in case CSS files are missing */
        .message { padding: 12px; margin: 10px 0; border-radius: 4px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .customer-avatar { 
            width: 40px; height: 40px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            color: white; font-weight: bold; 
        }
        .customer-avatar-large { 
            width: 80px; height: 80px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            color: white; font-weight: bold; font-size: 24px;
        }
        .status { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        .action-btn { padding: 6px; margin: 0 2px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .action-view { color: #4a90e2; }
        .action-edit { color: #ffa500; }
        .action-toggle { color: #50c878; }
        .action-delete { color: #e74c3c; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #4a90e2; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .no-data { text-align: center; padding: 40px; color: #6c757d; }
        
        /* Additional styles for the interface */
        .form-container { background: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-control { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; }
        .form-actions { display: flex; gap: 1rem; justify-content: flex-end; }
        
        .filter-bar { background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .filter-form { display: flex; gap: 1rem; align-items: center; }
        .filter-group { display: flex; align-items: center; gap: 0.5rem; }
        .filter-select { padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; }
        
        .customer-list-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        
        .customer-details-container { background: white; padding: 2rem; border-radius: 8px; }
        .customer-details-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .customer-info { display: flex; align-items: center; gap: 1rem; }
        .customer-actions { display: flex; gap: 1rem; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #f8f9fa; padding: 1.5rem; border-radius: 8px; text-align: center; }
        .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
        .stat-icon.orders { background: #4a90e2; color: white; }
        .stat-icon.spent { background: #50c878; color: white; }
        .stat-icon.avg { background: #ffa500; color: white; }
        .stat-value { font-size: 24px; font-weight: bold; margin-bottom: 0.5rem; }
        
        .customer-details-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        .detail-card { background: #f8f9fa; padding: 1.5rem; border-radius: 8px; }
        .detail-item { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
        
        .order-history { background: #f8f9fa; padding: 1.5rem; border-radius: 8px; }
        .order-history-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <?php 
    // Check if header file exists, otherwise create basic header
    if (file_exists('../header2.php')) {
        include '../header2.php'; 
    } else {
        echo '<div style="background: #343a40; color: white; padding: 1rem;">Basic Header - header2.php not found</div>';
    }
    ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1>Customer Management</h1>
                <p>Manage your customers and their information</p>
            </div>
            <div class="header-actions">
                <form method="GET" action="" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search customers..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </form>
                <div class="user-profile">
                    <div class="user-avatar">A</div>
                    <div class="user-info">
                        <h4>Admin User</h4>
                    </div>
                </div>
            </div>
        </header>

        <!-- Display Messages -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Content Tabs -->
        <div class="content-tabs">
            <div class="tab <?php echo $current_tab == 'customer-list' ? 'active' : ''; ?>" onclick="showTab('customer-list')">Customer List</div>
            <?php if ($customer_details): ?>
                <div class="tab active" id="customer-details-tab">Customer Details</div>
            <?php endif; ?>
        </div>

        <!-- Customer List -->
        <div id="customer-list" class="tab-content <?php echo $current_tab == 'customer-list' ? 'active' : ''; ?>">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <form method="GET" action="" class="filter-form">
                    <div class="filter-group">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['status']) ? 'selected' : ''; ?>>All Statuses (<?php echo $customer_counts['total']; ?>)</option>
                            <option value="active" <?php echo isset($filters['status']) && $filters['status'] == 'active' ? 'selected' : ''; ?>>Active (<?php echo $customer_counts['active']; ?>)</option>
                            <option value="inactive" <?php echo isset($filters['status']) && $filters['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive (<?php echo $customer_counts['inactive']; ?>)</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="sort-filter">Sort By:</label>
                        <select id="sort-filter" name="sort" class="filter-select" onchange="this.form.submit()">
                            <option value="newest" <?php echo (!isset($filters['sort']) || $filters['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo isset($filters['sort']) && $filters['sort'] == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="name" <?php echo isset($filters['sort']) && $filters['sort'] == 'name' ? 'selected' : ''; ?>>Name</option>
                            <option value="orders" <?php echo isset($filters['sort']) && $filters['sort'] == 'orders' ? 'selected' : ''; ?>>Most Orders</option>
                        </select>
                    </div>
                    <?php if (isset($_GET['search']) || isset($_GET['status']) || isset($_GET['sort'])): ?>
                        <div class="filter-group">
                            <a href="customer.php" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Add/Edit Customer Form -->
            <?php if (isset($_GET['add_customer']) || $edit_customer): ?>
                <div class="form-container">
                    <h2 class="form-title"><?php echo $edit_customer ? 'Edit Customer' : 'Add New Customer'; ?></h2>
                    <form method="POST" action="">
                        <?php if ($edit_customer): ?>
                            <input type="hidden" name="update_customer" value="1">
                            <input type="hidden" name="customer_id" value="<?php echo $edit_customer['id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="add_customer" value="1">
                        <?php endif; ?>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" name="first_name" class="form-control" 
                                       value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['first_name']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" id="lastName" name="last_name" class="form-control" 
                                       value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['last_name']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['email']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['phone']) : ''; ?>">
                            </div>
                        </div>
                        
                        <?php if ($edit_customer): ?>
                        <div class="form-group">
                            <label for="password">New Password (leave blank to keep current)</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" <?php echo ($edit_customer && $edit_customer['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_customer && $edit_customer['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <a href="customer.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><?php echo $edit_customer ? 'Update Customer' : 'Add Customer'; ?></button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Customer Table -->
                <div class="customer-list">
                    <div class="customer-list-header">
                        <h2 class="customer-list-title">All Customers (<?php echo count($customers); ?>)</h2>
                        <a href="?add_customer=true" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Customer
                        </a>
                    </div>
                    
                    <?php if (empty($customers)): ?>
                        <div class="no-data">
                            <p>No customers found.</p>
                        </div>
                    <?php else: ?>
                        <table class="customer-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <div class="customer-avatar" style="background-color: <?php echo getRandomAvatarColor(); ?>;">
                                                    <?php echo getAvatarInitials($customer['first_name'], $customer['last_name']); ?>
                                                </div>
                                                <div><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></td>
                                        <td><?php echo $customer['order_count'] ?? 0; ?></td>
                                        <td>$<?php echo number_format($customer['total_spent'] ?? 0, 2); ?></td>
                                        <td>
                                            <span class="status status-<?php echo $customer['status']; ?>">
                                                <?php echo ucfirst($customer['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?customer_id=<?php echo $customer['id']; ?>" class="action-btn action-view">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?edit_customer=<?php echo $customer['id']; ?>" class="action-btn action-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?toggle_status=<?php echo $customer['id']; ?>" class="action-btn action-toggle" title="<?php echo $customer['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                <i class="fas fa-power-off"></i>
                                            </a>
                                            <a href="?delete_customer=<?php echo $customer['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure you want to delete this customer?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Customer Details -->
        <?php if ($customer_details): ?>
        <div id="customer-details" class="tab-content active">
            <div class="customer-details-container">
                <div class="customer-details-header">
                    <div class="customer-info">
                        <div class="customer-avatar-large" style="background-color: <?php echo getRandomAvatarColor(); ?>;">
                            <?php echo getAvatarInitials($customer_details['first_name'], $customer_details['last_name']); ?>
                        </div>
                        <div class="customer-text-info">
                            <h2><?php echo htmlspecialchars($customer_details['first_name'] . ' ' . $customer_details['last_name']); ?></h2>
                            <p>Joined on <?php echo date('M j, Y', strtotime($customer_details['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="customer-actions">
                        <button class="btn btn-secondary" onclick="window.location.href='customer.php'">
                            <i class="fas fa-arrow-left"></i> Back to Customers
                        </button>
                        <a href="mailto:<?php echo htmlspecialchars($customer_details['email']); ?>" class="btn btn-primary">
                            <i class="fas fa-envelope"></i> Send Email
                        </a>
                    </div>
                </div>

                <!-- Stats Grid -->
                <?php if ($customer_stats): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-value"><?php echo $customer_stats['total_orders']; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon spent">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-value">$<?php echo number_format($customer_stats['total_spent'], 2); ?></div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon avg">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-value">$<?php echo number_format($customer_stats['avg_order_value'], 2); ?></div>
                        <div class="stat-label">Average Order Value</div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="customer-details-grid">
                    <div class="detail-card">
                        <h3>Contact Information</h3>
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($customer_details['email']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($customer_details['phone'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                <span class="status status-<?php echo $customer_details['status']; ?>">
                                    <?php echo ucfirst($customer_details['status']); ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($customer_addresses)): ?>
                        <div class="detail-card">
                            <h3>Default Shipping Address</h3>
                            <?php 
                            $default_shipping = null;
                            foreach ($customer_addresses as $address) {
                                if ($address['type'] == 'shipping' && $address['is_default']) {
                                    $default_shipping = $address;
                                    break;
                                }
                            }
                            if ($default_shipping): ?>
                                <div class="detail-item">
                                    <span class="detail-value"><?php echo htmlspecialchars($default_shipping['full_name']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-value"><?php echo htmlspecialchars($default_shipping['address_line1']); ?></span>
                                </div>
                                <?php if ($default_shipping['address_line2']): ?>
                                    <div class="detail-item">
                                        <span class="detail-value"><?php echo htmlspecialchars($default_shipping['address_line2']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="detail-item">
                                    <span class="detail-value"><?php echo htmlspecialchars($default_shipping['city'] . ', ' . $default_shipping['state'] . ' ' . $default_shipping['zip_code']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-value"><?php echo htmlspecialchars($default_shipping['country']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-value">Phone: <?php echo htmlspecialchars($default_shipping['phone']); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="detail-item">
                                    <span class="detail-value">No default shipping address set</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="detail-card">
                        <h3>Account Information</h3>
                        <div class="detail-item">
                            <span class="detail-label">Member Since:</span>
                            <span class="detail-value"><?php echo date('M j, Y', strtotime($customer_details['created_at'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Last Order:</span>
                            <span class="detail-value">
                                <?php echo $customer_stats['last_order'] ? date('M j, Y', strtotime($customer_stats['last_order'])) : 'No orders yet'; ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Last Login:</span>
                            <span class="detail-value">
                                <?php echo isset($customer_details['last_login']) && $customer_details['last_login'] ? date('M j, Y g:i A', strtotime($customer_details['last_login'])) : 'Never logged in'; ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email Verified:</span>
                            <span class="detail-value">
                                <?php echo isset($customer_details['email_verified']) && $customer_details['email_verified'] ? 'Yes' : 'No'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <div class="order-history">
                    <div class="order-history-header">
                        <h3 class="order-history-title">Order History</h3>
                    </div>
                    <?php if (empty($customer_orders)): ?>
                        <div class="no-data">
                            <p>No orders found for this customer.</p>
                        </div>
                    <?php else: ?>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customer_orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['order_number'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <span class="status status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status status-<?php echo $order['payment_status']; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <button class="action-btn action-view">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script>
        // Tab navigation
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.style.display = 'block';
                selectedTab.classList.add('active');
            }
            
            // Update active tab
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }
        
        // Auto-submit filter form when selections change
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelects = document.querySelectorAll('.filter-select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.form) {
                        this.form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>