<?php
include('../server/connection.php');
if (isset($_GET['status_products_id'])) {
    $status_products_id = $_GET['status_products_id'];
    $stmt = $conn->prepare('DELETE FROM status_products WHERE status_products_id = ?');
    $stmt->bind_param('i', $status_products_id);
    if ($stmt->execute()) {
        header("location:list_status_products.php?message=Status Products deleted successfully");
    } else {
        header("location:list_status_products.php?error=Error deleting status products");
    }
}
