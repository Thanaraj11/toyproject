<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include necessary files
include '../../databse/db_connection.php';
include 'order_backend.php';

// Initialize variables
$message = '';
$message_type = ''; // success or error
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'order-list';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_order_status'])) {
        $order_id = intval($_POST['order_id']);
        $new_status = $_POST['status'];
        $notes = $_POST['notes'] ?? '';
        
        if (updateOrderStatus($conn, $order_id, $new_status, $notes)) {
            $message = 'Order status updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error updating order status. Please try again.';
            $message_type = 'error';
        }
    }
}

// Apply filters
$filters = array();
if (isset($_GET['status']) && $_GET['status'] != 'all') {
    $filters['status'] = $_GET['status'];
}
if (isset($_GET['date_range']) && $_GET['date_range'] != 'all') {
    $filters['date_range'] = $_GET['date_range'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Get data for display
$orders = getAllOrders($conn, $filters);
$order_stats = getOrderStats($conn);
$status_counts = getOrdersCountByStatus($conn);

// Get order for details view
$order_details = null;
$order_items = array();
$order_history = array();
$timeline_status = array();

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $order_details = getOrderById($conn, $order_id);
    if ($order_details) {
        $order_items = getOrderItems($conn, $order_id);
        $order_history = getOrderStatusHistory($conn, $order_id);
        $timeline_status = getTimelineStatus($order_history, $order_details['status']);
        $current_tab = 'order-details';
    }
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../back.css">
    <link rel="stylesheet" href="../back1.css">
</head>
<body>
    <?php include '../header2.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1>Order Management</h1>
                <p>View and manage customer orders</p>
            </div>
            <div class="header-actions">
                <form method="GET" action="" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search orders..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon orders">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $order_stats['total_orders']; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $order_stats['pending_orders']; ?></h3>
                    <p>Pending Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon processing">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $order_stats['processing_orders']; ?></h3>
                    <p>Processing</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>$<?php echo number_format($order_stats['total_revenue'], 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="content-tabs">
            <div class="tab <?php echo $current_tab == 'order-list' ? 'active' : ''; ?>" onclick="showTab('order-list')">Order List</div>
            <?php if ($order_details): ?>
                <div class="tab active" id="order-details-tab">Order Details</div>
            <?php endif; ?>
        </div>

        <!-- Order List -->
        <div id="order-list" class="tab-content <?php echo $current_tab == 'order-list' ? 'active' : ''; ?>">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <form method="GET" action="" class="filter-form">
                    <div class="filter-group">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['status']) ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="pending" <?php echo isset($filters['status']) && $filters['status'] == 'pending' ? 'selected' : ''; ?>>Pending (<?php echo $status_counts['pending']; ?>)</option>
                            <option value="processing" <?php echo isset($filters['status']) && $filters['status'] == 'processing' ? 'selected' : ''; ?>>Processing (<?php echo $status_counts['processing']; ?>)</option>
                            <option value="shipped" <?php echo isset($filters['status']) && $filters['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped (<?php echo $status_counts['shipped']; ?>)</option>
                            <option value="delivered" <?php echo isset($filters['status']) && $filters['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered (<?php echo $status_counts['delivered']; ?>)</option>
                            <option value="cancelled" <?php echo isset($filters['status']) && $filters['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled (<?php echo $status_counts['cancelled']; ?>)</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date-filter">Date:</label>
                        <select id="date-filter" name="date_range" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['date_range']) ? 'selected' : ''; ?>>All Dates</option>
                            <option value="today" <?php echo isset($filters['date_range']) && $filters['date_range'] == 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="week" <?php echo isset($filters['date_range']) && $filters['date_range'] == 'week' ? 'selected' : ''; ?>>This Week</option>
                            <option value="month" <?php echo isset($filters['date_range']) && $filters['date_range'] == 'month' ? 'selected' : ''; ?>>This Month</option>
                        </select>
                    </div>
                    <?php if (isset($_GET['status']) || isset($_GET['date_range']) || isset($_GET['search'])): ?>
                        <div class="filter-group">
                            <a href="order.php" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Order Table -->
            <div class="order-list">
                <div class="order-list-header">
                    <h2 class="order-list-title">All Orders (<?php echo count($orders); ?>)</h2>
                    <button class="btn btn-primary">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                
                <?php if (empty($orders)): ?>
                    <div class="no-data">
                        <p>No orders found.</p>
                    </div>
                <?php else: ?>
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status <?php echo getStatusClass($order['status']); ?>">
                                            <?php echo formatOrderStatus($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?order_id=<?php echo $order['id']; ?>" class="action-btn action-view">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination">
                        <div class="page-item"><i class="fas fa-chevron-left"></i></div>
                        <div class="page-item active">1</div>
                        <div class="page-item">2</div>
                        <div class="page-item">3</div>
                        <div class="page-item">4</div>
                        <div class="page-item"><i class="fas fa-chevron-right"></i></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order Details -->
        <?php if ($order_details): ?>
        <div id="order-details" class="tab-content active">
            <div class="order-details-container">
                <div class="order-details-header">
                    <div class="order-info">
                        <h2>Order <?php echo htmlspecialchars($order_details['order_number']); ?></h2>
                        <p>Placed on <?php echo date('M j, Y \a\t g:i A', strtotime($order_details['created_at'])); ?></p>
                    </div>
                    <div class="order-actions">
                        <button class="btn btn-secondary" onclick="window.location.href='order.php'">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-print"></i> Print Invoice
                        </button>
                    </div>
                </div>

                <!-- Status Update Form -->
                <div class="status-update-form">
                    <form method="POST" action="">
                        <input type="hidden" name="update_order_status" value="1">
                        <input type="hidden" name="order_id" value="<?php echo $order_details['id']; ?>">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="status">Update Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="pending" <?php echo $order_details['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order_details['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order_details['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order_details['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order_details['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notes">Notes (Optional)</label>
                                <input type="text" id="notes" name="notes" class="form-control" placeholder="Add notes about status change">
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="order-details-grid">
                    <div class="detail-card">
                        <h3>Customer Information</h3>
                        <div class="detail-item">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order_details['first_name'] . ' ' . $order_details['last_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order_details['email']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order_details['phone']); ?></span>
                        </div>
                    </div>

                    <?php if ($order_details['shipping_address1']): ?>
                    <div class="detail-card">
                        <h3>Shipping Address</h3>
                        <div class="detail-item">
                            <span class="detail-value"><?php echo htmlspecialchars($order_details['shipping_address1']); ?></span>
                        </div>
                        <?php if ($order_details['shipping_address2']): ?>
                            <div class="detail-item">
                                <span class="detail-value"><?php echo htmlspecialchars($order_details['shipping_address2']); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <span class="detail-value">
                                <?php echo htmlspecialchars($order_details['shipping_city'] . ', ' . $order_details['shipping_state'] . ' ' . $order_details['shipping_zip']); ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-value"><?php echo htmlspecialchars($order_details['shipping_country']); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="detail-card">
                        <h3>Payment Information</h3>
                        <div class="detail-item">
                            <span class="detail-label">Method:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order_details['payment_method']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Transaction ID:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order_details['transaction_id']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                <span class="status status-<?php echo $order_details['payment_status']; ?>">
                                    <?php echo ucfirst($order_details['payment_status']); ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <h3>Order Summary</h3>
                        <div class="detail-item">
                            <span class="detail-label">Subtotal:</span>
                            <span class="detail-value">$<?php echo number_format($order_details['subtotal_amount'], 2); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Shipping:</span>
                            <span class="detail-value">$<?php echo number_format($order_details['shipping_amount'], 2); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tax:</span>
                            <span class="detail-value">$<?php echo number_format($order_details['tax_amount'], 2); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total:</span>
                            <span class="detail-value" style="font-weight: bold; color: var(--primary);">
                                $<?php echo number_format($order_details['total_amount'], 2); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <h3>Order Items</h3>
                <?php if (empty($order_items)): ?>
                    <div class="no-data">
                        <p>No items found for this order.</p>
                    </div>
                <?php else: ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <img src="<?php echo htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/50'); ?>" 
                                                 class="product-image" alt="Product" 
                                                 onerror="this.src='https://via.placeholder.com/50'">
                                            <div>
                                                <div><?php echo htmlspecialchars($item['product_name']); ?></div>
                                                <div style="font-size: 0.875rem; color: #6c757d;">SKU: <?php echo htmlspecialchars($item['product_sku']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div class="order-timeline">
                    <h3 class="timeline-title">Order Status Timeline</h3>
                    <ul class="timeline">
                        <?php
                        $statuses = array(
                            'pending' => 'Order Placed',
                            'processing' => 'Order Processed', 
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered'
                        );
                        
                        foreach ($statuses as $status => $label): 
                            $is_completed = $timeline_status[$status]['completed'] ?? false;
                            $is_active = $timeline_status[$status]['active'] ?? false;
                            $status_info = null;
                            
                            // Find the history entry for this status
                            foreach ($order_history as $history) {
                                if ($history['status'] == $status) {
                                    $status_info = $history;
                                    break;
                                }
                            }
                        ?>
                            <li class="timeline-item">
                                <div class="timeline-dot <?php echo $is_completed ? 'completed' : ($is_active ? 'active' : ''); ?>"></div>
                                <div class="timeline-content">
                                    <h4><?php echo $label; ?></h4>
                                    <p>
                                        <?php if ($status_info): ?>
                                            <?php echo date('M j, Y \a\t g:i A', strtotime($status_info['created_at'])); ?>
                                            <?php if (!empty($status_info['notes'])): ?>
                                                <br><small><?php echo htmlspecialchars($status_info['notes']); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php echo $is_active ? 'In progress' : 'Not yet started'; ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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
            document.getElementById(tabId).style.display = 'block';
            document.getElementById(tabId).classList.add('active');
            
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