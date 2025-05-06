<?php
session_start();
?>

<?php include('layouts/header.php') ?>

<!--Checkout page-->

<section class="my-5 py-5">

    <div class="container text-center mt-3 pt-5">
        <h2 class=" text-uppercase text-center"> Payment</h2>
        <hr class="mx-auto">
    </div>

    <div class="container text-center mx-auto">

        <h2 class="text-center">Total:
            <strong><?php echo number_format($_SESSION['total'], 3, '.', '.') . ' VND'; ?>
            </strong>
        </h2>
        <h2 class="text-center pt-4"><?php if (isset($_GET['order_status'])) {
                                            echo $_GET['order_status'];
                                        } ?></h2>
        <!-- Button to return to homepage -->
        <div class="return-btn-container">
            <a href="index.php" class="return-btn">Continue shopping</a>
        </div>
    </div>
</section>


<?php include('layouts/footer.php') ?>