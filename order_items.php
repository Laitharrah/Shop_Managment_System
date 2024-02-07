<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db_connect.php'; // Include your database connection file
// Initialize variables
$orderIdError = $productIdError = $quantityError = "";
$orderId = $productId = $quantity = 0;

// Fetch orders and products for the dropdowns
$orderOptions = "";
$productOptions = "";
$sql_orders = "SELECT id FROM orders";
$sql_products = "SELECT id, name FROM products";
$result_orders = $conn->query($sql_orders);
$result_products = $conn->query($sql_products);

if ($result_orders->num_rows > 0) {
    while($row = $result_orders->fetch_assoc()) {
        $orderOptions .= "<option value='" . $row['id'] . "'>" . $row['id'] . "</option>";
    }
} else {
    $orderOptions = "<option value=''>No orders available</option>";
}

if ($result_products->num_rows > 0) {
    while($row = $result_products->fetch_assoc()) {
        $productOptions .= "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
    }
} else {
    $productOptions = "<option value=''>No products available</option>";
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Validate order ID
    if (empty($_POST["orderId"]) || !is_numeric($_POST["orderId"])) {
        $orderIdError = "Valid Order ID is required";
        $isValid = false;
    } else {
        $orderId = intval($_POST["orderId"]);
    }

    // Validate product ID
    if (empty($_POST["productId"]) || !is_numeric($_POST["productId"])) {
        $productIdError = "Valid Product ID is required";
        $isValid = false;
    } else {
        $productId = intval($_POST["productId"]);
    }

    // Validate quantity
    if (empty($_POST["quantity"]) || !is_numeric($_POST["quantity"]) || $_POST["quantity"] < 1) {
        $quantityError = "Valid Quantity is required";
        $isValid = false;
    } else {
        $quantity = intval($_POST["quantity"]);
    }

    if ($isValid) {
        // Fetch price for the selected product
        $sql_price = "SELECT price FROM products WHERE id = ?";
        $stmt_price = $conn->prepare($sql_price);
        $stmt_price->bind_param("i", $productId);
        $stmt_price->execute();
        $result_price = $stmt_price->get_result();
        if ($row_price = $result_price->fetch_assoc()) {
            $price = $row_price['price'];
        }
        $stmt_price->close();

        // Insert new order item
        $total_item = $price * $quantity;
        $sql_insert = "INSERT INTO order_items (orderId, productId, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiid", $orderId, $productId, $quantity, $price);
        $stmt_insert->execute();
        $stmt_insert->close();

        // Update order total
        $sql_update_total = "UPDATE orders SET total = total + ? WHERE id = ?";
        $stmt_update_total = $conn->prepare($sql_update_total);
        $stmt_update_total->bind_param("di", $total_item, $orderId);
        $stmt_update_total->execute();
        $stmt_update_total->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Items Management</title>
    <link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>
    <h2>Order Items Management</h2>
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
    <!-- Form for adding new order item -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="orderItemForm">
        <label for="orderId">Order ID:</label>
        <select id="orderId" name="orderId">
            <option value="">Select an Order</option>
            <?php echo $orderOptions; ?>
        </select>
        <span class="error">* <?php echo $orderIdError;?></span>
        <br><br>

        <label for="productId">Product:</label>
        <select id="productId" name="productId">
            <option value="">Select a Product</option>
            <?php echo $productOptions; ?>
        </select>
        <span class="error">* <?php echo $productIdError;?></span>
        <br><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $quantity; ?>" min="1">
        <span class="error">* <?php echo $quantityError;?></span>
        <br><br>

        <input type="submit" value="Add Order Item">
    </form>

    <h3>Existing Order Items</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Order ID</th>
            <th>Product ID</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
        <?php
        // PHP code to fetch and display order items from the database
        $sql = "SELECT id, orderId, productId, quantity, price FROM order_items";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id"]. "</td><td>" . $row["orderId"]. "</td><td>" . $row["productId"]. "</td><td>" . $row["quantity"]. "</td><td>" . $row["price"]. "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No order items found</td></tr>";
        }
        ?>
    </table>
    <footer>
        <p>&copy; 2024 Caveni Digital Solutions. All Rights Reserved.</p>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
