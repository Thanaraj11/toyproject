<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include necessary files
include '../../databse/db_connection.php';
include 'inventory_backend.php';

// Initialize variables
$message = '';
$message_type = ''; // success or error

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['restock_product'])) {
        // Restock product
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $supplier_id = intval($_POST['supplier_id']);
        
        if (updateProductStock($conn, $product_id, $quantity, 'in', 'Manual restock', 'RESTOCK-' . time())) {
            $message = 'Product restocked successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error restocking product. Please try again.';
            $message_type = 'error';
        }
    }
}

// Handle resolve alert
if (isset($_GET['resolve_alert'])) {
    $alert_id = intval($_GET['resolve_alert']);
    $resolve_query = "UPDATE low_stock_alerts SET is_resolved = TRUE, resolved_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = mysqli_prepare($conn, $resolve_query);
    mysqli_stmt_bind_param($stmt, 'i', $alert_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = 'Alert resolved successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error resolving alert. Please try again.';
        $message_type = 'error';
    }
}

// Apply filters
$filters = array();
if (isset($_GET['category']) && $_GET['category'] != 'all') {
    $filters['category'] = $_GET['category'];
}
if (isset($_GET['stock_status']) && $_GET['stock_status'] != 'all') {
    $filters['stock_status'] = $_GET['stock_status'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Get data for display
$stats = getInventoryStats($conn);
$products = getProducts($conn, $filters);
$categories = getCategories($conn);
$suppliers = getSuppliers($conn);
$low_stock_alerts = getLowStockAlerts($conn, false);

// Get product for restocking if restock_id is set
$restock_product = null;
if (isset($_GET['restock_id'])) {
    $restock_id = intval($_GET['restock_id']);
    $restock_product = getProductById($conn, $restock_id);
}

// Close database connection
// mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Admin Panel</title>
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
                <h1>Inventory Management</h1>
                <p>Monitor stock levels and manage inventory</p>
            </div>
            <div class="header-actions">
                <form method="GET" action="" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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

        <!-- Alert Banner -->
        <?php if (!empty($low_stock_alerts)): ?>
            <div class="alert-banner">
                <div class="alert-content">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><strong><?php echo count($low_stock_alerts); ?> products</strong> are running low on stock and need restocking.</span>
                </div>
                <button class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon products">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_products']; ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon low-stock">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['low_stock_items']; ?></h3>
                    <p>Low Stock Items</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon value">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>$<?php echo number_format($stats['inventory_value'], 2); ?></h3>
                    <p>Inventory Value</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon categories">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_categories']; ?></h3>
                    <p>Categories</p>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label for="category-filter">Category:</label>
                    <select id="category-filter" name="category" class="filter-select" onchange="this.form.submit()">
                        <option value="all" <?php echo empty($filters['category']) ? 'selected' : ''; ?>>All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['slug']); ?>" 
                                    <?php echo isset($filters['category']) && $filters['category'] == $category['slug'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="stock-filter">Stock Status:</label>
                    <select id="stock-filter" name="stock_status" class="filter-select" onchange="this.form.submit()">
                        <option value="all" <?php echo empty($filters['stock_status']) ? 'selected' : ''; ?>>All Statuses</option>
                        <option value="in-stock" <?php echo isset($filters['stock_status']) && $filters['stock_status'] == 'in-stock' ? 'selected' : ''; ?>>In Stock</option>
                        <option value="low-stock" <?php echo isset($filters['stock_status']) && $filters['stock_status'] == 'low-stock' ? 'selected' : ''; ?>>Low Stock</option>
                        <option value="out-of-stock" <?php echo isset($filters['stock_status']) && $filters['stock_status'] == 'out-of-stock' ? 'selected' : ''; ?>>Out of Stock</option>
                    </select>
                </div>
                <?php if (isset($_GET['category']) || isset($_GET['stock_status']) || isset($_GET['search'])): ?>
                    <div class="filter-group">
                        <a href="inventory.php" class="btn btn-secondary">Clear Filters</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="inventory-table-container">
            <div class="inventory-header">
                <h2 class="inventory-title">Inventory Overview (<?php echo count($products); ?> products)</h2>
                <button class="btn btn-primary">
                    <i class="fas fa-download"></i> Export Report
                </button>
            </div>
            
            <?php if (empty($products)): ?>
                <div class="no-data">
                    <p>No products found.</p>
                </div>
            <?php else: ?>
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Stock Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): 
                            $stock_percentage = getStockPercentage($product['current_stock'], $product['max_stock_level']);
                            $stock_class = getStockLevelClass($stock_percentage);
                            $stock_status = getStockStatus($product['current_stock'], $product['min_stock_level']);
                        ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="product-image" alt="Product" onerror="this.src='https://via.placeholder.com/50'">
                                        <div><?php echo htmlspecialchars($product['name']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>
                                    <div class="stock-level">
                                        <div class="stock-bar">
                                            <div class="stock-fill <?php echo $stock_class; ?>" style="width: <?php echo $stock_percentage; ?>%;"></div>
                                        </div>
                                        <span><?php echo $product['current_stock']; ?>/<?php echo $product['max_stock_level']; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status status-<?php echo $stock_status; ?>">
                                        <?php 
                                        switch ($stock_status) {
                                            case 'in-stock': echo 'In Stock'; break;
                                            case 'low-stock': echo 'Low Stock'; break;
                                            case 'out-of-stock': echo 'Out of Stock'; break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn action-edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?restock_id=<?php echo $product['id']; ?>" class="action-btn action-restock">
                                        <i class="fas fa-plus-circle"></i>
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

        <!-- Low Stock Alerts -->
        <?php if (!empty($low_stock_alerts)): ?>
            <div class="alerts-container">
                <div class="alerts-header">
                    <h2 class="alerts-title">Low Stock Alerts</h2>
                    <button class="btn btn-primary">
                        <i class="fas fa-bell"></i> Manage Alerts
                    </button>
                </div>
                <?php foreach ($low_stock_alerts as $alert): ?>
                    <div class="alert-item">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="alert-content">
                            <h4><?php echo htmlspecialchars($alert['product_name']); ?> (<?php echo htmlspecialchars($alert['sku']); ?>)</h4>
                            <p>Only <?php echo $alert['current_stock']; ?> items left in stock (<?php echo round(($alert['current_stock'] / $alert['max_stock_level']) * 100); ?>% of capacity)</p>
                        </div>
                        <div class="alert-actions">
                            <a href="?restock_id=<?php echo $alert['product_id']; ?>" class="btn btn-primary">Restock</a>
                            <a href="?resolve_alert=<?php echo $alert['id']; ?>" class="btn btn-secondary">Mark Resolved</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Restock Modal -->
    <?php if ($restock_product): ?>
    <div class="modal active" id="restockModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Restock Product</h2>
                <a href="inventory.php" class="modal-close">&times;</a>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="restock_product" value="1">
                <input type="hidden" name="product_id" value="<?php echo $restock_product['id']; ?>">
                <div class="form-group">
                    <label for="productName">Product Name</label>
                    <input type="text" id="productName" class="form-control" value="<?php echo htmlspecialchars($restock_product['name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="productSKU">SKU</label>
                    <input type="text" id="productSKU" class="form-control" value="<?php echo htmlspecialchars($restock_product['sku']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="currentStock">Current Stock</label>
                    <input type="text" id="currentStock" class="form-control" value="<?php echo $restock_product['current_stock']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="restockQuantity">Quantity to Add</label>
                    <input type="number" id="restockQuantity" name="quantity" class="form-control" min="1" value="10" required>
                </div>
                <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select id="supplier" name="supplier_id" class="form-control" required>
                        <option value="">Select Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo $supplier['id']; ?>" <?php echo $supplier['id'] == $restock_product['supplier_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($supplier['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <a href="inventory.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Confirm Restock</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Close alert banner
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('.close');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.parentElement.style.display = 'none';
                });
            });
            
            // Auto-submit filter form when selections change
            const filterSelects = document.querySelectorAll('.filter-select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.form) {
                        this.form.submit();
                    }
                });
            });
        });
        
        // Function to open restock modal (for JavaScript-only functionality)
        function openRestockModal(productName, sku, currentStock) {
            // This function would be used if we were using JavaScript modals
            // Currently using PHP-based modal display
            console.log('Restocking:', productName, sku, currentStock);
        }
        
        function closeRestockModal() {
            // This function would close JavaScript modals
            window.location.href = 'inventory.php';
        }
    </script>
</body>
</html>