<?php
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection details (edit as needed)
    $host = 'localhost';
    $db = 'printcity';
    $user = 'root';
    $pass = '';

    // Connect to MySQL
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageDir = 'uploads/';
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0777, true); // Create folder if not exists
        }

        $imageName = basename($_FILES['image']['name']);
        $targetFile = $imageDir . time() . '_' . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        } else {
            echo "Failed to upload image.";
            exit;
        }
    }

    // Prepare and execute SQL query
    $stmt = $conn->prepare("INSERT INTO products (name, category, image_path, price, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $name, $category, $imagePath, $price, $description);

    if ($stmt->execute()) {
        echo "Product added successfully!";
        echo '<br><a href="./product.php">Go back</a>';
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
