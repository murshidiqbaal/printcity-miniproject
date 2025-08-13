<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Assuming user_id is stored in session after login
if (!isset($_SESSION['user_id'])) {
    echo("You must be logged in."); // Redirect to login page if not logged in
    header("Location: ../../Auth/login/login.html");
    exit();
    
}
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "imgs/"; // Current folder + imgs/
    $file_name = basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $file_name;

    // Move uploaded file to imgs folder
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        // Save just the filename in DB
        $profile_picture = $file_name;

        // Update DB
        $stmt = $pdo->prepare("UPDATE user_profiles SET profile_picture = ? WHERE id = ?");
        $stmt->execute([$profile_picture, $user_id]);
    } else {
        echo "Error moving file.";
    }
}


$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT * FROM user_profiles WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    // If profile not created yet, initialize empty values
    $user_data = [
        'fullname' => '',
        'email' => '',
        'phone' => '',
        'address' => '',
        'profile_picture' => '',
        'payment_method' => '',
        'delivery_notes' => '',
        
    ];
}

$profile_picture = htmlspecialchars($user_data['profile_picture'] ?? '');
$full_name       = htmlspecialchars($user_data['fullname'] ?? '');

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name       = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email           = mysqli_real_escape_string($conn, $_POST['email']);
    $phone           = mysqli_real_escape_string($conn, $_POST['phone']);
    $address         = mysqli_real_escape_string($conn, $_POST['address']);
    $city            = mysqli_real_escape_string($conn, $_POST['city']);
    $state           = mysqli_real_escape_string($conn, $_POST['state']);
    $zip_code        = mysqli_real_escape_string($conn, $_POST['zip_code']);
    $country         = mysqli_real_escape_string($conn, $_POST['country']);
    $delivery_notes  = mysqli_real_escape_string($conn, $_POST['delivery_notes']);
    $payment_method  = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $profile_picture = mysqli_real_escape_string($conn, $_POST['profile_picture']);

    // Check if user already has a profile
    $check = mysqli_query($conn, "SELECT id FROM user_profiles WHERE user_id = '$user_id' LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        // UPDATE existing profile
        $sql = "UPDATE user_profiles SET 
                    full_name='$full_name',
                    email='$email',
                    phone='$phone',
                    address='$address',
                    city='$city',
                    state='$state',
                    zip_code='$zip_code',
                    country='$country',
                    delivery_notes='$delivery_notes',
                    payment_method='$payment_method',
                    profile_picture='$profile_picture'
                WHERE user_id='$user_id'";
        $msg = "Profile updated successfully!";
    } else {
        // INSERT new profile
        $sql = "INSERT INTO user_profiles 
                (user_id, full_name, email, phone, address, city, state, zip_code, country, delivery_notes, payment_method, profile_picture) 
                VALUES 
                ('$user_id', '$full_name', '$email', '$phone', '$address', '$city', '$state', '$zip_code', '$country', '$delivery_notes', '$payment_method', '$profile_picture')";
        $msg = "Profile created successfully!";
    }

    if (mysqli_query($conn, $sql)) {
        echo $msg;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch user profile data to display in form
$result = mysqli_query($conn, "SELECT * FROM user_profiles WHERE user_id = '$user_id' LIMIT 1");
$profile = mysqli_fetch_assoc($result);
$stmt->close();
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Order Delivery Details</title>
    <script src="myprofile.js"></script>
   <link rel="stylesheet" href="myprofile.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    

    <main class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <h1 class="page-title">My Profile</h1>

        <div class="profile-container">
            <div class="profile-sidebar">
<img src="imgs/<?php echo htmlspecialchars($user_data['profile_picture'] ?? 'default.png'); ?>"
     alt="Profile picture of <?php echo htmlspecialchars($user_data['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
     class="profile-picture">

                <h2 class="profile-name"><?php echo htmlspecialchars($user_data['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                <div class="profile-email"><?php echo htmlspecialchars($user_data['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>

                <ul class="nav-menu">
                    <li class="nav-item"><a href="#profile-main" class="nav-link active"><i class="fas fa-user-circle"></i> Personal Info</a></li>
                    <li class="nav-item"><a href="#profile-delivery" class="nav-link"><i class="fas fa-map-marker-alt"></i> Delivery Addresses</a></li>
                    <li class="nav-item"><a href="#profile-payment" class="nav-link"><i class="fas fa-credit-card"></i> Payment Methods</a></li>
                    <li class="nav-item"><a href="#profile-orders" class="nav-link"><i class="fas fa-box-open"></i> Order History</a></li>
                </ul>
            </div>

            <div class="profile-main" id="profile-main">
                <form method="POST" id="profileForm">
                    <h2 class="section-title">Personal Information</h2>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" placeholder="Full Name" value="<?php echo $profile['full_name'] ?? ''; ?>" required><br>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?php echo $profile['email'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" placeholder="Phone Number" value="<?php echo $profile['phone'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <h2 class="section-title" id="profile-delivery" style="margin-top: 30px;">Delivery Information</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="address" class="form-label">Street Address</label>
                            <div class="location-container">
                                <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($user_data['address']); ?>" required> 
                                <button type="button" class="location-btn" id="getLocationBtn"><i class="fas fa-map-marker-alt"></i> Use Current</button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" class="form-control" placeholder="City" value="<?php echo $profile['city'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="state" class="form-label">State/Province</label>
                            <input type="text" name="state" class="form-control" placeholder="State/Province" value="<?php echo $profile['state'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="zip_code" class="form-label">ZIP/Postal Code</label>
                            <input type="text" name="zip_code" class="form-control" placeholder="Zip Code" value="<?php echo $profile['zip_code'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="country" class="form-label">Country</label>
                            <select id="country" name="country" class="form-control" required>
                                <option value="">Select Country</option>
                                <option value="United States" <?php echo $user_data['country'] === 'United States' ? 'selected' : ''; ?>>United States</option>
                                <option value="Canada" <?php echo $user_data['country'] === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                                <option value="United Kingdom" <?php echo $user_data['country'] === 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                                <option value="India" <?php echo $user_data['country'] === 'India' ? 'selected' : ''; ?>>India</option>

                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="delivery_notes" class="form-label">Delivery Notes (Optional)</label>
                            <textarea id="delivery_notes" name="delivery_notes" class="form-control">
                            </textarea>
                            <p style="font-size: 13px; color: var(--dark-gray); margin-top: 5px;">Special instructions for delivery drivers or package handling</p>
                        </div>
                    </div>

                    <h2 class="section-title" id="profile-payment" style="margin-top: 30px;">Payment Method</h2>

                    <div class="form-group">
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="credit_card" name="payment_method" value="credit_card" <?php echo $user_data['payment_method'] === 'credit_card' ? 'checked' : ''; ?> required>
                                <label for="credit_card">Credit/Debit Card</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="paypal" name="payment_method" value="paypal" <?php echo $user_data['payment_method'] === 'paypal' ? 'checked' : ''; ?>>
                                <label for="paypal">PayPal</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="cash" name="payment_method" value="cash" <?php echo $user_data['payment_method'] === 'cash' ? 'checked' : ''; ?>>
                                <label for="cash">Cash on Delivery</label>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-block">Save Changes</button>
                    </div>
                </form>

                <div class="order-history" id="profile-orders">
                    <h2 class="section-title">Recent Orders</h2>
                    
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-1001</td>
                                <td>June 15, 2023</td>
                                <td>2 Items</td>
                                <td>$45.99</td>
                                <td><span class="order-status status-delivered">Delivered</span></td>
                            </tr>
                            <tr>
                                <td>#ORD-1000</td>
                                <td>June 8, 2023</td>
                                <td>1 Item</td>
                                <td>$29.99</td>
                                <td><span class="order-status status-delivered">Delivered</span></td>
                            </tr>
                            <tr>
                                <td>#ORD-999</td>
                                <td>May 28, 2023</td>
                                <td>3 Items</td>
                                <td>$67.50</td>
                                <td><span class="order-status status-shipped">Shipped</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container footer-content">
            <div class="copyright">© <?php echo date("Y"); ?> ShopEase. All rights reserved.</div>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>

   
</body>
</html>
