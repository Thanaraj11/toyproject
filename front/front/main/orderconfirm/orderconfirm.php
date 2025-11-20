

<?php
// Start session to get order data
session_start();
// session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../useracc/login/login.php");
    exit();
}
require_once '../../../databse/db_connection.php';

// Include order functions
include 'orderconfirm_backend.php';

// Check if order was completed
if (!isset($_SESSION['order_complete']) || !$_SESSION['order_complete']) {
    header("Location: ../cart/cart.php");
    exit();
}

// Get order summary from session
$order_summary = $_SESSION['order_summary'] ?? calculateOrderSummary();

// Create order in database
$order_id = createOrderInDatabase();

if ($order_id) {
    // Generate order number for display
    $order_number = 'ORD' . date('YmdHis') . $order_id;
    
    // Calculate delivery date
    $delivery_date = date('F j, Y', strtotime('+5 weekdays'));
} else {
    // Handle database error
    $order_number = 'PENDING_' . uniqid();
    $delivery_date = 'To be confirmed';
}

// Clear session after displaying confirmation
unset($_SESSION['order_complete']);
unset($_SESSION['cart']);

// Helper function to calculate order summary (fallback)
function calculateOrderSummary() {
    $cart_items = [];
    $subtotal = 0;
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $price = rand(10, 100);
            $item_total = $price * $quantity;
            $subtotal += $item_total;
            
            $cart_items[] = [
                'product_id' => $product_id,
                'name' => "Product " . $product_id,
                'price' => $price,
                'quantity' => $quantity,
                'total' => $item_total
            ];
        }
    }
    
    $shipping_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : 'standard';
    $shipping_cost = getShippingCost($shipping_method);
    $order_total = $subtotal + $shipping_cost;
    
    return [
        'items' => $cart_items,
        'subtotal' => $subtotal,
        'shipping_method' => $shipping_method,
        'shipping_cost' => $shipping_cost,
        'order_total' => $order_total
    ];
}

function getShippingCost($method) {
    $shipping_costs = [
        'standard' => 5.00,
        'express' => 15.00,
        'overnight' => 25.00
    ];
    
    return isset($shipping_costs[$method]) ? $shipping_costs[$method] : 5.00;
}

function getShippingMethodName($method) {
    $method_names = [
        'standard' => 'Standard Shipping',
        'express' => 'Express Shipping',
        'overnight' => 'Overnight Shipping'
    ];
    
    return isset($method_names[$method]) ? $method_names[$method] : 'Standard Shipping';
}

// Function to create order in database
function createOrderInDatabase() {
    global $conn;
    
    if (!isset($_SESSION['order_summary']) || !isset($_SESSION['shipping_info']) || !isset($_SESSION['payment_info'])) {
        return false;
    }
    
    $order_summary = $_SESSION['order_summary'];
    $shipping_info = $_SESSION['shipping_info'];
    $payment_info = $_SESSION['payment_info'];
    
    // For demo, using customer ID 1 - in real app, use logged-in customer ID
    $customer_id = $_SESSION['user_id'];
    
    // Create shipping address
    $shipping_address_id = createAddress($shipping_info);
    $billing_address_id = $shipping_address_id; // Same as shipping for demo
    
    // Prepare order items
    $order_items = [];
    foreach ($order_summary['items'] as $item) {
        $order_items[] = [
            'product_id' => $item['product_id'],
            'product_name' => $item['name'],
            'product_sku' => $item['sku'] ?? 'SKU' . $item['product_id'],
            'price' => $item['price'],
            'quantity' => $item['quantity']
        ];
    }
    
    // Create order using the provided function
    $order_id = createOrder(
        $customer_id, 
        $order_items, 
        $order_summary['shipping_method'], 
        $payment_info['payment_method'], 
        $shipping_address_id, 
        $billing_address_id
    );
    
    return $order_id;
}

// Function to create address
function createAddress($address_info) {
    global $conn;
    
    $street = mysqli_real_escape_string($conn, $address_info['address1'] . ($address_info['address2'] ? ' ' . $address_info['address2'] : ''));
    $city = mysqli_real_escape_string($conn, $address_info['city']);
    $state = mysqli_real_escape_string($conn, 'CA'); // Default state
    $zip_code = mysqli_real_escape_string($conn, $address_info['postal']);
    $country = mysqli_real_escape_string($conn, $address_info['country']);
    
    $sql = "INSERT INTO addresses (street, city, state, zip_code, country) 
            VALUES ('$street', '$city', '$state', '$zip_code', '$country')";
    
    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
    }
    
    return 1; // Return default address ID if creation fails
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../../style1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Order Confirmation - ToyBox</title>
  <style>
    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .confirmation-header {
      text-align: center;
      background: #d4edda;
      padding: 2rem;
      border-radius: 8px;
      margin-bottom: 2rem;
      border: 1px solid #c3e6cb;
    }
    
    .confirmation-header h2 {
      color: #155724;
      margin-bottom: 1rem;
    }
    
    .confirmation-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .detail-card {
      background: white;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .detail-card h3 {
      color: #333;
      margin-bottom: 1rem;
      border-bottom: 2px solid #007bff;
      padding-bottom: 0.5rem;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
    }
    
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    
    th {
      background-color: #f8f9fa;
      font-weight: bold;
    }
    
    .order-totals {
      background: #f8f9fa;
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 2rem;
    }
    
    .order-totals p {
      margin: 0.5rem 0;
      display: flex;
      justify-content: space-between;
    }
    
    .order-total {
      font-size: 1.2em;
      font-weight: bold;
      border-top: 2px solid #dee2e6;
      padding-top: 1rem;
      margin-top: 1rem;
    }
    
    .action-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-top: 2rem;
    }
    
    .btn-primary {
      background-color: #007bff;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 4px;
      display: inline-block;
    }
    
    .btn-secondary {
      background-color: #6c757d;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 4px;
      display: inline-block;
    }
    
    .print-btn {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1em;
    }
  </style>
