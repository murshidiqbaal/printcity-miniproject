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

$user_id = $_SESSION['user_id'];

// Get POST data safely
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity   = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Validate inputs
if ($product_id <= 0 || $quantity <= 0) {
    die("Invalid product or quantity.");
}

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, address FROM user_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Fetch product
$stmt2 = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
$stmt2->bind_param("i", $product_id);
$stmt2->execute();
$product_result = $stmt2->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Calculate total price
$total_price = $product['price'] * $quantity;

// Prepare order data
$customer_name = $user['full_name'];
$address       = $user['address'];
$order_date    = date("Y-m-d H:i:s");
$status        = "Pending";
$product_type  = "products"; // default

// Insert order with total_price
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
    echo "Error placing order: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
