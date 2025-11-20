<?php
// useracc/orderhistory/orderdetails.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../useracc/login/login.php");
    exit();
}

// Include order history functions
include 'orderhistory_backend.php';

// Get user_id from session
$user_id = $_SESSION['user_id'];

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_details = $order_id ? getOrderDetails($order_id, $user_id) : null;

if (!$order_details) {
    echo "Order not found or you don't have permission to view this order.";
    exit();
}

$order_header = $order_details['header'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .back-link { 
            display: inline-block; 
            margin-bottom: 2rem;
            padding: 0.5rem 1rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background: #0056b3;
        }
        .order-details { 
            margin: 2rem 0; 
        }
        .order-header { 
            background: #f8f9fa; 
            padding: 1.5rem; 
            border-radius: 8px; 
            margin-bottom: 2rem;
            border-left: 4px solid #007bff;
        }
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .info-group {
            margin-bottom: 0.5rem;
        }
        .info-label {
            font-weight: 600;
            color: #555;
        }
        .order-items table { 
            width: 100%; 
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .order-items th, .order-items td { 
            padding: 12px; 
            border-bottom: 1px solid #ddd; 
            text-align: left;
        }
        .order-items th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .order-items tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #cce7ff; color: #004085; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #e8f5e8; color: #2e7d32; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .payment-pending { background: #fff3cd; color: #856404; }
        .payment-paid { background: #d4edda; color: #155724; }
        .payment-failed { background: #f8d7da; color: #721c24; }
        .payment-refunded { background: #e2e3e5; color: #383d41; }
        .address-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin: 2rem 0;
        }
        .address-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .address-card h4 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }
        .totals-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 2rem;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        .total-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }
        @media (max-width: 768px) {
            .address-section {
                grid-template-columns: 1fr;
            }
            .order-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="orderhistory.php" class="back-link">← Back to Order History</a>
        
        <div class="order-details">
            <div class="order-header">
                <h2>Order #<?php echo htmlspecialchars($order_header['order_number']); ?></h2>
                <div class="order-info-grid">
                    <div class="info-group">
                        <span class="info-label">Order Date:</span>
                        <span><?php echo formatOrderDate($order_header['created_at']); ?></span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Status:</span>
                        <span class="status-badge <?php echo getStatusClass($order_header['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($order_header['status'])); ?>
                        </span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Payment Status:</span>
                        <span class="status-badge <?php echo getPaymentStatusClass($order_header['payment_status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($order_header['payment_status'])); ?>
                        </span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Payment Method:</span>
                        <span><?php echo htmlspecialchars($order_header['payment_method'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <?php if ($order_header['shipping_address_id'] || $order_header['billing_address_id']): ?>
            <div class="address-section">
                <?php if ($order_header['shipping_address_id']): ?>
                <div class="address-card">
                    <h4>Shipping Address</h4>
                    <p><strong><?php echo htmlspecialchars($order_header['shipping_name'] ?? ''); ?></strong></p>
                    <p><?php echo htmlspecialchars($order_header['shipping_address1'] ?? ''); ?></p>
                    <?php if (!empty($order_header['shipping_address2'])): ?>
                    <p><?php echo htmlspecialchars($order_header['shipping_address2']); ?></p>
                    <?php endif; ?>
                    <p>
                        <?php echo htmlspecialchars($order_header['shipping_city'] ?? ''); ?>, 
                        <?php echo htmlspecialchars($order_header['shipping_state'] ?? ''); ?> 
                        <?php echo htmlspecialchars($order_header['shipping_zip'] ?? ''); ?>
                    </p>
                    <p><?php echo htmlspecialchars($order_header['shipping_country'] ?? ''); ?></p>
                </div>
                <?php endif; ?>

                <?php if ($order_header['billing_address_id']): ?>
                <div class="address-card">
                    <h4>Billing Address</h4>
                    <p><strong><?php echo htmlspecialchars($order_header['billing_name'] ?? ''); ?></strong></p>
                    <p><?php echo htmlspecialchars($order_header['billing_address1'] ?? ''); ?></p>
                    <?php if (!empty($order_header['billing_address2'])): ?>
                    <p><?php echo htmlspecialchars($order_header['billing_address2']); ?></p>
                    <?php endif; ?>
                    <p>
                        <?php echo htmlspecialchars($order_header['billing_city'] ?? ''); ?>, 
                        <?php echo htmlspecialchars($order_header['billing_state'] ?? ''); ?> 
                        <?php echo htmlspecialchars($order_header['billing_zip'] ?? ''); ?>
                    </p>
                    <p><?php echo htmlspecialchars($order_header['billing_country'] ?? ''); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Order Items -->
            <div class="order-items">
                <h3>Order Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_details['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['product_sku'] ?? 'N/A'); ?></td>
                            <td><?php echo formatCurrency($item['product_price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo formatCurrency($item['total_price']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Totals -->
            <div class="totals-section">
                <h3>Order Summary</h3>
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span><?php echo formatCurrency($order_header['subtotal_amount']); ?></span>
                </div>
                <?php if ($order_header['shipping_amount'] > 0): ?>
                <div class="total-row">
                    <span>Shipping:</span>
                    <span><?php echo formatCurrency($order_header['shipping_amount']); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order_header['tax_amount'] > 0): ?>
                <div class="total-row">
                    <span>Tax:</span>
                    <span><?php echo formatCurrency($order_header['tax_amount']); ?></span>
                </div>
                <?php endif; ?>
                <div class="total-row">
                    <span>Total:</span>
                    <span><?php echo formatCurrency($order_header['total_amount']); ?></span>
                </div>
            </div>

            <!-- Order Notes -->
            <?php if (!empty($order_header['notes'])): ?>
            <div class="address-card" style="margin-top: 2rem;">
                <h4>Order Notes</h4>
                <p><?php echo nl2br(htmlspecialchars($order_header['notes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>