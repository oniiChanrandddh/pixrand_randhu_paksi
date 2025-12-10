<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";

$totalPhotos = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM photos 
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
"))['total'];

$totalAlbums = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM albums
    WHERE visibility = 'public'
"))['total'];

$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$where = [];

if ($search !== "") {
    $s = mysqli_real_escape_string($conn, $search);
    $where[] = "(photos.title LIKE '%$s%' OR photos.caption LIKE '%$s%' OR albums.title LIKE '%$s%')";
}

$where[] = "albums.visibility = 'public'";

$whereSql = "WHERE " . implode(" AND ", $where);

$query = mysqli_query($conn, "
    SELECT photos.*, albums.title AS album_title
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    $whereSql
    ORDER BY photos.created_at DESC
");
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/photos/photos.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <h1 class="title-main">Kelola Foto Publik</h1>
    <p class="subtitle-main">Admin hanya dapat mengelola foto yang berada di album publik</p>

    <div class="stats-grid">
        <div class="stat-card">
            <i data-feather="image"></i>
            <div>
                <h2><?= $totalPhotos ?></h2><span>Total Public Photos</span>
            </div>
        </div>
        <div class="stat-card">
            <i data-feather="book"></i>
            <div>
                <h2><?= $totalAlbums ?></h2><span>Total Public Albums</span>
            </div>
        </div>
    </div>

    <div class="panel">

        <form class="actions-bar" method="GET">
            <a href="add_photo.php" class="btn-primary">
                <i data-feather="plus-circle"></i> Tambah Foto
            </a>

            <div class="search-form">
                <input type="text" name="q" placeholder="Cari foto atau album..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i data-feather="search"></i></button>
            </div>
        </form>

        <?php if ($query && mysqli_num_rows($query) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Preview</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Album</th>
                        <th>Diupload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($p = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++ ?></td>

                            <td class="preview-cell">
                                <?php
                                $filePath = "/gallery_app/public/uploads/" . htmlspecialchars($p['file_path']);
                                $realPath = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/" . $p['file_path'];
                                ?>
                                <?php if (!empty($p['file_path']) && file_exists($realPath)): ?>
                                    <img src="<?= $filePath ?>" class="thumb">
                                <?php else: ?>
                                    <div class="no-thumb">
                                        <i data-feather="camera-off"></i>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td><?= $p['title'] ? htmlspecialchars($p['title']) : "<em>Tanpa Judul</em>" ?></td>
                            <td><?= $p['caption'] ? htmlspecialchars($p['caption']) : "<em>-</em>" ?></td>

                            <td><?= htmlspecialchars($p['album_title']) ?></td>

                            <td><?= date("d M Y", strtotime($p['created_at'])) ?></td>

                            <td class="center">
                                <div class="action-buttons">
                                    <a href="edit_photo.php?id=<?= $p['id'] ?>" class="btn-edit">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                    <a href="../actions/photo_delete.php?id=<?= $p['id'] ?>"
                                        class="btn-delete delete-photo"
                                        data-id="<?= $p['id'] ?>">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="empty">Tidak ada foto publik ditemukan.</p>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-photo').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');

            Swal.fire({
                title: 'Hapus Foto?',
                text: "Foto akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#160626',
                color: '#fff'
            }).then(res => {
                if (res.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    feather.replace();
</script>
