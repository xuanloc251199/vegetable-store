<?php
include('../server/connection.php');
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $stmt = $conn->prepare('DELETE FROM category WHERE category_id = ?');
    $stmt->bind_param('i', $category_id);
    if ($stmt->execute()) {
        header("location:list_categories.php?message=Category deleted successfully");
    } else {
        header("location:list_categories.php?error=Error deleting category");
    }
}
