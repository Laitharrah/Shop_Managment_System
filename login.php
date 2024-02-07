<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);session_start();
include 'db_connect.php';

$loginError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // Assuming the password is stored in plain text (not recommended)

    // Validate login credentials
    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if ($password === $row['password']) { // Direct comparison (not secure)
                    // Password is correct, start the session
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    header("Location:index.php");
                    exit;
                } else {
                    $loginError = "Invalid email or password.";
                }
            } else {
                $loginError = "Invalid email or password.";
            }
        } else {
            $loginError = "Execution failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $loginError = "Preparation failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h2>Login</h2>
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
    <div class="custom-background">
        <!-- Content over your background image -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email">
        <br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
        <br><br>
        <input type="submit" value="Login">
        <span class="error"><?php echo $loginError;?></span>
    </form>
    </div>
    <footer>
        <p>&copy; 2024 Caveni Digital Solutions. All Rights Reserved.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
