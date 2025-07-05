<!-- track_order.php -->
<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_GET['order_id'])) {
    echo "<p>Order ID missing.</p>";
    exit;
}

$order_id = intval($_GET['order_id']);

$sql = "SELECT o.*, p.name AS product_name, p.image_path 
        FROM orders o
        JOIN products p ON o.product_id = p.product_id
        WHERE o.order_id = $order_id";

$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "<p>Order not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Track Order #<?= $order['order_id'] ?></title>
  <style>
    body { font-family: Arial; padding: 40px; background-color: #f0f0f0; }
    .track-box {
      max-width: 600px; margin: auto;
      background: white; padding: 20px;
      border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    img { width: 100%; max-height: 250px; object-fit: cover; border-radius: 10px; }
    .status { font-size: 18px; margin-top: 15px; color: #333; }
    .stage {
      margin-top: 20px;
      padding: 10px;
      border-left: 4px solid green;
      background: #e6ffe6;
      border-radius: 5px;
    }
  </style>
</head>
<body>

<div class="track-box">
  <h2>Tracking Order #<?= $order['order_id'] ?></h2>
  <img src="/miniproject/<?= htmlspecialchars($order['image_path']) ?>" alt="<?= htmlspecialchars($order['product_name']) ?>">
  <p><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
  <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
  <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
  <p class="status"><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>

  <div class="stage">
    <?php
      switch ($order['status']) {
        case 'Pending':
          echo "🕒 Your order has been received. We will start processing it soon.";
          break;
        case 'Processing':
          echo "⚙️ Your order is being prepared.";
          break;
        case 'Shipped':
          echo "🚚 Your order has been shipped and is on its way.";
          break;
        case 'Delivered':
          echo "✅ Your order has been delivered. Thank you!";
          break;
        default:
          echo "ℹ️ Status unknown. Please contact support.";
      }
    ?>
  </div>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
