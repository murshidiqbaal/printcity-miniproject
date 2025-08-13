<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Page</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }

    .floating-button {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      font-size: 30px;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }

    .popup-form {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      border-radius: 10px;
      z-index: 1000;
    }

    .popup-form input, .popup-form textarea {
      display: block;
      margin-bottom: 10px;
      padding: 8px;
      width: 100%;
    }

    .popup-form button {
      background-color: #007bff;
      color: white;
      padding: 8px 12px;
      border: none;
      cursor: pointer;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      z-index: 999;
    }

    .product-list {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 20px;
    }

    .product-card {
      border: 1px solid #a01e1eff;
      border-radius: 10px;
      padding: 15px;
      width: 250px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
    }

    .product-card h4 {
      margin: 10px 0 5px;
    }

    .product-card p {
      margin: 0;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <h2>Product Page</h2>

  <!-- Floating Add Button -->
  <button class="floating-button" onclick="openForm()">+</button>

  <!-- Overlay -->
  <div class="overlay" id="overlay" onclick="closeForm()"></div>

  <!-- Popup Form -->
  <div class="popup-form" id="popupForm">
    <form action="productlog.php" method="POST" enctype="multipart/form-data">
      <h3>Add Product</h3>
      <input type="text" name="name" placeholder="Product Name" required />
      <input type="text" name="category" placeholder="Category" required />
      <input type="file" name="image" accept="image/*" required />
      <input type="number" step="0.01" name="price" placeholder="Price" />
      <textarea name="description" placeholder="Description"></textarea>
      <button type="submit">Add Product</button>
    </form>
  </div>
<!-- Product List Section -->
<div class="product-list">
  <?php
    // MySQL connection
    $conn = new mysqli('localhost', 'root', '', 'printcity');

    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo '<div class="product-card">';
        if (!empty($row['image_path']) && file_exists($row['image_path'])) {
          echo '<img src="' . $row['image_path'] . '" alt="Product Image">';
        } else {
          echo '<img src="placeholder.jpg" alt="No image">';
        }
        echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
        echo '<p><strong>Category:</strong> ' . htmlspecialchars($row['category']) . '</p>';
        echo '<p><strong>Price:</strong> ₹' . number_format($row['price'], 2) . '</p>';
        echo '<p>' . htmlspecialchars($row['description']) . '</p>';

        // 🔧 Add Edit & Delete Buttons
        echo '<form action="edit.php" method="GET" style="display:inline-block; margin-top:10px; margin-right:10px;">
                <input type="hidden" name="id" value="' . $row['product_id'] . '">
                <button type="submit">Edit</button>
              </form>';

        echo '<form action="delete.php" method="POST" style="display:inline-block; margin-top:10px;" onsubmit="return confirm(\'Are you sure?\')">
                <input type="hidden" name="id" value="' . $row['product_id'] . '">
                <button type="submit" style="background-color:red; color:white;">Delete</button>
              </form>';

        echo '</div>';
      }
    } else {
      echo "<p>No products available.</p>";
    }

    $conn->close();
  ?>
</div>


  <script>
    function openForm() {
      document.getElementById('popupForm').style.display = 'block';
      document.getElementById('overlay').style.display = 'block';
    }

    function closeForm() {
      document.getElementById('popupForm').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }
  </script>

</body>
</html>
