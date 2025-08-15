<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Fetch all orders of the current user with product info
$stmt = $conn->prepare("
    SELECT 
        o.order_id, 
        o.customer_name, 
        o.address, 
        o.order_date, 
        o.status,
        GROUP_CONCAT(p.name SEPARATOR ', ') AS product_name,  -- Matches your PHP
        SUM(oi.quantity) AS quantity,                         -- Matches your PHP
        MIN(p.image_path) AS image_path                       -- Matches your PHP
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders | PrintCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="myorder.css">
</head>
<body>
<div class="container">
    <h1 class="page-title">My Orders</h1>
    <div class="order-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($order = $result->fetch_assoc()): 
                // Determine status class for badge
                $status_class = '';
                switch(strtolower($order['status'])) {
                    case 'pending': $status_class = 'status-pending'; break;
                    case 'processing': $status_class = 'status-processing'; break;
                    case 'shipped': case 'completed': $status_class = 'status-completed'; break;
                    case 'cancelled': $status_class = 'status-cancelled'; break;
                }
            ?>
            <div class="order-card">
                <img src="/miniproject/Admin/Products/<?= htmlspecialchars($order['image_path']) ?>" 
                     alt="<?= htmlspecialchars($order['product_name']) ?>" class="order-image">
                <div class="order-details">
                    <h3 class="product-name"><?= htmlspecialchars($order['product_name']) ?></h3>
                    <div class="order-meta">
                        <span>Qty: <?= $order['quantity'] ?></span>
                        <span><?= date('M d, Y', strtotime($order['order_date'])) ?></span>
                    </div>
                    <span class="status-badge <?= $status_class ?>">
                        <?= htmlspecialchars($order['status']) ?>
                    </span>
                    <a href="trackorder.php?order_id=<?= $order['order_id'] ?>" class="track-btn">
                        <i class="fas fa-truck"></i> Track Order
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>No orders found.</h3>
                <p>You haven’t placed any orders yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
?>
</body>
</html>
