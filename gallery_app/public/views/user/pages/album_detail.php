<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";

$userId = $_SESSION['user']['id'];
$userName = htmlspecialchars($_SESSION['user']['full_name'] ?? 'Pengguna');

// VALIDASI PARAMETER ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: albums.php");
    exit;
}

$albumId = (int)$_GET['id'];

// AMBIL DATA ALBUM
$albumQuery = "
    SELECT 
        albums.*,
        users.full_name AS owner_name
    FROM albums
    LEFT JOIN users ON users.id = albums.user_id
    WHERE albums.id = $albumId
    LIMIT 1
";
$albumRes = mysqli_query($conn, $albumQuery);

if (!$albumRes || mysqli_num_rows($albumRes) === 0) {
    header("Location: albums.php");
    exit;
}

$album = mysqli_fetch_assoc($albumRes);

// PAGINATION SETUP
$perPage = 12;
$currentPage = isset($_GET['page']) && ctype_digit($_GET['page']) && (int)$_GET['page'] > 0
    ? (int)$_GET['page']
    : 1;

$totalPhotosQuery = "
    SELECT COUNT(*) AS total 
    FROM photos 
    WHERE album_id = $albumId
";
$totalPhotosRes = mysqli_query($conn, $totalPhotosQuery);
$totalPhotosRow = mysqli_fetch_assoc($totalPhotosRes);
$totalPhotos = (int)($totalPhotosRow['total'] ?? 0);

$totalPages = $totalPhotos > 0 ? (int)ceil($totalPhotos / $perPage) : 1;

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset = ($currentPage - 1) * $perPage;

// AMBIL FOTO DALAM ALBUM
$photosQuery = "
    SELECT 
        photos.*
    FROM photos
    WHERE photos.album_id = $albumId
    ORDER BY photos.created_at DESC
    LIMIT $perPage OFFSET $offset
";
$photosRes = mysqli_query($conn, $photosQuery);

// URL HALAMAN PAGINATION
function albumDetailPageUrl($albumId, $page)
{
    return "album_detail.php?id=" . $albumId . "&page=" . $page;
}
?>


<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/user/albums/album_detail.css?v=<?= time() ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <header class="albums-header album-detail-header">
        <a href="albums.php" class="back-link">
            <i data-feather="arrow-left"></i> Kembali ke daftar album
        </a>

        <h1 class="page-title">
            <i data-feather="folder"></i>
            <?= htmlspecialchars($album['title']) ?>
        </h1>

        <p class="page-sub">
            <?= !empty($album['description']) ? htmlspecialchars($album['description']) : 'Kumpulan foto di dalam album ini.' ?>
        </p>

        <div class="album-meta-header">
            <span>
                <i data-feather="user"></i>
                Oleh: <?= htmlspecialchars($album['owner_name'] ?? 'Tidak diketahui') ?>
            </span>
            <span>
                <i data-feather="clock"></i>
                Dibuat: <?= date("d M Y", strtotime($album['created_at'])) ?>
            </span>
            <span>
                <i data-feather="image"></i>
                Total foto: <?= $totalPhotos ?>
            </span>
        </div>
    </header>

    <?php if ($photosRes && mysqli_num_rows($photosRes) > 0): ?>
        <section class="photos-grid">
            <?php while ($photo = mysqli_fetch_assoc($photosRes)): ?>
                <div class="photo-card">
                    <div class="photo-cover">
                        <img 
                            src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($photo['file_path']) ?>" 
                            alt="<?= htmlspecialchars($photo['title'] ?? 'Foto') ?>"
                        >
                    </div>

                    <div class="photo-info">
                        <h3 class="photo-title" title="<?= htmlspecialchars($photo['title'] ?? 'Tanpa judul') ?>">
                            <?= htmlspecialchars($photo['title'] ?? 'Tanpa judul') ?>
                        </h3>

                        <div class="photo-meta">
                            <span class="photo-meta-left">
                                <i data-feather="clock"></i>
                                <?= date("d M Y", strtotime($photo['created_at'])) ?>
                            </span>
                        </div>

                        <?php if (!empty($photo['description'])): ?>
                            <p class="photo-desc">
                                <?= htmlspecialchars($photo['description']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php
                    $prevPage = $currentPage - 1;
                    $nextPage = $currentPage + 1;

                    $maxButtons = 3;
                    $start = max(1, $currentPage - 1);
                    $end = min($totalPages, $start + $maxButtons - 1);
                    if (($end - $start + 1) < $maxButtons) {
                        $start = max(1, $end - $maxButtons + 1);
                    }
                ?>

                <a
                    class="page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>"
                    href="<?= $currentPage <= 1 ? '#' : albumDetailPageUrl($albumId, $prevPage) ?>"
                >&laquo;</a>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a
                        class="page-btn <?= $i === $currentPage ? 'active' : '' ?>"
                        href="<?= albumDetailPageUrl($albumId, $i) ?>"
                    ><?= $i ?></a>
                <?php endfor; ?>

                <a
                    class="page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
                    href="<?= $currentPage >= $totalPages ? '#' : albumDetailPageUrl($albumId, $nextPage) ?>"
                >&raquo;</a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <section class="empty-state">
            <div class="empty-icon">
                <i data-feather="image"></i>
            </div>
            <h3>Belum ada foto di album ini</h3>
            <p>Album ini masih kosong. Nantikan foto-foto keren di sini.</p>
        </section>
    <?php endif; ?>

</div>
<?php require_once __DIR__ . "/../layout/footer.php"; ?>
<script> feather.replace(); </script>
