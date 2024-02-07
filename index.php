<?php
session_start();

// Check if the user is logged in
$loggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Welcome to Our Shop</title>
</head>
<body>

    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="customer_reviews.php">Customer Reviews</a>
            <a href="order_items.php">Order Items</a>
            <a href="orders.php">Orders</a>
            <a href="products.php">Products</a>
            <?php if ($loggedIn): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <!-- Image Placement -->
    <div class="image-container">
        <img src="images/shop.jpg.webp" alt="Descriptive Alt Text">
    </div>
    <footer>
        <p>&copy; 2024 Caveni Digital Solutions. All Rights Reserved.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
