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
    // Google login remains the same...
    if (!empty($_POST['google_email'])) {
        // ... your Google login code ...
    } else {
        // Normal login (username OR phone OR email + password)
        $login_id = trim($_POST['username']); // can be username, phone, or email
        $password = trim($_POST['password']);

        // Get user by identifier only
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR phone = ? OR email = ?");
        $stmt->bind_param("sss", $login_id, $login_id, $login_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();

            // Verify password
            if (!empty($row['password']) && password_verify($password, $row['password'])) {
                // Identify login type
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

                if ($row['role'] === 'admin') {
                    header("Location: ../../Admin/HomeScreen/indexAdmin.html");
                } else {
                    header("Location: ../../User/HomePage/index.php");
                }
                exit();
            } else {
                echo "<script>alert('Invalid password!');</script>";
            }
        } else {
            echo "<script>alert('No account found with these details!');</script>";
        }
    }
}
?>
