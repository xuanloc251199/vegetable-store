<?php
include('../server/connection.php');

// Set the number of products per page to 12
$limit = 12;
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page or set default to 1
$offset = ($page - 1) * $limit;

// Truy vấn để lấy số thứ tự cho tất cả các sản phẩm, nhưng chỉ lấy 12 sản phẩm cho mỗi trang

// DESC truy vấn sản phẩm từ mới đến cũ
// ASC truy vấn sản phẩm từ cũ đến mới
$sql = "

    SELECT 
    ROW_NUMBER() OVER (ORDER BY products.product_id DESC) AS stt,
    products.product_id,
    products.product_name,
    category.category_name,
    products.product_price,
    products.product_price_discount,
    -- Trực tiếp lấy trạng thái của sản phẩm từ bảng status_products
    status_products.status_products_name,  
    products.product_image,
    products.quantity AS quantity,
    COALESCE(SUM(order_items.product_quantity), 0) AS total_sold_quantity,  -- Tổng số lượng đã bán
    (products.quantity - COALESCE(SUM(order_items.product_quantity), 0)) AS new_product  -- Tính số lượng còn lại
FROM products
LEFT JOIN category ON products.category_id = category.category_id
LEFT JOIN status_products ON products.status_products_id = status_products.status_products_id
LEFT JOIN order_items ON products.product_id = order_items.product_id
GROUP BY products.product_id, category.category_name, status_products.status_products_name, products.product_image, products.quantity
ORDER BY products.product_id DESC  -- Sắp xếp sản phẩm theo ID giảm dần (mới nhất)
LIMIT ?, ?

";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of products for pagination
$total_results = $conn->query('SELECT COUNT(*) as count FROM products')->fetch_assoc()['count'];
$total_pages = ceil($total_results / $limit);

// Kiểm tra và cập nhật trạng thái của sản phẩm nếu số lượng = 0
$checkZeroQuantitySql = "UPDATE products SET status_products_id = 6 WHERE quantity = 0";
$conn->query($checkZeroQuantitySql); // Cập nhật trạng thái sản phẩm hết hàng (status_products_id = 6)

?>

<?php include('../admin/layouts/app.php'); ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Products</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="create_products.php" class="btn btn-primary">New Product</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <form class="d-flex" action="../admin/search_product.php" method="GET">
                            <input class="form-control me-2" type="search" name="query_admin" placeholder="Search Products" aria-label="Search" required>
                            <button class="btn btn-outline-dark" type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_GET['message']; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_GET['error']; ?>
                    </div>
                    <?php endif; ?>

                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th width="80">Product Image</th>
                                <th>Product Name</th>
                                <th>Product Category</th>
                                <th>Price</th>
                               
                                <th>Status</th>    
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stt = $total_results - ($page - 1) * $limit; 
                            if ($result->num_rows > 0): 
                                while ($row = $result->fetch_assoc()): 
                                    // Gán badge class và icon dựa trên trạng thái sản phẩm
                                    $status = $row['status_products_name'];
                                    $badgeClass = 'badge-dark'; // Mặc định là màu xám
                                    $statusIcon = ''; // Mặc định không có biểu tượng

                                    if ($status === 'Mới') {
                                        $badgeClass = 'badge-danger'; // Màu đỏ cam
                                        $statusIcon = '<i class="fas fa-bolt"></i>'; // Biểu tượng tia sét
                                    } elseif ($status === 'Còn hàng') {
                                        $badgeClass = 'badge-success'; // Màu xanh lá
                                        $statusIcon = '<i class="fas fa-check-circle"></i>'; // Biểu tượng check
                                    } elseif ($status === 'Đặt hàng trước') {
                                        $badgeClass = 'badge-info'; // Màu xanh dương nhạt
                                        $statusIcon = '<i class="fas fa-clock"></i>'; // Biểu tượng đồng hồ
                                    } elseif ($status === 'Hết hàng') {
                                        $badgeClass = 'badge-secondary'; // Màu xám
                                        $statusIcon = '<i class="fas fa-times-circle"></i>'; // Biểu tượng dấu X
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $stt--; ?></td> <!-- Số thứ tự -->
                                        <td><img class="img-fluid mb-3"
                                                 src="../assets/images/<?php echo $row['product_image']; ?>" alt="Product Image"></td>
                                        <td><?php echo $row['product_name']; ?> </br></br>
                                        Quantity: <?php echo $row['quantity']; ?>
                                    </td>
                                        <td><?php echo $row['category_name']; ?></td>
                                        <td><?php echo number_format($row['product_price_discount'], 0, '.', '.'); ?> VND
                                        <br><br>
                                        <?php 
                                        if ($row['product_price_discount'] != 0) {
                                            echo '<style>
                                                    .discount-price {
                                                        font-style: italic; /* In nghiêng */
                                                        text-decoration: line-through; /* Gạch chữ */
                                                        color: black; /* Màu chữ, bạn có thể thay đổi */
                                                    }
                                                </style>';
                                            echo '<span class="discount-price">' . number_format($row['product_price'], 0, '.', '.') . ' VND</span>';
                                        } else {
                                            echo ''; 
                                        }
                                    ?>

                                        </td>

                                    
                                        <td>
                                            <!-- Hiển thị trạng thái -->
                                            <span class="badge <?php echo $badgeClass; ?> p-2 text-uppercase">
                                                <?php echo $statusIcon; ?> <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <a href="edit_products.php?product_id=<?php echo $row['product_id'] ?>">
                                                <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a href="delete_product.php?product_id=<?php echo $row['product_id'] ?>"
                                               class="text-danger w-4 h-4 mr-1">
                                                <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8">No products found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Pagination -->
    <div class="card-footer clearfix">
        <ul class="pagination pagination m-0 float-right">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">«</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">»</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>
