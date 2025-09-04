<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Example: product_id & quantity passed from previous page
$product_id = $_POST['product_id'] ?? $_GET['product_id'] ?? null;
$quantity   = $_POST['quantity'] ?? $_GET['quantity'] ?? 1;

if (!$product_id) {
    die("Product not selected.");
}

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

$total_amount = $product['price'] * $quantity;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation | PrintCity</title>
    <link rel="stylesheet" href="payment.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 90%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .order-summary {
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .order-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        h2 {
            color: #007bff;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            margin: 5px 0;
        }

        .payment-form {
            margin-top: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        select {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Payment Confirmation</h1>
    <div class="order-summary">
        <h2>Order Summary</h2>
        <img src="/miniproject/Admin/Products/<?= htmlspecialchars($product['image_path']) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>" class="order-image">
        <h3><?= htmlspecialchars($product['name']) ?></h3>
        <p>Quantity: <?= $quantity ?></p>
        <p>Total Amount: $<?= number_format($total_amount, 2) ?></p>
        <p>Estimated Delivery: 3-5 business days</p>
        <p>Shipping: Free</p>
        <p>Return Policy: 30-Day Return Policy</p>
    </div>

...
<form id="orderForm" action="../submitorder.php" method="POST" class="payment-form">
    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
    <input type="hidden" name="quantity" value="<?= $quantity ?>">
    <input type="hidden" name="total_amount" value="<?= $total_amount ?>">

    <h2>Select Payment Method</h2>
    <label for="payment_method">Payment Method:</label>
    <select name="payment_method" id="payment_method" required>
        <option value="" disabled selected>Select a payment method</option>
        <option value="credit_card">Credit Card</option>
        <option value="debit_card">Debit Card</option>
        <option value="paypal">PayPal</option>
        <option value="bank_transfer">Bank Transfer</option>
        <option value="cash_on_delivery">Cash on Delivery</option>
    </select>

    <p>By clicking "Confirm Order", you agree to our terms and conditions.</p>
    
    <button type="submit">Confirm Order</button>
</form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById("orderForm").addEventListener("submit", function(e) {
    e.preventDefault(); // stop default form submit

    // send the form using AJAX
    let form = this;
    let formData = new FormData(form);

    fetch(form.action, {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Show success popup
        Swal.fire({
            icon: 'success',
            title: 'Order Successful!',
            text: 'Thank you for your purchase.',
            confirmButtonText: 'Go to Orders'
        }).then(() => {
            // navigate after popup
            window.location.href = "../../myorder/myorder.php"; // change to your orders page
        });
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong. Please try again!'
        });
    });
});
</script>

</body>
</html>
