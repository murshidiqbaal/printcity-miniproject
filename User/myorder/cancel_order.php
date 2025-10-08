<?php
// cancel_order.php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get order_id from URL
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = intval($_GET['order_id']);

// Check current status before cancelling
$sql_check = "SELECT status FROM orders WHERE order_id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt_check, "i", $order_id);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_bind_result($stmt_check, $current_status);
mysqli_stmt_fetch($stmt_check);
mysqli_stmt_close($stmt_check);

$cancelable_statuses = ['ordered', 'processing', 'shipped'];

if (!in_array(strtolower($current_status), $cancelable_statuses)) {
    $_SESSION['msg'] = "Order cannot be cancelled.";
    header("Location: orders.php"); // redirect back
    exit;
}

// Update status to 'cancelled'
$sql_update = "UPDATE orders SET status='cancelled' WHERE order_id=?";
$stmt_update = mysqli_prepare($conn, $sql_update);
mysqli_stmt_bind_param($stmt_update, "i", $order_id);

if (mysqli_stmt_execute($stmt_update)) {
    $_SESSION['msg'] = "Order #$order_id has been cancelled successfully.";
} else {
    $_SESSION['msg'] = "Failed to cancel order. Please try again.";
}

mysqli_stmt_close($stmt_update);
mysqli_close($conn);

// Redirect back to orders page
header("Location: orders.php");
exit;
?>
