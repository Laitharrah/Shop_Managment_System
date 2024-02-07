<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db_connect.php'; // Include your database connection file
if (isset($_GET['productId'])) {
    $productId = $_GET['productId'];
    $sql = "SELECT price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo $row['price'];
    } else {
        echo "0";
    }
    $stmt->close();
}
