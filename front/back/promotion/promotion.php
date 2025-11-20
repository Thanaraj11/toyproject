<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include necessary files
include '../../databse/db_connection.php';
include 'promotion_backend.php';

// Initialize variables
$message = '';
$message_type = ''; // success or error
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'discount-codes';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_promotion'])) {
        // Add new promotion
        $data = array(
            'name' => $_POST['name'],
            'code' => !empty($_POST['code']) ? $_POST['code'] : generatePromoCode($conn),
            'type' => $_POST['type'],
            'discount_type' => $_POST['discount_type'],
            'discount_value' => $_POST['discount_value'],
            'min_order_amount' => $_POST['min_order_amount'] ?? 0,
            'max_discount_amount' => $_POST['max_discount_amount'] ?? NULL,
            'usage_limit' => $_POST['usage_limit'] ?? NULL,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'] ?? NULL,
            'status' => $_POST['status'],
            'applicable_categories' => implode(',', $_POST['applicable_categories'] ?? []),
            'applicable_products' => implode(',', $_POST['applicable_products'] ?? [])
        );
        
        if (addPromotion($conn, $data)) {
            $message = 'Promotion created successfully!';
            $message_type = 'success';
            $current_tab = 'discount-codes';
        } else {
            $message = 'Error creating promotion. Please try again.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['update_promotion'])) {
        // Update promotion
        $promotion_id = intval($_POST['promotion_id']);
        $data = array(
            'name' => $_POST['name'],
            'code' => $_POST['code'],
            'type' => $_POST['type'],
            'discount_type' => $_POST['discount_type'],
            'discount_value' => $_POST['discount_value'],
            'min_order_amount' => $_POST['min_order_amount'] ?? 0,
            'max_discount_amount' => $_POST['max_discount_amount'] ?? NULL,
            'usage_limit' => $_POST['usage_limit'] ?? NULL,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'] ?? NULL,
            'status' => $_POST['status'],
            'applicable_categories' => implode(',', $_POST['applicable_categories'] ?? []),
            'applicable_products' => implode(',', $_POST['applicable_products'] ?? [])
        );
        
        if (updatePromotion($conn, $promotion_id, $data)) {
            $message = 'Promotion updated successfully!';
            $message_type = 'success';
            $current_tab = 'discount-codes';
        } else {
            $message = 'Error updating promotion. Please try again.';
            $message_type = 'error';
        }
    }
}

// Handle delete requests
if (isset($_GET['delete_promotion'])) {
    $promotion_id = intval($_GET['delete_promotion']);
    if (deletePromotion($conn, $promotion_id)) {
        $message = 'Promotion deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error deleting promotion. Please try again.';
        $message_type = 'error';
    }
}

// Handle copy code request
if (isset($_GET['copy_code'])) {
    $code = $_GET['copy_code'];
    $_SESSION['copied_code'] = $code;
    $message = 'Promo code copied to clipboard!';
    $message_type = 'success';
}

