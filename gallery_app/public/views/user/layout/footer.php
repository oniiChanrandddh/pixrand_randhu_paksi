<?php ?>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/components/footer.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<footer class="footer">
    <div class="footer-container">

        <div class="footer-col footer-brand">
            <h2 class="footer-logo">Pixrand</h2>
            <p class="footer-desc">
                Platform berbagi momen fotografi untuk mengekspresikan kreativitas visualmu.
            </p>
        </div>

        <div class="footer-col">
            <h3 class="footer-title">Menu</h3>
            <ul class="footer-list">
                <li><a href="<?= BASE_URL ?>views/user/pages/home.php">Beranda</a></li>
                <li><a href="<?= BASE_URL ?>views/user/pages/photos.php">Galeri</a></li>
                <li><a href="<?= BASE_URL ?>views/user/pages/add_photo.php">Upload Foto</a></li>
                <li><a href="<?= BASE_URL ?>views/user/pages/albums.php">Upload Album</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3 class="footer-title">Kontak</h3>
            <div class="contact-row">
                <i class="far fa-envelope"></i>
                <a href="mailto:support@pixrandapp.com">
                    Pixrand@gmail.com
                </a>
            </div>
            <div class="contact-row">
                <i class="fab fa-whatsapp"></i>
                <a href="https://wa.me/6281234567890" target="_blank">
                    +62 812-3456-7890
                </a>
            </div>
        </div>

        <div class="footer-col">
            <h3 class="footer-title">Ikuti Kami</h3>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-x-twitter"></i></a>
            </div>
        </div>

    </div>

    <div class="footer-divider"></div>

    <p class="footer-copy">
        &copy; <?= date('Y') ?> Pixrand — Capture • Share • Inspire.
    </p>
</footer>
