<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";

$totalAlbums = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM albums 
    WHERE visibility = 'public'
"))['total'];

$totalPhotos = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM photos 
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
"))['total'];

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$escaped = mysqli_real_escape_string($conn, $search);

$searchQuery = $search 
    ? " AND albums.title LIKE '%$escaped%' " 
    : "";

$albumsQuery = mysqli_query($conn, "
    SELECT albums.*, 
        (SELECT COUNT(*) FROM photos 
         WHERE photos.album_id = albums.id) AS photo_count
    FROM albums
    WHERE albums.visibility = 'public'
    $searchQuery
    ORDER BY albums.created_at DESC
");
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/albums/albums.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <div class="header-section">
        <h1 class="title-main">Kelola Albums (Publik)</h1>
        <p class="subtitle-main">Hanya album publik yang dapat dikelola admin</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i data-feather="layers"></i>
            <div>
                <h2><?= $totalAlbums ?></h2>
                <span>Total Public Albums</span>
            </div>
        </div>

        <div class="stat-card">
            <i data-feather="image"></i>
            <div>
                <h2><?= $totalPhotos ?></h2>
                <span>Total Public Photos</span>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="actions-bar">
            <a href="add_album.php" class="btn-primary">
                <i data-feather="plus-circle"></i> Tambah Album
            </a>

            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari album..."
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i data-feather="search"></i></button>
            </form>
        </div>

        <?php if (mysqli_num_rows($albumsQuery) == 0): ?>
            <p class="empty">Tidak ada album publik ditemukan.</p>

        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Album</th>
                        <th>Jumlah Foto</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    while ($a = mysqli_fetch_assoc($albumsQuery)) : ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($a['title']) ?></td>
                            <td><?= $a['photo_count'] ?></td>
                            <td><?= date("d M Y", strtotime($a['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_album.php?id=<?= $a['id'] ?>" class="btn-edit">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                    <a href="../actions/album_delete.php?id=<?= $a['id'] ?>"
                                        class="btn-delete delete-album" data-id="<?= $a['id'] ?>">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-album').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const albumId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Hapus Album?',
                text: "Semua foto publik di dalam album ini juga akan terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#160626',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `../actions/album_delete.php?id=${albumId}`;
                }
            });
        });
    });

    feather.replace();
</script>
