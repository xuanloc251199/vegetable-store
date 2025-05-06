<?php
include('../server/connection.php');

if (isset($_GET['query_admin'])) {
    $query = trim($_GET['query_admin']);
    $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); // Sanitize input

    if (!empty($query)) {
        // Prepare the SQL query to search across product_name, category_name, and status_products_name
        $stmt = $conn->prepare('
            SELECT products.*, category.category_name, status_products.status_products_name
            FROM products
            INNER JOIN category ON products.category_id = category.category_id
            INNER JOIN status_products ON products.status_products_id = status_products.status_products_id
            WHERE 
                products.product_name LIKE ? OR
                category.category_name LIKE ? OR
                status_products.status_products_name LIKE ?
        ');

        // Wildcards for partial match
        $searchTerm = "%" . $query . "%"; 

        // Bind parameters for product_name, category_name, and status_products_name
        $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $products = $stmt->get_result();
    } else {
        // If no search query, show TẤT CẢ SẢN PHẨM
        $stmt = $conn->prepare('
            SELECT products.*, category.category_name, status_products.status_products_name
            FROM products
            INNER JOIN category ON products.category_id = category.category_id
            INNER JOIN status_products ON products.status_products_id = status_products.status_products_id
        ');
        $stmt->execute();
        $products = $stmt->get_result();
    }
} else {
    header("Location: index.php");
    exit();
}



?>

<?php include('../admin/layouts/app.php'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_products.php" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th width="80">Product Image</th>
                                <th>Product Name</th>
                                <th>Product Category</th>
                                <th>Price</th>
                                <th width="100">Status</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products->num_rows > 0) { 
                                while ($row = $products->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['product_id']; ?></td>
                                        <td><img class="img-fluid mb-3"
                                                 src="../assets/images/<?php echo $row['product_image']; ?>"></td>
                                        <td><?php echo $row['product_name']; ?></td>
                                        <td><?php echo $row['category_name']; ?></td>
                                        <td><?php echo number_format($row['product_price'], 0, '.', '.') . ' VND'; ?></td>
                                      
                                        <td>
                                        <?php 
                                        $status = $row['status_products_name']; // Lấy trạng thái từ $row
                                        $badgeClass = 'badge-dark'; // Mặc định là màu xám
                                        $statusIcon = ''; // Mặc định không có biểu tượng

                                        // Gán màu và biểu tượng tùy theo trạng thái
                                        if ($status === 'New Product') {
                                            $badgeClass = 'badge-danger'; // Màu đỏ cam
                                            $statusIcon = '<i class="fas fa-bolt"></i>'; // Biểu tượng tia sét
                                        
                                        } elseif ($status === 'In Stock') {
                                            $badgeClass = 'badge-success'; // Màu xanh lá
                                            $statusIcon = '<i class="fas fa-check-circle"></i>'; // Biểu tượng check
                                        } elseif ($status === 'Pre Order') {
                                            $badgeClass = 'badge-info'; // Màu xanh dương nhạt
                                            $statusIcon = '<i class="fas fa-clock"></i>'; // Biểu tượng đồng hồ
                                        }elseif ($status === 'Sold Out') {
                                            $badgeClass = 'badge-secondary '; // Màu cam nhạt
                                            $statusIcon = '<i class="fas fa-times-circle"></i>'; // Biểu tượng dấu X
                                        }
                                        ?>
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
                                                <svg wire:loading.remove.delay="" wire:target=""
                                                     class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center">No results found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>
