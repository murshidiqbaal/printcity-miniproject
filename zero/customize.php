<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customize Product - PrintCity</title>
  <link rel="stylesheet" href="style.css" />
  <script src="customize.js" defer></script>
</head>
<body>
  <header class="header">
    <div class="logo">🖨️ PrintCity</div>
    <nav class="navbar">
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="products.html">Products</a></li>
        <li><a href="customize.php" class="active">Customize</a></li>
        <li><a href="order-tracking.php">Track Order</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </nav>
  </header>

  <main class="customize-container">
    <h2>Customize Your Product</h2>
    <form id="customizer-form" method="POST" action="save_order.php" enctype="multipart/form-data">
      <label>Choose Template:</label>
      <select name="template" id="template-selector">
        <option value="frame1">Frame Template 1</option>
        <option value="card1">Card Template 1</option>
        <option value="poster1">Poster Template 1</option>
      </select>

      <label>Enter Custom Text:</label>
      <input type="text" name="customText" id="custom-text" placeholder="Your Message Here" required/>

      <label>Upload Image:</label>
      <input type="file" name="customImage" id="image-upload" accept="image/*" />

      <div class="preview-section">
        <h3>Live Preview</h3>
        <div id="preview-area">
          <img id="template-preview" src="assets/frame.jpg" alt="Template Preview">
          <p id="text-preview">Your Message Here</p>
          <img id="uploaded-image" />
        </div>
      </div>

      <button type="submit" class="btn">Place Order</button>
    </form>
  </main>

  <footer class="footer">
    <p>&copy; 2025 PrintCity</p>
  </footer>
</body>
</html>
