<!-- admin_orders.php -->
<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Protect this page to allow only admin users
// if ($_SESSION['role'] !== 'admin') {
//     echo "Access denied."; exit;
//}

$sql = "SELECT o.*, p.name AS product_name, p.image_path 
        FROM orders o
        JOIN products p ON o.product_id = p.product_id
        ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard - All Orders</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 30px; background-color: #f4f4f4; }
    h2 { text-align: center; margin-bottom: 30px; }

    .order-table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .order-table th, .order-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    .order-table th {
      background-color: #007bff;
      color: white;
    }

    .order-table tr:hover {
      background-color: #f1f1f1;
    }

    .status {
      padding: 5px 10px;
      border-radius: 5px;
      color: white;
    }

    .Pending { background-color: #ffc107; }
    .Processing { background-color: #17a2b8; }
    .Shipped { background-color: #007bff; }
    .Delivered { background-color: #28a745; }
  </style>
</head>
<body>

<h2>📋 Admin Dashboard - All Orders</h2>

<table class="order-table">
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Product</th>
      <th>Customer</th>
      <th>Address</th>
      <th>Quantity</th>
      <th>Date</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($order = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $order['order_id'] ?></td>
          <td>
<img src="/miniproject/Admin/Products/<?= htmlspecialchars($order['image_path']) ?>" style="width:60px; height:60px; object-fit:cover; border-radius:8px;" alt="<?= htmlspecialchars($order['product_name']) ?>">
            <br><?= htmlspecialchars($order['product_name']) ?>
          </td>
          <td><?= htmlspecialchars($order['customer_name']) ?></td>
          <td><?= htmlspecialchars($order['address']) ?></td>
          <td><?= $order['quantity'] ?></td>
          <td><?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></td>
       <td>
  <span class="status <?= $order['status'] ?>">
    <?= htmlspecialchars($order['status']) ?>
  </span><br>
  <a href="editorder.php?order_id=<?= $order['order_id'] ?>" style="margin-top: 5px; display: inline-block; padding: 5px 10px; background: #28a745; color: white; border-radius: 4px; text-decoration: none;">Edit</a>
</td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="7">No orders found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

</body>
</html>

<?php mysqli_close($conn); ?>
