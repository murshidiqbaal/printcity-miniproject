<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Page</title>
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
      flex-direction: column;
      align-items: center;
      padding: 20px;
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
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
      transition: background-color 0.3s;
    }

    .floating-button:hover {
      background-color: #218838;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      z-index: 999;
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
      width: 400px;
    }

    .popup-form input, .popup-form textarea {
      display: block;
      margin-bottom: 10px;
      padding: 10px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .popup-form button {
      background-color: #007bff;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .popup-form button:hover {
      background-color: #0056b3;
    }

    .product-list {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 20px;
      justify-content: center;
    }

    .product-card {
      background-color: #fff;
      width: 300px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.3s ease;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .product-card h4 {
      margin: 10px 0 5px;
      padding: 0 10px;
      color: #333;
    }

    .product-card p {
      margin: 0;
      padding: 0 10px;
      font-size: 14px;
      color: #666;
    }

    .product-card .price {
      font-size: 18px;
      color: #27ae60;
      font-weight: bold;
      padding: 0 10px;
    }

    .product-card form {
      display: flex;
      justify-content: space-between;
      padding: 10px;
    }

    .product-card form button {
      padding: 6px 10px;
      border: none;
      border-radius: 5px;
      color: white;
      background-color: #007bff;
      cursor: pointer;
      transition: opacity 0.3s;
    }

    .product-card form button:hover {
      opacity: 0.9;
    }

    .product-card form .delete-button {
      background-color: red;
    }

    .product-card form .delete-button:hover {
      opacity: 0.8;
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
      <input type="number" step="0.01" name="price" placeholder="Price" required />
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
          echo '<p class="price">₹' . number_format($row['price'], 2) . '</p>';
          echo '<p>' . htmlspecialchars($row['description']) . '</p>';

          // Edit & Delete Buttons
          echo '<form action="edit.php" method="GET" style="display:inline-block;">
                  <input type="hidden" name="id" value="' . $row['product_id'] . '">
                  <button type="submit">Edit</button>
                </form>';

          echo '<form action="delete.php" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">
                  <input type="hidden" name="id" value="' . $row['product_id'] . '">
                  <button type="submit" class="delete-button">Delete</button>
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
