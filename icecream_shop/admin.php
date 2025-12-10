<?php
session_start();

// 1. Check if the admin is logged in. If not, redirect to the home page (where login is).
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit();
}

// 2. Handle Logout
if (isset($_GET['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: index.php"); // Redirect to the home page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Ice Cream ni Iska</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Roboto&display=swap" rel="stylesheet">
    
    <style>
        /* Optional: Basic styling for the admin-specific content */
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px 20px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            min-height: 70vh;
        }

        .admin-container h1 {
            color: #6A4BB8;
            margin-bottom: 20px;
            border-bottom: 2px solid #E3DFFF;
            padding-bottom: 10px;
        }

        .welcome-message {
            font-size: 1.2em;
            color: #7D5BA6;
            margin-bottom: 30px;
        }

        /* Added quick-nav styling */
        .quick-nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .quick-nav a {
            display: block;
            padding: 20px;
            border-radius: 8px;
            background-color: #E3DFFF;
            color: #4F3691;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: background-color 0.3s;
        }
        .quick-nav a:hover {
            background-color: #C7B3FF;
        }

        /* Nav link adjustment for better appearance */
        nav a {
            font-size: 1.1em;
            padding: 0 15px; /* Added spacing to nav links */
        }
        
        /* Style the logout link/button */
        .logout-link {
            background-color: #a33;
            color: #FFFFFF !important;
            padding: 5px 10px;
            border-radius: 5px;
            margin-left: 20px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            font-weight: bold;
        }
        .logout-link:hover {
            background-color: #c44;
            color: #FFFFFF !important;
        }

    </style>
</head>

<body>

<header>
    <div class="header-brand-name">
        <h4>Ice Cream ni Iska - Admin</h4>
    </div>

    <nav>
        <a href="admin.php" class="active">Dashboard</a>
        <a href="admin_flavors.php">Flavors</a>
        <a href="admin_toppings.php">Toppings</a>
        <a href="admin_orders.php">Orders</a>

        <a href="admin.php?logout=true" class="logout-link">Logout</a>
    </nav>
</header>

<div class="hero">
    <div class="hero-content">
        <img src="images/logo.jpg" alt="Logo" class="big-logo"> 
        <h1>Admin Dashboard</h1>
    </div>
</div>

<div class="admin-container">
    <p class="welcome-message">
        Welcome, **<?php echo htmlspecialchars($_SESSION['admin_user']); ?>**!
        Use the links below or the navigation bar to manage your shop.
    </p>

    <h2>Quick Management Links</h2>
    <div class="quick-nav">
        <a href="admin_flavors.php">🍦 Manage Flavors</a>
        <a href="admin_toppings.php">🍒 Manage Toppings</a>
        <a href="admin_orders.php">📦 View and Process Orders</a>
    </div>
    
    </div>

<footer>
    <p>&copy; 2025 Ice Cream ni Iska | Admin Panel</p>
</footer>

</body>
</html>
