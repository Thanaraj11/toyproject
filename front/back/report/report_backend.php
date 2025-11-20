<?php
// Include database connection
include '../../databse/db_connection.php';

// Function to get report statistics for a date range
function getReportStats($conn, $start_date = null, $end_date = null) {
    $stats = array();
    
    // Build WHERE clause for date range
    $date_where = "";
    if ($start_date && $end_date) {
        $date_where = " WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'";
    } elseif ($start_date) {
        $date_where = " WHERE o.created_at >= '$start_date'";
    } elseif ($end_date) {
        $date_where = " WHERE o.created_at <= '$end_date 23:59:59'";
    }
    
    // Total sales
    $query = "SELECT SUM(total_amount) as total_sales FROM orders o 
              WHERE o.status IN ('processing', 'shipped', 'delivered')" . 
              ($date_where ? str_replace('o.', '', $date_where) : '');
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $stats['total_sales'] = mysqli_fetch_assoc($result)['total_sales'] ?? 0;
    } else {
        $stats['total_sales'] = 0;
    }
    
    // Total orders
    $query = "SELECT COUNT(*) as total_orders FROM orders o 
              WHERE o.status IN ('processing', 'shipped', 'delivered')" . 
              ($date_where ? str_replace('o.', '', $date_where) : '');
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $stats['total_orders'] = mysqli_fetch_assoc($result)['total_orders'] ?? 0;
    } else {
        $stats['total_orders'] = 0;
    }
    
    // New customers
    $query = "SELECT COUNT(*) as new_customers FROM customers c";
    if ($start_date && $end_date) {
        $query .= " WHERE c.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'";
    } elseif ($start_date) {
        $query .= " WHERE c.created_at >= '$start_date'";
    } elseif ($end_date) {
        $query .= " WHERE c.created_at <= '$end_date 23:59:59'";
    }
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $stats['new_customers'] = mysqli_fetch_assoc($result)['new_customers'] ?? 0;
    } else {
        $stats['new_customers'] = 0;
    }
    
    // Average order value
    if ($stats['total_orders'] > 0) {
        $stats['avg_order_value'] = $stats['total_sales'] / $stats['total_orders'];
    } else {
        $stats['avg_order_value'] = 0;
    }
    
    return $stats;
}
// Function to get sales chart data
function getSalesChartData($conn, $period = 'week') {
    $data = array();
    
    switch ($period) {
        case 'week':
            $query = "SELECT 
                         DATE(created_at) as date,
                         SUM(total_amount) as daily_sales,
                         COUNT(*) as order_count
                      FROM orders 
                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        AND status IN ('processing', 'shipped', 'delivered')
                      GROUP BY DATE(created_at)
                      ORDER BY date ASC";
            break;
            
        case 'month':
            $query = "SELECT 
                         DATE(created_at) as date,
                         SUM(total_amount) as daily_sales,
                         COUNT(*) as order_count
                      FROM orders 
                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        AND status IN ('processing', 'shipped', 'delivered')
                      GROUP BY DATE(created_at)
                      ORDER BY date ASC";
            break;
            
        case 'quarter':
            $query = "SELECT 
                         YEAR(created_at) as year,
                         QUARTER(created_at) as quarter,
                         SUM(total_amount) as total_sales,
                         COUNT(*) as order_count
                      FROM orders 
                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                        AND status IN ('processing', 'shipped', 'delivered')
                      GROUP BY YEAR(created_at), QUARTER(created_at)
                      ORDER BY year, quarter ASC";
            break;
            
        case 'year':
            $query = "SELECT 
                         YEAR(created_at) as year,
                         MONTH(created_at) as month,
                         SUM(total_amount) as monthly_sales,
                         COUNT(*) as order_count
                      FROM orders 
                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                        AND status IN ('processing', 'shipped', 'delivered')
                      GROUP BY YEAR(created_at), MONTH(created_at)
                      ORDER BY year, month ASC";
            break;
            
        default:
            return $data;
    }
    
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    return $data;
}

// Function to get category sales data
function getCategorySalesData($conn, $type = 'value') {
    $query = "SELECT 
                 c.name as category_name,
                 SUM(oi.total_price) as total_sales,
                 SUM(oi.quantity) as units_sold,
                 COUNT(DISTINCT o.id) as order_count
              FROM categories c
              LEFT JOIN products p ON c.id = p.category_id
              LEFT JOIN order_items oi ON p.id = oi.product_id
              LEFT JOIN orders o ON oi.order_id = o.id
              WHERE o.status IN ('processing', 'shipped', 'delivered')
              GROUP BY c.id, c.name
              ORDER BY total_sales DESC";
    
    $result = mysqli_query($conn, $query);
    $data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    return $data;
}

// Function to get top selling products
function getTopSellingProducts($conn, $limit = 10) {
    $query = "SELECT 
                 p.name,
                 p.sku,
                 c.name as category,
                 SUM(oi.quantity) as units_sold,
                 SUM(oi.total_price) as revenue,
                 COUNT(DISTINCT oi.order_id) as order_count
              FROM products p
              LEFT JOIN order_items oi ON p.id = oi.product_id
              LEFT JOIN orders o ON oi.order_id = o.id
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE o.status IN ('processing', 'shipped', 'delivered')
              GROUP BY p.id, p.name, p.sku, c.name
              ORDER BY revenue DESC
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $products = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    return $products;
}

