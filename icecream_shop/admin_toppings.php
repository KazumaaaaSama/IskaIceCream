<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit();
}

$message = '';
$editTopping = null;

// --- HANDLE CRUD OPERATIONS ---

// 1. Delete Topping
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM toppings WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Topping deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting topping: " . $e->getMessage();
    }
}

// 2. Fetch Topping for Editing
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM toppings WHERE id = ?");
    $stmt->execute([$id]);
    $editTopping = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editTopping) {
        $message = "Topping not found.";
    }
}

// 3. Add or Update Topping
if (isset($_POST['submit_topping'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Update existing topping
        $sql = "UPDATE toppings SET name=?, price=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $price, $id]);
        $message = "Topping updated successfully!";
        header("Location: admin_toppings.php?msg=" . urlencode($message));
        exit();
    } else {
        // Add new topping
        $sql = "INSERT INTO toppings (name, price) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $price]);
        $message = "Topping added successfully!";
        header("Location: admin_toppings.php?msg=" . urlencode($message));
        exit();
    }
}

// --- DISPLAY LOGIC ---
$message = $_GET['msg'] ?? $message;

// Fetch all toppings
$stmt = $pdo->prepare("SELECT * FROM toppings ORDER BY id DESC");
$stmt->execute();
$toppings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Toppings | Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container { max-width: 1000px; margin: 30px auto; padding: 30px 20px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .data-table th { background-color: #f2f2f2; }
        .action-btns a, .action-btns button { padding: 5px 10px; margin-right: 5px; border-radius: 5px; text-decoration: none; cursor: pointer; }
        .edit-btn { background-color: #28a745; color: white; border: none; }
        .delete-btn { background-color: #dc3545; color: white; border: none; }
        .add-form { background-color: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .add-form input { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>

<header>
    <div class="header-brand-name"><h4>Ice Cream ni Iska - Admin</h4></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="admin_flavors.php">Flavors</a>
        <a href="admin_toppings.php" class="active">Toppings</a>
        <a href="admin_orders.php">Orders</a>
        <a href="admin.php?logout=true" class="logout-link">Logout</a>
    </nav>
</header>

<div class="hero">
    <div class="hero-content"><h1>Manage Toppings</h1></div>
</div>

<div class="admin-container">
    <?php if ($message): ?>
        <p style="padding:10px; background:#e6ffe6; border:1px solid #0a0; color:#0a0; border-radius:5px; margin-bottom:15px;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2><?php echo $editTopping ? 'Edit Topping: ' . htmlspecialchars($editTopping['name']) : 'Add New Topping'; ?></h2>
    <div class="add-form">
        <form method="POST" action="admin_toppings.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($editTopping['id'] ?? ''); ?>">
            
            <input type="text" name="name" placeholder="Topping Name (e.g., Peanuts)" value="<?php echo htmlspecialchars($editTopping['name'] ?? ''); ?>" required>
            
            <input type="number" name="price" step="0.01" placeholder="Price (e.g., 5.00)" value="<?php echo htmlspecialchars($editTopping['price'] ?? ''); ?>" required>
            
            <button type="submit" name="submit_topping" class="add-btn"><?php echo $editTopping ? 'Update Topping' : 'Add Topping'; ?></button>
            <?php if ($editTopping): ?>
                <a href="admin_toppings.php"><button type="button" class="add-btn" style="background:#aaa;">Cancel Edit</button></a>
            <?php endif; ?>
        </form>
    </div>

    <h2>Current Toppings</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($toppings as $topping): ?>
                <tr>
                    <td><?php echo htmlspecialchars($topping['id']); ?></td>
                    <td><?php echo htmlspecialchars($topping['name']); ?></td>
                    <td>₱<?php echo number_format($topping['price'], 2); ?></td>
                    <td class="action-btns">
                        <a href="admin_toppings.php?edit_id=<?php echo $topping['id']; ?>" class="edit-btn">Edit</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this topping?');">
                            <input type="hidden" name="delete_id" value="<?php echo $topping['id']; ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<footer><p>&copy; 2025 Ice Cream ni Iska | Admin Panel</p></footer>
</body>
</html>