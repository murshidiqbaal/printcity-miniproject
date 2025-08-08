<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all orders with product and user info
$sql = "
SELECT 
    o.order_id,
    o.customer_name,
    o.address,
    o.quantity,
    o.order_date,
    o.status,
    o.user_id,
    u.username,
    p.name AS product_name,
    p.price
FROM orders o
LEFT JOIN users u ON o.user_id = u.user_id
LEFT JOIN products p ON o.product_id = p.product_id
ORDER BY o.order_date DESC
";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f0f2f5;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: #4CAF50;
            color: white;
        }
        .status {
            font-weight: bold;
        }
        .pending {
            color: orange;
        }
        .completed {
            color: green;
        }
    </style>
</head>
<body>

<h1>All Orders (Admin View)</h1>

<table>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Customer Name</th>
        <th>Address</th>
        <th>Product</th>
        <th>Price (₹)</th>
        <th>Quantity</th>
        <th>Total (₹)</th>
        <th>Order Date</th>
        <th>Status</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= $row['username'] ?? 'Guest' ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= number_format($row['price'] * $row['quantity'], 2) ?></td>
            <td><?= $row['order_date'] ?></td>
            <td class="status <?= strtolower($row['status']) ?>"><?= $row['status'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
<?php mysqli_close($conn); ?>
