<?php
session_start();
if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
    }
}

?>

<?php include('layouts/header.php') ?>
<style>
    /* CSS cho breadcrumb container */
    .breadcrumb {
        background-color: #f8f9fa;
        /* Màu nền sáng */
        padding: 10px 10px;
        /* Khoảng cách trong breadcrumb */
        border-radius: 5px;
        /* Bo góc mềm mại */
        margin-top: 20px;
        /* Khoảng cách trên để tách khỏi phần header */
        font-size: 12px;
        /* Kích thước chữ nhỏ hơn */
        list-style: none;
        /* Loại bỏ dấu đầu dòng */
        display: flex;
        /* Căn các mục breadcrumb thành hàng ngang */
        gap: 1px;
        /* Khoảng cách giữa các mục */
        align-items: center;
        /* Căn giữa dọc các mục */
    }

    /* Định dạng cho các mục breadcrumb */
    .breadcrumb-item {
        color: #6c757d;
        /* Màu chữ mặc định cho breadcrumb */
        font-style: italic;
        /* Làm chữ nghiêng */
    }

    /* Liên kết trong breadcrumb (bỏ gạch chân) */
    .breadcrumb-item a {
        color: #000;
        /* Màu chữ cho link */
        text-decoration: none;
        /* Xóa gạch chân cho link */
        transition: color 0.3s ease;
        /* Hiệu ứng khi hover */
        font-style: italic;
        /* Làm chữ nghiêng cho link */
    }

    .breadcrumb-item a:hover {
        color: #0056b3;
        /* Màu khi rê chuột qua link */
        text-decoration: underline;
        /* Thêm gạch chân khi hover */
    }

    /* Hiển thị dấu phân cách ">" giữa các mục breadcrumb */
    .breadcrumb-item+.breadcrumb-item::before {
        content: "›";
        /* Dấu phân cách */
        color: #6c757d;
        /* Màu dấu phân cách */
        padding: 0 8px;
        /* Khoảng cách bên trái và phải của dấu */
    }

    /* Mục breadcrumb hiện tại */
    .breadcrumb-item.active {
        color: #212529;
        /* Màu chữ cho mục hiện tại */
        font-style: normal;
        /* Không nghiêng cho mục hiện tại */
    }
</style>
<!-- Account Page -->
<section class="my-5 py-5">
    <div class="row container mx-auto">
        <div class="info text-center col-md-6 col-lg-12 col-sm-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item"><a href="account.php">My Account</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                </ol>
            </nav>



            <!-- Thanh Menu -->
            <div class="d-flex justify-content-center mb-5">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a href="account.php" class="nav-link font-weight-bold">
                            <i class="fas fa-user-alt"></i> My Acconut
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="my_profile.php" class="nav-link font-weight-bold active">
                            <i class="fas fa-shopping-bag"></i> My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="account.php?logout=1" class="nav-link font-weight-bold">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            <div class="container my-5 py-5">
                <div class="row">
                    <div class="account-update col-lg-6 col-md-8 col-sm-10 mx-auto">
                        <div class="text-center mb-4">
                            <h1 class="text-uppercase">Account Info</h1>
                        </div>
                        <div class="account-info">
                            <p><strong>Account:</strong> <span><?php echo $_SESSION['user_name'] ?? 'Not Available'; ?></span></p>
                            <p><strong>Email:</strong> <span><?php echo $_SESSION['user_email'] ?? 'Not Available'; ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
</section>

<?php include('layouts/footer.php') ?>