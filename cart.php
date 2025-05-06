<!-- <?php

        session_start();
        calculateTotalCart();


        if (isset($_POST['add_to_cart'])) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            //If user has already added to a product
            if (isset($_SESSION['cart'])) {

                $product_array_ids = array_column($_SESSION['cart'], "product_id");
                // If product already been added to cart or not.
                if (!in_array($_POST['product_id'], $product_array_ids)) {
                    $product_array = array(
                        'product_id' => $_POST['product_id'],
                        'product_name' => $_POST['product_name'],
                        'product_image' => $_POST['product_image'],
                        'product_price' => $_POST['product_price'],
                        'product_quantity' => $_POST['product_quantity']
                    );
                    $_SESSION['cart'][$_POST['product_id']] = $product_array;
                } else {
                    echo '<script> alert("Product already added to cart");  </script>';
                }
            } else {
                // If the first product
                $product_array = array(
                    'product_id' => $_POST['product_id'],
                    'product_name' => $_POST['product_name'],
                    'product_image' => $_POST['product_image'],
                    'product_price' => $_POST['product_price'],
                    'product_quantity' => $_POST['product_quantity']
                );
                $_SESSION['cart'][$_POST['product_id']] = $product_array;
            }
            calculateTotalCart();
        } else if (isset($_POST['remove_product'])) {
            //Remove product from cart
            unset($_SESSION['cart'][$_POST['product_id']]);
            calculateTotalCart();
        } else if (isset($_POST['update_quantity'])) {
            // // update quantity


            // //get id and product quantity from form.
            $product_id = $_POST['product_id'];
            $product_quantity = $_POST['product_quantity'];
            //update product quantity in session cart.
            $product_array = $_SESSION['cart'][$product_id];
            // update quantity
            $product_array['product_quantity'] = $product_quantity;
            // return array
            $_SESSION['cart'][$product_id] = $product_array;
        } else {
            echo "<p>Your cart is empty.</p>";
            echo "<img src='../assets/images/logo_hnl.jpg' alt='Empty Cart' />";
        }


        function calculateTotalCart()
        {
            $total = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $key => $value) {
                    $price = $value['product_price'];
                    $quantity = $value['product_quantity'];
                    $total += $price * $quantity;
                }
                $_SESSION['total'] = $total;
            } else {
                $_SESSION['total'] = 0; // Set total to 0 if the cart is empty
            }
        }

        ?> -->



<?php include('layouts/header.php') ?>




<!-- Cart-->
<section class="cart container my-5 pt-5">

    <div class="container mt-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                <li class="breadcrumb-item active" aria-current="page">Your Cart</li>
            </ol>
        </nav>
        <h2 class="font-weight-bold">Your Cart</h2>
    </div>

    <table class="mt-3 pt-5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Action</th>
            <th>Total</th>
        </tr>

        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
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
                    <td>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">

                            <input type="number" name="product_quantity" value="<?php echo $value['product_quantity']; ?>"
                                min="1">
                            <button type="submit" name="update_quantity" class="remove-btn rounded-2">Update</button>
                        </form>
                    </td>

                    <td>
                        <p><?php echo $value['product_price']; ?></p>

                    </td>
                    <td>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                            <button type="submit" name="remove_product" class="remove-btn rounded-2">Remove</button>
                        </form>
                    </td>
                    <td>
                        <p><?php echo $value['product_price']; ?></p>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5" class="text-center fs-2 text-uppercase p-4">Your cart is empty</td>
            </tr>
        <?php } ?>
    </table>

    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
        <div class="cart-total">
            <table>
                <tr>
                    <td>Total</td>
                    <td><?php echo $value['product_price']; ?></td>
                </tr>
            </table>
        </div>

        <div class="checkout-container">
            <form action="checkout.php" method="POST">
                <button class="checkout-btn rounded-2 text-uppercase text-white" name="check-out">Process to
                    Checkout</button>
            </form>
        </div>
    <?php } ?>

</section>


<?php include('layouts/footer.php') ?>