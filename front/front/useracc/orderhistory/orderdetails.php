<?php
include 'orderhistory_backend.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_details = $order_id ? getOrderDetails($order_id, $user_id) : null;

if (!$order_details) {
    echo "Order not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <style>
        .order-details { margin: 2rem 0; }
        .order-header { background: #f8f9fa; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .order-items table { width: 100%; border-collapse: collapse; }
        .order-items th, .order-items td { padding: 12px; border-bottom: 1px solid #ddd; }
        .back-link { display: inline-block; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <a href="orderhistory.php" class="back-link">← Back to Order History</a>
    
    <div class="order-details">
        <div class="order-header">
            <h2>Order #<?php echo $order_details['header']['order_number']; ?></h2>
            <p>Date: <?php echo formatOrderDate($order_details['header']['order_date']); ?></p>
            <p>Status: <span class="status-badge <?php echo getStatusClass($order_details['header']['status']); ?>">
                <?php echo ucfirst($order_details['header']['status']); ?>
            </span></p>
            <p>Total: $<?php echo number_format($order_details['header']['order_total'], 2); ?></p>
        </div>
        
        <div class="order-items">
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_details['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>