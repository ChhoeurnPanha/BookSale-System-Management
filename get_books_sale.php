<?php
include 'Mysql.php';

$sql = "SELECT book_id, title, price FROM books WHERE is_deleted = 0";
$result = $conn->query($sql);

$books = [];

while($row = $result->fetch_assoc()){
    $books[] = $row;
}

echo json_encode($books);
?>