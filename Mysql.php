<?php
$conn = new mysqli("localhost", "root", "", "BookSdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
