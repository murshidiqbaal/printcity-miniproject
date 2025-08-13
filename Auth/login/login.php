<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "printcity");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Get the user with matching username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        // Check if password matches
        if (password_verify($password, $row['password'])) {

            // Store all required session data
            $_SESSION["user_id"] = $row['user_id'];  // <-- Correct field name from your table
            $_SESSION["username"] = $row['username'];
            $_SESSION["role"] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: ../../Admin/HomeScreen/indexAdmin.html");
            } else {
                header("Location: ../../User/HomePage/index.php");
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Username not found'); window.location.href='login.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
