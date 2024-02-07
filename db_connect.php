
<?php
$servername = "localhost";
$username = "root"; // default username for XAMPP is 'root'
$password = ""; // default password for XAMPP is empty
$dbname = "shop_management_system";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