// Apply filters
$filters = array();
if (isset($_GET['status']) && $_GET['status'] != 'all') {
    $filters['status'] = $_GET['status'];
}
if (isset($_GET['type']) && $_GET['type'] != 'all') {
    $filters['type'] = $_GET['type'];
}
if (isset($_GET['discount_type']) && $_GET['discount_type'] != 'all') {
    $filters['discount_type'] = $_GET['discount_type'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Get data for display
$promotion_stats = getPromotionStats($conn);
$promotions = getAllPromotions($conn, $filters);
$categories = getCategories($conn);

// Get promotion for editing
$edit_promotion = null;
if (isset($_GET['edit_promotion'])) {
    $edit_promotion_id = intval($_GET['edit_promotion']);
    $edit_promotion = getPromotionById($conn, $edit_promotion_id);
    $current_tab = 'create-promotion';
}

// Update promotion statuses based on current date
foreach ($promotions as $promotion) {
    updatePromotionStatus($conn, $promotion['id']);
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions Management - Admin Panel</title>
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
                <h1>Promotions Management</h1>
                <p>Create and manage discount codes and special offers</p>
            </div>
            <div class="header-actions">
                <form method="GET" action="" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search promotions..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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
                <?php if (isset($_SESSION['copied_code'])): ?>
                    <br><small>Code: <?php echo htmlspecialchars($_SESSION['copied_code']); ?></small>
                    <?php unset($_SESSION['copied_code']); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon codes">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $promotion_stats['total_codes']; ?></h3>
                    <p>Total Discount Codes</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $promotion_stats['active_promotions']; ?></h3>
                    <p>Active Promotions</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon usage">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $promotion_stats['total_usage']; ?></h3>
                    <p>Total Usage</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>$<?php echo number_format($promotion_stats['revenue_generated'], 2); ?></h3>
                    <p>Revenue Generated</p>
                </div>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="content-tabs">
            <div class="tab <?php echo $current_tab == 'discount-codes' ? 'active' : ''; ?>" onclick="showTab('discount-codes')">Discount Codes</div>
            <div class="tab <?php echo $current_tab == 'special-offers' ? 'active' : ''; ?>" onclick="showTab('special-offers')">Special Offers</div>
            <div class="tab <?php echo $current_tab == 'create-promotion' ? 'active' : ''; ?>" onclick="showTab('create-promotion')">Create Promotion</div>
        </div>

        <!-- Discount Codes -->
        <div id="discount-codes" class="tab-content <?php echo $current_tab == 'discount-codes' ? 'active' : ''; ?>">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <form method="GET" action="" class="filter-form">
                    <input type="hidden" name="tab" value="discount-codes">
                    <div class="filter-group">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['status']) ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="active" <?php echo isset($filters['status']) && $filters['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="upcoming" <?php echo isset($filters['status']) && $filters['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                            <option value="expired" <?php echo isset($filters['status']) && $filters['status'] == 'expired' ? 'selected' : ''; ?>>Expired</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="type-filter">Discount Type:</label>
                        <select id="type-filter" name="discount_type" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['discount_type']) ? 'selected' : ''; ?>>All Types</option>
                            <option value="percentage" <?php echo isset($filters['discount_type']) && $filters['discount_type'] == 'percentage' ? 'selected' : ''; ?>>Percentage</option>
                            <option value="fixed" <?php echo isset($filters['discount_type']) && $filters['discount_type'] == 'fixed' ? 'selected' : ''; ?>>Fixed Amount</option>
                            <option value="shipping" <?php echo isset($filters['discount_type']) && $filters['discount_type'] == 'shipping' ? 'selected' : ''; ?>>Free Shipping</option>
                        </select>
                    </div>
                    <?php if (isset($_GET['status']) || isset($_GET['discount_type']) || isset($_GET['search'])): ?>
                        <div class="filter-group">
                            <a href="?tab=discount-codes" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Discount Codes Table -->
            <div class="promotions-table-container">
                <div class="promotions-header">
                    <h2 class="promotions-title">Discount Codes (<?php echo count($promotions); ?>)</h2>
                    <button class="btn btn-primary" onclick="showTab('create-promotion')">
                        <i class="fas fa-plus"></i> Create Code
                    </button>
                </div>
                
                <?php if (empty($promotions)): ?>
                    <div class="no-data">
                        <p>No discount codes found.</p>
                    </div>
                <?php else: ?>
                    <table class="promotions-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Usage</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($promotions as $promotion): 
                                if ($promotion['type'] != 'discount-code') continue;
                                
                                $usage_display = $promotion['usage_count'] . 
                                               ($promotion['usage_limit'] ? '/' . $promotion['usage_limit'] : '/∞');
                                $discount_display = getDiscountDisplay($promotion['discount_type'], $promotion['discount_value']);
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($promotion['code']); ?></strong></td>
                                    <td><span class="discount-badge"><?php echo $discount_display; ?></span></td>
                                    <td><?php echo $usage_display; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($promotion['start_date'])); ?></td>
                                    <td><?php echo $promotion['end_date'] ? date('M j, Y', strtotime($promotion['end_date'])) : 'No expiry'; ?></td>
                                    <td>
                                        <span class="status status-<?php echo $promotion['status']; ?>">
                                            <?php echo ucfirst($promotion['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?edit_promotion=<?php echo $promotion['id']; ?>" class="action-btn action-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?copy_code=<?php echo urlencode($promotion['code']); ?>" class="action-btn action-copy">
                                            <i class="fas fa-copy"></i>
                                        </a>
                                        <a href="?delete_promotion=<?php echo $promotion['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure you want to delete this promotion?')">
                                            <i class="fas fa-trash"></i>
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

        <!-- Special Offers -->
        <div id="special-offers" class="tab-content <?php echo $current_tab == 'special-offers' ? 'active' : ''; ?>" style="<?php echo $current_tab == 'special-offers' ? '' : 'display: none;'; ?>">
            <div class="filter-bar">
                <form method="GET" action="" class="filter-form">
                    <input type="hidden" name="tab" value="special-offers">
                    <div class="filter-group">
                        <label for="offer-status-filter">Status:</label>
                        <select id="offer-status-filter" name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['status']) ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="active" <?php echo isset($filters['status']) && $filters['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="upcoming" <?php echo isset($filters['status']) && $filters['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                            <option value="expired" <?php echo isset($filters['status']) && $filters['status'] == 'expired' ? 'selected' : ''; ?>>Expired</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="offer-type-filter">Offer Type:</label>
                        <select id="offer-type-filter" name="type" class="filter-select" onchange="this.form.submit()">
                            <option value="all" <?php echo empty($filters['type']) ? 'selected' : ''; ?>>All Types</option>
                            <option value="special-offer" <?php echo isset($filters['type']) && $filters['type'] == 'special-offer' ? 'selected' : ''; ?>>Special Offers</option>
                            <option value="flash-sale" <?php echo isset($filters['type']) && $filters['type'] == 'flash-sale' ? 'selected' : ''; ?>>Flash Sales</option>
                        </select>
                    </div>
                    <?php if (isset($_GET['status']) || isset($_GET['type']) || isset($_GET['search'])): ?>
                        <div class="filter-group">
                            <a href="?tab=special-offers" class="btn btn-secondary">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <div class="offers-grid">
                <?php 
                $special_offers = array_filter($promotions, function($promotion) {
                    return $promotion['type'] != 'discount-code';
                });
                
                if (empty($special_offers)): ?>
                    <div class="no-data">
                        <p>No special offers found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($special_offers as $offer): 
                        $discount_display = getDiscountDisplay($offer['discount_type'], $offer['discount_value']);
                    ?>
                        <div class="offer-card">
                            <div class="offer-header">
                                <h3 class="offer-title"><?php echo htmlspecialchars($offer['name']); ?></h3>
                                <span class="status status-<?php echo $offer['status']; ?>"><?php echo ucfirst($offer['status']); ?></span>
                            </div>
                            <div class="offer-content">
                                <div class="offer-details">
                                    <div class="offer-detail">
                                        <span class="detail-label">Type:</span>
                                        <span class="detail-value"><?php echo ucfirst(str_replace('-', ' ', $offer['type'])); ?></span>
                                    </div>
                                    <div class="offer-detail">
                                        <span class="detail-label">Discount:</span>
                                        <span class="detail-value"><?php echo $discount_display; ?></span>
                                    </div>
                                    <div class="offer-detail">
                                        <span class="detail-label">Code:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($offer['code']); ?></span>
                                    </div>
                                    <div class="offer-detail">
                                        <span class="detail-label">Period:</span>
                                        <span class="detail-value">
                                            <?php echo date('M j', strtotime($offer['start_date'])); ?> - 
                                            <?php echo $offer['end_date'] ? date('M j, Y', strtotime($offer['end_date'])) : 'No expiry'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="offer-actions">
                                    <a href="?edit_promotion=<?php echo $offer['id']; ?>" class="action-btn action-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete_promotion=<?php echo $offer['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure you want to delete this offer?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Create/Edit Promotion Form -->
        <div id="create-promotion" class="tab-content <?php echo $current_tab == 'create-promotion' ? 'active' : ''; ?>" style="<?php echo $current_tab == 'create-promotion' ? '' : 'display: none;'; ?>">
            <div class="form-container">
                <h2 class="form-title"><?php echo $edit_promotion ? 'Edit Promotion' : 'Create New Promotion'; ?></h2>
                <form method="POST" action="">
                    <?php if ($edit_promotion): ?>
                        <input type="hidden" name="update_promotion" value="1">
                        <input type="hidden" name="promotion_id" value="<?php echo $edit_promotion['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_promotion" value="1">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="promotionType">Promotion Type</label>
                            <select id="promotionType" name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="discount-code" <?php echo ($edit_promotion && $edit_promotion['type'] == 'discount-code') ? 'selected' : ''; ?>>Discount Code</option>
                                <option value="special-offer" <?php echo ($edit_promotion && $edit_promotion['type'] == 'special-offer') ? 'selected' : ''; ?>>Special Offer</option>
                                <option value="flash-sale" <?php echo ($edit_promotion && $edit_promotion['type'] == 'flash-sale') ? 'selected' : ''; ?>>Flash Sale</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="promotionName">Promotion Name</label>
                            <input type="text" id="promotionName" name="name" class="form-control" 
                                   value="<?php echo $edit_promotion ? htmlspecialchars($edit_promotion['name']) : ''; ?>" 
                                   placeholder="Enter promotion name" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="discountType">Discount Type</label>
                            <select id="discountType" name="discount_type" class="form-control" required>
                                <option value="">Select Discount Type</option>
                                <option value="percentage" <?php echo ($edit_promotion && $edit_promotion['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Percentage</option>
                                <option value="fixed" <?php echo ($edit_promotion && $edit_promotion['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount</option>
                                <option value="shipping" <?php echo ($edit_promotion && $edit_promotion['discount_type'] == 'shipping') ? 'selected' : ''; ?>>Free Shipping</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="discountValue">Discount Value</label>
                            <input type="number" id="discountValue" name="discount_value" class="form-control" 
                                   value="<?php echo $edit_promotion ? $edit_promotion['discount_value'] : ''; ?>" 
                                   placeholder="Enter discount value" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="promoCode">Promo Code</label>
                            <input type="text" id="promoCode" name="code" class="form-control" 
                                   value="<?php echo $edit_promotion ? htmlspecialchars($edit_promotion['code']) : ''; ?>" 
                                   placeholder="Enter promo code" required>
                            <small style="color: #6c757d; font-size: 0.875rem;">Leave blank to auto-generate</small>
                        </div>
                        <div class="form-group">
                            <label for="usageLimit">Usage Limit</label>
                            <input type="number" id="usageLimit" name="usage_limit" class="form-control" 
                                   value="<?php echo $edit_promotion ? $edit_promotion['usage_limit'] : ''; ?>" 
                                   placeholder="Enter usage limit" min="0">
                            <small style="color: #6c757d; font-size: 0.875rem;">Leave blank for unlimited usage</small>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="startDate">Start Date</label>
                            <input type="datetime-local" id="startDate" name="start_date" class="form-control" 
                                   value="<?php echo $edit_promotion ? date('Y-m-d\TH:i', strtotime($edit_promotion['start_date'])) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="endDate">End Date</label>
                            <input type="datetime-local" id="endDate" name="end_date" class="form-control" 
                                   value="<?php echo $edit_promotion && $edit_promotion['end_date'] ? date('Y-m-d\TH:i', strtotime($edit_promotion['end_date'])) : ''; ?>">
                            <small style="color: #6c757d; font-size: 0.875rem;">Leave blank for no expiration</small>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="minOrderAmount">Minimum Order Amount ($)</label>
                            <input type="number" id="minOrderAmount" name="min_order_amount" class="form-control" 
                                   value="<?php echo $edit_promotion ? $edit_promotion['min_order_amount'] : '0'; ?>" 
                                   placeholder="Enter minimum order amount" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="maxDiscountAmount">Maximum Discount ($)</label>
                            <input type="number" id="maxDiscountAmount" name="max_discount_amount" class="form-control" 
                                   value="<?php echo $edit_promotion ? $edit_promotion['max_discount_amount'] : ''; ?>" 
                                   placeholder="Enter maximum discount" min="0" step="0.01">
                            <small style="color: #6c757d; font-size: 0.875rem;">For percentage discounts only</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="applicableCategories">Applicable Categories</label>
                        <select id="applicableCategories" name="applicable_categories[]" class="form-control" multiple>
                            <option value="all" <?php echo ($edit_promotion && strpos($edit_promotion['applicable_categories'], 'all') !== false) ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['slug']; ?>" 
                                        <?php echo ($edit_promotion && strpos($edit_promotion['applicable_categories'], $category['slug']) !== false) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: #6c757d; font-size: 0.875rem;">Hold Ctrl/Cmd to select multiple</small>
                    </div>

                    <div class="form-group">
                        <label for="promotionStatus">Status</label>
                        <select id="promotionStatus" name="status" class="form-control" required>
                            <option value="active" <?php echo ($edit_promotion && $edit_promotion['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($edit_promotion && $edit_promotion['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="showTab('discount-codes')">Cancel</button>
                        <button type="submit" class="btn btn-primary"><?php echo $edit_promotion ? 'Update Promotion' : 'Create Promotion'; ?></button>
                    </div>
                </form>
            </div>
        </div>
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
            
            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
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
            
            // Set active tab based on URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                showTab(tabParam);
            }
            
            // Auto-generate promo code if empty
            const promoCodeInput = document.getElementById('promoCode');
            if (promoCodeInput && !promoCodeInput.value) {
                promoCodeInput.addEventListener('blur', function() {
                    if (!this.value) {
                        // Generate a simple code (in real implementation, this would call the server)
                        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                        let code = '';
                        for (let i = 0; i < 8; i++) {
                            code += chars.charAt(Math.floor(Math.random() * chars.length));
                        }
                        this.value = code;
                    }
                });
            }
        });
        
        // Functions for action buttons
        function editDiscountCode(code) {
            // This would typically redirect to edit form with the code
            console.log('Editing code:', code);
            // window.location.href = '?edit_promotion=' + code;
        }
        
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                alert('Promo code copied to clipboard: ' + code);
            }, function() {
                alert('Failed to copy code to clipboard');
            });
        }
        
        function deleteCode(code) {
            if (confirm('Are you sure you want to delete the promotion code: ' + code + '?')) {
                // This would typically make an AJAX call or redirect to delete endpoint
                console.log('Deleting code:', code);
                // window.location.href = '?delete_promotion=' + code;
            }
        }
    </script>
</body>
</html>