<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all products
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>

<section id="products" style="padding: 40px; background: linear-gradient(to bottom, rgb(196, 196, 8) 0%, black 100%);">

    <h2 style="text-align:center; font-size: 32px; margin-bottom: 30px; color: #2e2c2cff; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); font-family: 'Arial', sans-serif; letter-spacing: 1px;">
        🌟 Explore Our Products
    </h2>

    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; max-width: 1200px; margin: 0 auto;">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div style="
                width: 280px; 
                height: 350px;
                border: none; 
                border-radius: 20px; 
                box-shadow: 0 4px 20px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08); 
                overflow: hidden; 
                background-image: url('/miniproject/Admin/Products/<?= htmlspecialchars($row['image_path']) ?>');
                background-size: cover;
                background-position: center;
                text-align: center; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                cursor: pointer;
                position: relative;
            " 
            onclick="location.href='../OrderPage/orderpage.php?product_id=<?= urlencode($row['product_id']) ?>'"
            onmouseover="
                this.style.transform='translateY(-4px) scale(1.01)';
                this.style.boxShadow='0 8px 30px rgba(0,0,0,0.16), 0 4px 12px rgba(0,0,0,0.12), 0 0 0 1px rgba(196,196,8,0.1)';
                this.style.backgroundSize='102%';
            "
            onmouseout="
                this.style.transform='translateY(0) scale(1)';
                this.style.boxShadow='0 4px 20px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08)';
                this.style.backgroundSize='100%';
            "
            >
                <!-- Price Badge at Top-Left -->
                <div style="
                    position: absolute; 
                    top: 12px; 
                    left: 12px; 
                    background: rgba(40, 167, 74, 0.95); /* Green for price to differentiate from yellow category */
                    color: white; 
                    padding: 8px 12px; 
                    border-radius: 12px; 
                    font-size: 14px; 
                    font-weight: 700; 
                    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                    text-shadow: 1px 1px 2px rgba(0,0,0,0.8), 0 0 4px rgba(128,128,128,0.6);
                ">
                    ₹<?= number_format($row['price'], 2) ?>
                </div>

                <!-- Category Badge at Top-Right -->
                <div style="
                    position: absolute; 
                    top: 12px; 
                    right: 12px; 
                    background: rgba(196, 196, 8, 0.9); 
                    color: white; 
                    padding: 6px 12px; 
                    border-radius: 12px; 
                    font-size: 12px; 
                    font-weight: 600; 
                    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                    text-shadow: 1px 1px 2px rgba(0,0,0,0.8), 0 0 4px rgba(128,128,128,0.6);
                ">
                    <?= htmlspecialchars($row['category']) ?>
                </div>

                <!-- Bottom Content: Name and Description Only -->
                <div style="position: absolute; bottom: 20px; left: 0; right: 0; padding: 0 20px; background: transparent;">
                    <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 600; color: #ffffff; letter-spacing: 0.3px; 
                               text-shadow: 
                                2px 2px 6px rgba(0,0,0,0.9), 
                                -1px -1px 2px rgba(0,0,0,0.7),
                                0 0 6px rgba(128,128,128,0.5);"><?= htmlspecialchars($row['name']) ?></h3>
                    <p style="margin: 0 0 12px 0; color: #f5f5f5; font-size: 14px; font-style: italic; 
                              text-shadow: 
                                1px 1px 3px rgba(0,0,0,0.8), 
                                0 0 4px rgba(128,128,128,0.4); 
                              line-height: 1.4; max-height: 2.8em; overflow: hidden;"><?= htmlspecialchars($row['description'] ?? "") ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php mysqli_close($conn); ?>
