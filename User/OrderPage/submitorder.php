<?php
session_start();

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    die("DB connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, address FROM user_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// Check if this is a normal product order or custom order
if (isset($_POST['product_id'])) {
    // === NORMAL PRODUCT ORDER ===
    $product_id = intval($_POST['product_id']);
    $quantity   = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($product_id <= 0 || $quantity <= 0) {
        die("Invalid product or quantity.");
    }

    // Fetch product price
    $stmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    $product = $product_result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        die("Product not found.");
    }

// Fetch current stock
$stmt = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stock_result = $stmt->get_result();
$stock_row = $stock_result->fetch_assoc();
$stmt->close();

if (!$stock_row) {
    die("Product stock not found.");
}

$new_stock = $stock_row['stock'] - $quantity;
if ($new_stock < 0) {
    die("Not enough stock available.");
}

// Update stock in products table
$stmt = $conn->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
$stmt->bind_param("ii", $new_stock, $product_id);
$stmt->execute();
$stmt->close();


    $total_price = $product['price'] * $quantity;
    $customer_name = $user['full_name'];
    $address       = $user['address'];
    $order_date    = date("Y-m-d H:i:s");
    $status        = "Pending";
    $product_type  = "products";

    // Insert into orders table
    $stmt = $conn->prepare("
        INSERT INTO orders 
            (product_id, customer_name, address, quantity, order_date, status, user_id, total_price, product_type)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ississids", 
        $product_id, 
        $customer_name, 
        $address, 
        $quantity, 
        $order_date, 
        $status, 
        $user_id, 
        $total_price, 
        $product_type
    );

    if ($stmt->execute()) {
        header("Location: ../myorder/myorder.php");
        exit();
    } else {
        die("Error placing order: " . $stmt->error);
    }

} elseif (isset($_POST['file_name'])) {
    // === CUSTOM ORDER ===
    $file_name      = $_POST['file_name'];
    $pages          = isset($_POST['pages']) ? intval($_POST['pages']) : 1;
    $quantity       = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $print_type     = $_POST['print_type'] ?? null;
    $paper_size     = $_POST['paper_size'] ?? null;
    $notes          = $_POST['notes'] ?? null;
    $total_amount   = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
    $payment_method = $_POST['payment_method'] ?? null;
    $created_at     = date("Y-m-d H:i:s");

    // Insert into custom_orders table
    $stmt = $conn->prepare("
        INSERT INTO custom_orders 
            (user_id, file_name, pages, quantity, print_type, paper_size, notes, total_amount, payment_method, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isiiissdss", 
        $user_id, 
        $file_name, 
        $pages, 
        $quantity, 
        $print_type, 
        $paper_size, 
        $notes, 
        $total_amount, 
        $payment_method, 
        $created_at
    );

    if ($stmt->execute()) {
        header("Location: ../myorder/myorder.php");
        exit();
    } else {
        die("Error placing custom order: " . $stmt->error);
    }

} else {
    die("No order data provided.");
}

$stmt->close();
$conn->close();
?>
