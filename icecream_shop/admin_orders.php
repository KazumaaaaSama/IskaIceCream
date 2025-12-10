<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit();
}

$message = '';

// --- HANDLE CRUD OPERATIONS ---

// 1. Update Status
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $message = "Order #{$order_id} status updated to **{$new_status}**.";
    } catch (PDOException $e) {
        $message = "Error updating status: " . $e->getMessage();
    }
}

// 2. Delete Order
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    try {
        // Since order_items is linked to orders with ON DELETE CASCADE, 
        // deleting the order will automatically delete its items.
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Order #{$id} deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting order: " . $e->getMessage();
    }
}

// --- DISPLAY LOGIC ---
$status_options = ['pending', 'processing', 'completed', 'cancelled'];

// Fetch all orders and their items
$stmt = $pdo->prepare("SELECT * FROM orders ORDER BY created_at DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orders_with_items = [];
foreach ($orders as $order) {
    $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmtItems->execute([$order['id']]);
    $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    $orders_with_items[] = $order;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders | Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 30px 20px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        .order-card { 
            border: 1px solid #ddd; margin-bottom: 20px; padding: 15px; border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .order-header h3 { margin: 0; color: #4F3691; }
        .order-status { font-weight: bold; padding: 5px 10px; border-radius: 5px; color: white; }
        .status-pending { background-color: #ffc107; color: #333; }
        .status-processing { background-color: #17a2b8; }
        .status-completed { background-color: #28a745; }
        .status-cancelled { background-color: #dc3545; }
        .order-details { border-top: 1px dashed #eee; padding-top: 10px; margin-top: 10px; }
        .item-list { list-style: none; padding-left: 0; }
        .item-list li { margin-bottom: 5px; border-bottom: 1px dotted #eee; padding-bottom: 5px; }
        .action-btns button { padding: 5px 10px; margin-left: 5px; border-radius: 5px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<header>
    <div class="header-brand-name"><h4>Ice Cream ni Iska - Admin</h4></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="admin_flavors.php">Flavors</a>
        <a href="admin_toppings.php">Toppings</a>
        <a href="admin_orders.php" class="active">Orders</a>
        <a href="admin.php?logout=true" class="logout-link">Logout</a>
    </nav>
</header>

<div class="hero">
    <div class="hero-content"><h1>Manage Orders</h1></div>
</div>

<div class="admin-container">
    <?php if ($message): ?>
        <p style="padding:10px; background:#e6ffe6; border:1px solid #0a0; color:#0a0; border-radius:5px; margin-bottom:15px;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>All Orders</h2>

    <?php if (empty($orders_with_items)): ?>
        <p>No orders have been placed yet.</p>
    <?php else: ?>
        <?php foreach ($orders_with_items as $order): 
            $status_class = 'status-' . strtolower($order['status']);
        ?>
        <div class="order-card">
            <div class="order-header">
                <h3>Order #<?php echo $order['id']; ?></h3>
                <span class="order-status <?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span>
            </div>
            
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo htmlspecialchars($order['phone']); ?>)</p>
            <p><strong>Placed On:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_amount'], 2); ?></p>
            <?php if (!empty($order['notes'])): ?>
                <p><strong>Notes:</strong> <em><?php echo nl2br(htmlspecialchars($order['notes'])); ?></em></p>
            <?php endif; ?>

            <div class="order-details">
                <h4>Items Ordered:</h4>
                <ul class="item-list">
                    <?php foreach ($order['items'] as $item): ?>
                        <li>
                            **<?php echo htmlspecialchars($item['quantity']); ?>x** <?php echo htmlspecialchars($item['flavor_name']); ?> 
                            (Unit Price: ₱<?php echo number_format($item['unit_price'], 2); ?>)
                            <?php 
                                $toppings_data = json_decode($item['toppings_json'], true);
                                if (!empty($toppings_data)):
                                    $topping_names = array_column($toppings_data, 'name');
                            ?>
                                <small>+ Toppings: <?php echo implode(', ', array_map('htmlspecialchars', $topping_names)); ?></small>
                            <?php endif; ?>
                            <span style="float:right;">Subtotal: ₱<?php echo number_format($item['line_subtotal'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="action-btns" style="margin-top: 15px;">
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <label for="status-<?php echo $order['id']; ?>">Update Status:</label>
                    <select name="status" id="status-<?php echo $order['id']; ?>">
                        <?php foreach ($status_options as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo ($s === $order['status']) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($s); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update_status" class="edit-btn">Save</button>
                </form>

                <form method="POST" style="display:inline-block;" onsubmit="return confirm('WARNING: Are you sure you want to delete this order and all its items?');">
                    <input type="hidden" name="delete_id" value="<?php echo $order['id']; ?>">
                    <button type="submit" class="delete-btn">Delete Order</button>
                </form>
            </div>

        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer><p>&copy; 2025 Ice Cream ni Iska | Admin Panel</p></footer>
</body>
</html>