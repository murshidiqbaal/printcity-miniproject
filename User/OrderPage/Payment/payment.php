<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once __DIR__ . '/../../../vendor/autoload.php';
use setasign\Fpdi\Fpdi;

// Function to count PDF pages
function countPdfPages($filePath) {
    if (!file_exists($filePath)) {
        return 1; // fallback if file doesn't exist
    }
    $pdf = new Fpdi();
    return $pdf->setSourceFile($filePath);
}

// Get logged-in user ID
$user_id = $_SESSION['user_id'];

// Fetch user profile
$sql = "SELECT full_name, email, phone, address, city, state, zip_code, country 
        FROM user_profiles WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc() ?? [];

// Fallbacks for user info
$user_name    = $user_data['full_name'] ?? 'Customer';
$user_email   = $user_data['email'] ?? 'customer@example.com';
$user_phone   = !empty($user_data['phone']) ? $user_data['phone'] : '(555) 123-4567';
$user_address = !empty($user_data['address']) ? $user_data['address'] : '123 Main St';
$user_city    = !empty($user_data['city']) ? $user_data['city'] : 'City';
$user_state   = !empty($user_data['state']) ? $user_data['state'] : 'State';
$user_zip     = !empty($user_data['zip_code']) ? $user_data['zip_code'] : '000000';
$user_country = !empty($user_data['country']) ? $user_data['country'] : 'India';

// Determine order type
$order_type = $_GET['order_type'] ?? 'standard';

if ($order_type === 'custom') {
    $file_name  = $_GET['file_name'] ?? null;
    $print_type = $_GET['print_type'] ?? 'black_white';
    $paper_size = $_GET['paper_size'] ?? 'A4';
    $notes      = $_GET['notes'] ?? '';
    $pages      = intval($_GET['pages'] ?? 1);

    
    $uploads_dir = __DIR__ . '/uploads'; // Make sure this folder exists
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true); // create uploads folder if not exists
    }
$filePath = $file_name ? $uploads_dir . '/' . basename($file_name) : null;

 // Determine quantity
    if ($filePath && file_exists($filePath) && preg_match('/\.pdf$/i', $file_name)) {
        $quantity = countPdfPages($filePath);
    } elseif ($filePath && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file_name)) {
        // treat an image as 1 page
        $quantity = 1;
    } else {
        // fallback if no file uploaded
        $quantity = intval($_GET['quantity'] ?? 1);
        $file_name = null; // reset since no file exists
        $filePath = null;
    }

    $unit_price = (strtolower($print_type) === 'color') ? 10 : 1;
    $subtotal = ($unit_price * $pages) * $quantity;
    $tax_rate = 0.00;
    $tax = $subtotal ;
    $shipping = 0;
    $total_amount = $subtotal  + $shipping;

    $product = [
        'product_id'  => 0,
        'name'        => "Custom Print ({$paper_size} - " . ucfirst($print_type) . ")",
        'description' => $notes ?: 'Custom print job',
        'price'       => $unit_price,
        'image_path'  => $file_name // can show thumbnail if image exists
    ];
} else {
    // Normal product order path
    $product_id = $_POST['product_id'] ?? $_GET['product_id'] ?? null;
    $quantity   = intval($_POST['quantity'] ?? $_GET['quantity'] ?? 1);

    if (!$product_id) {
        die("Product not selected.");
    }

    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        die("Product not found.");
    }

    $subtotal = $product['price'] * $quantity;
    $tax_rate = 0.08;
    $tax = $subtotal * $tax_rate;
    $shipping = 0;
    $total_amount = $subtotal  + $shipping;
}

