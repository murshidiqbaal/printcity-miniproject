<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ✅ Handle product insertion
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $category = trim($_POST["category"]);
    $price = $_POST["price"];
    $description = trim($_POST["description"]);

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $upload_dir = "../../assets/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_path = $upload_dir . $image_name;
        $db_image_path = "assets/" . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, image_path, price, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $name, $category, $db_image_path, $price, $description);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// ✅ Fetch all products
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Add & View Products</title>
  <style>
    body {
      font-family: Arial;
      padding: 20px;
      background-color: #f0f0f0;
    }
    form {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      width: 300px;
      margin-bottom: 30px;
    }
    input, textarea {
      width: 100%;
      margin-bottom: 10px;
      padding: 8px;
    }
    button {
      padding: 10px;
      background: green;
      color: white;
      border: none;
    }

    .product-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .product-card {
      background: #fff;
      padding: 15px;
      border-radius: 8px;
      width: 220px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .product-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 5px;
    }

    .product-card h3 {
      margin: 10px 0 5px;
    }

    .product-card p {
      font-size: 14px;
      color: #555;
    }

    .product-price {
      font-weight: bold;
      color: green;
    }
  </style>
</head>
<body>

<h2>Add New Product</h2>
<form action="product.php" method="POST" enctype="multipart/form-data">
  <input type="text" name="name" placeholder="Product Name" required>
  <input type="text" name="category" placeholder="Category" required>
  <input type="number" name="price" step="0.01" placeholder="Price" required>
  <textarea name="description" placeholder="Description"></textarea>
  <input type="file" name="image" accept="image/*" required>
  <button type="submit">Add Product</button>
</form>

<h2>All Products</h2>
<div class="product-grid">
  <?php foreach ($products as $product): ?>
    <div class="product-card">
      <img src="../../<?= htmlspecialchars($product['image_path']) ?>" alt="Product">
      <h3><?= htmlspecialchars($product['name']) ?></h3>
      <p><?= htmlspecialchars($product['description']) ?></p>
      <span class="product-price">₹<?= number_format($product['price'], 2) ?></span>
    </div>
  <?php endforeach; ?>
</div>

</body>
</html>
