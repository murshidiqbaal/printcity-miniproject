<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  // Use bcrypt in real apps

    // Check if user exists
    $check = "SELECT * FROM users WHERE username='$username'";
    $res = $conn->query($check);

    if ($res->num_rows > 0) {
        echo "<script>alert('Username already taken!'); window.location.href='signup.php';</script>";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql)) {
            echo "<script>alert('Registration successful! Please log in.'); window.location.href='login.html';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

