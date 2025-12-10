<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";

$userId = intval($_SESSION['user']['id']);

$totalPublicQuery = "SELECT COUNT(*) AS total FROM albums WHERE visibility='public'";
$totalPublicRes = mysqli_query($conn, $totalPublicQuery);
$totalPublicAlbums = (int)($totalPublicRes->fetch_assoc()['total'] ?? 0);

$totalPrivateQuery = "SELECT COUNT(*) AS total FROM albums WHERE visibility='private' AND user_id = $userId";
$totalPrivateRes = mysqli_query($conn, $totalPrivateQuery);
$totalPrivateAlbums = (int)($totalPrivateRes->fetch_assoc()['total'] ?? 0);

$totalAlbums = $totalPublicAlbums + $totalPrivateAlbums;
$perPage = 9;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalPages = max(1, ceil($totalAlbums / $perPage));
if ($currentPage > $totalPages) $currentPage = $totalPages;
$offset = ($currentPage - 1) * $perPage;

$q = "
SELECT 
    a.*,
    (SELECT file_path FROM photos WHERE photos.album_id = a.id ORDER BY created_at DESC LIMIT 1) AS cover_image,
    (SELECT COUNT(*) FROM photos WHERE photos.album_id = a.id) AS total_photos
FROM albums AS a
WHERE a.visibility='public'
OR (a.visibility='private' AND a.user_id=$userId)
ORDER BY a.created_at DESC
LIMIT $perPage OFFSET $offset
";
$res = mysqli_query($conn, $q);

$albumsPublic = [];
$albumsPrivate = [];

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['visibility'] === 'public') $albumsPublic[] = $row;
        else $albumsPrivate[] = $row;
    }
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/user/albums/albums.css?v=<?= time() ?>">
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="page-wrapper">
    <header class="albums-header">
        <div>
            <h1 class="page-title"><i data-feather="grid"></i> Jelajahi Album</h1>
            <p class="page-sub">Temukan dan kelola album publik serta album pribadi milikmu.</p>
        </div>
    </header>

    <section class="toolbar">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari album..." autocomplete="off">
            <i data-feather="search"></i>
        </div>
    </section>

    <?php if (!empty($albumsPublic)): ?>
    <section class="albums-section">
        <div class="section-header">
            <h2 class="section-title">Album Publik</h2>
        </div>
        <div class="album-grid">
        <?php foreach ($albumsPublic as $album): ?>
            <a href="album_detail.php?id=<?= $album['id'] ?>" class="album-card"
               data-title="<?= strtolower(htmlspecialchars($album['title'])) ?>">
                <div class="album-cover">
                    <?php if ($album['cover_image']): ?>
                        <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($album['cover_image']) ?>">
                    <?php else: ?>
                        <div class="album-cover-placeholder"><i data-feather="camera-off"></i></div>
                    <?php endif; ?>
                </div>
                <div class="album-info">
                    <h3 class="album-title"><?= htmlspecialchars($album['title']) ?></h3>
                    <div class="album-meta">
                        <span><i data-feather="image"></i> <?= $album['total_photos'] ?> Foto</span>
                        <span><i data-feather="clock"></i> <?= date("d M Y", strtotime($album['created_at'])) ?></span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="albums-section">
        <div class="section-header">
            <h2 class="section-title">Album Pribadi Saya</h2>
            <a href="../pages/add_priv_album.php" class="btn-primary"><i data-feather="plus"></i> Album Baru</a>
        </div>

        <?php if (!empty($albumsPrivate)): ?>
        <div class="album-grid">
        <?php foreach ($albumsPrivate as $album): ?>
            <div class="album-card private-card-wrapper" data-title="<?= strtolower(htmlspecialchars($album['title'])) ?>">

                <div class="card-actions">
                    <a href="../pages/edit_priv_album.php?id=<?= $album['id'] ?>" class="btn-edit-private">
                        <i data-feather="edit-3"></i>
                    </a>

                    <button onclick="confirmDelete(<?= $album['id'] ?>)" class="btn-delete-private">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>

                <a href="album_detail.php?id=<?= $album['id'] ?>">
                    <div class="album-cover">
                        <?php if ($album['cover_image']): ?>
                            <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($album['cover_image']) ?>">
                        <?php else: ?>
                            <div class="album-cover-placeholder"><i data-feather="camera-off"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="album-info">
                        <h3 class="album-title"><?= htmlspecialchars($album['title']) ?></h3>
                        <div class="album-meta">
                            <span><i data-feather="image"></i> <?= $album['total_photos'] ?> Foto</span>
                            <span><i data-feather="clock"></i> <?= date("d M Y", strtotime($album['created_at'])) ?></span>
                        </div>
                    </div>
                </a>

            </div>
        <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="section-sub" style="opacity:.7;margin-top:10px;">Belum ada album pribadi</p>
        <?php endif; ?>
    </section>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="page-btn <?= $i == $currentPage ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>

<script>
feather.replace();
function confirmDelete(albumId) {
    Swal.fire({
        title: 'Hapus Album?',
        text: "Semua foto di dalam album ini akan terhapus permanen!",
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
              window.location.href = "/gallery_app/public/views/user/actions/album_delete_priv.php?id=" + albumId;
        }
    });
}

const s = document.getElementById('searchInput');
const cards = document.querySelectorAll('.album-card');
s.addEventListener('input', () => {
    const k = s.value.toLowerCase();
    cards.forEach(card => {
        card.style.display = card.getAttribute('data-title').includes(k) ? "" : "none";
    });
});
</script>
