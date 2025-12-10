<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";
require_once __DIR__ . "/../layout/sidebar.php";

$totalUsers = $pdo->query("
    SELECT COUNT(*) 
    FROM users
    WHERE role = 'user'
")->fetchColumn();

$totalAlbums = $pdo->query("
    SELECT COUNT(*) 
    FROM albums
    WHERE visibility = 'public'
")->fetchColumn();

$totalPhotos = $pdo->query("
    SELECT COUNT(*) 
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
")->fetchColumn();

$totalLikes = $pdo->query("
    SELECT COUNT(*)
    FROM likes
    INNER JOIN photos ON photos.id = likes.photo_id
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
")->fetchColumn();

$topAlbums = $pdo->query("
    SELECT albums.title, COUNT(photos.id) AS total
    FROM albums
    LEFT JOIN photos 
        ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
    GROUP BY albums.id
    ORDER BY total DESC
    LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);

$topAlbumsLabels = array_column($topAlbums, 'title');
$topAlbumsData   = array_map('intval', array_column($topAlbums, 'total'));

$dist = $pdo->query("
    SELECT albums.title, COUNT(photos.id) AS total
    FROM albums
    LEFT JOIN photos 
        ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
    GROUP BY albums.id
")->fetchAll(PDO::FETCH_ASSOC);

$distLabels = array_column($dist, 'title');
$distData   = array_map('intval', array_column($dist, 'total'));

$recentPhotos = $pdo->query("
    SELECT 
        photos.file_path, 
        photos.caption, 
        photos.created_at, 
        albums.title AS album
    FROM photos
    INNER JOIN albums ON photos.album_id = albums.id
    WHERE albums.visibility = 'public'
    ORDER BY photos.created_at DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$topLikers = $pdo->query("
    SELECT users.username, COUNT(likes.id) AS likes
    FROM users
    LEFT JOIN likes 
        ON users.id = likes.user_id
    LEFT JOIN photos 
        ON photos.id = likes.photo_id
    LEFT JOIN albums 
        ON albums.id = photos.album_id
    WHERE users.role = 'user'
      AND albums.visibility = 'public'
    GROUP BY users.id
    ORDER BY likes DESC
    LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/admin/dashboard/dashboard.css?v=<?= time(); ?>">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <div class="header-section">
        <h1 class="title-main">Dashboard Admin</h1>
        <p class="subtitle-main">Pemantauan aktivitas galeri & pengguna secara real-time (hanya konten publik)</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i data-feather="users"></i>
            <div>
                <h2><?= $totalUsers ?></h2>
                <span>Total Users</span>
            </div>
        </div>
        <div class="stat-card">
            <i data-feather="book"></i>
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
        <div class="stat-card">
            <i data-feather="heart"></i>
            <div>
                <h2><?= $totalLikes ?></h2>
                <span>Total Likes on Public Photos</span>
            </div>
        </div>
    </div>

    <div class="grid-charts">
        <div class="panel">
            <h3><i data-feather="bar-chart"></i> Album Publik dengan Foto Terbanyak</h3>
            <canvas id="albumChart"></canvas>
        </div>

        <div class="panel">
            <h3><i data-feather="pie-chart"></i> Distribusi Foto per Album Publik</h3>
            <?php if (empty($distData) || array_sum($distData) == 0): ?>
                <p class="empty">Belum ada data publik untuk ditampilkan.</p>
            <?php else: ?>
                <canvas id="distChart"></canvas>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel full">
        <h3><i data-feather="clock"></i> Foto Terbaru (Publik)</h3>
        <?php if (empty($recentPhotos)): ?>
            <p class="empty">Belum ada foto publik yang diupload.</p>
        <?php else: ?>
            <div class="recent-photos">
                <?php foreach ($recentPhotos as $photo): ?>
                    <div class="recent-card">
                        <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($photo['file_path']); ?>" alt="Recent photo">
                        <div class="recent-meta">
                            <p><?= htmlspecialchars($photo['caption']); ?></p>
                            <small>
                                <?= date("d M Y", strtotime($photo['created_at'])) ?>
                                — <?= htmlspecialchars($photo['album']); ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="panel full">
        <h3><i data-feather="thumbs-up"></i> Top Pemberi Like (di Foto Publik)</h3>
        <?php if (empty($topLikers)): ?>
            <p class="empty">Belum ada aktivitas like pada foto publik.</p>
        <?php else: ?>
            <table class="mini-table">
                <thead>
                    <tr>
                        <th class="center">Username</th>
                        <th class="center">Likes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topLikers as $l): ?>
                        <tr>
                            <td class="center"><?= htmlspecialchars($l['username']) ?></td>
                            <td class="center"><?= $l['likes'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

<script>
feather.replace();

new Chart(document.getElementById("albumChart"), {
    type: "bar",
    data: {
        labels: <?= json_encode($topAlbumsLabels) ?>,
        datasets: [{
            data: <?= json_encode($topAlbumsData) ?>,
            backgroundColor: "#9333ea"
        }]
    },
    options: {
        plugins: { legend: { display: false }},
        scales: {
            x: { ticks: { color: "#ffffff" }},
            y: { ticks: { color: "#ffffff", precision: 0 }}
        }
    }
});

<?php if (!empty($distData) && array_sum($distData) > 0): ?>
new Chart(document.getElementById("distChart"), {
    type: "doughnut",
    data: {
        labels: <?= json_encode($distLabels) ?>,
        datasets: [{
            data: <?= json_encode($distData) ?>,
            backgroundColor: [
                "#9333ea", "#a855f7", "#c084fc",
                "#6b21a8", "#5b21b6", "#7e22ce",
                "#d946ef", "#a21caf"
            ],
            borderWidth: 0,
            hoverOffset: 12
        }]
    },
    options: {
        aspectRatio: 1,
        plugins: {
            legend: {
                position: "bottom",
                labels: {
                    color: "#fff",
                    font: { size: 13 }
                }
            }
        }
    }
});
<?php endif; ?>
</script>
