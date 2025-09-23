<?php
$conn = new mysqli("localhost", "root", "", "printcity");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example product ID (you should pass it dynamically via GET/POST/session/cart)
$product_id = 1;

$sql = "SELECT * FROM products WHERE product_id = $product_id";
$result = $conn->query($sql);

// Default values (to avoid undefined warnings)
$productName = "";
$price = 0;
$discount = 0;
$quantity = 1; // Default 1 if not provided
$subtotal = 0;

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $productName = $row['name'];
    $price = (float)$row['price'];
    $discount = (float)$row['discount'];
    $finalPrice = $price - ($price * ($discount / 100));

    $quantity = 2; // Example → replace with cart/session value
    $subtotal = $finalPrice * $quantity;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .invoice-box {
            max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0,0,0,0.15); font-size: 16px; line-height: 24px;
        }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 8px; vertical-align: top; }
        tr.heading td { background: #f2f2f2; border-bottom: 1px solid #ddd; font-weight: bold; }
        tr.item td { border-bottom: 1px solid #eee; }
        tr.total td { font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h2>Invoice</h2>
        <p><strong>Date:</strong> <?php echo date("Y-m-d"); ?></p>
        <p><strong>Invoice ID:</strong> INV-<?php echo time(); ?></p>

        <table>
            <tr class="heading">
                <td>Product</td>
                <td>Price</td>
                <td>Discount</td>
                <td>Qty</td>
                <td>Total</td>
            </tr>

            <tr class="item">
                <td><?php echo htmlspecialchars($productName); ?></td>
                <td>₹<?php echo number_format($price, 2); ?></td>
                <td><?php echo $discount; ?>%</td>
                <td><?php echo $quantity; ?></td>
                <td>₹<?php echo number_format($subtotal, 2); ?></td>
            </tr>

            <tr class="total">
                <td colspan="4" style="text-align:right">Grand Total:</td>
                <td>₹<?php echo number_format($subtotal, 2); ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