// Function to get customer activity
function getCustomerActivity($conn, $limit = 10) {
    $query = "SELECT 
                 c.id,
                 CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                 c.email,
                 COUNT(o.id) as total_orders,
                 SUM(o.total_amount) as total_spent,
                 MAX(o.created_at) as last_activity
              FROM customers c
              LEFT JOIN orders o ON c.id = o.customer_id
              WHERE o.status IN ('processing', 'shipped', 'delivered') OR o.id IS NULL
              GROUP BY c.id, c.first_name, c.last_name, c.email
              ORDER BY total_spent DESC
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $customers = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }
    
    return $customers;
}

// Function to generate sales report
function generateSalesReport($conn, $start_date, $end_date, $format = 'csv') {
    $query = "SELECT 
                 o.order_number,
                 o.created_at as order_date,
                 CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                 o.total_amount,
                 o.status,
                 COUNT(oi.id) as item_count,
                 SUM(oi.quantity) as total_quantity
              FROM orders o
              LEFT JOIN customers c ON o.customer_id = c.id
              LEFT JOIN order_items oi ON o.id = oi.order_id
              WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
                AND o.status IN ('processing', 'shipped', 'delivered')
              GROUP BY o.id, o.order_number, o.created_at, customer_name, o.total_amount, o.status
              ORDER BY o.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $report_data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $report_data[] = $row;
    }
    
    return $report_data;
}

// Function to generate customer report
function generateCustomerReport($conn, $start_date, $end_date, $format = 'csv') {
    $query = "SELECT 
                 c.id,
                 CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                 c.email,
                 c.phone,
                 c.created_at as join_date,
                 COUNT(o.id) as total_orders,
                 SUM(o.total_amount) as total_spent,
                 MAX(o.created_at) as last_order_date,
                 AVG(o.total_amount) as avg_order_value
              FROM customers c
              LEFT JOIN orders o ON c.id = o.customer_id
              WHERE (o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59' OR o.id IS NULL)
                AND (o.status IN ('processing', 'shipped', 'delivered') OR o.id IS NULL)
              GROUP BY c.id, c.first_name, c.last_name, c.email, c.phone, c.created_at
              ORDER BY total_spent DESC";
    
    $result = mysqli_query($conn, $query);
    $report_data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $report_data[] = $row;
    }
    
    return $report_data;
}

// Function to generate product report
function generateProductReport($conn, $start_date, $end_date, $format = 'csv') {
    $query = "SELECT 
                 p.name,
                 p.sku,
                 c.name as category,
                 p.price,
                 SUM(oi.quantity) as units_sold,
                 SUM(oi.total_price) as revenue,
                 COUNT(DISTINCT oi.order_id) as order_count,
                 AVG(oi.quantity) as avg_quantity_per_order
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              LEFT JOIN order_items oi ON p.id = oi.product_id
              LEFT JOIN orders o ON oi.order_id = o.id
              WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
                AND o.status IN ('processing', 'shipped', 'delivered')
              GROUP BY p.id, p.name, p.sku, c.name, p.price
              ORDER BY revenue DESC";
    
    $result = mysqli_query($conn, $query);
    $report_data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $report_data[] = $row;
    }
    
    return $report_data;
}

// Function to generate financial report
function generateFinancialReport($conn, $start_date, $end_date, $format = 'csv') {
    $query = "SELECT 
                 DATE(o.created_at) as date,
                 COUNT(o.id) as order_count,
                 SUM(o.total_amount) as total_sales,
                 SUM(o.total_amount - (SELECT SUM(oi.total_price) FROM order_items oi WHERE oi.order_id = o.id)) as total_profit,
                 AVG(o.total_amount) as avg_order_value,
                 COUNT(DISTINCT o.customer_id) as unique_customers
              FROM orders o
              WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
                AND o.status IN ('processing', 'shipped', 'delivered')
              GROUP BY DATE(o.created_at)
              ORDER BY date DESC";
    
    $result = mysqli_query($conn, $query);
    $report_data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $report_data[] = $row;
    }
    
    return $report_data;
}

// Function to save report record
function saveReportRecord($conn, $report_data) {
    $report_type = mysqli_real_escape_string($conn, $report_data['report_type']);
    $report_name = mysqli_real_escape_string($conn, $report_data['report_name']);
    $date_range_start = mysqli_real_escape_string($conn, $report_data['date_range_start']);
    $date_range_end = mysqli_real_escape_string($conn, $report_data['date_range_end']);
    $file_format = mysqli_real_escape_string($conn, $report_data['file_format']);
    $file_path = mysqli_real_escape_string($conn, $report_data['file_path']);
    $generated_by = intval($report_data['generated_by']);
    
    $query = "INSERT INTO reports (report_type, report_name, date_range_start, date_range_end, file_format, file_path, generated_by) 
              VALUES ('$report_type', '$report_name', '$date_range_start', '$date_range_end', '$file_format', '$file_path', $generated_by)";
    
    return mysqli_query($conn, $query);
}

// Function to format date for display
function formatDateDisplay($date) {
    return date('M j, Y', strtotime($date));
}

// Function to format date for "time ago" display
function formatTimeAgo($date) {
    $time = strtotime($date);
    $current = time();
    $diff = $current - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' days ago';
    } else {
        return date('M j, Y', $time);
    }
}

// Function to calculate trend (placeholder - in real implementation, this would compare with previous period)
function calculateTrend($current, $previous) {
    if ($previous == 0) return 0;
    return (($current - $previous) / $previous) * 100;
}
?>