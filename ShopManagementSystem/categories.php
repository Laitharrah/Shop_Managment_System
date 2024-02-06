<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db_connect.php';

// Initialize variables
$nameError = $descError = "";
$name = $description = "";

// Function to sanitize input data
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if we are editing an existing category
$editingCategoryId = isset($_GET['editCategoryId']) ? $_GET['editCategoryId'] : null;
$editingCategoryData = null;

if ($editingCategoryId) {
    $sql_edit = "SELECT name, description FROM categories WHERE id = ?";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->bind_param("i", $editingCategoryId);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    if ($row_edit = $result_edit->fetch_assoc()) {
        $editingCategoryData = $row_edit;
    }
    $stmt_edit->close();
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Validate name
    if (empty($_POST["name"])) {
        $nameError = "Category name is required";
        $isValid = false;
    } else {
        $name = test_input($_POST["name"]);
        // Check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameError = "Only letters and white space allowed";
            $isValid = false;
        }
    }

    // Validate description
    if (empty($_POST["description"])) {
        $descError = "Description is required";
        $isValid = false;
    } else {
        $description = test_input($_POST["description"]);
    }

    // Check if we are updating an existing category
    $updatingCategoryId = isset($_POST['updatingCategoryId']) ? $_POST['updatingCategoryId'] : null;

    // Determine SQL based on add or update
    if ($isValid) {
        if ($updatingCategoryId) {
            $sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $description, $updatingCategoryId);
        } else {
            $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $name, $description);
        }

        // Execute and check SQL operation
        if ($stmt->execute()) {
            echo $updatingCategoryId ? "Category updated successfully." : "Category added successfully.";
            $name = $description = ""; // Reset form values
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handling delete request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['deleteCategoryId'])) {
    $deleteCategoryId = $_GET['deleteCategoryId'];
    $sql_delete = "DELETE FROM categories WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $deleteCategoryId);
    if ($stmt_delete->execute()) {
        echo "Category deleted successfully.";
    } else {
        echo "Error: " . $stmt_delete->error;
    }
    $stmt_delete->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories Management</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Category Management</h2>
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

    <!-- Form for adding or editing a category -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="updatingCategoryId" value="<?php echo $editingCategoryId; ?>">
        <label for="name">Category Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $editingCategoryData ? $editingCategoryData['name'] : $name; ?>">
        <span class="error">* <?php echo $nameError;?></span>
        <br><br>
    
        <label for="description">Description:</label>
        <textarea id="description" name="description"><?php echo $editingCategoryData ? $editingCategoryData['description'] : $description; ?></textarea>
        <span class="error">* <?php echo $descError;?></span>
        <br><br>

        <input type="submit" value="<?php echo $editingCategoryId ? 'Update Category' : 'Add Category'; ?>">
    </form>
    <table>
    <h3>Existing Categories</h3>
    <?php
        $sql = "SELECT id, name, description FROM categories";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["description"]. "</td>";
                echo "<td><a href='categories.php?deleteCategoryId=" . $row["id"] . "'>Delete</a></td>";
                echo "<td><a href='categories.php?editCategoryId=" . $row["id"] . "'>Edit</a></td></tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No categories found</td></tr>";
        }
        ?>
    </table>
    <footer>
        <p>&copy; 2024 Caveni Digital Solutions. All Rights Reserved.</p>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
