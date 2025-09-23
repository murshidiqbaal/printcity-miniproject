<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Auth/login/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Fetch user profile data first (to populate form)
$sql = "SELECT * FROM user_profiles WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    // If no profile exists, initialize with defaults
    $user_data = [
        'id' => null,
        'user_id' => $user_id,
        'full_name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'phone' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'zip_code' => '',
        'country' => 'India',
        'delivery_notes' => '',
        'payment_method' => '',
        'profile_picture' => '',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
}

$stmt->close();

// Handle profile picture upload if file is present (standalone, before form processing)
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "imgs/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Secure filename: prefix with timestamp and uniqid to avoid conflicts
    $file_extension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
    $file_name = time() . "_" . uniqid() . "." . strtolower($file_extension);
    $target_file = $target_dir . $file_name;

    // Validate file type and size (max 2MB, images only)
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array(strtolower($file_extension), $allowed_types) && $_FILES['profile_picture']['size'] <= 2000000) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $file_name;

            // Update or insert profile picture (using INSERT ... ON DUPLICATE KEY UPDATE)
            $update_stmt = $conn->prepare("INSERT INTO user_profiles (user_id, profile_picture, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE profile_picture = ?, updated_at = NOW()");
            $update_stmt->bind_param("sss", $user_id, $profile_picture, $profile_picture);
            if ($update_stmt->execute()) {
                $success_message = "Profile picture updated successfully.";
                $user_data['profile_picture'] = $profile_picture; // Update in memory
            } else {
                $error_message = "Error updating profile picture: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $error_message = "Error uploading file. Please try again.";
        }
    } else {
        $error_message = "Invalid file type or size. Please upload JPG, PNG, or GIF (max 2MB).";
    }
}

// Handle form submission for profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) { // Check for specific submit button
    $full_name       = trim($_POST['full_name'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $phone           = trim($_POST['phone'] ?? '');
    $address         = trim($_POST['address'] ?? '');
    $city            = trim($_POST['city'] ?? '');
    $state           = trim($_POST['state'] ?? '');
    $zip_code        = trim($_POST['zip_code'] ?? '');
    $country         = trim($_POST['country'] ?? 'India');
    $delivery_notes  = trim($_POST['delivery_notes'] ?? '');
    $payment_method  = trim($_POST['payment_method'] ?? '');

    // Validation
    if (empty($full_name) || empty($email)) {
        $error_message = "Full name and email are required.";
    } else {
        // Keep existing profile picture if no new upload
        $profile_picture = $user_data['profile_picture'] ?? '';

        // Use prepared statement for security
        if (isset($user_data['id']) && $user_data['id'] > 0) {
            // UPDATE existing profile
            $sql = "UPDATE user_profiles SET 
                        full_name = ?, email = ?, phone = ?, address = ?, city = ?, state = ?, 
                        zip_code = ?, country = ?, delivery_notes = ?, payment_method = ?, 
                        profile_picture = ?, updated_at = NOW() 
                    WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssssssi", $full_name, $email, $phone, $address, $city, $state, $zip_code, $country, $delivery_notes, $payment_method, $profile_picture, $user_id);
            $msg = "Profile updated successfully!";
        } else {
            // INSERT new profile
            $sql = "INSERT INTO user_profiles 
                    (user_id, full_name, email, phone, address, city, state, zip_code, country, delivery_notes, payment_method, profile_picture, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssssssss", $user_id, $full_name, $email, $phone, $address, $city, $state, $zip_code, $country, $delivery_notes, $payment_method, $profile_picture);
            $msg = "Profile created successfully!";
        }

        if ($stmt->execute()) {
            $success_message = $msg;
            // Refresh user_data after update
            $refresh_sql = "SELECT * FROM user_profiles WHERE user_id = ?";
            $refresh_stmt = $conn->prepare($refresh_sql);
            $refresh_stmt->bind_param("i", $user_id);
            $refresh_stmt->execute();
            $refresh_result = $refresh_stmt->get_result();
            if ($refresh_result->num_rows > 0) {
                $user_data = $refresh_result->fetch_assoc();
            }
            $refresh_stmt->close();
        } else {
            $error_message = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}
$errors = [];
$profilePicturePath = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['profile_picture']['name'])) {
        $uploadDir = __DIR__ . "/User/myprofile/imgs/"; // Absolute path
        $webDir = "User/myprofile/imgs/"; // Relative path for storing in DB

        // Create folder if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['profile_picture']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Allowed file types
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($fileExt, $allowedExt)) {
            $errors['profile_picture'] = "Only JPG, PNG, GIF, and WEBP images are allowed.";
        } else {
            // Unique filename
            $newFileName = uniqid("profile_", true) . "." . $fileExt;
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                $profilePicturePath = $webDir . $newFileName;
            } else {
                $errors['profile_picture'] = "Failed to upload image.";
            }
        }
    }

    if (empty($errors)) {
        // Example update query (modify user_id as needed)
        $userId = 1; // Get from session/auth system
        $stmt = $conn->prepare("UPDATE user_profiles SET profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("si", $profilePicturePath, $userId);
        $stmt->execute();
        $stmt->close();

        echo "<p style='color:green;'>Profile picture updated successfully!</p>";
    }
}
// Fetch recent orders for current user (limit 5)
$orders = [];
$order_sql = "SELECT order_id, order_date, quantity, total_price, status FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}
$order_stmt->close();

