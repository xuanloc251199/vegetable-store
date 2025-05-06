<?php
session_start();
include('layouts/header.php');
include('server/connection.php');

// --- Slider Query ---
$slider_stmt = $conn->prepare("SELECT * FROM slider ORDER BY slider_id ASC");
$slider_stmt->execute();
$sliders = $slider_stmt->get_result();

// --- Featured Product ---
include('server/get_featured_product.php');

// --- Pagination Setup ---
$products_per_page = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $products_per_page;

// --- Search Filter ---
if (isset($_POST['search'])) {
    $category = $_POST['category'];
    $min_price = (int)$_POST['min_price'];
    $max_price = (int)$_POST['max_price'];

    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM products JOIN category ON products.category_id = category.category_id WHERE category.category_name = ? AND product_price BETWEEN ? AND ?");
    $count_stmt->bind_param('sii', $category, $min_price, $max_price);
    $count_stmt->execute();
    $total_products = $count_stmt->get_result()->fetch_assoc()['total'];

    $product_stmt = $conn->prepare("SELECT products.* FROM products JOIN category ON products.category_id = category.category_id WHERE category.category_name = ? AND product_price BETWEEN ? AND ? LIMIT ? OFFSET ?");
    $product_stmt->bind_param('siiii', $category, $min_price, $max_price, $products_per_page, $start_from);
} else {
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM products");
    $count_stmt->execute();
    $total_products = $count_stmt->get_result()->fetch_assoc()['total'];

    $product_stmt = $conn->prepare("
        SELECT p.*, COALESCE(sp.status_products_name, 'Unknown') AS status_products_name 
        FROM products p 
        LEFT JOIN status_products sp ON p.status_products_id = sp.status_products_id 
        ORDER BY p.product_id DESC 
        LIMIT ? OFFSET ?");
    $product_stmt->bind_param('ii', $products_per_page, $start_from);
}

$product_stmt->execute();
$products = $product_stmt->get_result();
$total_pages = ceil($total_products / $products_per_page);
?>

<!-- Home Slider -->
<section id="home">
    <div class="home-slider">
        <?php while ($slider = $sliders->fetch_assoc()) : ?>
            <img src="./assets/images/slides/<?= htmlspecialchars($slider['slider_image']) ?>"
                 alt="<?= htmlspecialchars($slider['slider_name']) ?>"
                 class="banner-img">
        <?php endwhile; ?>
    </div>
</section>

<!-- Featured Products -->
<section id="featured" class="my-5 py-5">
    <div class="container text-center mt-5 py-5">
        <h3 class="text-uppercase fs-1">Sản phẩm mới</h3>
        <hr>
        <p class="fs-4">Ở đây bạn có thể xem sản phẩm của chúng tôi</p>
    </div>

    <div class="container">
        <div class="row">
            <?php while ($row = $featured_product->fetch_assoc()) : ?>
                <div class="product text-center me-5 col-lg-3 col-md-6 col-sm-12">
                    <a href="single_product.php?product_id=<?= $row['product_id'] ?>" class="product-link">
                        <div class="img-container">
                            <div class="product-status new-product">New Product</div>
                            <img class="img-fluid mb-3" src="./assets/images/<?= htmlspecialchars($row['product_image']) ?>">
                            <img class="img-fluid img-second" src="./assets/images/<?= htmlspecialchars($row['product_image2']) ?>">
                        </div>
                        <div class="star">
                            <?php for ($i = 0; $i < 5; $i++) : ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <h3 class="p-product"><?= htmlspecialchars($row['product_name']) ?></h3>
                        <p class="p-price"><?= number_format($row['product_price'], 0, '.', '.') ?> VND</p>
                        <?php if ($row['product_price_discount']) : ?>
                            <p class="p-price-discount"><?= number_format($row['product_price_discount'], 0, '.', '.') ?> VND</p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- All Products -->
<section class="my-5 py-5">
    <div class="container text-center mt-5 py-5">
        <h3 class="text-uppercase fs-1">TẤT CẢ SẢN PHẨM</h3>
        <hr>
        <p class="fs-4">Ở đây bạn có thể xem sản phẩm của chúng tôi</p>
    </div>

    <div class="container">
        <div class="row">
            <?php while ($row = $products->fetch_assoc()) :
                $status = strtolower(str_replace(' ', '-', $row['status_products_name']));
                $link = match ($row['status_products_name']) {
                    'Sold Out' => "sold_out.php?product_id={$row['product_id']}",
                    'Pre Order' => "pre_order.php?product_id={$row['product_id']}",
                    default => "single_product.php?product_id={$row['product_id']}",
                };
            ?>
                <div class="product text-center me-5 col-lg-3 col-md-6 col-sm-12">
                    <a href="<?= $link ?>" class="product-link">
                        <div class="product-status <?= $status ?>"><?= $row['status_products_name'] ?></div>
                        <div class="img-container">
                            <img class="img-fluid mb-3" src="./assets/images/<?= htmlspecialchars($row['product_image']) ?>">
                            <img class="img-fluid img-second" src="./assets/images/<?= htmlspecialchars($row['product_image2']) ?>">
                        </div>
                        <div class="star">
                            <?php for ($i = 0; $i < 5; $i++) : ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <h3 class="p-product"><?= htmlspecialchars($row['product_name']) ?></h3>
                        <p class="p-price"><?= number_format($row['product_price'], 0, '.', '.') ?> VND</p>
                        <?php if ($row['product_price_discount']) : ?>
                            <p class="p-price-discount"><?= number_format($row['product_price_discount'], 0, '.', '.') ?> VND</p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-5">
                <?php if ($page > 1) : ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages) : ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</section>

<script>
    function updatePriceLabel(value) {
        document.getElementById('selectedPrice').textContent = value;
        document.getElementById('maxPrice').value = value;
    }
</script>

<?php include('layouts/footer.php'); ?>
