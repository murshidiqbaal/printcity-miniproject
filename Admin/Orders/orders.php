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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background: #f0f2f5;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background: #4CAF50;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
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
        .cancelled {
            color: red;
        }
        .guest {
            color: #888;
        }
    </style>
</head>
<body>

<h1>All Orders (Admin View)</h1>

<table>
    <thead>
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
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['order_id'] ?></td>
                <td><?= $row['username'] ?? '<span class="guest">Guest</span>' ?></td>
                <td><?= htmlspecialchars($row['customer_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['address'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['product_name'] ?? 'N/A') ?></td>
                <td><?= number_format($row['price'] ?? 0, 2) ?></td>
                <td><?= $row['quantity'] ?? 0 ?></td>
                <td><?= number_format(($row['price'] ?? 0) * ($row['quantity'] ?? 0), 2) ?></td>
                <td><?= date('d M Y, h:i A', strtotime($row['order_date'])) ?></td>
                <td class="status <?= strtolower($row['status'] ?? 'unknown') ?>"><?= htmlspecialchars($row['status'] ?? 'Unknown') ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
<?php mysqli_close($conn); ?>