$conn->close();

// Profile picture path
$profile_picture_path = !empty($user_data['profile_picture']) 
    ? "imgs/" . htmlspecialchars($user_data['profile_picture']) 
    : "imgs/default.png"; // Assume default.png exists in imgs/ folder

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - PrintCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --background: #f8f9fa;
            --white: #ffffff;
            --text-dark: #212529;
            --text-light: #6c757d;
            --border-color: #dee2e6;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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
        }

        header {
            background: var(--primary-color);
            color: var(--white);
            padding: 1rem;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow-lg);
        }

        header a {
            color: var(--white);
            text-decoration: none;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .profile-sidebar {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: var(--white);
            padding: 2rem;
            text-align: center;
        }

        .profile-picture {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .profile-picture:hover {
            transform: scale(1.05);
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .profile-email {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }

        .profile-joined {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .nav-menu {
            list-style: none;
            margin-top: 2rem;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
        }

        .profile-content {
            padding: 2rem;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
            font-size: 1.75rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .form-control {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .location-container {
            display: flex;
            gap: 0.5rem;
        }

        .location-btn {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }

        .location-btn:hover {
            background: #0056b3;
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .radio-option:hover {
            background: #e9ecef;
        }

        .radio-option input[type="radio"] {
            margin: 0;
        }

        .radio-option label {
            cursor: pointer;
            margin: 0;
            font-weight: normal;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-block {
            width: 100%;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: var(--white);
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .order-table th,
        .order-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .order-table th {
            background: var(--primary-color);
            color: var(--white);
            font-weight: 600;
        }

        .order-table tr:hover {
            background: #f8f9fa;
        }

        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        footer {
            background: var(--text-dark);
            color: var(--white);
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
            .profile-sidebar {
                padding: 1.5rem;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .location-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="../HomePage/index.php">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>My Profile</h1>
    </header>

    <div class="container">
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-sidebar">
                <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" class="profile-picture" onerror="this.src='https://via.placeholder.com/140?text=Profile'">
                <h2 class="profile-name"><?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?></h2>
                <div class="profile-email"><?= htmlspecialchars($user_data['email'] ?? ''); ?></div>
                <div class="profile-joined">Member since: <?= date('F j, Y', strtotime($user_data['created_at'] ?? '')); ?></div>   
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" data-section="profile-section">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="orders-section">
                            <i class="fas fa-box"></i> Orders
                        </a>
                    </li>
                </ul>
            </div>
            <div class="profile-content">
                <div id="profile-section" class="section active">
                    <h2 class="section-title"><i class="fas fa-user-cog"></i> Update Profile</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-grid">
                            <div class="form-group
">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" required>
                                <div class="form-error"><?php echo htmlspecialchars($errors['full_name'] ?? ''); ?></div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                                <div class="form-error"><?php echo htmlspecialchars($errors['email'] ?? ''); ?></div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                                <div class="form-error"><?php echo htmlspecialchars($errors['phone'] ?? ''); ?></div>
                            </div>
                            <div class="form-group">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>">
                                <div class="form-error"><?php echo htmlspecialchars($errors['address'] ?? ''); ?></div>
                            </div>
                            <div class="form-group">
                                <label for="city" class="form-label">City</label>
                                <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>">
                                <div class="form-error"><?php echo htmlspecialchars($errors['city'] ?? ''); ?></div>
                            </div>
                            <div class="form-group">
                                <label for="state" class="form-label">State</label>
                                <input type="text" id="state" name="state" class="form-control" value="<?php echo htmlspecialchars($user_data['state'] ?? ''); ?>">
                                <div class="form-error"><?php echo htmlspecialchars($errors['state'] ?? ''); ?></div>
                            </div>  
                            <div class="form-group">
                                <label for="zip_code" class="form-label">Zip Code</label>
                                <input type="text" id="zip_code" name="zip_code" class="form-control" value="<?php echo htmlspecialchars($user_data['zip_code'] ?? ''); ?>">
                                <div class="form-error"><?php echo htmlspecialchars($errors['zip_code'] ?? ''); ?></div>
                            </div>
                            <div class="form-group">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" id="country" name="country" class="form-control" value="<?php echo htmlspecialchars($user_data['country'] ?? 'India'); ?>">
                                <div class="form-error"><?php echo htmlspecialchars($errors['country'] ?? ''); ?></div>
                            </div>
                             <div class="form-group">
        <label for="profile_picture" class="form-label">Profile Picture</label>
        <input type="file" id="profile_picture" name="profile_picture" class="form-control" accept="image/*">
        <div class="form-error"><?php echo htmlspecialchars($errors['profile_picture'] ?? ''); ?></div>
    </div>
                            <div class="form-group">
                                <label for="delivery_notes" class="form-label">Delivery Notes</label>
                                <textarea id="delivery_notes" name="delivery_notes" class="form-control"><?php echo htmlspecialchars($user_data['delivery_notes'] ?? ''); ?></textarea>
                                <div class="form-error"><?php echo htmlspecialchars($errors['delivery_notes'] ?? ''); ?></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Payment Method</label>
                                <div class="radio-group">
                                    <?php 
                                    $payment_methods = ['Credit Card', 'Debit Card', 'PayPal', 'Cash on Delivery'];
                                    foreach ($payment_methods as $method): 
                                        $checked = (isset($user_data['payment_method']) && $user_data['payment_method'] === $method) ? 'checked' : '';
                                    ?>
                                    <div class="radio-option">
                                        <input type="radio" id="payment_<?php echo strtolower(str_replace(' ', '_', $method)); ?>" name="payment_method" value="<?php echo $method; ?>" <?php echo $checked; ?>>
                                        <label for="payment_<?php echo strtolower(str_replace(' ', '_', $method)); ?>"><?php echo $method; ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="form-error"><?php echo htmlspecialchars($errors['payment_method'] ?? ''); ?></div>
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-block">Update Profile</button>
                    </form>
                </div>
                <div id="orders-section" class="section">
                    <h2 class="section-title"><i class="fas fa-box"></i> Recent Orders</h2>
                    <?php if (count($orders) > 0): ?>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Quantity</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                        <td><?php echo date('F j, Y', strtotime($order['order_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                        <td>
                                            <?php 
                                                $status_class = '';
                                                switch (strtolower($order['status'])) {
                                                    case 'pending':
                                                        $status_class = 'status-pending';
                                                        break;
                                                    case 'shipped':
                                                        $status_class = 'status-shipped';
                                                        break;
                                                    case 'delivered':
                                                        $status_class = 'status-delivered';
                                                        break;
                                                    case 'cancelled':
                                                        $status_class = 'status-cancelled';
                                                        break;
                                                    default:
                                                        $status_class = '';
                                                }
                                            ?>
                                            <span class="order-status <?php echo $status_class; ?>">
                                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="no-orders">No recent orders found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <footer>
        &copy; <?php echo date('Y'); ?> PrintCity. All rights reserved.
    </footer>
    <script>
        // Navigation tab functionality
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('.section');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetSection = link.getAttribute('data-section');

                // Remove active class from all links and sections
                navLinks.forEach(l => l.classList.remove('active'));
                sections.forEach(sec => sec.classList.remove('active'));

                // Add active class to clicked link and corresponding section
                link.classList.add('active');
                document.getElementById(targetSection).classList.add('active');
            });
        });

        // Geolocation for address autofill
        document.querySelector('.location-btn').addEventListener('click', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const { latitude, longitude } = position.coords;
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.address) {
                                document.getElementById('address').value = data.address.road || '';
                                document.getElementById('city').value = data.address.city || data.address.town || data.address.village || '';
                                document.getElementById('state').value = data.address.state || '';
                                document.getElementById('zip_code').value = data.address.postcode || '';
                                document.getElementById('country').value = data.address.country || 'India';
                            } else {
                                alert('Unable to retrieve address details.');
                            }
                        })
                        .catch(() => alert('Error fetching address details.'));
                }, () => {
                    alert('Geolocation permission denied or unavailable.');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        });
    </script>
</body>
</html>
<?php
// Helper function to escape HTML
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>