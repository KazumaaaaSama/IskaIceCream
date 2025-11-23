<?php
// DATABASE SETTINGS
$host = 'localhost';
$dbname = 'icecream_shop'; // This matches the database name in your screenshot
$username = 'root';        // Default XAMPP username
$password = '';            // Default XAMPP password is empty

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set error mode to exception to catch problems
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, show error
    die("Could not connect to the database: " . $e->getMessage());
}
?>