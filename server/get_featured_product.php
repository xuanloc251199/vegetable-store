<?php
include('connection.php');

// $stmt = $conn->prepare("SELECT * FROM products LIMIT 4");

// $stmt->execute();

// $featured_product = $stmt->get_result();

// Truy vấn các sản phẩm có status_product_name là 'New Product'
$stmt = $conn->prepare("
    SELECT products.* 
    FROM products
    INNER JOIN status_products 
    ON products.status_products_id = status_products.status_products_id
    WHERE status_products.status_products_name = 'Mới'
    LIMIT 4
");

$stmt->execute();
$featured_product = $stmt->get_result();
   
      ?>
