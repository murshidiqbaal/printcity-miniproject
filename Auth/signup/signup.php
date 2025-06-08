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

<!-- Simple Signup Form -->
<!DOCTYPE html>
<html>
<head><title>Sign Up</title></head>
<body>
  <h2>Sign Up</h2>
  <form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Sign Up</button>
  </form>
  <p>Already have an account? <a href="login.html">Login here</a></p>
</body>
</html>
