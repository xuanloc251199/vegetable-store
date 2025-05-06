<?php

include('../server/connection.php');

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $stmt = $conn->prepare('DELETE FROM users WHERE user_id=?');
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        header('location:list_users.php?message=User deleted successfully');
    } else {
        header('location:list_users.php?error=Error deleting user');
    }
}
