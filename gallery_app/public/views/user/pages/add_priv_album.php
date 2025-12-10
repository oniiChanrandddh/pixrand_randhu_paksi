<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";
?>


<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/albums/add_album.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Buat Album Pribadi</h1>
    <p class="subtitle-main">Album ini hanya dapat dilihat oleh kamu sendiri</p>

    <div class="form-panel">
        <form action="../actions/album_create_priv.php" method="POST" class="album-form">

            <div class="form-group">
                <label>Nama Album *</label>
                <input type="text" name="title" placeholder="Contoh: Momen Spesial" required>
            </div>

            <div class="form-group">
                <label>Deskripsi Album</label>
                <textarea name="description" placeholder="Tambahkan deskripsi untuk album ini" rows="4"></textarea>
            </div>

            <input type="hidden" name="visibility" value="private">

            <div class="form-actions">
                <a href="albums.php" class="btn-secondary">
                    <i data-feather="arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-primary">
                    <i data-feather="check"></i> Buat Album
                </button>
            </div>

        </form>
    </div>

</div>
<?php require_once __DIR__ . "/../layout/footer.php"; ?>
<script>feather.replace();</script>
