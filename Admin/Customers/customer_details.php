<?php
// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "printcity";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if userid is set via GET or POST (here using GET)
if (isset($_GET['userid']) && !empty($_GET['userid'])) {
    $userid = intval($_GET['userid']);

    // Prepare and bind to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM user_profiles WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user profile details
        $user = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Customer Details</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                }
                .profile-container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    background-color: #f9f9f9;
                }
                .profile-container h2 {
                    text-align: center;
                }
                .profile-item {
                    margin-bottom: 10px;
                }
                .profile-label {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="profile-container">
                <h2>Customer Profile Details</h2>
                <div class="profile-item">
                    <span class="profile-label">User ID:</span> <?php echo htmlspecialchars($user['userid']); ?>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Name:</span> <?php echo htmlspecialchars($user['name']); ?>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Email:</span> <?php echo htmlspecialchars($user['email']); ?>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Phone:</span> <?php echo htmlspecialchars($user['phone']); ?>
                </div>
                <!-- Add more fields as per your user_profiles table -->
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "No user found with the given ID.";
    }

    $stmt->close();
} else {
    echo "Please provide a valid userid.";
}

// Close connection
$conn->close();
?>
