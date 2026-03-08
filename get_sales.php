<?php
session_start();
include 'Mysql.php';
header('Content-Type: application/json');

// Check user login (optional)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$sql = "SELECT sale_id, title, price, qty, discount, total_amount, user_id, staff_name FROM sales ORDER BY sale_id DESC";
$result = $conn->query($sql);

$sales = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
}

echo json_encode(["status"=>"success", "data"=>$sales]);
$conn->close();