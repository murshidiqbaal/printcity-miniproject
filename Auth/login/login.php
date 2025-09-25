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
    // Normal login (username OR phone OR email + password)
    $login_id = isset($_POST['username']) ? trim($_POST['username']) : "";
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";

    if (empty($login_id) || empty($password)) {
        echo "<script>alert('Please enter both login ID and password!'); window.location.href='login.php';</script>";
        exit();
    }

    $current_phone = $_SESSION['phone'] ?? '';
$current_email = $_SESSION['email'] ?? '';

    // Fetch user record by username OR phone OR email
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR phone = ? OR email = ?");
    $stmt->bind_param("sss", $login_id, $login_id, $login_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        // Verify password
        if (!empty($row['password']) && password_verify($password, $row['password'])) {
            // Set session variables
            if ($login_id === $row['username']) {
                $_SESSION["username"] = $row['username'];
                $_SESSION["login_type"] = "username";
            } elseif ($login_id === $row['phone']) {
                $_SESSION["username"] = $row['phone'];
                $_SESSION["login_type"] = "phone";
            } elseif ($login_id === $row['email']) {
                $_SESSION["username"] = $row['email'];
                $_SESSION["login_type"] = "email";
            } else {
                $_SESSION["username"] = $row['username'] ?? $row['phone'] ?? $row['email'];
                $_SESSION["login_type"] = "unknown";
            }

            $_SESSION["user_id"] = $row['user_id'];
            $_SESSION["role"] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'admin') {
                $_SESSION["login_attempts"] = 0;
                $_SESSION["last_attempt_time"] = null;
                header("Location: ../../Admin/HomeScreen/indexAdmin.html");
            } else {
                $_SESSION["login_attempts"] = 0;
                $_SESSION["last_attempt_time"] = null;
                header("Location: ../../User/HomePage/index.php");
            }
            exit();
        } else {
            $_SESSION["login_attempts"]++;
$_SESSION["last_attempt_time"] = time();
echo "<script>alert('Invalid password!'); window.location.href='login.html';</script>";
exit();

        }
    } else {
        echo "<script>alert('No account found with these details!'); window.location.href='login.html';</script>";
        exit();
    }
}

// Initialize login attempts
if (!isset($_SESSION["login_attempts"])) {
    $_SESSION["login_attempts"] = 0;
    $_SESSION["last_attempt_time"] = time();
}

// Check if user is locked
if ($_SESSION["login_attempts"] >= 5) {
    $time_diff = time() - $_SESSION["last_attempt_time"];
    if ($time_diff < 900) { // 900 = 15 minutes
        echo "<script>alert('Too many failed attempts! Please try again after 15 minutes.'); window.location.href='login.php';</script>";
        exit();
    } else {
        // Reset attempts after 15 min
        $_SESSION["login_attempts"] = 0;
    }
}

?>
