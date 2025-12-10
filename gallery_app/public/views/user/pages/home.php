<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";

if (!isset($_SESSION['user']) && isset($_SESSION['user_id'])) {
    $_SESSION['user'] = [
        'id'        => $_SESSION['user_id'],
        'username'  => $_SESSION['username'] ?? 'user',
        'full_name' => $_SESSION['full_name'] ?? ($_SESSION['username'] ?? 'user')
    ];
}

$userId   = (int) ($_SESSION['user']['id'] ?? 0);
$userName = htmlspecialchars($_SESSION['user']['full_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8');
$userUsername = htmlspecialchars($_SESSION['user']['username'] ?? 'user', ENT_QUOTES, 'UTF-8');

if ($userId <= 0) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$rowPhotos = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM photos WHERE user_id = $userId
"));
$totalPhotos = (int)($rowPhotos['total'] ?? 0);

$rowLikes = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM likes
    JOIN photos ON likes.photo_id = photos.id
    WHERE photos.user_id = $userId
"));
$totalLikes = (int)($rowLikes['total'] ?? 0);

$lastPhoto = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id, file_path, caption, created_at
    FROM photos
    WHERE user_id = $userId
    ORDER BY created_at DESC
    LIMIT 1
"));

$lastUploadText = $lastPhoto
    ? date("d M Y", strtotime($lastPhoto['created_at']))
    : "-";

$topGlobalRes = mysqli_query($conn, "
    SELECT 
        photos.id,
        photos.caption,
        COUNT(DISTINCT likes.id) AS total_likes,
        COUNT(DISTINCT comments.id) AS total_comments,
        (COUNT(DISTINCT likes.id) * 2 + COUNT(DISTINCT comments.id)) AS score
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    LEFT JOIN likes ON likes.photo_id = photos.id
    LEFT JOIN comments ON comments.photo_id = photos.id
    WHERE albums.visibility = 'public'
    GROUP BY photos.id
    HAVING score > 0
    ORDER BY score DESC, photos.created_at DESC
    LIMIT 6
");

$latestGlobalRes = mysqli_query($conn, "
    SELECT photos.id, photos.file_path, photos.caption
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE albums.visibility = 'public'
    ORDER BY photos.created_at DESC
    LIMIT 15
");

$quotes = [
    "Fotografi adalah seni membekukan waktu.",
    "Setiap foto menyimpan cerita yang tidak terucap.",
    "Cahaya menciptakan keajaiban dalam setiap bingkai.",
    "Bukan hanya memotret, tapi menangkap perasaan.",
    "Foto membuat detik kecil terasa abadi.",
    "Lihatlah dunia dari sudut pandang baru.",
    "Warna adalah emosi, biarkan ia berbicara.",
    "Setiap foto punya suara yang diam.",
    "Bidik dengan hati, bukan hanya mata.",
    "Momen kecil adalah karya besar."
];
$quote = $quotes[array_rand($quotes)];
?>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/user/dashboard/dashboard.css?v=<?= time(); ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper user-dashboard">

    <section class="hero-card">
        <div class="hero-left">
            <h1 class="hero-title">Selamat datang di Pixrand!</h1>
            <p class="hero-sub">
                Bagikan momen terbaikmu dan jelajahi karya visual keren di Pixrand.
            </p>

            <div class="hero-user-chip">
                <div class="chip-icon">
                    <i data-feather="user"></i>
                </div>
                <div class="chip-text">
                    <div class="chip-name"><?= $userName ?></div>
                    <div class="chip-username">@<?= $userUsername ?></div>
                </div>
            </div>

            <div class="hero-actions">
                <a href="<?= BASE_URL ?>views/user/pages/add_photo.php" class="btn-secondary">
                    <i data-feather="upload-cloud"></i>
                    <span>Upload Foto</span>
                </a>
                <a href="<?= BASE_URL ?>views/user/pages/photos.php" class="btn-secondary">
                    <i data-feather="grid"></i>
                    <span>Lihat Semua Foto</span>
                </a>
            </div>
        </div>

        <div class="hero-right">
            <div class="hero-stat">
                <div class="hero-stat-value"><?= $totalPhotos ?></div>
                <div class="hero-stat-label">Foto Kamu</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value"><?= $totalLikes ?></div>
                <div class="hero-stat-label">Total Likes</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value"><?= $lastUploadText ?></div>
                <div class="hero-stat-label">Upload Terakhir</div>
            </div>
        </div>
    </section>

    <div class="main-grid">
        <div class="col-left">
            <section class="panel">
                <h3 class="panel-title"><i data-feather="camera"></i> Foto Terakhir Kamu</h3>

                <?php if ($lastPhoto): ?>
                    <div class="last-photo">
                        <div class="last-photo-thumb">
                            <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($lastPhoto['file_path']) ?>" alt="">
                        </div>
                        <div class="last-photo-info">
                            <div class="last-photo-caption">
                                <?= htmlspecialchars(mb_strimwidth($lastPhoto['caption'] ?: "Tanpa caption", 0, 80, "...")) ?>
                            </div>
                            <div class="last-photo-meta">
                                Diunggah pada <?= date("d M Y, H:i", strtotime($lastPhoto['created_at'])) ?>
                            </div>
                            <a href="<?= BASE_URL ?>views/user/pages/photo_detail.php?id=<?= $lastPhoto['id'] ?>" class="panel-link">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="empty">
                        Belum ada foto kamu.
                        <a href="<?= BASE_URL ?>views/user/pages/add_photo.php">Upload sekarang</a>
                    </p>
                <?php endif; ?>
            </section>
        </div>

        <div class="col-right">
            <section class="panel">
                <h3 class="panel-title"><i data-feather="award"></i> Foto Terpopuler</h3>

                <?php if ($topGlobalRes && mysqli_num_rows($topGlobalRes) > 0): ?>
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th class="center">Likes</th>
                                <th class="center">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($r = mysqli_fetch_assoc($topGlobalRes)): ?>
                            <tr>
                                <td>
                                    <a href="<?= BASE_URL ?>views/user/pages/photo_detail.php?id=<?= $r['id'] ?>">
                                        <?= htmlspecialchars(mb_strimwidth($r['caption'] ?: "Tanpa caption", 0, 40, "...")) ?>
                                    </a>
                                </td>
                                <td class="center"><?= (int)$r['total_likes'] ?></td>
                                <td class="center"><?= (int)$r['total_comments'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty">Belum ada foto populer dari album publik.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <section class="panel">
        <h3 class="panel-title"><i data-feather="map"></i> Ayo Jelajahi Foto-Foto di Pixrand</h3>

        <?php if ($latestGlobalRes && mysqli_num_rows($latestGlobalRes) > 0): ?>
            <div class="masonry-grid">
                <?php while ($p = mysqli_fetch_assoc($latestGlobalRes)): ?>
                    <a class="masonry-card" href="<?= BASE_URL ?>views/user/pages/photo_detail.php?id=<?= $p['id'] ?>">
                        <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($p['file_path']) ?>" alt="">
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="empty">Belum ada foto terbaru dari album publik.</p>
        <?php endif; ?>
    </section>

    <section class="panel small">
        <h3 class="panel-title"><i data-feather="align-left"></i> Kata Hari Ini</h3>
        <p class="quote">“<?= htmlspecialchars($quote) ?>”</p>
    </section>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/views/user/layout/footer.php"; ?>

<script>
feather.replace();
</script>
