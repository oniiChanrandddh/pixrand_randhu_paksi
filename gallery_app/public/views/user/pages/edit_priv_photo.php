<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";

$user_id = $_SESSION['user']['id'];
$photo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$q = mysqli_query($conn, "
    SELECT photos.*, albums.user_id 
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE photos.id='$photo_id' AND albums.user_id='$user_id' LIMIT 1
");

if (!$q || mysqli_num_rows($q) == 0) {
    header("Location: photos.php");
    exit;
}

$photo = mysqli_fetch_assoc($q);
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/albums/add_album.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Edit Foto Pribadi</h1>
    <p class="subtitle-main">Perbarui informasi foto ini sesuai keinginanmu</p>

    <div class="form-panel">
        <form action="/gallery_app/public/views/user/actions/photo_edit_priv.php" method="POST" class="album-form">

            <input type="hidden" name="photo_id" value="<?= $photo['id']; ?>">

            <div class="form-group">
                <label>Judul Foto *</label>
                <input type="text" name="title" value="<?= isset($photo['title']) ? htmlspecialchars($photo['title']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Komentar / Deskripsi</label>
               <textarea name="caption" rows="4"><?= isset($photo['caption']) ? htmlspecialchars($photo['caption']) : '' ?></textarea>

            </div>

            <div class="form-actions">
                <a href="photos.php" class="btn-secondary">
                    <i data-feather="arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-primary">
                    <i data-feather="check"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>
<script>feather.replace();</script>
