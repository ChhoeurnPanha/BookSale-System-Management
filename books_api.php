<?php
include 'Mysql.php';

// Helper: escape input
function clean($conn, $val) {
    return mysqli_real_escape_string($conn, trim($val));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'];

    $title    = $_POST['title'] ?? '';
    $author   = $_POST['author'] ?? '';
    $isbn   = $_POST['isbn'] ?? '';
    $category = $_POST['category'] ?? '';
    $price    = $_POST['price'] ?? 0;
    $qty      = $_POST['qty'] ?? 0;
    $date     = $_POST['date'] ?? '';
     // ───────── ADD BOOK ─────────
     if ($action === "add") {

        $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, category, price, qty, date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdis", $title, $author, $isbn, $category, $price, $qty, $date);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $stmt->close();
        exit;
    }

     // ───────── EDIT BOOK ─────────
     if ($action === "edit") {

        $book_id = $_POST['book_id'];

        $stmt = $conn->prepare("UPDATE books SET title=?, author=?, isbn=?, category=?, price=?, qty=?, date=? WHERE book_id=?");
        $stmt->bind_param("ssssdisi", $title, $author, $isbn, $category, $price, $qty, $date, $book_id);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $stmt->close();
        exit;
    }

     // ───────── DELETE BOOK ─────────
     if ($action === "delete") {

        $book_id = $_POST['book_id'];

        $stmt = $conn->prepare("DELETE FROM books WHERE book_id=?");
        $stmt->bind_param("i", $book_id);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $stmt->close();
        exit;
    }
}

// If GET request, return all books
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = mysqli_query($conn, "SELECT * FROM books ORDER BY book_id DESC");
    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Optional: format date
        $row['date'] = date('d/m/Y', strtotime($row['date']));
        $books[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($books);
    exit;
}


?>