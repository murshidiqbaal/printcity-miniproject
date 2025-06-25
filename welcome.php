<?php
include("connect.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = md5(trim($_POST["password"]));  // ⚠️ Use bcrypt in production

    // Prepare the query to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $_SESSION["username"] = $username;
        // ✅ Redirect to welcome page
        header("Location: ../welcome/welcome.php"); // adjust path if needed
        exit();
    } else {
        echo "<script>alert('Invalid credentials'); window.location.href='login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
