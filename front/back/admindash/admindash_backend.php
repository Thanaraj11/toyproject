<?php
// Include database connection
include '../../databse/db_connection.php';

// Function to get total sales
function getTotalSales($conn) {
    $query = "SELECT SUM(amount) as total_sales FROM sales";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total_sales'] ? $row['total_sales'] : 0;
}

// Function to get total orders
function getTotalOrders($conn) {
    $query = "SELECT COUNT(*) as total_orders FROM orders";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total_orders'] ? $row['total_orders'] : 0;
}

// Function to get total customers
function getTotalCustomers($conn) {
    $query = "SELECT COUNT(*) as total_customers FROM customers";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total_customers'] ? $row['total_customers'] : 0;
}

// Function to get total revenue
function getTotalRevenue($conn) {
    $query = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE status = 'completed'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total_revenue'] ? $row['total_revenue'] : 0;
}

// Function to get recent activities
function getRecentActivities($conn, $limit = 5) {
    $query = "SELECT * FROM activities ORDER BY created_at DESC LIMIT $limit";
    $result = mysqli_query($conn, $query);
    $activities = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = $row;
    }
    
    return $activities;
}

// Function to get sales data for chart
function getSalesData($conn) {
    $query = "SELECT DATE(created_at) as date, SUM(amount) as daily_sales 
              FROM sales 
              WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
              GROUP BY DATE(created_at) 
              ORDER BY date";
    $result = mysqli_query($conn, $query);
    $salesData = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $salesData[] = $row;
    }
    
    return $salesData;
}

// Close database connection
// mysqli_close($conn);
?>