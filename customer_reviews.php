<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db_connect.php'; // Include your database connection file

// Fetching reviews and corresponding product names from the database
$sql = "SELECT customer_reviews.rating, customer_reviews.review, customer_reviews.reviewDate, products.name AS productName 
        FROM customer_reviews
        JOIN products ON customer_reviews.productId = products.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Reviews Management</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h2>Customer Reviews</h2>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="customer_reviews.php">Customer Reviews</a>
            <a href="order_items.php">Order Items</a>
            <a href="orders.php">Orders</a>
            <a href="products.php">Products</a>
        </nav>
    </header>

    <h3>Existing Reviews</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Review Date</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>". htmlspecialchars($row["productName"]) . "</td><td>" . 
                     htmlspecialchars($row["rating"]) . "</td><td>" . 
                     htmlspecialchars($row["review"]) . "</td><td>" . 
                     htmlspecialchars($row["reviewDate"]) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No reviews found</td></tr>";
        }
        ?>
    </table>

    <footer>
        <p>&copy; 2024 Caveni Digital Solutions. All Rights Reserved.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
