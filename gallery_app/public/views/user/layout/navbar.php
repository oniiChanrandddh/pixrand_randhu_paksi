<?php
require_once __DIR__ . "/user_guard.php";

$username = $_SESSION['user']['username'] ?? '';
$name     = $_SESSION['user']['full_name'] ?? $username;
?>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/components/navbar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<nav class="user-navbar">

    <div class="nav-left">
        <a href="<?= BASE_URL ?>views/user/pages/home.php" class="nav-logo">
            Pixrand
        </a>

        <ul class="nav-links">

            <li><a href="<?= BASE_URL ?>views/user/pages/home.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'home.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i> Beranda
            </a></li>

            <li><a href="<?= BASE_URL ?>views/user/pages/albums.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'albums.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-images"></i> Album
            </a></li>

            <li><a href="<?= BASE_URL ?>views/user/pages/photos.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'photos.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-image"></i> Foto
            </a></li>
            
            <li><a href="<?= BASE_URL ?>views/user/pages/add_photo.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'add_photo.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-upload"></i> Upload
            </a></li>


        </ul>
    </div>

    <div class="nav-right">
        <div class="nav-user">
            <i class="fa-solid fa-circle-user"></i>
            <span class="username"><?= htmlspecialchars($name) ?></span>
        </div>

        <a href="<?= BASE_URL ?>logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>

</nav>
