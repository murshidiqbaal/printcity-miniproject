<!-- my_orders.php -->
<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// This example filters by customer name from GET (can be session-based instead)
$customer_name = isset($_GET['name']) ? mysqli_real_escape_string($conn, $_GET['name']) : '';

$sql = "SELECT o.*, p.name AS product_name, p.image_path 
        FROM orders o
        JOIN products p ON o.product_id = p.product_id
        WHERE o.customer_name = '$customer_name'
        ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Orders</title>
  <style>
    body { font-family: Arial; padding: 30px; background-color: #f4f4f4; }
    .order-card {
      background: white;
      padding: 20px;
      margin-bottom: 15px;
      border-radius: 10px;
      box-shadow: 0 1px 6px rgba(0,0,0,0.1);
    }
    img {
      width: 120px; height: 120px; object-fit: cover; border-radius: 10px;
    }
    .info { margin-left: 140px; margin-top: -120px; }
    .info h3 { margin: 0 0 10px 0; }
    .status {
      padding: 5px 10px;
      border-radius: 5px;
      display: inline-block;
      background-color: #007bff;
      color: white;
      font-size: 14px;
    }
    .track-btn {
      margin-top: 10px;
      display: inline-block;
      padding: 6px 12px;
      background-color: #28a745;
      color: white;
      border-radius: 5px;
      text-decoration: none;
    }
    .track-btn:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<h2>📦 My Orders</h2>

<?php if (mysqli_num_rows($result) > 0): ?>
  <?php while ($order = mysqli_fetch_assoc($result)): ?>
    <div class="order-card">
      <img src="/miniproject/<?= htmlspecialchars($order['image_path']) ?>" alt="<?= htmlspecialchars($order['product_name']) ?>">
      <div class="info">
        <h3><?= htmlspecialchars($order['product_name']) ?></h3>
        <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
        <p><strong>Ordered on:</strong> <?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></p>
        <span class="status"><?= htmlspecialchars($order['status']) ?></span><br>
        <a href="trackorder.php?order_id=<?= $order['order_id'] ?>" class="track-btn">Track</a>
      </div>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p>No orders found for <strong><?= htmlspecialchars($customer_name) ?></strong>.</p>
<?php endif; ?>

</body>
</html>

<?php mysqli_close($conn); ?>
