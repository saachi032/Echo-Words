<?php
$host = "localhost";  // Change if your database is on another server
$user = "root";       // Change if you have a different username
$pass = "";           // Change if you have a database password
$dbname = "echowords"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
