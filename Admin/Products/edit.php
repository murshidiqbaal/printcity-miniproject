<?php
$conn = new mysqli('localhost', 'root', '', 'printcity');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE product_id = $id")->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f5f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .form-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 400px;
    }

    .form-container input, 
    .form-container textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

    .form-container button {
      background-color: #007bff;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
      width: 100%;
    }

    .form-container button:hover {
      background-color: #0056b3;
    }

    .form-container label {
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>Edit Product</h2>
    <form action="update.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $product['product_id'] ?>">
      
      <label for="name">Product Name</label>
      <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required />
      
      <label for="category">Category</label>
      <input type="text" name="category" id="category" value="<?= htmlspecialchars($product['category']) ?>" required />
      
      <label for="price">Price (₹)</label>
      <input type="number" step="0.01" name="price" id="price" value="<?= $product['price'] ?>" required />
      
      <label for="description">Description</label>
      <textarea name="description" id="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
      
      <label for="image">Product Image</label>
      <input type="file" name="image" id="image" accept="image/*" />
      
      <button type="submit">Update</button>
    </form>
  </div>

</body>
</html>
