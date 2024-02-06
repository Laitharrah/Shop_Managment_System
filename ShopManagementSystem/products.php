<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db_connect.php';

$categoryId = $description = $name = "";
$price = 0.0;
$productId = null;
$successMsg = $errorMsg = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryId = $_POST['categoryId'];
    $description = test_input($_POST['description']);
    $name = test_input($_POST['name']);
    $price = $_POST['price'];
    $productId = $_POST['productId'];

    if ($productId) {
        // Update existing product
        $sql = "UPDATE products SET categoryId = ?, description = ?, name = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdi", $categoryId, $description, $name, $price, $productId);
    } else {
        // Insert new product
        $sql = "INSERT INTO products (categoryId, description, name, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issd", $categoryId, $description, $name, $price);
    }

    if ($stmt->execute()) {
        $successMsg = $productId ? "Product updated successfully." : "Product added successfully.";
        $categoryId = $description = $name = "";
        $price = 0.0;
        $productId = null;
    } else {
        $errorMsg = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch products for display
$sql = "SELECT products.id, products.categoryId, categories.name AS categoryName, products.description, products.name, products.price 
        FROM products 
        JOIN categories ON products.categoryId = categories.id";
$result = $conn->query($sql);

// Handle Delete Request
if (isset($_GET['deleteProductId'])) {
    $deleteProductId = $_GET['deleteProductId'];
    $sql_delete = "DELETE FROM products WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $deleteProductId);
    if ($stmt_delete->execute()) {
        $successMsg = "Product deleted successfully.";
    } else {
        $errorMsg = "Error deleting product: " . $stmt_delete->error;
    }
    $stmt_delete->close();

    // Refresh the page to reflect the deletion
    header("Location: products.php");
    exit;
}

// Handle Edit Request
if (isset($_GET['editProductId'])) {
    $productId = $_GET['editProductId'];
    $sql_edit = "SELECT categoryId, description, name, price FROM products WHERE id = ?";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->bind_param("i", $productId);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    if ($row_edit = $result_edit->fetch_assoc()) {
        $categoryId = $row_edit['categoryId'];
        $description = $row_edit['description'];
        $name = $row_edit['name'];
        $price = $row_edit['price'];
    }
    $stmt_edit->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Products Management</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Products Management</h2>
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

    <!-- Form for adding new product -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="categoryId">Category ID:</label>
        <input type="number" id="categoryId" name="categoryId" value="<?php echo $categoryId; ?>">
        <br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"><?php echo $description; ?></textarea>
        <br><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">
        <br><br>

        <label for="price">Price:</label>
        <input type="text" id="price" name="price" value="<?php echo $price; ?>">
        <br><br>

        <input type="submit" value="Add Product">
    </form>

    <!-- Display Success/Error Messages -->
    <?php if ($successMsg) echo "<p>$successMsg</p>"; ?>
    <?php if ($errorMsg) echo "<p>$errorMsg</p>"; ?>

    <!-- Table for displaying products -->
    <h3>Existing Products</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Category ID</th>
            <th>Category Name</th>
            <th>Description</th>
            <th>Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["categoryId"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["categoryName"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["description"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["price"]) . "</td>";
                echo "<td><a href='products.php?editProductId=" . $row["id"] . "'>Edit</a> | ";
                echo "<a href='products.php?deleteProductId=" . $row["id"] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No products found</td></tr>";
        }
        ?>
    </table>


    <footer>
        <!-- Footer content -->
    </footer>
    <script src="js/script.js"></script>
</body>
</html>