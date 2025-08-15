<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Get form data
$product_id   = intval($_POST['product_id']);
$amount       = floatval($_POST['amount']);  // amount entered manually

// Fetch user details from user_profiles
$user_query = "SELECT full_name, address, phone FROM user_profiles WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_query);
if (!$user_result || mysqli_num_rows($user_result) == 0) {
    die("User profile not found.");
}
$user = mysqli_fetch_assoc($user_result);

// Insert order
$order_sql = "INSERT INTO orders (product_id, customer_name, address, quantity, order_date, status, user_id) 
              VALUES ($product_id, '" . mysqli_real_escape_string($conn, $user['full_name']) . "', 
                      '" . mysqli_real_escape_string($conn, $user['address']) . "', 
                      $amount, NOW(), 'Pending', $user_id)";

if (mysqli_query($conn, $order_sql)) {
   header("Location: ../myorder/myorder.php");
   exit();
} else {
    echo "Error placing order: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
