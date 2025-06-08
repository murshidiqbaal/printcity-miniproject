<?php
$servername = "localhost";
$username = "root"; // or your MySQL username
$password = ""; // your password
$dbname = "printcity";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";


?>
