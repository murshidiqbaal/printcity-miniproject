<!-- myorder.php -->
<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | PrintCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3a86ff;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f94144;
            --dark: #212529;
            --light: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            color: var(--dark);
            font-weight: 600;
        }
        
        .order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .order-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .order-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .order-details {
            padding: 1.5rem;
        }
        
        .product-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .order-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .status-pending {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning);
        }
        
        .status-processing {
            background-color: rgba(58, 134, 255, 0.1);
            color: var(--primary);
        }
        
        .status-completed {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }
        
        .status-cancelled {
            background-color: rgba(249, 65, 68, 0.1);
            color: var(--danger);
        }
        
        .track-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 0.6rem 1.2rem;
            background-color: var(--primary);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        
        .track-btn:hover {
            background-color: #2a75e6;
        }
        
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 0;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #666;
        }
        
        .empty-state p {
            color: #999;
            max-width: 500px;
            margin: 0 auto;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .order-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">My Orders</h1>
        </div>
        
        <div class="order-grid">
            <?php
// Fetch orders
$stmt = $conn->prepare("
SELECT 
    o.order_id, o.customer_name, o.address, o.order_date, o.status,
    p.name AS product_name, p.price, oi.quantity
FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id
JOIN products p ON oi.product_id = p.product_id
WHERE o.user_id = ?
ORDER BY o.created_at DESC
");

$stmt->bind_param("i", $_SESSION['user_id']); // assuming you store the logged-in user ID in session
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($order = $result->fetch_assoc()) {
        ?>
        <img src="<?= htmlspecialchars($order['image_path']) ?>" alt="<?= htmlspecialchars($order['product_name']) ?>" class="order-image">
        <p>Qty: <?= htmlspecialchars($order['quantity']) ?></p>
        <p>Date: <?= date("M d, Y", strtotime($order['order_date'])) ?></p>
        <p>Status: <?= htmlspecialchars($order['status']) ?></p>
        <a href="track.php?id=<?= $order['order_id'] ?>" class="track-btn">Track Order</a>
        <?php
    }
} else {
    echo "<p>No orders found.</p>";
}


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$current_order = null;

while ($row = $result->fetch_assoc()) {
    if ($current_order != $row['order_id']) {
        echo "<h3>Order #{$row['order_id']} - {$row['status']} ({$row['created_at']})</h3>";
        echo "<p>Total: {$row['total_price']}</p>";
        $current_order = $row['order_id'];
    }
    echo "<p>Product: {$row['product_name']} - Qty: {$row['quantity']} - Price: {$row['price']}</p>";
}

            ?>
                <div class="order-card">
                    <img src="/miniproject/<?= htmlspecialchars($order['image_path']) ?>" 
                         alt="<?= htmlspecialchars($order['product_name']) ?>" 
                         class="order-image">
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
            <?php 
            // Remove 'endwhile;' and 'else:' and 'endif;' as they are not needed with curly brace syntax.
            ?>
        </div>
    </div>
    
    <?php mysqli_close($conn); ?>
</body>
</html>
