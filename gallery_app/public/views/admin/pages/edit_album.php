<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";

if (!isset($_GET['id'])) {
    header("Location: albums.php");
    exit;
}

$id = intval($_GET['id']);

$result = mysqli_query($conn, "
    SELECT * FROM albums 
    WHERE id = $id 
      AND visibility = 'public'
");

$album = mysqli_fetch_assoc($result);

if (!$album) {
    header("Location: albums.php?error=unauthorized");
    exit;
}
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/albums/edit_album.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Edit Album Publik</h1>
    <p class="subtitle-main">Admin hanya dapat mengedit album yang bersifat publik.</p>

    <div class="panel">
        <form action="../actions/album_update.php" method="POST">
            <input type="hidden" name="id" value="<?= $album['id'] ?>">

            <label>Judul Album</label>
            <input type="text" name="title" value="<?= htmlspecialchars($album['title']) ?>" required>

            <label>Deskripsi</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($album['description']) ?></textarea>

            <div class="form-actions">
                <a href="albums.php" class="btn-secondary">
                    <i data-feather="arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i data-feather="save"></i> Perbarui Album
                </button>
            </div>
        </form>
    </div>

</div>

<script>feather.replace();</script>
