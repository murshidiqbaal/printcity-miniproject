<?php

$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("DB connection failed: " . mysqli_connect_error());
}

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    
    // Count favourites for this user
    $countQuery = $conn->prepare("SELECT COUNT(*) as total FROM favourites WHERE user_id = ?");
    $countQuery->bind_param("i", $user_id);
    $countQuery->execute();
    $countResult = $countQuery->get_result()->fetch_assoc();
    $cart_count = $countResult['total'] ?? 0;
}
?>