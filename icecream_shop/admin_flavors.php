<?php
session_start();
require_once 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_user'])) {
    header("Location: index.php");
    exit();
}

$message = '';
$editFlavor = null;

// --- HANDLE CRUD OPERATIONS ---

// 1. Delete Flavor
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM flavors WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Flavor deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting flavor: " . $e->getMessage();
    }
}

// 2. Fetch Flavor for Editing
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM flavors WHERE id = ?");
    $stmt->execute([$id]);
    $editFlavor = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editFlavor) {
        $message = "Flavor not found.";
    }
}

// 3. Add or Update Flavor
if (isset($_POST['submit_flavor'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_path = trim($_POST['image_path']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Update existing flavor
        $sql = "UPDATE flavors SET name=?, description=?, price=?, image_path=?, is_active=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $price, $image_path, $is_active, $id]);
        $message = "Flavor updated successfully!";
        header("Location: admin_flavors.php?msg=" . urlencode($message));
        exit();
    } else {
        // Add new flavor
        $sql = "INSERT INTO flavors (name, description, price, image_path, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $price, $image_path, $is_active]);
        $message = "Flavor added successfully!";
        header("Location: admin_flavors.php?msg=" . urlencode($message));
        exit();
    }
}

// --- DISPLAY LOGIC ---
$message = $_GET['msg'] ?? $message;

// Fetch all flavors
$stmt = $pdo->prepare("SELECT * FROM flavors ORDER BY id DESC");
$stmt->execute();
$flavors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Flavors | Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 30px 20px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .data-table th { background-color: #f2f2f2; }
        .action-btns a, .action-btns button { padding: 5px 10px; margin-right: 5px; border-radius: 5px; text-decoration: none; cursor: pointer; }
        .edit-btn { background-color: #28a745; color: white; border: none; }
        .delete-btn { background-color: #dc3545; color: white; border: none; }
        .add-form { background-color: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .add-form input, .add-form textarea { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .form-row { display: flex; gap: 20px; }
        .form-row > div { flex: 1; }
        .active-dot { height: 10px; width: 10px; background-color: #28a745; border-radius: 50%; display: inline-block; }
        .inactive-dot { background-color: #dc3545; }
    </style>
</head>
<body>

<header>
    <div class="header-brand-name"><h4>Ice Cream ni Iska - Admin</h4></div>
    <nav>
        <a href="admin.php">Dashboard</a>
        <a href="admin_flavors.php" class="active">Flavors</a>
        <a href="admin_toppings.php">Toppings</a>
        <a href="admin_orders.php">Orders</a>
        <a href="admin.php?logout=true" class="logout-link">Logout</a>
    </nav>
</header>

<div class="hero">
    <div class="hero-content"><h1>Manage Flavors</h1></div>
</div>

<div class="admin-container">
    <?php if ($message): ?>
        <p style="padding:10px; background:#e6ffe6; border:1px solid #0a0; color:#0a0; border-radius:5px; margin-bottom:15px;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2><?php echo $editFlavor ? 'Edit Flavor: ' . htmlspecialchars($editFlavor['name']) : 'Add New Flavor'; ?></h2>
    <div class="add-form">
        <form method="POST" action="admin_flavors.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($editFlavor['id'] ?? ''); ?>">
            
            <input type="text" name="name" placeholder="Flavor Name (e.g., Strawberry)" value="<?php echo htmlspecialchars($editFlavor['name'] ?? ''); ?>" required>
            
            <textarea name="description" placeholder="Description" rows="3" required><?php echo htmlspecialchars($editFlavor['description'] ?? ''); ?></textarea>
            
            <div class="form-row">
                <div>
                    <input type="number" name="price" step="0.01" placeholder="Price (e.g., 35.00)" value="<?php echo htmlspecialchars($editFlavor['price'] ?? ''); ?>" required>
                </div>
                <div>
                    <input type="text" name="image_path" placeholder="Image File Name (e.g., strawberry.jpg)" value="<?php echo htmlspecialchars($editFlavor['image_path'] ?? ''); ?>" required>
                </div>
            </div>

            <label style="display:block; margin-bottom:10px;">
                <input type="checkbox" name="is_active" value="1" <?php echo ($editFlavor['is_active'] ?? 1) ? 'checked' : ''; ?>>
                Is Active (Show on Menu)
            </label>

            <button type="submit" name="submit_flavor" class="add-btn"><?php echo $editFlavor ? 'Update Flavor' : 'Add Flavor'; ?></button>
            <?php if ($editFlavor): ?>
                <a href="admin_flavors.php"><button type="button" class="add-btn" style="background:#aaa;">Cancel Edit</button></a>
            <?php endif; ?>
        </form>
    </div>

    <h2>Current Flavors</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Image Path</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($flavors as $flavor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($flavor['id']); ?></td>
                    <td><?php echo htmlspecialchars($flavor['name']); ?></td>
                    <td>₱<?php echo number_format($flavor['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($flavor['image_path']); ?></td>
                    <td>
                        <span class="active-dot <?php echo $flavor['is_active'] ? '' : 'inactive-dot'; ?>" 
                              title="<?php echo $flavor['is_active'] ? 'Active' : 'Inactive'; ?>"></span>
                    </td>
                    <td class="action-btns">
                        <a href="admin_flavors.php?edit_id=<?php echo $flavor['id']; ?>" class="edit-btn">Edit</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this flavor?');">
                            <input type="hidden" name="delete_id" value="<?php echo $flavor['id']; ?>">
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