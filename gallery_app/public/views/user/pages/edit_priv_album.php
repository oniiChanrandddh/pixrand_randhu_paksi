<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";

$user_id = $_SESSION['user']['id'];
$album_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$q = mysqli_query($conn, "SELECT * FROM albums WHERE id='$album_id' AND user_id='$user_id' LIMIT 1");
if (!$q || mysqli_num_rows($q) == 0) {
    header("Location: albums.php");
    exit;
}

$album = mysqli_fetch_assoc($q);
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/albums/add_album.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Edit Album Pribadi</h1>
    <p class="subtitle-main">Atur kembali informasi album ini sesuai keinginanmu</p>

    <div class="form-panel">
<form action="/gallery_app/public/views/user/actions/album_edit_priv.php" method="POST" class="album-form">

            <input type="hidden" name="album_id" value="<?= $album['id']; ?>">
            <input type="hidden" name="visibility" value="private">

            <div class="form-group">
                <label>Nama Album *</label>
                <input type="text" name="title" value="<?= htmlspecialchars($album['title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi Album</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($album['description']); ?></textarea>
            </div>

            <div class="form-actions">
                <a href="albums.php" class="btn-secondary">
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
