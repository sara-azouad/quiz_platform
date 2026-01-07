<?php
// db.php: Database connection

$host = "localhost";      // usually localhost
$dbname = "test_quizdb";  // your database name
$username = "root";       // your MySQL username
$password = "";           // your MySQL password (default empty for XAMPP)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successfully"; // for testing
?>
