<?php
include('server/connection.php');
session_start();

if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
    }
}

if (isset($_POST['update_account'])) {
    $user_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check if the email is valid
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        header('location:account.php?error=Invalid email format');
        exit();
    }

    // Update email and username (if changed)
    $stmt = $conn->prepare('UPDATE users SET user_name = ?, user_email = ? WHERE user_id = ?');
    $stmt->bind_param('ssi', $user_name, $user_email, $_SESSION['user_id']);
    if (!$stmt->execute()) {
        header('location:account.php?error=Failed to update account details');
        exit();
    }

    // If password is being updated, validate and change it
    if (!empty($old_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            header('location:account.php?error=Passwords do not match');
            exit();
        } elseif (strlen($new_password) < 6) {
            header('location:account.php?error=Password too short');
            exit();
        } else {
            // Check the old password
            $stmt = $conn->prepare('SELECT user_password FROM users WHERE user_email = ?');
            $stmt->bind_param('s', $user_email);
            $stmt->execute();
            $stmt->bind_result($hashed_old_password);
            $stmt->fetch();
            $stmt->close();

            if (md5($old_password) !== $hashed_old_password) {
                header('location:account.php?error=Old password is incorrect');
                exit();
            }

            // Hash the new password and update it
            $hashed_new_password = md5($new_password);

            $stmt = $conn->prepare('UPDATE users SET user_password = ? WHERE user_email = ?');
            $stmt->bind_param('ss', $hashed_new_password, $user_email);
            if ($stmt->execute()) {
                header('location:account.php?message=Account updated successfully');
            } else {
                header('location:account.php?error=Failed to update password');
            }
            $stmt->close();
        }
    } else {
        header('location:account.php?message=Account updated successfully');
    }
}
?>

<?php include('layouts/header.php') ?>

<!-- Account page -->

<section class="my-5 py-5">
    <div class="container">
        <div class="row">
            <div class="account-update col-lg-8 col-md-10 col-sm-12 mx-auto">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Account</li>
                    </ol>
                </nav>

                <h3 class="font-weight-bold text-center text-uppercase">My Account</h3>

                <!-- Account Menu -->
                <ul id="account-panel" class="nav nav-pills justify-content-center mb-4">
                    <li class="nav-item">
                        <a href="my_profile.php" class="nav-link font-weight-bold" role="tab">
                            <i class="fas fa-user-alt"></i> My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="my_orders.php" class="nav-link font-weight-bold" role="tab">
                            <i class="fas fa-shopping-bag"></i> My Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="account.php?logout=1" class="nav-link font-weight-bold" role="tab">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>

                <!-- Account Update Form -->
                <div class="account-update-form">
                    <form id="account-update" action="account.php" method="POST">
                        <?php if (isset($_GET['message'])): ?>
                            <div class="alert alert-success" role="alert">
                                <p><?php echo $_GET['message']; ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <p><?php echo $_GET['error']; ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Account Info Fields -->
                        <div class="form-group">
                            <label for="user_name">Username</label>
                            <input type="text" id="user_name" name="user_name" class="form-control" value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required>
                        </div>

                        <!-- Password Update Fields -->
                        <div class="form-group">
                            <label for="old_password">Old Password</label>
                            <input type="password" id="old_password" name="old_password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" name="update_account" class="btn btn-primary w-100">Update Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('layouts/footer.php') ?>