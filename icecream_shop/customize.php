<?php
// customize.php
require_once 'db_connect.php';
require_once 'toppings.php';
session_start();

$flavor_id = $_POST['flavor_id'] ?? null;
if (!$flavor_id) {
    header('Location: menu.php');
    exit;
}

// Fetch flavor
$sql = "SELECT * FROM flavors WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$flavor_id]);
$flavor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flavor) {
    die("Flavor not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Customize | Ice Cream ni Iska</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="header-brand-name">
        <h4>Ice Cream ni Iska</h4>
    </div>
    <nav>
        <a href="index.html">Home</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php">Order</a>
    </nav>
</header>

<section class="hero">
    <div class="hero-content">
        <h1>Customize Your Ice Cream</h1>
        <img src="/mnt/data/874478b3-b336-4caf-b324-71893bf45d1a.png" alt="Fox" class="big-logo">
    </div>
</section>

<main style="padding: 30px 5%;">
    <div class="customize-container">
        <div class="flavor-preview" style="display:flex; gap:20px; align-items:center;">
            <img src="images/<?php echo htmlspecialchars($flavor['image_path']); ?>" alt="<?php echo htmlspecialchars($flavor['name']); ?>" style="width:200px; height:200px; object-fit:cover; border-radius:12px; border: 3px solid #6A4BB8;">
            <div>
                <h2><?php echo htmlspecialchars($flavor['name']); ?></h2>
                <p><?php echo htmlspecialchars($flavor['description']); ?></p>
                <p><strong>Base Price:</strong> ₱<?php echo number_format($flavor['price'],2); ?></p>
            </div>
        </div>

        <form action="add_to_cart.php" method="POST" style="margin-top:20px;">
            <input type="hidden" name="flavor_id" value="<?php echo $flavor['id']; ?>">

            <h3>Select Toppings (optional)</h3>
            <div class="toppings-list">
                <?php foreach($toppings as $t): ?>
                    <label class="topping-option">
                        <input type="checkbox" name="toppings[]" value="<?php echo $t['id']; ?>">
                        <?php echo htmlspecialchars($t['name']); ?> (+₱<?php echo number_format($t['price'],2); ?>)
                    </label>
                <?php endforeach; ?>
            </div>

            <label style="display:block; margin-top:15px;">
                Quantity:
                <input type="number" name="quantity" value="1" min="1" style="width:70px; padding:6px; margin-left:10px;">
            </label>

            <button class="add-btn" style="margin-top:20px;">Add to Cart</button>
            <a href="menu.php" style="margin-left:10px; text-decoration:none;">
                <button type="button" class="add-btn" style="background:#fff; border:1px solid #C7B3FF; color:#4F3691;">Back to Menu</button>
            </a>
        </form>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Ice Cream ni Iska</p>
</footer>

</body>
</html>
