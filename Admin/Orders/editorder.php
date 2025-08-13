<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) die("Connection failed: " . mysqli_connect_error());

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "Order ID not provided.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];

    $update_sql = "UPDATE orders SET status = '$new_status' WHERE order_id = $order_id";
    mysqli_query($conn, $update_sql);
    
    echo "<script>alert('Order updated!'); window.location.href='admin_orders.php';</script>";
    exit;
}

// Fetch order
$sql = "SELECT o.*, p.name AS product_name FROM orders o
        JOIN products p ON o.product_id = p.product_id
        WHERE o.order_id = $order_id";
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "Order not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Order</title>
  <style>
    body { font-family: Arial; padding: 40px; background: #f4f4f4; }
    .card {
      background: white; padding: 20px; max-width: 500px;
      margin: auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label { display: block; margin: 10px 0 5px; }
    select, input[type="submit"] {
      width: 100%; padding: 10px; margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="card">
  <h2>Edit Order #<?= $order_id ?></h2>
  <p><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
  <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>

  <form method="POST">
    <label for="status">Update Status:</label>
    <select name="status" id="status" required>
      <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
      <option value="Processing" <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
      <option value="Shipped" <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
      <option value="Delivered" <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
      <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>

    <input type="submit" value="Update Order">
  </form>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
