<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "printcity");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
} else {
    echo "missing_data";
}

$conn->close();
?>
