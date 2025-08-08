<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = intval($_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $quantity = intval($_POST['quantity']);

    $sql = "INSERT INTO orders (product_id, customer_name, address, quantity, order_date)
            VALUES ($product_id, '$name', '$address', $quantity, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "<p>✅ Order placed successfully!</p>";
        header("Location: ../myorder/myorder.php?name=" . urlencode($name));
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
