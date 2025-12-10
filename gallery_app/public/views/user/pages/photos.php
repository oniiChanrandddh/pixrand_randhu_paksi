<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";

$userId = $_SESSION['user']['id'];

$publicPhotos = mysqli_query($conn, "
    SELECT photos.*,
        (SELECT COUNT(*) FROM likes WHERE likes.photo_id = photos.id) AS total_likes,
        (SELECT COUNT(*) FROM comments WHERE comments.photo_id = photos.id) AS total_comments
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
    ORDER BY photos.created_at DESC
");

$privatePhotos = mysqli_query($conn, "
    SELECT photos.*,
        (SELECT COUNT(*) FROM likes WHERE likes.photo_id = photos.id) AS total_likes,
        (SELECT COUNT(*) FROM comments WHERE comments.photo_id = photos.id) AS total_comments
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'private'
      AND albums.user_id = $userId
    ORDER BY photos.created_at DESC
");
?>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/user/photos/photos.css?v=<?= time() ?>">
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.card-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    gap: 6px;
    z-index: 10;
}
.photo-card {
    position: relative;
}
.btn-edit-private,
.btn-delete-private {
    border: none;
    padding: 8px;
    font-size: 11px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    transition: .25s;
}
.btn-edit-private {
    background: #9333ea;
    color: #fff;
}
.btn-edit-private:hover {
    background: #a855f7;
    transform: translateY(-3px);
    box-shadow: 0 0 12px rgba(147, 51, 234, 0.5);
}
.btn-delete-private {
    background: #ef4444;
    color: #fff;
}
.btn-delete-private:hover {
    background: #dc2626;
    transform: translateY(-3px);
    box-shadow: 0 0 12px rgba(220, 38, 38, 0.5);
}
</style>

<div class="page-wrapper">

    <header class="photos-header">
        <h1 class="page-title"><i data-feather='image'></i> Jelajahi Foto</h1>
        <p class="page-sub">Foto-foto publik dan koleksi pribadimu.</p>
    </header>

    <section class="toolbar">
        <div class="search-box">
            <input type="text" id="searchPhoto" placeholder="Cari foto" autocomplete="off">
            <i data-feather="search"></i>
        </div>

        <a href="add_photo.php" class="btn-primary toolbar-btn">
            <i data-feather="upload"></i> Upload Foto
        </a>
    </section>

    <?php if ($publicPhotos && mysqli_num_rows($publicPhotos) > 0): ?>
    <h2 class="section-title">Foto Publik</h2>

    <section class="photo-grid">
        <?php while($p = mysqli_fetch_assoc($publicPhotos)): ?>
        <div class="photo-card" data-title="<?= strtolower(htmlspecialchars($p['title'])) ?>">
            <a href="photo_detail.php?id=<?= $p['id'] ?>" class="photo-img-area">
                <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($p['file_path']) ?>" alt="">
            </a>
            <div class="photo-info">
                <h4 class="photo-title"><?= htmlspecialchars($p['title'] ?: 'Tanpa Judul') ?></h4>
                <div class="info-footer">
                    <div class="meta-group">
                        <span><i data-feather="heart"></i> <?= $p['total_likes'] ?></span>
                        <span><i data-feather="message-circle"></i> <?= $p['total_comments'] ?></span>
                    </div>
                    <small class="info-date"><?= date("d M Y", strtotime($p['created_at'])) ?></small>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </section>
    <?php endif; ?>

    <h2 class="section-title" style="margin-top:32px;">Foto Pribadi Saya</h2>

    <?php if ($privatePhotos && mysqli_num_rows($privatePhotos) > 0): ?>
    <section class="photo-grid">
        <?php while($p = mysqli_fetch_assoc($privatePhotos)): ?>
        <div class="photo-card" data-title="<?= strtolower(htmlspecialchars($p['title'])) ?>">

            <div class="card-actions">
                <a href="edit_priv_photo.php?id=<?= $p['id'] ?>" class="btn-edit-private">
                    <i data-feather="edit-3"></i>
                </a>

                <button onclick="confirmDeletePhoto(<?= $p['id'] ?>)" class="btn-delete-private">
                    <i data-feather="trash-2"></i>
                </button>
            </div>

            <a href="photo_detail.php?id=<?= $p['id'] ?>" class="photo-img-area">
                <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($p['file_path']) ?>" alt="">
            </a>
            <div class="photo-info">
                <h4 class="photo-title"><?= htmlspecialchars($p['title'] ?: 'Tanpa Judul') ?></h4>
                <div class="info-footer">
                    <div class="meta-group">
                        <span><i data-feather="heart"></i> <?= $p['total_likes'] ?></span>
                        <span><i data-feather="message-circle"></i> <?= $p['total_comments'] ?></span>
                    </div>
                    <small class="info-date"><?= date("d M Y", strtotime($p['created_at'])) ?></small>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </section>

    <?php else: ?>
    <section class="empty-state">
        <div class="empty-icon"><i data-feather="lock"></i></div>
        <h3>Belum ada foto pribadi</h3>
        <p>Ayo upload foto untuk melengkapi albummu!</p>
    </section>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>

<script>
feather.replace();

function confirmDeletePhoto(photoId) {
    Swal.fire({
        title: 'Hapus Foto?',
        text: "Foto ini akan terhapus permanen!",
        icon: 'warning',
        background: '#160626',
        color: '#ffffff',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        iconColor: '#facc15'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "/gallery_app/public/views/user/actions/photo_delete_priv.php?id=" + photoId;
        }
    });
}

const searchInput = document.getElementById('searchPhoto');
const photoCards = document.querySelectorAll('.photo-card');

searchInput.addEventListener('input', function () {
    const keyword = searchInput.value.toLowerCase();
    photoCards.forEach(card => {
        card.style.display = card.getAttribute('data-title').includes(keyword) ? "" : "none";
    });
});
</script>
