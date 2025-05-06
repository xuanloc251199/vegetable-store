<?php
if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vegetable Store</title>
    <link href='./assets/images/logo/logo.png' rel='icon' type='image/x-icon' />
    <link rel="stylesheet" type="text/css" href="./assets/css/header.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">

    <link rel="stylesheet" type="text/css" href="./assets/css/styles.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/index.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/note.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" />

</head>


<style>

</style>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container-fluid px-5">
            <a href="index.php">
                <img src="./assets/images/logo/logo.png" height="60px" alt="Logo">
            </a>

            <!-- Menu chính -->
            <div class="navbar-nav d-flex align-items-center">
                <a class="nav-link me-5 fw-bold menu-link" href="index.php">Trang chủ</a>
                <a class="nav-link me-5 fw-bold menu-link" href="all_product.php">Sản phẩm</a>
                <a class="nav-link me-5 fw-bold menu-link" href="abouts.php">Giới thiệu</a>
                <form class="d-flex ms-3" action="search.php" method="GET">
                    <input class="form-control me-2" type="search" name="query" placeholder="Tìm kiếm" aria-label="Search" required>
                    <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                </form>
                <div class="nav-icons ms-3">
                    <a href="javascript:void(0);" onclick="toggleCartPopup()">
                        <i class="fas fa-shopping-cart"></i></a>
                </div>
                <?php
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                    $username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Người dùng';
                ?>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle nav-link menu-link" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class=""></i> <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li>
                                <a class="dropdown-item" href="account.php">
                                    <i class="fas fa-user-circle"></i> Tài khoản của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="my_orders.php">
                                    <i class="fas fa-shopping-bag"></i> Đơn hàng
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="account.php?logout=1">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a>


                                <!-- <a href="account.php?logout=1" class="nav-link font-weight-bold" role="tab">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a> -->
                            </li>
                        </ul>

                    </div>
                <?php } else { ?>
                    <a href="login.php" class="nav-link menu-link"><i class="fas fa-user"></i> </a>
                <?php } ?>
            </div>
        </div>
    </nav>
</body>

</html>
<!-- Cart Pop-up Modal -->
<div id="cartModal" class="cart-modal">
    <div class="cart-content">
        <span class="close" onclick="toggleCartPopup()">&times;</span>
        <h2>Giỏ hàng</h2>

        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>

                <?php foreach ($_SESSION['cart'] as $key => $value) { ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="./assets/images/<?php echo $value['product_image']; ?>"
                                    alt="<?php echo $value['product_name']; ?>">
                                <div>
                                    <p class="pt-4"><?php echo $value['product_name']; ?></p>
                                </div>

                            </div>
                        </td>
                        <td><?php echo $value['product_quantity']; ?></td>
                        <td><?php echo $value['product_price']; ?></td>
                        <td><?php echo number_format($_SESSION['total'], 3, '.', '.') . ' VND'; ?> </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td colspan="4">Total</td>
                    <td><?php echo number_format($_SESSION['total'], 3, '.', '.') . ' VND'; ?></td>
                </tr>
            </table>
            <a href="checkout.php" class="btn btn-dark">Proceed to Checkout</a>
            <a href="cart.php" class="btn btn-dark">Show Full</a>
        <?php } else { ?>
            <div class="empty-cart">
                <!-- Hình ảnh giỏ hàng trống -->
                <img src="./assets/images/empty-cart.png" alt="Giỏ hàng trống" style="max-width: 300px; display: block; margin: 0 auto;">
                <p>Giỏ hàng trống</p>
                <a href="index.php" class="btn btn-dark">Tiếp tục mua sắm</a>
            </div>
        <?php } ?>
    </div>
</div>



<!-- cart -->
<script>
    function toggleCartPopup() {
        const cartModal = document.getElementById('cartModal');
        if (cartModal.style.display === 'flex') {
            cartModal.style.display = 'none';
        } else {
            cartModal.style.display = 'flex';
        }
    }


    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById("cartModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }


    window.addEventListener('scroll', function() {
        let scrollPosition = window.scrollY; // Vị trí cuộn của trang
        let banner = document.querySelector('.home-slider');

        // Nếu người dùng cuộn xuống, thu nhỏ banner
        if (scrollPosition > 100) { // Bạn có thể thay đổi giá trị 100 để tùy chỉnh
            banner.classList.add('banner-shrunk');
            banner.classList.remove('banner-expanded');
        }
        // Nếu người dùng cuộn lên, phóng to banner
        else {
            banner.classList.add('banner-expanded');
            banner.classList.remove('banner-shrunk');
        }
    });
</script>