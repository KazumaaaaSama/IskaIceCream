_<?php
// add_to_cart.php
session_start();
require_once 'db_connect.php';
require_once 'toppings.php';

$flavor_id = $_POST['flavor_id'] ?? null;
$selected_toppings = $_POST['toppings'] ?? [];
$quantity = max(1, intval($_POST['quantity'] ?? 1));

if (!$flavor_id) {
    header('Location: menu.php');
    exit;
}

// fetch flavor
$sql = "SELECT * FROM flavors WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$flavor_id]);
$flavor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$flavor) {
    die("Flavor not found.");
}

// Map topping IDs to topping info
$final_toppings = [];
$total_toppings_price = 0.0;
foreach ($selected_toppings as $tid) {
    foreach ($toppings as $t) {
        if ($t['id'] == $tid) {
            $final_toppings[] = $t;
            $total_toppings_price += floatval($t['price']);
            break;
        }
    }
}

// Create a cart item structure
$item = [
    "cart_item_id" => uniqid('ci_'), // unique id for client-side removal/updates
    "flavor_id" => $flavor['id'],
    "flavor_name" => $flavor['name'],
    "flavor_price" => floatval($flavor['price']),
    "toppings" => $final_toppings,
    "toppings_price" => $total_toppings_price,
    "quantity" => $quantity,
    "subtotal" => ($flavor['price'] + $total_toppings_price) * $quantity
];

// Initialize cart if necessary
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Append item
$_SESSION['cart'][] = $item;

// Redirect to cart
header("Location: cart.php");
exit;
