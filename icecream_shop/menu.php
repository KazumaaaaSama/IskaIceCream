<?php
// menu.php
require_once 'db_connect.php';

// Get data from table
$sql = "SELECT * FROM flavors ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$flavors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | Ice Cream ni Iska</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <div class="header-brand-name">
            <h4>Ice Cream ni Iska</h4>
        </div>
        <nav>
            <a href="index.html">Home</a>
            <a href="menu.php" class="active">Menu</a>
            <a href="cart.php">Order</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Our Flavors</h1>
            <!-- Using uploaded logo image path -->
            <img src="images/logo.jpg" alt="Ice Cream ni Iska Logo" class="big-logo">
        </div>
    </section>

    <main class="flavor-grid">
        
        <?php if(count($flavors) > 0): ?>
            
            <?php foreach($flavors as $row): ?>
                <?php 
                    // Check if item is active (1 = Active, 0 = Out of Stock)
                    $isActive = $row['is_active'] == 1;
                ?>

                <div class="flavor-card <?php echo $isActive ? '' : 'out-of-stock'; ?>">
                    
                    <img src="images/<?php echo htmlspecialchars($row['image_path']); ?>" 
                         class="flavor-img" 
                         alt="<?php echo htmlspecialchars($row['name']); ?>">

                    <?php if(!$isActive): ?>
                        <div class="oos-badge">OUT OF STOCK</div>
                    <?php endif; ?>

                    <div class="card-content">
                        <h3 class="flavor-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="flavor-description"><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div class="card-footer">
                            <span class="price">₱<?php echo number_format($row['price'], 2); ?></span>
                            
                            <?php if($isActive): ?>
                                <!-- Option B: only Customize button -->
                                <form action="customize.php" method="POST" style="margin:0;">
                                    <input type="hidden" name="flavor_id" value="<?php echo $row['id']; ?>">
                                    <button class="add-btn" type="submit">Customize & Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <button class="add-btn" disabled>Unavailable</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        <?php else: ?>
            <p style="text-align:center; width:100%;">No flavors found! Please add some in the database.</p>
        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Ice Cream ni Iska</p>
    </footer>

</body>
</html>
