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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #<?= $order['order_id'] ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            background-color: #f0f0f0;
            color: #333;
        }
        .track-box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .track-box:hover {
            transform: translateY(-5px);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        img {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .status {
            font-size: 18px;
            margin-top: 15px;
            font-weight: bold;
        }
        .stage {
            margin-top: 20px;
            padding: 15px;
            border-left: 4px solid #28a745;
            background: #e6ffe6;
            border-radius: 5px;
            font-size: 16px;
        }
        .icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="track-box">
    <h2>Tracking Order #<?= $order['order_id'] ?></h2>
    <img src="/miniproject/Admin/Products/<?= htmlspecialchars($order['image_path']) ?>" alt="<?= htmlspecialchars($order['product_name']) ?>">
    <p><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
    <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
    <p class="status"><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>

    <div class="stage">
        <?php
        switch (strtolower($order['status'])) {
            case 'pending':
                echo "<i class='icon fas fa-hourglass-half'></i> 🕒 Your order has been received. We will start processing it soon.";
                break;
            case 'processing':
                echo "<i class='icon fas fa-cogs'></i> ⚙️ Your order is being prepared.";
                break;
            case 'shipped':
                echo "<i class='icon fas fa-truck'></i> 🚚 Your order has been shipped and is on its way.";
                break;
            case 'delivered':
                echo "<i class='icon fas fa-check-circle'></i> ✅ Your order has been delivered. Thank you!";
                break;
            default:
                echo "<i class='icon fas fa-question-circle'></i> ℹ️ Status unknown. Please contact support.";
        }
        ?>
    </div>
</div>

<div class="footer">
    <p>&copy; <?= date("Y") ?> PrintCity. All rights reserved.</p>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
