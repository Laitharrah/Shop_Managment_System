<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db_connect.php'; // Include your database connection file

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Handling AJAX request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = isset($_POST['orderId']) ? intval($_POST['orderId']) : 0;
    $productId = isset($_POST['productId']) ? intval($_POST['productId']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $price = 0;

    // Fetch price for the selected product
    $stmt_price = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt_price->bind_param("i", $productId);
    $stmt_price->execute();
    $result_price = $stmt_price->get_result();
    if ($result_price->num_rows > 0) {
        $row_price = $result_price->fetch_assoc();
        $price = $row_price['price'];
    }
    $stmt_price->close();

    // Calculate total for this item
    $total_item = $price * $quantity;

    // Add order item
    $stmt_add_item = $conn->prepare("INSERT INTO order_items (orderId, productId, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_add_item->bind_param("iiid", $orderId, $productId, $quantity, $price);
    if ($stmt_add_item->execute()) {
        // Update total in orders table
        $stmt_update_order = $conn->prepare("UPDATE orders SET total = total + ? WHERE id = ?");
        $stmt_update_order->bind_param("di", $total_item, $orderId);
        if ($stmt_update_order->execute()) {
            $response['success'] = true;
            $response['message'] = 'Order item added successfully and order total updated.';
        } else {
            $response['message'] = 'Failed to update order total.';
        }
        $stmt_update_order->close();
    } else {
        $response['message'] = 'Failed to add order item.';
    }
    $stmt_add_item->close();
}

header('Content-Type: application/json');
echo json_encode($response);
