<?php
session_start();

if (isset($_SESSION["user_id"])) {
    // Already logged in → go directly to homepage
    if ($_SESSION["role"] === 'admin') {
        header("Location: ../../Admin/HomeScreen/indexAdmin.html");
    } else {
        header("Location: ../../User/HomePage/index.php");
    }
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = isset($_POST["identifier"]) ? trim($_POST["identifier"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
    $confirm_password = isset($_POST["confirm_password"]) ? trim($_POST["confirm_password"]) : "";

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='signup.php';</script>";
        exit();
    }

    // Detect type: email / phone / username
    $email = null;
    $phone = null;
    $username = null;

    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $email = $identifier;
    } elseif (preg_match('/^[0-9]{10}$/', $identifier)) {
        $phone = $identifier;
    } else {
        $username = $identifier;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if already exists
    if ($email) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
    } elseif ($phone) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->bind_param("s", $phone);
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo "<script>alert('This account already exists!'); window.location.href='signup.php';</script>";
    } else {
        $insert = $conn->prepare("INSERT INTO users (username, email, phone, password, role, created_at) 
                                  VALUES (?, ?, ?, ?, 'user', NOW())");
        $insert->bind_param("ssss", $username, $email, $phone, $hashed_password);

        if ($insert->execute()) {
            $_SESSION["user_id"] = $insert->insert_id;
            $_SESSION["username"] = $username ?? $email ?? $phone;
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
