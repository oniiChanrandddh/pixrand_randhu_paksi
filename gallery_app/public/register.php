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
    <title>Pixrand - Register</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/global.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/user/auth/login.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <main class="auth-wrapper">
        <div class="auth-box">

            <h1 class="brand-text">Join Pixrand</h1>
            <p class="brand-sub">Create your account and start inspiring!</p>

            <?php if ($msg = getFlash('register_error')): ?>
                <div class="alert-error"><?= esc($msg) ?></div>
            <?php endif; ?>

            <?php if ($msg = getFlash('register_success')): ?>
                <script>
                    Swal.fire({
                        title: "Akun Berhasil Dibuat!",
                        text: "Silakan login untuk melanjutkan.",
                        icon: "success",
                        background: "#160626",
                        color: "#fff",
                        iconColor: "#a855f7",
                        confirmButtonText: "OK",
                        buttonsStyling: false,
                        showConfirmButton: true,
                        showCancelButton: false,
                        customClass: {
                            confirmButton: "pix-btn-confirm"
                        }
                    }).then(() => {
                        window.location.href = "<?= BASE_URL ?>index.php";
                    });
                </script>

                <style>
                    .pix-btn-confirm {
                        background: #a855f7 !important;
                        color: #fff !important;
                        border: none !important;
                        border-radius: 10px !important;
                        padding: 10px 28px !important;
                        font-size: 15px !important;
                        font-weight: 600 !important;
                        text-shadow: none !important;
                        outline: none !important;
                        box-shadow: none !important;
                        transition: .25s ease-in-out !important;
                    }

                    .pix-btn-confirm:hover {
                        background: #be72ff !important;
                        box-shadow: 0 0 14px rgba(168, 85, 247, 0.6) !important;
                    }
                </style>

            <?php endif; ?>

            <form action="<?= BASE_URL ?>actions/register_process.php" method="POST" class="auth-form">

                <div class="input-group">
                    <input type="text" name="full_name" required placeholder=" " autocomplete="off">
                    <label>Full Name</label>
                </div>

                <div class="input-group">
                    <input type="text" name="username" required placeholder=" " autocomplete="off">
                    <label>Username</label>
                </div>

                <div class="input-group">
                    <input type="password" name="password" required placeholder=" ">
                    <label>Password</label>
                </div>

                <div class="input-group">
                    <input type="password" name="confirm_password" required placeholder=" ">
                    <label>Confirm Password</label>
                </div>

                <button type="submit" class="btn-primary">Create Account</button>
            </form>

            <p class="form-note">
                Sudah punya akun?
                <a href="<?= BASE_URL ?>index.php">Login Disini</a>
            </p>

        </div>
    </main>

</body>

</html>