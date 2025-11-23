<?php
// cart.php
session_start();
$cart = $_SESSION['cart'] ?? [];
$total = 0.00;
foreach ($cart as $c) {
    $total += floatval($c['subtotal']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Your Cart | Ice Cream ni Iska</title>
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
        <a href="cart.php" class="active">Order</a>
    </nav>
</header>

<section class="hero">
    <div class="hero-content">
        <h1>Your Order</h1>
        <img src="/mnt/data/1f501d3a-5f79-4936-877b-f3a7f41d814e.png" alt="Logo" class="big-logo">
    </div>
</section>

<main style="padding: 30px 5%;">
    <div class="cart-container">
        <?php if (empty($cart)): ?>
            <p>Your cart is empty — <a href="menu.php">browse the menu</a>.</p>
        <?php else: ?>
            <?php foreach ($cart as $index => $item): ?>
                <div class="cart-item" style="border:1px solid #eee; padding:15px; margin-bottom:12px; border-radius:10px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <h3><?php echo htmlspecialchars($item['flavor_name']); ?></h3>
                            <p>Base: ₱<?php echo number_format($item['flavor_price'],2); ?></p>

                            <?php if (!empty($item['toppings'])): ?>
                                <p><strong>Toppings:</strong></p>
                                <ul>
                                    <?php foreach ($item['toppings'] as $t): ?>
                                        <li><?php echo htmlspecialchars($t['name']); ?> (₱<?php echo number_format($t['price'],2); ?>)</li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <p><strong>Quantity:</strong> <?php echo intval($item['quantity']); ?></p>
                            <p><strong>Subtotal:</strong> ₱<?php echo number_format($item['subtotal'],2); ?></p>
                        </div>
                        <div>
                            <!-- Remove item form -->
                            <form action="remove_item.php" method="POST">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <button class="add-btn" type="submit" style="background:#fff; border:1px solid #C7B3FF; color:#4F3691;">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div style="text-align:right; margin-top:10px;">
                <h3>Total: ₱<?php echo number_format($total,2); ?></h3>
                <a href="checkout.php"><button class="add-btn" style="margin-top:10px;">Proceed to Checkout</button></a>
            </div>

        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Ice Cream ni Iska</p>
</footer>

</body>
</html>
