<?php
// Include necessary files
include '../../databse/db_connection.php';
include 'admindash_backend.php';

 session_start();
// Prevent unauthorized access
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin/login.php");
    exit();
}

// Get dashboard data
$totalSales = getTotalSales($conn);
$totalOrders = getTotalOrders($conn);
$totalCustomers = getTotalCustomers($conn);
$totalRevenue = getTotalRevenue($conn);
$recentActivities = getRecentActivities($conn);
$salesData = getSalesData($conn);

// Close database connection
// mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <link rel="stylesheet" href="../back.css">
    <link rel="stylesheet" href="../back1.css">

</head>
<body>
<?php include '../header2.php' ?>
    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1>Dashboard</h1>
                <p>Welcome back, Admin! Here's what's happening today.</p>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="user-profile">
                    <div class="user-avatar">A</div>
                    <div class="user-info">
                        <h4>Admin User</h4>
                    </div>
                </div>
            </div>
        </header>

        <!-- Quick Stats -->
        <section class="dashboard-content">
            <div class="card stat-card">
                <div class="stat-icon sales">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>$<?php echo number_format($totalSales, 2); ?></h3>
                    <p>Total Sales</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon orders">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($totalOrders); ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($totalCustomers); ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
            <div class="card stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </section>

        <!-- Sales Overview -->
        <section class="charts-section">
            <div class="card">
                <h2>Sales Overview</h2>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h2>Recent Activity</h2>
                <ul class="activity-list">
                    <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $activity): ?>
                            <li class="activity-item">
                                <div class="activity-icon <?php echo $activity['type']; ?>">
                                    <i class="fas <?php echo $activity['icon']; ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <h4><?php echo $activity['title']; ?></h4>
                                    <p><?php echo $activity['description']; ?></p>
                                </div>
                                <div class="activity-time"><?php echo timeAgo($activity['created_at']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="activity-item">
                            <div class="activity-content">
                                <p>No recent activities</p>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </section>
    </main>

    <script>
        // Sales Chart
        const salesChart = document.getElementById('salesChart').getContext('2d');
        
        // Prepare chart data from PHP
        const salesData = <?php echo json_encode($salesData); ?>;
        const labels = salesData.map(item => item.date);
        const data = salesData.map(item => item.daily_sales);
        
        new Chart(salesChart, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Sales',
                    data: data,
                    borderColor: '#4a90e2',
                    backgroundColor: 'rgba(74, 144, 226, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Sales Over Time'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
// Helper function to format time ago
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $current = time();
    $diff = $current - $time;
    
    if ($diff < 60) {
        return $diff . "s ago";
    } elseif ($diff < 3600) {
        return floor($diff / 60) . "m ago";
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . "h ago";
    } else {
        return floor($diff / 86400) . "d ago";
    }
}
?>