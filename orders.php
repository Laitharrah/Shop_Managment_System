<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db_connect.php';

$userId = $_SESSION['user_id'];
$total = 0;

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderItems = isset($_POST['orderItems']) ? $_POST['orderItems'] : []; // Assuming orderItems is an array of product IDs and quantities

    // Calculate total based on order items
    foreach ($orderItems as $item) {
        $productId = $item['productId'];
        $quantity = $item['quantity'];

        // Fetch price for each product
        $sql_price = "SELECT price FROM products WHERE id = ?";
        $stmt_price = $conn->prepare($sql_price);
        $stmt_price->bind_param("i", $productId);
        $stmt_price->execute();
        $result_price = $stmt_price->get_result();
        if ($row_price = $result_price->fetch_assoc()) {
            $total += $row_price['price'] * $quantity;
        }
        $stmt_price->close();
    }

    $orderDate = date('Y-m-d H:i:s');
    $sql = "INSERT INTO orders (orderDate, total, userId) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdi", $orderDate, $total, $userId);
    if ($stmt->execute()) {
        echo "Order created successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
}

// Close database connection
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders Management</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h2>Orders Management</h2>
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
    <!-- Form for creating new order -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <!-- Add fields for order items here -->
        <!-- ... -->

        <input type="submit" value="Create Order">
    </form>

    <h3>Existing Orders</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Order Date</th>
            <th>Total</th>
            <th>User ID</th>
        </tr>
        <?php
        // PHP code to fetch and display orders from the database
        $sql = "SELECT id, orderDate, total, userId FROM orders";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id"]. "</td><td>" . $row["orderDate"]. "</td><td>" . $row["total"]. "</td><td>" . $row["userId"]. "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No orders found</td></tr>";
        }
        ?>
    </table>
    <footer>
        <p>&copy; 2024 Caveni Digital Solutions. All Rights Reserved.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
