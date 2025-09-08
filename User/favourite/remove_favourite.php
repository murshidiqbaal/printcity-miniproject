<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get user ID and product ID
$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// Remove from favourites
$sql = "DELETE FROM favourites WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Removed from favourites"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to remove"]);
}
?>
