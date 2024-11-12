<?php
$servername = "localhost";
$username = "root";
$password = "Sadhana@04";
$dbname = "itms";
$port = "3307";

$conn = new mysqli($servername, $username, $password, $dbname,$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
