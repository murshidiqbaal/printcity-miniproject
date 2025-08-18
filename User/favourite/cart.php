<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_GET['action'] === 'add') {
    $id = $_GET['id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    // Save order
    $conn->query("INSERT INTO orders (user_id) VALUES (" . $_SESSION['user_id'] . ")");
    $order_id = $conn->insert_id;

    foreach ($_SESSION['cart'] as $pid => $qty) {
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity)
                      VALUES ($order_id, $pid, $qty)");
    }

    $_SESSION['cart'] = []; // Clear cart
    echo "Order placed!";
    exit;
}

// Display cart
echo "<h2>Cart</h2>";
foreach ($_SESSION['cart'] as $pid => $qty) {
    $p = $conn->query("SELECT * FROM products WHERE id=$pid")->fetch_assoc();
    echo $p['name'] . " x $qty = ₹" . $p['price'] * $qty . "<br>";
}
?>

<form method="POST">
    <button>Place Order</button>
</form>
<a href="index.php">Back to Products</a>
