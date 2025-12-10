<?php 
session_start();
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../includes/helper.php";

if (isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') {
        redirect(BASE_URL . "views/admin/pages/dashboard.php");
    } else {
        redirect(BASE_URL . "views/user/pages/home.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixrand - Login</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/user/auth/login.css">
</head>
<body>

<main class="auth-wrapper">
    <div class="auth-box">

        <h1 class="brand-text">Pixrand</h1>
        <p class="brand-sub">Capture • Share • Inspire</p>

        <?php if ($msg = getFlash('login_error')): ?>
        <div class="alert-error"><?= $msg ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>actions/login_process.php" method="POST" class="auth-form">

            <div class="input-group">
                <input type="text" name="username" required placeholder=" " autocomplete="off">
                <label>Username</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" required placeholder=" ">
                <label>Password</label>
            </div>

            <button type="submit" class="btn-primary">Login</button>
        </form>

        <p class="form-note">
            Belum punya akun?
            <a href="<?= BASE_URL ?>register.php">Daftar Disini</a>
        </p>

    </div>
</main>

</body>
</html>
