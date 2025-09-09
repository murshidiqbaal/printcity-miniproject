<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'printcity');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form fields
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$discount = $_POST['discount'] ?? 0;
$stock = $_POST['stock'];
$is_best_seller = isset($_POST['is_best_seller']) ? 1 : 0;
$description = $_POST['description'];
$features = $_POST['features'];

// Handle file upload
$image_path = "";
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $targetDir = "uploads/offerProduct/";
    
    // Create directory if not exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES['image']['name']);
    $targetFile = $targetDir . time() . "_" . $fileName; // unique filename

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $image_path = $targetFile;
    }
}

// Insert into DB
$sql = "INSERT INTO offer_products (name, category, image_path, price, discount, stock, is_best_seller, description, features, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssdiisss", $name, $category, $image_path, $price, $discount, $stock, $is_best_seller, $description, $features);

if ($stmt->execute()) {
    echo "Offer product added successfully!";
    header("Location: product.php"); // redirect back to product page
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
