<?php
session_start();
include 'Mysql.php';

header('Content-Type: application/json');

// Total Books
$booksQuery = $conn->query("SELECT COUNT(*) as total_books FROM books");
$totalBooks = $booksQuery->fetch_assoc()['total_books'] ?? 0;

// Total Sales
$salesQuery = $conn->query("SELECT COUNT(*) as total_sales FROM sales");
$totalSales = $salesQuery->fetch_assoc()['total_sales'] ?? 0;

// Total Revenue
$revenueQuery = $conn->query("SELECT SUM(total_amount) as total_revenue FROM sales");
$totalRevenue = $revenueQuery->fetch_assoc()['total_revenue'] ?? 0;

// Total Staff (users table)
$staffQuery = $conn->query("SELECT COUNT(*) as total_staff FROM users");
$totalStaff = $staffQuery->fetch_assoc()['total_staff'] ?? 0;

echo json_encode([
    "total_books" => (int)$totalBooks,
    "total_sales" => (int)$totalSales,
    "total_revenue" => (float)$totalRevenue,
    "total_staff" => (int)$totalStaff
]);

$conn->close();
?>