<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'printcity');
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $id          = $_POST['id'];
    $name        = $_POST['name'];
    $category    = $_POST['category'];
    $price       = $_POST['price'];
    $discount    = $_POST['discount'] ?? 0;
    $stock       = $_POST['stock'] ?? 0;
    $isBest      = isset($_POST['is_best_seller']) ? 1 : 0;
    $description = $_POST['description'];
    $features    = $_POST['features'];

    // Get current image
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE product_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($currentImage);
    $stmt->fetch();
    $stmt->close();

    // Handle image upload
    $imagePath = $currentImage;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageDir = 'uploads/';
        if (!is_dir($imageDir)) mkdir($imageDir, 0777, true);
        $imageName = time().'_'.basename($_FILES['image']['name']);
        $targetFile = $imageDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
            if (!empty($currentImage) && file_exists($currentImage)) unlink($currentImage);
        }
    }

    // Update all fields
    $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, discount=?, stock=?, is_best_seller=?, description=?, features=?, image_path=? WHERE product_id=?");
    $stmt->bind_param("ssddiisssi", $name, $category, $price, $discount, $stock, $isBest, $description, $features, $imagePath, $id);

    if ($stmt->execute()) {
        header("Location: product.php?msg=updated");
        exit;
    } else {
        echo "Error: ".$stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
