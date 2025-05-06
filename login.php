<?php
session_start();
include('server/connection.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password FROM users WHERE user_email = ? AND user_password = ? LIMIT 1");
    $stmt->bind_param("ss", $email, $password);
    if ($stmt->execute()) {
        $stmt->bind_result($user_id, $user_name, $user_email, $user_password);
        $stmt->store_result();
        if ($stmt->num_rows() == 1) {
            $stmt->fetch();

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_email'] = $user_email;
            $_SESSION['user_password'] = $user_password;
            $_SESSION['logged_in'] = true;
            header("location:account.php?status=You login successfully");
        } else {
            header("location:login.php?error=Invalid email or password");
        }
    }
}

?>

<?php include('layouts/header.php') ?>

<!--Login-->
<section class="my-5 py-5">

    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Login</h2>
        <br class="mx-auto">
    </div>
    <div class="mx-auto container">
        <form id="login-form" action="login.php" method="POST">
            <?php if (isset($_GET['status'])): ?>
                <div class="alert alert-success" role="alert">
                    <p>
                        <?php echo $_GET['status']; ?>
                    </p>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <p>
                        <?php echo $_GET['error']; ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="form-group">

                <input type="text" placeholder="Email" id="email" name="email" class="form-control">
            </div>
            <div class="form-group">

                <input type="password" placeholder="Password" id="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" value="Login" name="login" class="btn-dark" id="login">
            </div>
            <div class="form-group">
                <a id="register-url" class="btn-text" href="register.php">Don't have account? Register</a>
            </div>
        </form>
    </div>

</section>




<?php include('layouts/footer.php') ?>