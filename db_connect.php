<?php
$servername = "shopmanagementsystem-server.mysql.database.azure.com";
$username = "vbsbtrsrfr"; // default username for XAMPP is 'root'
$password = "OM1281MIN770IX53$"; // default password for XAMPP is empty
$dbname = "shopmanagementsystem-database";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>
