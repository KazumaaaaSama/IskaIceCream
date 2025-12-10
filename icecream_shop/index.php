<?php
require_once 'db_connect.php'; // This now provides $pdo
session_start();

// Handle login
if (isset($_POST['loginSubmit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection - NOW USING PDO ($pdo)
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    
    // PDO execute with array replaces bind_param and execute()
    $stmt->execute([$username, $password]); 
    
    // PDO fetch
    $admin_user = $stmt->fetch(PDO::FETCH_ASSOC); 

    if ($admin_user) {
        // Login successful
        $_SESSION['admin_user'] = $username;
        header("Location: admin.php");
        exit();
    } else {
        $login_error = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Ice Cream ni Iska</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Roboto&display=swap" rel="stylesheet">

    <style>
        /* ---------------- LOGIN POPUP ---------------- */
        .login-icon {
            width: 35px;
            height: 35px;
            cursor: pointer;
            margin-left: 20px;
            filter: brightness(0) invert(1);
            transition: 0.2s ease;
        }
        .login-icon:hover { transform: scale(1.1); }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .login-popup {
            background: white;
            width: 350px;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .login-popup h2 { margin-bottom: 15px; color: #4F3691; }
        .login-popup input {
            width: 100%; padding: 10px; margin: 8px 0;
            border-radius: 8px; border: 1px solid #ccc;
        }

        .login-popup button {
            width: 100%; padding: 10px;
            background: #6A4BB8; border: none; color:white;
            border-radius: 8px; font-weight: bold;
            cursor: pointer; margin-top: 10px;
        }
        .login-popup button:hover { background: #8f74e6; }

        .close-btn {
            background: #aaa;
        }
        .close-btn:hover { background: #888; }
    </style>
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

        <img src="images/icons/user.png" alt="Login" class="login-icon" id="loginBtn">
    </nav>
</header>

<div class="popup-overlay" id="popupOverlay">
    <div class="login-popup">
        <h2>Login</h2>

        <?php if(isset($login_error)): ?>
            <p style="color:red; margin-bottom:10px;"><?php echo $login_error; ?></p>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="loginSubmit">Login</button>
        </form>

        <button class="close-btn" id="closePopup">Back</button>
    </div>
</div>

<div class="home-container">
    <img src="images/logo.jpg" alt="Ice Cream ni Iska Logo" class="big-logo-home">

    <h1>Welcome to Ice Cream ni Iska!</h1>
    <p class="subtitle">Crafting premium scoops and flavors perfect for every mood.</p>

    <div class="quick-nav">
        <h2>📌 Quick Navigation</h2>

        <a href="menu.php">🍦 Menu</a>
        <p>View all our ice cream flavors.</p>

        <a href="customize.php">🎨 Customize</a>
        <p>Create your own cup or cone.</p>

        <a href="toppings.php">🍒 Toppings</a>
        <p>Select delicious toppings.</p>

        <a href="cart.php">🛒 Cart</a>
        <p>Review your order.</p>

        <a href="checkout.php">✅ Checkout</a>
        <p>Complete your purchase.</p>
    </div>
</div>

<footer>
    <p>&copy; 2025 Ice Cream ni Iska</p>
</footer>

<script>
    const loginBtn = document.getElementById("loginBtn");
    const popupOverlay = document.getElementById("popupOverlay");
    const closePopup = document.getElementById("closePopup");

    loginBtn.addEventListener("click", () => {
        popupOverlay.style.display = "flex";
    });

    closePopup.addEventListener("click", () => {
        popupOverlay.style.display = "none";
    });

    popupOverlay.addEventListener("click", (e) => {
        if (e.target === popupOverlay) popupOverlay.style.display = "none";
    });

    // PHP trick to show the popup on failed login attempt
    <?php if(isset($login_error)): ?>
        popupOverlay.style.display = "flex";
    <?php endif; ?>
</script>

</body>
</html>
