<?php
session_start();

// Connect to MySQL
$conn = mysqli_connect("localhost", "root", "", "printcity");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in both username and password.'); window.location.href='signup.html';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo "<script>alert('Username already taken!'); window.location.href='signup.html';</script>";
    } else {
        // Insert new user with role 'user'
        $insert = $conn->prepare("INSERT INTO users (username, password, role, created_at) VALUES (?, ?, 'user', NOW())");
        $insert->bind_param("ss", $username, $hashed_password);

        if ($insert->execute()) {
            $_SESSION["username"] = $username;
            $_SESSION["role"] = 'user';
            header("Location: ../../User/HomePage/index.php");
            exit();
        } else {
            echo "Error: " . $insert->error;
        }

        $insert->close();
    }

    $stmt->close();
}

$conn->close();
?>
