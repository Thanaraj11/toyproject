<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include necessary files
include '../../databse/db_connection.php';
include 'report_backend.php';

// Initialize variables
$message = '';
$message_type = ''; // success or error

// Handle date range selection
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : 'last7';
$start_date = null;
$end_date = null;

// Calculate date range based on selection
switch ($date_range) {
    case 'today':
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        break;
    case 'yesterday':
        $start_date = date('Y-m-d', strtotime('-1 day'));
        $end_date = date('Y-m-d', strtotime('-1 day'));
        break;
    case 'last7':
        $start_date = date('Y-m-d', strtotime('-7 days'));
        $end_date = date('Y-m-d');
        break;
    case 'last30':
        $start_date = date('Y-m-d', strtotime('-30 days'));
        $end_date = date('Y-m-d');
        break;
    case 'month':
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        break;
    case 'last-month':
        $start_date = date('Y-m-01', strtotime('-1 month'));
        $end_date = date('Y-m-t', strtotime('-1 month'));
        break;
    case 'quarter':
        $quarter = ceil(date('n') / 3);
        $start_date = date('Y-m-d', mktime(0, 0, 0, ($quarter - 1) * 3 + 1, 1, date('Y')));
        $end_date = date('Y-m-t', mktime(0, 0, 0, $quarter * 3, 1, date('Y')));
        break;
    case 'year':
        $start_date = date('Y-01-01');
        $end_date = date('Y-12-31');
        break;
    case 'custom':
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $start_date = $_GET['start_date'];
            $end_date = $_GET['end_date'];
        }
        break;
}

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_report'])) {
    $report_type = $_POST['report_type'];
    $file_format = $_POST['file_format'];
    $custom_start = $_POST['custom_start'] ?? $start_date;
    $custom_end = $_POST['custom_end'] ?? $end_date;
    
    // Generate report data based on type
    switch ($report_type) {
        case 'sales':
            $report_data = generateSalesReport($conn, $custom_start, $custom_end, $file_format);
            $report_name = "Sales_Report_" . date('Y_m_d');
            break;
        case 'customers':
            $report_data = generateCustomerReport($conn, $custom_start, $custom_end, $file_format);
            $report_name = "Customer_Report_" . date('Y_m_d');
            break;
        case 'products':
            $report_data = generateProductReport($conn, $custom_start, $custom_end, $file_format);
            $report_name = "Product_Report_" . date('Y_m_d');
            break;
        case 'financial':
            $report_data = generateFinancialReport($conn, $custom_start, $custom_end, $file_format);
            $report_name = "Financial_Report_" . date('Y_m_d');
            break;
        default:
            $report_data = array();
            $report_name = "Report_" . date('Y_m_d');
    }
    
    if (!empty($report_data)) {
        // In a real implementation, you would generate the actual file here
        // For now, we'll just show a success message
        $message = "Report generated successfully! (" . count($report_data) . " records)";
        $message_type = 'success';
        
        // Save report record
        $report_record = array(
            'report_type' => $report_type,
            'report_name' => $report_name,
            'date_range_start' => $custom_start,
            'date_range_end' => $custom_end,
            'file_format' => $file_format,
            'file_path' => 'reports/' . $report_name . '.' . $file_format,
            'generated_by' => $_SESSION['admin_id'] ?? 1
        );
        
        saveReportRecord($conn, $report_record);
    } else {
        $message = "No data found for the selected criteria.";
        $message_type = 'error';
    }
}

// Get data for display
$stats = getReportStats($conn, $start_date, $end_date);
$sales_chart_data = getSalesChartData($conn, 'week');
$category_data = getCategorySalesData($conn);
$top_products = getTopSellingProducts($conn, 5);
$customer_activity = getCustomerActivity($conn, 5);

