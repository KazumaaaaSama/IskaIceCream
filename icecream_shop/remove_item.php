<?php
// remove_item.php
session_start();
$cart_item_id = $_POST['cart_item_id'] ?? null;
if (!$cart_item_id || !isset($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

foreach ($_SESSION['cart'] as $k => $ci) {
    if ($ci['cart_item_id'] === $cart_item_id) {
        unset($_SESSION['cart'][$k]);
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        break;
    }
}

header('Location: cart.php');
exit;
