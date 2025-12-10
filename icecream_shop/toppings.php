<?php
// toppings.php
// This file is now used to fetch all available toppings from the database.
require_once 'db_connect.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM toppings ORDER BY name ASC");
    $stmt->execute();
    $toppings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    
    $toppings = []; 
    
}
?>
