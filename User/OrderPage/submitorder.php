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

$user_id = $_SESSION['user_id'];

// Fetch user profile
$user_query = "SELECT * FROM user_profiles WHERE user_id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Validate profile
$valid_profile = !empty($user['full_name']) && !empty($user['email']) && !empty($user['phone']) && !empty($user['address']);

if (!$valid_profile) {
    // Redirect to profile if incomplete
    header("Location: ../myprofile/myprofile.php");
    exit();
}

// Check POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $quantity = intval($_POST['quantity']); // number of items
    $total_amount = floatval($_POST['total_amount']); // total cost

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, product_name, quantity, total_amount, order_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisid", $user_id, $product_id, $product_name, $quantity, $total_amount);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect to order confirmation page
        header("Location: orderpage.php?order_success=1");
        exit();
    } else {
        echo "Failed to place order. Please try again.";
    }
}
?>
