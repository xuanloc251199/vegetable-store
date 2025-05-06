<?php
session_start();
include('../server/connection.php');

// Kiểm tra nếu người dùng đã xác nhận xoá
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xoá tất cả các bản ghi trong bảng orders
    $stmt = $conn->prepare('DELETE FROM orders');
    $stmt->execute();

    // Kiểm tra xem có lỗi không
    if ($stmt->affected_rows > 0) {
        // Nếu thành công, chuyển hướng về trang với thông báo thành công
        header("Location: orders.php?message=All orders have been deleted.");
    } else {
        // Nếu không có đơn hàng để xoá
        header("Location: orders.php?message=No orders found to delete.");
    }
    exit;
} else {
    // Nếu không phải POST, chuyển hướng về trang orders
    header("Location: list_orders.php");
    exit;
}