// Generate invoice number
$invoice_number = 'INV-' . date('Ymd') . '-' . $user_id;
$invoice_date = date('F j, Y');
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice & Payment | PrintCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --background: #f8f9fa;
            --white: #ffffff;
            --text-dark: #333;
            --text-light: #666;
            --border-color: #e9ecef;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --gradient: linear-gradient(135deg, #007bff, #0056b3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            color: var(--text-dark);
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .invoice-header {
            background: var(--gradient);
            color: var(--white);
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .invoice-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }

        .company-logo {
            font-size: 3rem;
            margin-bottom: 10px;
            display: block;
        }

        .company-name {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .invoice-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .invoice-meta div {
            background: rgba(255,255,255,0.2);
            padding: 10px;
            border-radius: 8px;
        }

        .invoice-content {
            padding: 30px;
        }

        .billing-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 20px;
        }

        .info-section h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-section p {
            margin: 5px 0;
            color: var(--text-light);
        }

        .order-items {
            margin-bottom: 30px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .items-table th,
        .items-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .items-table th {
            background: var(--gradient);
            color: var(--white);
            font-weight: 600;
        }

        .items-table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .totals-table .label {
            font-weight: 600;
            color: var(--text-light);
        }

        .totals-table .value {
            text-align: right;
            font-weight: bold;
        }

        .totals-table .total-row {
            border-top: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .payment-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .payment-section h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-form {
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
        }

        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background: var(--white);
        }

        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        .terms {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 20px;
            text-align: center;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        button {
            background: var(--gradient);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }

        .print-btn {
            background: var(--secondary-color);
        }

        .print-btn:hover {
            box-shadow: 0 4px 12px rgba(108,117,125,0.3);
        }

        @media print {
            body { background: var(--white); }
            .container { box-shadow: none; max-width: none; }
            .action-buttons { display: none; }
            button { display: none; }
        }

        @media (max-width: 768px) {
            body { padding: 10px; }
            .invoice-header { padding: 20px; }
            .invoice-content { padding: 20px; }
            .billing-info { grid-template-columns: 1fr; gap: 20px; }
            .invoice-meta { grid-template-columns: 1fr; }
            .action-buttons { flex-direction: column; }
            .items-table { font-size: 0.9rem; }
            .items-table img { width: 40px; height: 40px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <i class="fas fa-print company-logo"></i>
            <div class="company-name">PrintCity</div>
            <div class="invoice-title">Professional Invoice & Payment Confirmation</div>
            <div class="invoice-meta">
                <div>
                    <strong>Invoice Number:</strong><br>
                    <?= htmlspecialchars($invoice_number) ?>
                </div>
                <div>
                    <strong>Date:</strong><br>
                    <?= $invoice_date ?>
                </div>
            </div>
        </div>

        <!-- Invoice Content -->
        <div class="invoice-content">
            <!-- Billing Information -->
            <div class="billing-info">
                <div class="info-section">
    <h3><i class="fas fa-user"></i> Bill From</h3>
    <p><strong>ABM</strong></p>
    <p>abmenterprises@gmail.com</p>
    <p>
        Mangattukavala Thodupuzha - 685584,<br>
        Idukki, Kerala,<br> India
    </p>
    <p>Phone: 8137878813</p>
</div>

                 <div class="info-section">
    <h3><i class="fas fa-user"></i> Bill To</h3>
    <p><strong><?= htmlspecialchars($user_name) ?></strong></p>
    <p><?= htmlspecialchars($user_email) ?></p>
    <p>
        <?= htmlspecialchars($user_address) ?>,
        <?= htmlspecialchars($user_city) ?>,
        <?= htmlspecialchars($user_state) ?> - <?= htmlspecialchars($user_zip) ?>,
        <?= htmlspecialchars($user_country) ?>
    </p>
    <p>Phone: <?= htmlspecialchars($user_phone) ?></p>
</div>
            </div>

            <!-- Order Items -->
            <div class="order-items">
                <h3 style="color: var(--primary-color); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-shopping-cart"></i> Order Items
                </h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php if ($order_type === 'custom'): ?>
<tr>
    <td>
        <?php if ($file_name && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file_name)): ?>
            <img src="/miniproject/User/uploads/<?= htmlspecialchars($file_name) ?>" 
                 alt="Custom File">
        <?php else: ?>
            <i class="fas fa-file-alt" style="font-size:40px;color:#007bff;"></i>
        <?php endif; ?>
    </td>
    <td>
        <strong>Custom Print (<?= ucfirst($print_type) ?>)</strong><br>
        <small>Paper: <?= htmlspecialchars($paper_size) ?><br>
        Notes: <?= htmlspecialchars($notes ?: 'No special notes') ?></small>
    </td>
    <td><?= $pages ?> pages</td>
    <td>₹<?= number_format($unit_price, 2) ?></td>
    <td>₹<?= number_format($subtotal, 2) ?></td>
</tr>
<?php else: ?>
<tr>
    <td>
        <img src="/miniproject/Admin/Products/<?= htmlspecialchars($product['image_path']) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>">
    </td>
    <td>
        <strong><?= htmlspecialchars($product['name']) ?></strong><br>
        <small><?= htmlspecialchars($product['description'] ?? 'High-quality print product') ?></small>
    </td>
    <td><?= $quantity ?></td>
    <td>₹<?= number_format($product['price'], 2) ?></td>
    <td>₹<?= number_format($subtotal, 2) ?></td>
</tr>
<?php endif; ?>


                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value"><?= number_format($subtotal, 2) ?></td>
                    </tr>
                   <!-- <tr>
                        <td class="label">Tax (5%)</td>
                        <td class="value"><?= number_format($tax, 2) ?></td>
                    </tr> -->
                    <tr>
                        <td class="label">Shipping</td>
                        <td class="value">Free</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Amount</td>
                        <td><?= number_format($total_amount, 2) ?></td>
                    </tr>
                </table>
            </div>

            <!-- Additional Info -->
            <div style="background: #e7f3ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--primary-color);">
                <p><i class="fas fa-info-circle"></i> <strong>Estimated Delivery:</strong> 3-5 business days</p>
                <p><i class="fas fa-shield-alt"></i> <strong>Return Policy:</strong> 7-Day Replacement</p>
                <p><i class="fas fa-truck"></i> <strong>Shipping:</strong> Free worldwide shipping on orders over 50</p>
            </div>

            <!-- Payment Section -->
            <div class="payment-section">
                <h3><i class="fas fa-credit-card"></i> Select Payment Method</h3>
                <form id="orderForm" action="../submitorder.php" method="POST" class="payment-form">


  <?php if ($order_type === 'custom'): ?>
    <input type="hidden" name="order_type" value="custom">
    <input type="hidden" name="quantity" value="<?= $quantity ?>">
    <input type="hidden" name="print_type" value="<?= htmlspecialchars($print_type ?? '') ?>">
    <input type="hidden" name="paper_size" value="<?= htmlspecialchars($paper_size ?? '') ?>">
    <input type="hidden" name="notes" value="<?= htmlspecialchars($notes ?? '') ?>">
    <input type="hidden" name="file_name" value="<?= htmlspecialchars($file_name ?? '') ?>">
<?php else: ?> 

                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                    <input type="hidden" name="quantity" value="<?= $quantity ?>">
                    <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
                    <input type="hidden" name="invoice_number" value="<?= $invoice_number ?>">
<?php endif; ?>
                    <label for="payment_method">Choose your preferred payment method:</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="" disabled selected>Select a payment method</option>
                        <option value="credit_card"><i class="fas fa-credit-card"></i> Credit Card</option>
                        <option value="gpay"><i class="fas fa-gpay"></i> GPay</option>
                        <option value="paypal"><i class="fab fa-paypal"></i> PayPal</option>
                        <option value="bank_transfer"><i class="fas fa-university"></i> Bank Transfer</option>
                        <option value="cash_on_delivery"><i class="fas fa-money-bill-wave"></i> Cash on Delivery</option>
                    </select>

                    <p class="terms">By confirming your order, you agree to PrintCity's <a href="#" style="color: var(--primary-color);">Terms and Conditions</a> and <a href="#" style="color: var(--primary-color);">Privacy Policy</a>.</p>
                    
                    <div class="action-buttons">
                        <button type="button" onclick="window.print()" class="print-btn">
                            <i class="fas fa-print"></i> Print Invoice
                        </button>
                        <button type="submit">
                            <i class="fas fa-check"></i> Confirm & Pay Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>


    
document.getElementById("orderForm").addEventListener("submit", function(e) {
    e.preventDefault(); // stop normal submit

    let form = this;
    let formData = new FormData(form);
    let paymentMethod = formData.get("payment_method");

   // Payment Method Handling
if (paymentMethod === "cash_on_delivery") {
    // 🧾 CASE 1: Cash On Delivery
    Swal.fire({
        title: 'Confirm Your Order',
        html: `
            <div style="text-align:left">
                <p><strong>Payment Type:</strong> Cash on Delivery</p>
                <p><strong>Invoice No:</strong> <?= $invoice_number ?></p>
                <p><strong>Amount Payable:</strong> ₹<?= number_format($total_amount, 2) ?></p>
                <p><strong>Delivery Address:</strong> 
                    <?= htmlspecialchars($user_address) ?>, 
                    <?= htmlspecialchars($user_city) ?>, 
                    <?= htmlspecialchars($user_state) ?> - 
                    <?= htmlspecialchars($user_zip) ?>
                </p>
            </div>
            <br>
            <button onclick="window.print()" 
                    style="background:#007bff;color:#fff;border:none;border-radius:5px;padding:8px 14px;cursor:pointer">
                <i class="fas fa-print"></i> Print Bill
            </button>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'OK, Confirm Order',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#007bff'
    }).then((result) => {
        if (result.isConfirmed) {
            processOrder();
        }
    });

} else if (paymentMethod === "gpay" || paymentMethod === "paypal" ) {
    // 💳 CASE 2: GPay / UPI QR
    fetch(form.action, {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        Swal.fire({
            title: 'Scan QR for Payment/Order Confirmation',
            html: `
                <div style="text-align: center;">
                    <p>Please scan the QR code to complete your payment or confirm your order.</p>
                    <img src="../../../assets/qrcode.jpg" 
                         alt="Payment QR Code" 
                         style="width: 200px; height: 200px; border: 1px solid #ccc; border-radius: 10px;">
                    <p style="margin-top: 10px; font-size: 14px;">Invoice: <?= $invoice_number ?></p>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: 5000,
            timerProgressBar: true,
            width: '400px'
        }).then(() => showSuccess());
    })
    .catch(showError);

} else if (paymentMethod === "credit_card") {
    // 💰 CASE 3: Credit Card
    Swal.fire({
        title: 'Enter Credit Card Details',
        html: `
            <input type="text" id="cardNumber" class="swal2-input" placeholder="Card Number" maxlength="16">
            <input type="text" id="cardExpiry" class="swal2-input" placeholder="MM/YY" maxlength="5">
            <input type="text" id="cardCVC" class="swal2-input" placeholder="CVC" maxlength="4">
        `,
        confirmButtonText: 'Pay Now',
        showCancelButton: true,
        preConfirm: () => {
            const cardNumber = Swal.getPopup().querySelector('#cardNumber').value;
            const cardExpiry = Swal.getPopup().querySelector('#cardExpiry').value;
            const cardCVC = Swal.getPopup().querySelector('#cardCVC').value;
            if (!cardNumber || !cardExpiry || !cardCVC) {
                Swal.showValidationMessage(`Please enter all card details`);
            }
            return { cardNumber, cardExpiry, cardCVC };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            processOrder();
        }
    });
}else if (paymentMethod === "bank_transfer") {
    // 🏦 CASE 4: Bank Transfer
    Swal.fire({
        title: 'Enter Your Bank Details',
        html: `
            <input type="text" id="userAccountName" class="swal2-input" placeholder="Your Account Name">
            <input type="text" id="userAccountNumber" class="swal2-input" placeholder="Your Account Number">
            <input type="text" id="userIFSC" class="swal2-input" placeholder="Your IFSC Code">
            <input type="text" id="userBankName" class="swal2-input" placeholder="Your Bank Name">
            <div style="text-align:left; margin-top:10px;">
                <p>Please transfer the total amount to the following bank account:</p>
                <p><strong>Account Name:</strong> PrintCity Pvt Ltd</p>
                <p><strong>Account Number:</strong> 1234567890</p>
                <p><strong>IFSC Code:</strong> PCITY0001</p>
                <p><strong>Bank:</strong> ABC Bank</p>
                <br>
                <p>After completing the transfer, please email the transaction receipt to <a href="mailto:support@printcity.com">support@printcity.com</a>.</p>
            </div>
        `,
        confirmButtonText: 'Done',
        showCancelButton: true,
        preConfirm: () => {
            const userAccountName = Swal.getPopup().querySelector('#userAccountName').value;
            const userAccountNumber = Swal.getPopup().querySelector('#userAccountNumber').value;
            const userIFSC = Swal.getPopup().querySelector('#userIFSC').value;
            const userBankName = Swal.getPopup().querySelector('#userBankName').value;
            if (!userAccountName || !userAccountNumber || !userIFSC || !userBankName) {
                Swal.showValidationMessage('Please enter all your bank details');
            }
            return { userAccountName, userAccountNumber, userIFSC, userBankName };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Optionally, you can append these details to formData before submitting
            formData.append('user_account_name', result.value.userAccountName);
            formData.append('user_account_number', result.value.userAccountNumber);
            formData.append('user_ifsc', result.value.userIFSC);
            formData.append('user_bank_name', result.value.userBankName);
            processOrder();
        }
    });
}


// 🔄 Reusable helper functions
function processOrder() {
    fetch(form.action, {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => showSuccess())
    .catch(showError);
}

function showSuccess() {
    Swal.fire({
        icon: 'success',
        title: 'Order Confirmed!',
        html: `
            Thank you for your purchase!<br>
            <strong>Invoice #<?= $invoice_number ?></strong><br>
            Your order will be processed shortly.
        `,
        confirmButtonText: '<i class="fas fa-shopping-bag"></i> View My Orders',
        confirmButtonColor: '#007bff',
        timer: 3000,
        timerProgressBar: true
    }).then(() => {
        window.location.href = "../../myorder/myorder.php";
    });
}

function showError() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Something went wrong while placing the order. Please try again.',
        confirmButtonColor: '#dc3545'
    });
}
});
        
    </script>
</body>
</html>