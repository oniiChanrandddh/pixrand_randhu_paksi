<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/albums/add_album.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Tambah Album Baru</h1>
    <p class="subtitle-main">Isi data album untuk menambah koleksi baru</p>

    <div class="form-panel">
        <form action="../actions/album_create.php" method="POST" class="album-form">

            <div class="form-group">
                <label>Judul Album</label>
                <input type="text" name="title" placeholder="Contoh: Sunset Collection" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" placeholder="Tambahkan deskripsi album (optional)" rows="4"></textarea>
            </div>

            <div class="form-actions">
                <a href="albums.php" class="btn-secondary">
                    <i data-feather="arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i data-feather="check"></i> Simpan Album
                </button>
            </div>

        </form>
    </div>

</div>

<script>feather.replace();</script>
