<?php

include('../server/connection.php');

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn->prepare('DELETE FROM products WHERE product_id = ?');
    $stmt->bind_param('i', $product_id);
    if ($stmt->execute()) {
        header('location:list_products.php?message=Product deleted successfully');
    } else {
        header('location:list_products.php?error=Error deleting product');
    }
}
