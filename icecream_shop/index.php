<?php
require_once 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Ice Cream ni Iska</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="header-brand-name">
        <h4>Ice Cream ni Iska</h4>
    </div>
    <nav>
        <a href="index.php" class="active">Home</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php">Order</a>
    </nav>
</header>

<div class="home-container">

    <!-- Logo -->
    <img src="images/logo.jpg" alt="Ice Cream ni Iska Logo" class="big-logo-home">

    <!-- Title + Subtitle -->
    <h1>Welcome to Ice Cream ni Iska!</h1>
    <p class="subtitle">Crafting premium scoops and flavors perfect for every mood.</p>

    <!-- Quick Navigation -->
    <div class="quick-nav">
        <h2>🍦 Quick Navigation</h2>

        <a href="menu.php">🍨 Menu</a>
        <p>View all our ice cream flavors.</p>

        <a href="customize.php">🧁 Customize</a>
        <p>Create your own cup or cone.</p>

        <a href="toppings.php">🔮 Toppings</a>
        <p>Select delicious toppings.</p>

        <a href="cart.php">🛒 Cart</a>
        <p>Review your order.</p>

        <a href="checkout.php">💳 Checkout</a>
        <p>Complete your purchase.</p>
    </div>
</div>

<footer>
    <p>&copy; 2025 Ice Cream ni Iska</p>
</footer>

</body>
</html>
