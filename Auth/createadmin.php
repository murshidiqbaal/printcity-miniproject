<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Connect to database
        $mysqli = new mysqli("localhost", "root", "", "printcity");
        if ($mysqli->connect_errno) {
            die("Database connection failed: " . $mysqli->connect_error);
        }

        // Insert admin
        $stmt = $mysqli->prepare("INSERT INTO users (username, password, role, created_at) VALUES (?, ?, 'admin', NOW())");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            $success = "✅ Admin user created successfully.";
        } else {
            $error = "❌ Error creating admin: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>Create Admin</h2>
        
        <?php if (!empty($success)): ?>
            <div class="message success"><?= $success ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Create Admin</button>
    </form>
</body>
</html>
