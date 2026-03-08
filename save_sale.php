<?php
session_start();
include 'Mysql.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id    = $_SESSION['user_id'];
$staff_name = trim($_POST['staff_name']);
$sale_date  = $_POST['sale_date'];
$discount   = floatval($_POST['discount'] ?? 0);
$grand_total = floatval($_POST['grand_total'] ?? 0);
$items      = json_decode($_POST['items'] ?? '[]', true);

if (empty($items)) {
    echo json_encode(["status" => "error", "message" => "Cart is empty"]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO sales 
(title, price, qty, discount, total_amount, sale_date, user_id, staff_name)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($items as $item) {
    $title = $item['title'];
    $price = floatval($item['price']);
    $qty   = intval($item['qty']);
    $total_amount = ($price * $qty) - $discount;

    $stmt->bind_param(
        "sdiidiss",
        $title,
        $price,
        $qty,
        $discount,
        $total_amount,
        $sale_date,
        $user_id,
        $staff_name
    );

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
        exit();
    }

    // Optional: update stock
    $updateStock = $conn->prepare("UPDATE books SET qty = qty - ? WHERE title = ?");
    $updateStock->bind_param("is", $qty, $title);
    $updateStock->execute();
    $updateStock->close();
}

$stmt->close();
$conn->close();

echo json_encode(["status" => "success"]);