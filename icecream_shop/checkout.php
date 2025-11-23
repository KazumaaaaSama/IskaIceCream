<?php
// checkout.php
session_start();
require_once 'db_connect.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($customer_name === '') $errors[] = "Please enter your name.";
    if ($phone === '') $errors[] = "Please enter your contact number.";

    if (empty($errors)) {
        // Calculate total
        $total = 0;
        foreach ($cart as $c) {
            $total += floatval($c['subtotal']);
        }

        try {
            // Start transaction
            $pdo->beginTransaction();

            // Insert into orders
            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone, notes, total_amount, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$customer_name, $phone, $notes, $total]);
            $order_id = $pdo->lastInsertId();

            // Insert order_items
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, flavor_id, flavor_name, unit_price, quantity, toppings_json, line_subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($cart as $c) {
                $toppings_json = json_encode($c['toppings']);
                $stmtItem->execute([
                    $order_id,
                    $c['flavor_id'],
                    $c['flavor_name'],
                    $c['flavor_price'],
                    $c['quantity'],
                    $toppings_json,
                    $c['subtotal']
                ]);
            }

            $pdo->commit();

            // Clear cart
            unset($_SESSION['cart']);
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Error saving order: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Checkout | Ice Cream ni Iska</title>
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
        <h1>Checkout</h1>
    </div>
</section>

<main style="padding: 30px 5%; max-width:800px; margin:0 auto;">
    <?php if ($success): ?>
        <div style="padding:20px; border-radius:8px; background:#E3DFFF; color:#4F3691;">
            <h2>Thank you! Your order has been placed.</h2>
            <p>We received your order and will contact you shortly.</p>
            <a href="menu.php"><button class="add-btn">Back to Menu</button></a>
        </div>
    <?php else: ?>

        <?php if (!empty($errors)): ?>
            <div style="background:#ffdede; padding:12px; margin-bottom:12px; border-radius:8px;">
                <?php foreach ($errors as $err): ?>
                    <p style="margin:4px 0; color:#8b0000;"><?php echo htmlspecialchars($err); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom:20px;">
            <h3>Order Summary</h3>
            <?php $grandTotal = 0; ?>
            <?php foreach ($cart as $c): ?>
                <div style="border-bottom:1px solid #eee; padding:8px 0;">
                    <strong><?php echo htmlspecialchars($c['flavor_name']); ?></strong>
                    <div>Qty: <?php echo intval($c['quantity']); ?> — Subtotal: ₱<?php echo number_format($c['subtotal'],2); ?></div>
                    <?php if (!empty($c['toppings'])): ?>
                        <div>Toppings:
                            <?php foreach ($c['toppings'] as $t): ?>
                                <?php echo htmlspecialchars($t['name']); ?><?php if(end($c['toppings']) !== $t) echo ', '; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php $grandTotal += floatval($c['subtotal']); ?>
            <?php endforeach; ?>
            <h4 style="margin-top:12px;">Total: ₱<?php echo number_format($grandTotal,2); ?></h4>
        </div>

        <form method="POST">
            <label style="display:block; margin-bottom:8px;">
                Your Name
                <input type="text" name="name" required style="width:100%; padding:8px; margin-top:6px;">
            </label>

            <label style="display:block; margin-bottom:8px;">
                Contact Number
                <input type="text" name="phone" required style="width:100%; padding:8px; margin-top:6px;">
            </label>

            <label style="display:block; margin-bottom:8px;">
                Notes (optional)
                <textarea name="notes" style="width:100%; padding:8px; margin-top:6px;"></textarea>
            </label>

            <button class="add-btn" type="submit">Place Order</button>
        </form>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Ice Cream ni Iska</p>
</footer>

</body>
</html>
