<?php

include("server/connection.php");

// if (isset($_GET['product_id'])) {
//     $product_id = $_GET['product_id'];

//     $stmt = $conn->prepare('SELECT * FROM products WHERE product_id = ?');

//     $stmt->bind_param('i', $product_id);

//     $stmt->execute();

//     $sg_product = $stmt->get_result();



//     $stmt1 = $conn->prepare("SELECT * FROM products  LIMIT 4 OFFSET 8");
//     $stmt1->execute();
//     $related_products = $stmt1->get_result();
// } else {
//     header("location: index.php");
// }


include("server/connection.php");

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Lấy sản phẩm và danh mục
    $stmt = $conn->prepare('
        SELECT p.*, c.category_name 
        FROM products p 
        JOIN category c ON p.category_id = c.category_id 
        WHERE product_id = ?
    ');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $sg_product = $stmt->get_result();

    // Lấy 4 sản phẩm ngẫu nhiên, loại trừ sản phẩm hiện tại
    $stmt1 = $conn->prepare("
   SELECT * 
   FROM products 
   WHERE product_id != ? 
   ORDER BY RAND() 
   LIMIT 4
");
    $stmt1->bind_param('i', $product_id); // Đảm bảo sản phẩm hiện tại không được chọn
    $stmt1->execute();
    $related_products = $stmt1->get_result();
} else {
    header("location: index.php");
}

?>

<!-- css for size -->
<style>
    .size-options {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .size-label {
        padding: 10px 20px;
        border: 2px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease, border 0.3s ease;
    }

    .size-label:hover {
        background-color: #f0f0f0;
    }

    input[type="radio"]:checked+.size-label {
        background-color: #000;
        color: #fff;
        border-color: #fff;
    }

    input[type="radio"] {
        display: none;
        /* Ẩn nút radio gốc */
    }
</style>


<?php include('layouts/header.php') ?>

<!--Single Product-->

<section class=" container single_product my-5 pt-5">
    <div class="row mt-5">

        <?php while ($row = $sg_product->fetch_assoc()) { ?>



            <div class="col-lg-5 col-md-6">
                <img src="./assets/images/<?php echo $row['product_image'] ?>" class="img-fluid w-100 pb-2 main-img"
                    id="mainImg">

                <div class="small-img-group">

                    <div class="small-img-col">
                        <img src="./assets/images/<?php echo $row['product_image2'] ?>" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="./assets/images/<?php echo $row['product_image3'] ?>" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="./assets/images/<?php echo $row['product_image4'] ?>" class="small-img">
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-6">

                <h2 class="py-4"><?php echo $row['product_name']; ?></h2>
                <h4><?php echo number_format($row['product_price_discount'], 0, '.', '.') . ' VND'; ?></h4>
                <h3 class="p-price-discount">
                    <?php
                    if ($row['product_price_discount'] != 0) {
                        // Định dạng giá với dấu chấm cách 3 chữ số và thêm "VND"
                        echo number_format($row['product_price'], 0, '.', '.') . ' VND';
                    } else {
                        echo ''; // Hiển thị khoảng trống nếu giá giảm bằng 0
                    }
                    ?>
                </h3>
                <!-- Hiển thị số lượng nếu quantity < 10 -->
                <?php if ($row['quantity'] < 10) { ?>
                    <p class="stock-status">Only <?php echo $row['quantity']; ?> left in stock</p>
                <?php } ?>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo number_format($row['product_price'], 0, '.', '.') . ' VND'; ?>">
                    <input type="number" name="product_quantity" value="1" min="1" class="mt-3">
                    <button class="buy-btn rounded-2 text-uppercase" type="submit" name="add_to_cart">Add To Cart</button>
                </form>
                <h3 class="py-5 text-uppercase">Product Details</h3>
                <p><?php echo $row['product_description']; ?></p>







            </div>
        <?php } ?>
</section>

<section id="featured" class="my-5 py-5">
    <div class="container text-center mt-5 py-5">
        <h3 class="text-uppercase fs-1">Related Products</h3>
    </div>

    <div class="row mx-auto container-fluid">

        <?php
        // Kết nối tới cơ sở dữ liệu
        include('server/connection.php');

        // Giả sử bạn đã có product_id từ trang single_product.php
        if (isset($_GET['product_id'])) {
            $product_id = $_GET['product_id'];

            // Truy vấn lấy thông tin sản phẩm hiện tại
            $stmt = $conn->prepare("
                SELECT p.*, sp.status_products_name
                FROM products p
                LEFT JOIN status_products sp ON p.status_products_id = sp.status_products_id
                WHERE p.product_id != ? 
                ORDER BY RAND() 
                LIMIT 4
            ");
            $stmt->bind_param('i', $product_id); // Loại trừ sản phẩm hiện tại
            $stmt->execute();
            $related_products = $stmt->get_result();
        } else {
            echo "No related products available.";
        }

        // Lặp qua các sản phẩm liên quan và hiển thị
        while ($related_product = $related_products->fetch_assoc()) {
            // Kiểm tra trạng thái sản phẩm
            if ($related_product['status_products_name'] == 'Sold Out') {
                // Nếu sản phẩm đã "Sold Out", chuyển hướng đến trang sold_out.php
                $link = "sold_out.php?product_id=" . $related_product['product_id'];
            } elseif ($related_product['status_products_name'] == 'Pre Order') {
                // Nếu sản phẩm là "Pre Order", chuyển hướng đến trang pre_order.php
                $link = "pre_order.php?product_id=" . $related_product['product_id'];
            } else {
                // Nếu sản phẩm còn hàng, chuyển hướng đến trang single_product.php
                $link = "single_product.php?product_id=" . $related_product['product_id'];
            }
        ?>

            <div class="product text-center col-lg-3 col-md-6 col-sm-12">
                <a href="<?php echo $link; ?>" class="product-link">

                    <!-- Hiển thị trạng thái sản phẩm -->
                    <div class="product-status <?php echo strtolower(str_replace(' ', '-', $related_product['status_products_name'])); ?>">
                        <?php echo $related_product['status_products_name']; ?>
                    </div>

                    <div class="img-container">
                        <!-- Ảnh sản phẩm chính -->
                        <img class="img-fluid mb-3" src="./assets/images/<?php echo $related_product['product_image']; ?>">

                        <!-- Ảnh sản phẩm thứ hai sẽ xuất hiện khi hover -->
                        <img class="img-fluid img-second" src="./assets/images/<?php echo $related_product['product_image2']; ?>" alt="Second Image">
                    </div>

                    <div class="star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>

                    <h3 class="p-product"><?php echo $related_product['product_name']; ?></h3>
                    <p class="p-price"><?php echo number_format($related_product['product_price'], 0, '.', '.') . ' VND'; ?></p>

                    <!-- Hiển thị giá giảm nếu có -->
                    <p class="p-price-discount">
                        <?php
                        if ($related_product['product_price_discount'] != 0) {
                            echo number_format($related_product['product_price_discount'], 0, '.', '.') . ' VND';
                        } else {
                            echo ''; // Nếu không có giá giảm
                        }
                        ?>
                    </p>
                </a>
            </div>

        <?php } // Kết thúc vòng lặp 
        ?>
    </div>
</section>






<?php include('layouts/footer.php') ?>


<script>
    var mainImg = document.getElementById('mainImg');
    var small_Img = document.getElementsByClassName('small-img');

    for (let i = 0; i <= 4; i++) {
        small_Img[i].addEventListener('click', function() {
            mainImg.src = small_Img[i].src;
        });
    }
</script>