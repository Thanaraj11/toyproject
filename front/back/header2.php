<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: ../admin/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="../back.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" aria-label="Admin Sidebar">
        <div class="sidebar-header">
            <i class="fas fa-chart-line"></i>
            <h2>AdminPanel</h2>
        </div>
        <nav class="sidebar-menu">
            <div class="sidebar-item active">
                <a href="../admindash/admindash.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../order/order.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../productmanage/productmanage.php">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../customer/customer.php">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../category/category.php">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../inventory/inventory.php">
                    <i class="fas fa-warehouse"></i>
                    <span>Inventory</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../promotion/promotion.php">
                    <i class="fas fa-percent"></i>
                    <span>Promotions</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../content/content.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Content</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../report/reports.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="../admin/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>
</body>
</html>