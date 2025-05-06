<?php
include('connection.php');

$stmt = $conn->prepare("SELECT * FROM products LIMIT 8 OFFSET 4 ");


$stmt->execute();

$clothes = $stmt->get_result();
