<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";

$totalComments = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM comments
    INNER JOIN photos ON photos.id = comments.photo_id
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
"))['total'];

$totalPhotos = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
"))['total'];

$searchComment = isset($_GET['comment']) ? trim($_GET['comment']) : "";
$where = [];

if ($searchComment !== "") {
    $s = mysqli_real_escape_string($conn, $searchComment);
    $where[] = "comments.comment LIKE '%$s%'";
}

$where[] = "albums.visibility = 'public'";

$whereSql = "WHERE " . implode(" AND ", $where);

$query = mysqli_query($conn, "
    SELECT comments.*, photos.file_path, users.username
    FROM comments
    INNER JOIN photos ON comments.photo_id = photos.id
    INNER JOIN albums ON albums.id = photos.album_id
    LEFT JOIN users ON comments.user_id = users.id
    $whereSql
    ORDER BY comments.created_at DESC
");
?>

<link rel="stylesheet" href="/gallery_app/public/assets/styles/admin/photos/photos.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="page-wrapper">

    <h1 class="title-main">Kelola Komentar Publik</h1>
    <p class="subtitle-main">Admin hanya dapat mengelola komentar pada foto publik</p>

    <div class="stats-grid">
        <div class="stat-card">
            <i data-feather="message-circle"></i>
            <div><h2><?= $totalComments ?></h2><span>Total Public Comments</span></div>
        </div>
        <div class="stat-card">
            <i data-feather="image"></i>
            <div><h2><?= $totalPhotos ?></h2><span>Total Public Photos</span></div>
        </div>
    </div>

    <div class="panel">

        <form class="actions-bar" method="GET">
            <div class="search-form">
                <input type="text" name="comment"
                       placeholder="Cari komentar publik..."
                       value="<?= htmlspecialchars($searchComment) ?>">
                <button type="submit">
                    <i data-feather="search"></i>
                </button>
            </div>
        </form>

        <?php if (!$query || mysqli_num_rows($query) == 0): ?>
            <p class="empty">Tidak ada komentar publik ditemukan.</p>
        <?php else: ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th class="center">#</th>
                    <th>Foto</th>
                    <th>Komentar</th>
                    <th>Pengguna</th>
                    <th class="center">Waktu</th>
                    <th class="center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while($c = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td class="center"><?= $no++ ?></td>
                    <td class="preview-cell">
                        <?php if ($c['file_path']): ?>
                            <img src="/gallery_app/public/uploads/<?= $c['file_path'] ?>" class="thumb">
                        <?php else: ?>
                            <span class="empty">Foto Dihapus</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($c['comment']) ?></td>
                    <td><?= htmlspecialchars($c['username']) ?></td>
                    <td class="center"><?= date("d M Y", strtotime($c['created_at'])) ?></td>
                    <td class="center">
                        <div class="action-buttons">
                            <a href="../actions/comment_delete.php?id=<?= $c['id'] ?>"
                               class="btn-delete delete-comment-btn"
                               data-id="<?= $c['id'] ?>">
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

<script>
feather.replace();

document.querySelectorAll('.delete-comment-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.href;

        Swal.fire({
            title: 'Hapus Komentar?',
            text: 'Komentar ini akan dihapus permanen!',
            icon: 'warning',
            background: '#160626',
            color: '#ffffff',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
