<?php
session_start();

// Sample user data (in a real app, this would come from your database)
$user_data = array(
    'full_name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '+1 (555) 123-4567',
    'address' => '1234 Main St, Apt 301',
    'city' => 'San Francisco',
    'state' => 'CA',
    'zip_code' => '94105',
    'country' => 'United States',
    'delivery_notes' => 'Ring the bell twice for delivery',
    'payment_method' => 'credit_card', // credit_card, paypal, etc.
    'profile_picture' => 'https://placehold.co/150x150'
);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real app, you would:
    // 1. Validate all inputs
    // 2. Sanitize dataz
    // 3. Update database
    // 4. Show success/error message
    $user_data = array_merge($user_data, $_POST);
    
    // For demonstration, we'll just set a success message
    $success_message = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Order Delivery Details</title>
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #f9fafb;
            --text-color: #1f2937;
            --light-gray: #f3f4f6;
            --medium-gray: #e5e7eb;
            --dark-gray: #6b7280;
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f9fafb;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .nav-links a {
            margin-left: 20px;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
        }

        .page-title {
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            margin-bottom: 50px;
        }

        .profile-sidebar {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 30px;
            height: fit-content;
        }

        .profile-main {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            border: 3px solid var(--primary-color);
        }

        .profile-name {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-email {
            text-align: center;
            color: var(--dark-gray);
            margin-bottom: 30px;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 10px;
        }

        .nav-link {
            display: block;
            padding: 10px 15px;
            color: var(--text-color);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--light-gray);
            color: var(--primary-color);
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--medium-gray);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--medium-gray);
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
        }

        .radio-option input {
            margin-right: 8px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--primary-hover);
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .location-container {
            position: relative;
        }

        .location-btn {
            position: absolute;
            right: 10px;
            top: 35px;
            background: var(--light-gray);
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .location-btn:hover {
            background: var(--medium-gray);
        }

        .order-history {
            margin-top: 40px;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-table th, .order-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--medium-gray);
        }

        .order-table th {
            font-weight: 600;
            background-color: var(--light-gray);
        }

        .order-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-shipped {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .security-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--medium-gray);
        }

        .security-item:last-child {
            border-bottom: none;
        }

        .security-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .security-info p {
            color: var(--dark-gray);
            font-size: 14px;
        }

        .security-action {
            color: var(--primary-color);
            font-weight: 500;
            cursor: pointer;
        }

        .security-action:hover {
            text-decoration: underline;
        }

        footer {
            padding: 30px 0;
            background-color: white;
            border-top: 1px solid var(--medium-gray);
            margin-top: 50px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .copyright {
            color: var(--dark-gray);
        }

        .footer-links a {
            margin-left: 20px;
            color: var(--text-color);
            text-decoration: none;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        @media (max-width: 992px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .nav-links {
                margin-top: 15px;
            }
            
            .nav-links a {
                margin-left: 0;
                margin-right: 15px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <a href="/" class="logo">ShopEase</a>
            <div class="nav-links">
                <a href="#"><i class="fas fa-home"></i> Home</a>
                <a href="#"><i class="fas fa-shopping-bag"></i> Orders</a>
                <a href="#"><i class="fas fa-user"></i> Profile</a>
            </div>
        </div>
    </header>

    <main class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <h1 class="page-title">My Profile</h1>

        <div class="profile-container">
            <div class="profile-sidebar">
                <img src="<?php echo htmlspecialchars($user_data['profile_picture']); ?>" alt="Profile picture of <?php echo htmlspecialchars($user_data['full_name']); ?>" class="profile-picture">
                <h2 class="profile-name"><?php echo htmlspecialchars($user_data['full_name']); ?></h2>
                <div class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></div>
                
                <ul class="nav-menu">
                    <li class="nav-item"><a href="#" class="nav-link active"><i class="fas fa-user-circle"></i> Personal Info</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-map-marker-alt"></i> Delivery Addresses</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-credit-card"></i> Payment Methods</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-box-open"></i> Order History</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-shield-alt"></i> Security</a></li>
                </ul>
            </div>

            <div class="profile-main">
                <form method="POST" id="profileForm">
                    <h2 class="section-title">Personal Information</h2>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" id="profile_picture" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <h2 class="section-title" style="margin-top: 30px;">Delivery Information</h2>
                    
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
                            <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($user_data['city']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="state" class="form-label">State/Province</label>
                            <input type="text" id="state" name="state" class="form-control" value="<?php echo htmlspecialchars($user_data['state']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="zip_code" class="form-label">ZIP/Postal Code</label>
                            <input type="text" id="zip_code" name="zip_code" class="form-control" value="<?php echo htmlspecialchars($user_data['zip_code']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="country" class="form-label">Country</label>
                            <select id="country" name="country" class="form-control" required>
                                <option value="">Select Country</option>
                                <option value="United States" <?php echo $user_data['country'] === 'United States' ? 'selected' : ''; ?>>United States</option>
                                <option value="Canada" <?php echo $user_data['country'] === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                                <option value="United Kingdom" <?php echo $user_data['country'] === 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="delivery_notes" class="form-label">Delivery Notes (Optional)</label>
                            <textarea id="delivery_notes" name="delivery_notes" class="form-control"><?php echo htmlspecialchars($user_data['delivery_notes']); ?></textarea>
                            <p style="font-size: 13px; color: var(--dark-gray); margin-top: 5px;">Special instructions for delivery drivers or package handling</p>
                        </div>
                    </div>

                    <h2 class="section-title" style="margin-top: 30px;">Payment Method</h2>
                    
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

                <div class="order-history">
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

    <script>
        // Use current location for address
        document.getElementById('getLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        
                        // In a real app, you would call a geocoding API (like Google Maps or Mapbox)
                        // to convert coordinates to a human-readable address
                        // This is just a simulation
                        document.getElementById('address').value = "456 Current Location St";
                        document.getElementById('city').value = "San Francisco";
                        document.getElementById('state').value = "CA";
                        document.getElementById('zip_code').value = "94102";
                        
                        alert("Location detected and address fields populated automatically!");
                    },
                    function(error) {
                        alert("Error getting location: " + error.message);
                    }
                );
            } else {
                alert("Geolocation is not supported by your browser");
            }
        });

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            const city = document.getElementById('city').value.trim();
            const state = document.getElementById('state').value.trim();
            const zipCode = document.getElementById('zip_code').value.trim();
            const country = document.getElementById('country').value;
            
            // Basic phone validation
            if (!/^[\d\s\(\)\-\+]{10,}$/.test(phone)) {
                alert("Please enter a valid phone number with at least 10 digits");
                e.preventDefault();
                return;
            }
            
            // Basic address validation
            if (!address || !city || !state || !zipCode || !country) {
                alert("Please fill in all required address fields");
                e.preventDefault();
                return;
            }
            
            // In a real app, you might make an AJAX call to save the form
            // Here we just allow the form to submit normally
        });

        // Preview profile picture when selected
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.querySelector('.profile-picture').src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