// Close database connection
// mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h1>Reports & Analytics</h1>
                <p>Track sales performance and customer activity</p>
            </div>
            <div class="header-actions">
                <form method="GET" action="" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search reports..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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

        <!-- Date Range Filter -->
        <div class="date-filter">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label for="date-range">Date Range:</label>
                    <select id="date-range" name="date_range" class="filter-select" onchange="this.form.submit()">
                        <option value="today" <?php echo $date_range == 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="yesterday" <?php echo $date_range == 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                        <option value="last7" <?php echo $date_range == 'last7' ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="last30" <?php echo $date_range == 'last30' ? 'selected' : ''; ?>>Last 30 Days</option>
                        <option value="month" <?php echo $date_range == 'month' ? 'selected' : ''; ?>>This Month</option>
                        <option value="last-month" <?php echo $date_range == 'last-month' ? 'selected' : ''; ?>>Last Month</option>
                        <option value="quarter" <?php echo $date_range == 'quarter' ? 'selected' : ''; ?>>This Quarter</option>
                        <option value="year" <?php echo $date_range == 'year' ? 'selected' : ''; ?>>This Year</option>
                        <option value="custom" <?php echo $date_range == 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                    </select>
                </div>
                
                <?php if ($date_range == 'custom'): ?>
                <div class="filter-group" id="custom-range">
                    <label for="start-date">From:</label>
                    <input type="date" id="start-date" name="start_date" class="filter-input" value="<?php echo $start_date; ?>">
                    <label for="end-date">To:</label>
                    <input type="date" id="end-date" name="end_date" class="filter-input" value="<?php echo $end_date; ?>">
                </div>
                <?php endif; ?>
                
                <?php if ($date_range != 'last7'): ?>
                    <div class="filter-group">
                        <a href="reports.php" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon sales">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>$<?php echo number_format($stats['total_sales'], 2); ?></h3>
                    <p>Total Sales</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orders">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon customers">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['new_customers']; ?></h3>
                    <p>New Customers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon aov">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3>$<?php echo number_format($stats['avg_order_value'], 2); ?></h3>
                    <p>Average Order Value</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-header">
                    <h2 class="chart-title">Sales Overview</h2>
                    <div class="chart-actions">
                        <button data-period="week" class="active">Week</button>
                        <button data-period="month">Month</button>
                        <button data-period="quarter">Quarter</button>
                        <button data-period="year">Year</button>
                    </div>
                </div>
                <div class="chart-canvas">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-header">
                    <h2 class="chart-title">Sales by Category</h2>
                    <div class="chart-actions">
                        <button data-type="percentage">%</button>
                        <button data-type="value" class="active">Value</button>
                    </div>
                </div>
                <div class="chart-canvas">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Reports Tables -->
        <div class="reports-section">
            <div class="report-container">
                <div class="report-header">
                    <h2 class="report-title">Top Selling Products</h2>
                    <button class="btn btn-secondary" onclick="exportReport('products')">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($top_products)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #6c757d;">No data available</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($top_products as $product): 
                                // Calculate trend (placeholder - in real implementation, compare with previous period)
                                $trend = rand(-20, 30) / 10;
                                $trend_class = $trend >= 0 ? 'trend-up' : 'trend-down';
                                $trend_icon = $trend >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo $product['units_sold'] ?? 0; ?></td>
                                    <td>$<?php echo number_format($product['revenue'] ?? 0, 2); ?></td>
                                    <td class="trend <?php echo $trend_class; ?>">
                                        <i class="fas <?php echo $trend_icon; ?>"></i> 
                                        <?php echo number_format(abs($trend), 1); ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="report-container">
                <div class="report-header">
                    <h2 class="report-title">Customer Activity</h2>
                    <button class="btn btn-secondary" onclick="exportReport('customers')">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Orders</th>
                            <th>Spent</th>
                            <th>Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customer_activity)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #6c757d;">No data available</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customer_activity as $customer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                                    <td><?php echo $customer['total_orders'] ?? 0; ?></td>
                                    <td>$<?php echo number_format($customer['total_spent'] ?? 0, 2); ?></td>
                                    <td><?php echo $customer['last_activity'] ? formatTimeAgo($customer['last_activity']) : 'Never'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <div class="export-header">
                <h2 class="export-title">Export Reports</h2>
                <p>Generate and download detailed reports in various formats</p>
            </div>
            <form method="POST" action="" class="export-options">
                <input type="hidden" name="generate_report" value="1">
                <div class="filter-group">
                    <label for="export-report">Report Type:</label>
                    <select id="export-report" name="report_type" class="filter-select" required>
                        <option value="sales">Sales Report</option>
                        <option value="customers">Customer Activity</option>
                        <option value="products">Product Performance</option>
                        <option value="financial">Financial Summary</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="export-format">Format:</label>
                    <select id="export-format" name="file_format" class="filter-select" required>
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="custom-start">Start Date:</label>
                    <input type="date" id="custom-start" name="custom_start" class="filter-input" value="<?php echo $start_date; ?>">
                </div>
                <div class="filter-group">
                    <label for="custom-end">End Date:</label>
                    <input type="date" id="custom-end" name="custom_end" class="filter-input" value="<?php echo $end_date; ?>">
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Chart data from PHP
        const salesChartData = <?php echo json_encode($sales_chart_data); ?>;
        const categoryData = <?php echo json_encode($category_data); ?>;
        
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: salesChartData.map(item => {
                        if (item.date) return new Date(item.date).toLocaleDateString();
                        if (item.month && item.year) return `${item.year}-${item.month}`;
                        return '';
                    }),
                    datasets: [{
                        label: 'Sales ($)',
                        data: salesChartData.map(item => item.daily_sales || item.monthly_sales || 0),
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
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

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.category_name),
                    datasets: [{
                        data: categoryData.map(item => item.total_sales || 0),
                        backgroundColor: [
                            '#4361ee', '#f72585', '#4cc9f0', '#f8961e', '#4895ef',
                            '#3a0ca3', '#7209b7', '#f77f00', '#2ec4b6', '#e71d36'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: 'Sales by Category'
                        }
                    }
                }
            });

            // Chart period buttons
            document.querySelectorAll('.chart-actions button[data-period]').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.chart-actions button[data-period]').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // In a real implementation, this would reload chart data via AJAX
                    console.log('Switching to period:', this.dataset.period);
                });
            });

            // Chart type buttons
            document.querySelectorAll('.chart-actions button[data-type]').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.chart-actions button[data-type]').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // In a real implementation, this would switch chart display type
                    console.log('Switching to type:', this.dataset.type);
                });
            });
        });

        // Export report function
        function exportReport(type) {
            // In a real implementation, this would trigger the report generation
            console.log('Exporting report:', type);
            alert('Report export functionality would be implemented here');
        }

        // Show/hide custom date range
        document.getElementById('date-range').addEventListener('change', function() {
            const customRange = document.getElementById('custom-range');
            if (this.value === 'custom') {
                customRange.style.display = 'flex';
            } else {
                customRange.style.display = 'none';
            }
        });

        // Initialize custom range visibility
        document.addEventListener('DOMContentLoaded', function() {
            const dateRange = document.getElementById('date-range');
            const customRange = document.getElementById('custom-range');
            if (dateRange.value === 'custom') {
                customRange.style.display = 'flex';
            }
        });
    </script>
</body>
</html>