</head>
<body>
  <!-- <?php include '../header1.php'; ?> -->

  <main class="container">
    <!-- <nav aria-label="Breadcrumb" class="breadcrumb">
      <ol>
        <li><a href="../index/index.php">Home</a></li>
        <li><a href="../cart/cart.php">Shopping Cart</a></li>
        <li aria-current="page">Order Confirmation</li>
      </ol>
    </nav> -->

    <section class="confirmation-header">
      <h2><i class="fas fa-check-circle"></i> Thank you for your purchase!</h2>
      <p>Your order has been successfully placed and is being processed.</p>
      <p><strong>Order Number: <?php echo htmlspecialchars($order_number); ?></strong></p>
    </section>

    <div class="confirmation-details">
      <div class="detail-card">
        <h3>Order Information</h3>
        <p><strong>Order Date:</strong> <?php echo date('F j, Y'); ?></p>
        <p><strong>Order Status:</strong> Confirmed</p>
        <p><strong>Payment Status:</strong> Completed</p>
        <p><strong>Estimated Delivery:</strong> <?php echo $delivery_date; ?></p>
      </div>
      
      <div class="detail-card">
        <h3>Shipping Information</h3>
        <?php if (isset($_SESSION['shipping_info'])): ?>
          <?php $shipping = $_SESSION['shipping_info']; ?>
          <p><strong><?php echo htmlspecialchars($shipping['fullname']); ?></strong></p>
          <p><?php echo htmlspecialchars($shipping['address1']); ?></p>
          <?php if (!empty($shipping['address2'])): ?>
            <p><?php echo htmlspecialchars($shipping['address2']); ?></p>
          <?php endif; ?>
          <p><?php echo htmlspecialchars($shipping['city']); ?>, <?php echo htmlspecialchars($shipping['postal']); ?></p>
          <p><?php echo htmlspecialchars($shipping['country']); ?></p>
          <p><strong>Shipping Method:</strong> <?php echo getShippingMethodName($order_summary['shipping_method']); ?></p>
        <?php endif; ?>
      </div>
      
      <div class="detail-card">
        <h3>Payment Information</h3>
        <?php if (isset($_SESSION['payment_info'])): ?>
          <?php $payment = $_SESSION['payment_info']; ?>
          <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></p>
          <p><strong>Amount Paid:</strong> $<?php echo number_format($payment['amount'], 2); ?></p>
          <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($payment['transaction_id']); ?></p>
          <?php if ($payment['card_last_four']): ?>
            <p><strong>Card Ending:</strong> **** <?php echo htmlspecialchars($payment['card_last_four']); ?></p>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <section>
      <h3>Order Summary</h3>
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
          <?php foreach ($order_summary['items'] as $item): ?>
            <tr>
              <td><?php echo htmlspecialchars($item['name']); ?></td>
              <td><?php echo $item['quantity']; ?></td>
              <td>$<?php echo number_format($item['price'], 2); ?></td>
              <td>$<?php echo number_format($item['total'], 2); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <div class="order-totals">
        <p><span>Subtotal:</span> <span>$<?php echo number_format($order_summary['subtotal'], 2); ?></span></p>
        <p><span>Shipping:</span> <span>$<?php echo number_format($order_summary['shipping_cost'], 2); ?></span></p>
        <p class="order-total"><span>Total:</span> <span>$<?php echo number_format($order_summary['order_total'], 2); ?></span></p>
      </div>
    </section>

    <section>
      <h3>What's Next?</h3>
      <ul>
        <li>You will receive an order confirmation email shortly</li>
        <li>We will notify you when your order ships</li>
        <li>Track your order using your order number: <strong><?php echo htmlspecialchars($order_number); ?></strong></li>
        <li>For any questions, contact our support team</li>
      </ul>
      
      <div class="action-buttons">
        <a href="../index/index.php" class="btn-primary">Continue Shopping</a>
        <a href="../../informationalpages/contact/contact.php" class="btn-secondary">Contact Support</a>
        <button onclick="window.print()" class="print-btn">Print Order Details</button>
      </div>
    </section>
  </main>

  <!-- <?php include '../footer.php'; ?> -->

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    console.log('Order confirmation page loaded');
  });
  </script>
</body>
</html>