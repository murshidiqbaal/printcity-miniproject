<?php
session_start(); // If you need user_id from login

$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data
$product_id    = intval($_POST['product_id']);
$price         = floatval($_POST['price']);
$customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
$address       = mysqli_real_escape_string($conn, $_POST['address']);
$quantity      = intval($_POST['quantity']);
$user_id       = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

// 1. Insert into orders
$order_sql = "INSERT INTO orders (customer_name, address, order_date, status, user_id) 
              VALUES ('$customer_name', '$address', NOW(), 'Pending', " . ($user_id ? $user_id : "NULL") . ")";

if (mysqli_query($conn, $order_sql)) {
    $order_id = mysqli_insert_id($conn); // Get the new order ID

    // 2. Insert into order_items
    $item_sql = "INSERT INTO order_items (order_id, product_id, quantity) 
                 VALUES ($order_id, $product_id, $quantity)";
    if (mysqli_query($conn, $item_sql)) {
        echo "<p>Order placed successfully!</p>";
        echo "<a href='myorder.php'>View My Orders</a>";
    } else {
        echo "Error adding order item: " . mysqli_error($conn);
    }
} else {
    echo "Error creating order: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
