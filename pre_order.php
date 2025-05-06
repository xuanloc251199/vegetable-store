<?php
include('layouts/header.php');
?>
<style>
    /* Cấu hình chung cho body */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f6f9;
        /* Nền sáng, dễ nhìn */
        margin: 0;
        padding: 0;
    }

    /* Container chính */
    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Đặt tên cho phần của đơn hàng */
    #order-section {
        padding: 60px 20px;
        background-color: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        /* Tạo bóng đổ để làm nổi bật */
        border-radius: 10px;
        margin-top: 50px;
        text-align: center;
    }

    /* Tiêu đề chính */
    h3 {
        font-size: 3rem;
        color: #333;
        font-weight: bold;
        margin-bottom: 20px;
    }

    /* Tiêu đề nhỏ */
    hr {
        width: 80px;
        border: 2px solid #007bff;
        margin: 20px auto;
    }

    /* Phần breadcrumb */
    .breadcrumb {
        background-color: #f8f9fa;
        font-size: 1.1rem;
        padding: 10px 20px;
        border-radius: 5px;
    }

    .breadcrumb-item a {
        color: #007bff;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    /* Cải tiến đoạn văn */
    .fs-4 {
        font-size: 1.5rem;
        color: #555;
        margin-bottom: 30px;
    }

    /* QR code container */
    .qr-container {
        background-color: #e9ecef;
        padding: 25px;
        border-radius: 15px;
        display: inline-block;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .qr-container img {
        max-width: 250px;
        border-radius: 8px;
    }

    /* Phần chú thích bên dưới */
    .fs-5 {
        font-size: 1.2rem;
        color: #333;
        margin-top: 20px;
    }

    /* Nút liên hệ (optional) */
    a.btn-contact {
        background-color: #007bff;
        color: #fff;
        padding: 12px 25px;
        font-size: 1.2rem;
        border-radius: 30px;
        margin-top: 30px;
        display: inline-block;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    a.btn-contact:hover {
        background-color: #0056b3;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        h3 {
            font-size: 2.5rem;
        }

        .qr-container img {
            max-width: 200px;
        }

        .fs-4 {
            font-size: 1.3rem;
        }

        .fs-5 {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        h3 {
            font-size: 2rem;
        }

        .fs-4,
        .fs-5 {
            font-size: 1rem;
        }

        a.btn-contact {
            padding: 10px 20px;
            font-size: 1rem;
        }
    }
</style>
<section id="order-section" class="my-5 py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pre Order</li>
        </ol>
    </nav>
    <div class="container text-center">

        <h3 class="text-uppercase fs-1">Đặt Hàng</h3>
        <hr>
        <p class="fs-4">Vui lòng quét mã QR Zalo để được nhân viên tư vấn và hỗ trợ đặt hàng.</p>

        <!-- Hiển thị mã QR Zalo -->
        <div class="qr-container my-4">
            <img src="./assets/images/zalo_qr_code.png" alt="QR Zalo" class="img-fluid" style="max-width: 250px;">
        </div>

        <p class="fs-5">Chúng tôi sẽ liên hệ với bạn ngay sau khi bạn quét mã QR.</p>
    </div>
</section>
<?php include('layouts/footer.php'); ?>