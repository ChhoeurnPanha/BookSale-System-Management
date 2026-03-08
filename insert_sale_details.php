<?php
session_start();
include 'Mysql.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$sale_id = intval($input['sale_id']);
$items   = $input['items'];

if (!$items || !is_array($items)) {
    echo json_encode(["status"=>"error","message"=>"No items"]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO sale_details (sale_id, book_id, price, qty, total_amount) VALUES (?,?,?,?,?)");
if(!$stmt){
    echo json_encode(["status"=>"error","message"=>$conn->error]);
    exit();
}

$conn->begin_transaction();

try {
    foreach($items as $item){
        $book_id = intval($item['book_id']);
        $price   = floatval($item['price']);
        $qty     = intval($item['qty']);
        $total   = $price * $qty;

        if(!$stmt->bind_param("iidid", $sale_id, $book_id, $price, $qty, $total)){
            throw new Exception($stmt->error);
        }
        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }
    }
    $conn->commit();
    echo json_encode(["status"=>"success"]);
} catch(Exception $e){
    $conn->rollback();
    echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
}

$stmt->close();
$conn->close();
?>