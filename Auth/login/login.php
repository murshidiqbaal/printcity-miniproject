<?php
include("connect.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = md5($_POST["password"]);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $res = $conn->query($sql);

    if ($res->num_rows == 1) {
        $_SESSION["username"] = $username;
        header("Location: welcome.php");
        exit();
    } else {
        echo "<script>alert('Invalid credentials'); window.location.href='login.html';</script>";
    }
}
?>
