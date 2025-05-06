<?php
include('server/connection.php');

// Lấy từ khóa tìm kiếm từ URL (nếu có)
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Thiết lập số lượng sản phẩm hiển thị trên mỗi trang
$products_per_page = 8;

// Kiểm tra trang hiện tại, mặc định là trang 1 nếu không có trang nào được chọn
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $products_per_page;

// Truy vấn lấy tổng số sản phẩm tìm kiếm (để phân trang)
$total_stmt = $conn->prepare("
    SELECT COUNT(*) as total_products
    FROM products
    LEFT JOIN status_products
    ON products.status_products_id = status_products.status_products_id
    WHERE products.product_name LIKE ?
");
$search_query = "%" . $query . "%";
$total_stmt->bind_param('s', $search_query);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total_products']; // Tổng số sản phẩm tìm được

// Tính tổng số trang
$total_pages = ceil($total_products / $products_per_page);

// Truy vấn lấy sản phẩm theo từ khóa và phân trang
$stmt = $conn->prepare("
    SELECT 
        products.product_id, 
        products.product_name, 
        products.product_price, 
         products.product_price_discount, 
        products.product_image, 
        products.product_image2, 
        COALESCE(status_products.status_products_name, 'Unknown') AS status_products_name
    FROM products
    LEFT JOIN status_products 
    ON products.status_products_id = status_products.status_products_id
    WHERE products.product_name LIKE ?
    LIMIT ? OFFSET ?
");
$stmt->bind_param('sii', $search_query, $products_per_page, $start_from);
$stmt->execute();
$products = $stmt->get_result();
?>


<?php include('layouts/header.php') ?>

<!-- Featured Section (8 Columns) -->
<div class="container">
    <div class="row">

        <!-- Search Section (Filter) -->
        <div class="col-lg-3 col-md-4 col-sm-12">
            <section id="search" class="my-5 py-5 ms-2">
                <div class="container mt-5 py-5">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                            <li class="breadcrumb-item active" aria-current="page">SEARCH</li>
                        </ol>
                    </nav>
                    <p class="text-uppercase fs-3">Search Product</p>
                    <hr class="mx-auto">
                </div>
                <form action="#" method="POST">
                    <div class="row mx-auto container">
                        <div class="row">
                            <!-- Price Section -->
                            <div class="col-lg-12">
                                <p class="text-uppercase fw-bold">Price Range</p>
                                <input type="range" name="price" value="5000" class="form-range w-100" min="1" max="10000000" id="priceRange" oninput="updatePriceLabel(this.value)">
                                <div class="w-100">
                                    <span style="float: left;">1</span>
                                    <span style="float: right;">10.000.000 VND</span>
                                </div>
                                <!-- Display the selected price -->
                                <p class="m-4 pt-4 text-uppercase fw-bold">Price: <span id="selectedPrice">5000</span> VND</p>

                                <!-- Hidden input fields to store the min and max price (for backend usage) -->
                                <input type="hidden" name="min_price" id="minPrice" value="1">


                                <input type="hidden" name="max_price" id="maxPrice" value="10000000">
                            </div>
                        </div>
                    </div>

                    <div class="form-group m-4">
                        <hr class="mx-auto">
                        <input type="submit" name="search" value="Search" class="btn btn-primary">
                    </div>
                </form>
            </section>
        </div>

        <!-- Products Section -->
        <div class="col-lg-9 col-md-8 col-sm-12">
            <section id="products" class="my-5 py-5">
                <div class="container text-center mt-5 py-5">
                    <h3 class="text-uppercase fs-3">SEARCH : <strong><?php echo htmlspecialchars($query); ?></strong></h3>
                    <!-- Hiển thị số lượng kết quả tìm kiếm -->
                    <p class="text-muted">Found <?php echo $total_products; ?> results for your search</p>
                    <hr class="mx-auto">
                </div>

                <?php
                // Kiểm tra nếu có kết quả tìm kiếm

                if ($products->num_rows > 0): ?>
                    <div class="row">
                        <!-- Products Section -->
                        <?php while ($row = $products->fetch_assoc()) {
                            // Kiểm tra trạng thái sản phẩm, nếu sản phẩm đã "Sold Out", "Pre Order"
                            if ($row['status_products_name'] == 'Sold Out') {
                                // Nếu sản phẩm đã Sold Out, chuyển hướng đến trang sold_out.php khi người dùng click vào
                                $link = "sold_out.php?product_id=" . $row['product_id'];
                            } elseif ($row['status_products_name'] == 'Pre Order') {
                                // Nếu sản phẩm là "Pre Order", chuyển hướng đến trang pre_order.php
                                $link = "pre_order.php?product_id=" . $row['product_id'];
                            } else {
                                // Nếu sản phẩm còn hàng, chuyển hướng đến trang single_product.php
                                $link = "single_product.php?product_id=" . $row['product_id'];
                            }

                        ?>
                            <div class="product text-center col-lg-3 col-md-6 col-sm-12">
                                <a href="<?php echo $link; ?>" class="product-link">


                                    <div class="product-status <?php echo strtolower(str_replace(' ', '-', $row['status_products_name'])); ?>">
                                        <?php echo $row['status_products_name']; ?>
                                    </div>
                                    <div class="img-container">
                                        <img class="img-fluid mb-3" src="./assets/images/<?php echo $row['product_image'] ?>" alt="Product Image">
                                        <img class="img-fluid img-second" src="./assets/images/<?php echo $row['product_image2']; ?>" alt="Second Image">
                                    </div>
                                    <div class="star">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h3 class="p-product"><?php echo $row['product_name'] ?></h3>
                                    <p class="p-price"><?php echo number_format($row['product_price'], 0, ',', '.') ?> VND</p>
                                    <p class="p-price-discount">
                                        <?php
                                        if ($row['product_price_discount'] != 0) {
                                            // Định dạng giá với dấu chấm cách 3 chữ số và thêm "VND"
                                            echo number_format($row['product_price_discount'], 0, '.', '.') . ' VND';
                                        } else {
                                            echo ''; // Hiển thị khoảng trống nếu giá giảm bằng 0
                                        }
                                        ?>
                                    </p>

                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <p class="alert alert-danger">No Search Results For The Keyword: <strong>
                                <?php echo htmlspecialchars($query); ?></strong>.</p>
                    </div>
                <?php endif; ?>


                <!-- Pagination Section -->
                <nav aria-label="Page navigation example">
                    <ul class="container text-center pagination mt-5">
                        <?php if ($page > 1) : ?>
                            <li class="page-item"><a href="TOPS.php?page=<?php echo $page - 1; ?>"
                                    class="page-link">
                                    << </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a
                                    href="TOPS.php?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages) : ?>
                            <li class="page-item"><a href="TOPS.php?page=<?php echo $page + 1; ?>"
                                    class="page-link"> >> </a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </section>
        </div>
    </div>
</div>


<?php include('layouts/footer.php') ?>

<script>
    function updatePriceLabel(value) {
        document.getElementById('selectedPrice').textContent = value;
        document.getElementById('maxPrice').value = value;
    }
</script>

<!-- JavaScript -->
<script>
    // Lắng nghe sự thay đổi trên các radio button
    document.querySelectorAll('input[name="category"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            // Khi một radio button được chọn, chuyển hướng tới trang tương ứng
            var category = this.value.toLowerCase(); // Lấy giá trị của category (TOPS, BOTTOMS, BAGS)
            if (category) {
                window.location.href = category + '.php'; // Chuyển hướng đến trang tương ứng
            }
        });
    });
</script>