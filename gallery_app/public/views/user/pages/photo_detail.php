<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/user_guard.php";
require_once __DIR__ . "/../layout/navbar.php";

if (!isset($_GET['id'])) {
    header("Location: photos.php");
    exit;
}

$photoId = intval($_GET['id']);
$userId = $_SESSION['user']['id'];

$q = mysqli_query($conn, "
    SELECT photos.*, 
           albums.title AS album_title,
           albums.visibility,
           albums.user_id AS owner_id,
           (SELECT COUNT(*) FROM likes WHERE likes.photo_id = photos.id) AS total_likes,
           (SELECT COUNT(*) FROM comments WHERE comments.photo_id = photos.id) AS total_comments,
           (SELECT COUNT(*) FROM likes WHERE likes.photo_id = photos.id AND likes.user_id = $userId) AS user_liked
    FROM photos
    LEFT JOIN albums ON albums.id = photos.album_id
    WHERE photos.id = $photoId
");

$photo = mysqli_fetch_assoc($q);

if (!$photo) {
    header("Location: photos.php");
    exit;
}

if ($photo['visibility'] === 'private' && $photo['owner_id'] != $userId) {
    header("Location: photos.php?error=forbidden");
    exit;
}

$commentsQ = mysqli_query($conn, "
    SELECT comments.*, users.full_name 
    FROM comments
    LEFT JOIN users ON users.id = comments.user_id
    WHERE comments.photo_id = $photoId
    ORDER BY comments.created_at DESC
");
?>


<link rel="stylesheet" href="<?= BASE_URL ?>assets/styles/user/photos/photo_detail.css?v=<?= time() ?>">
<script src="https://unpkg.com/feather-icons"></script>

<div class="page-wrapper">

    <div class="photo-detail-container">

        <div class="photo-detail-image">
            <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($photo['file_path']) ?>" alt="">
        </div>

        <div class="photo-detail-info">

            <h2 class="photo-title"><?= htmlspecialchars($photo['title']) ?></h2>

            <p class="album-label">Album: <?= htmlspecialchars($photo['album_title']) ?></p>

            <p class="photo-caption"><?= $photo['caption'] ? htmlspecialchars($photo['caption']) : "Tidak ada deskripsi" ?></p>

            <div class="detail-actions">

                <!-- LIKE -->
                <form action="../actions/like_toggle.php" method="POST" class="action-form">
                    <input type="hidden" name="photo_id" value="<?= $photoId ?>">
                    <button type="submit" class="btn-action <?= $photo['user_liked'] ? 'liked' : '' ?>">
                        <i data-feather="heart"></i>
                        <span><?= $photo['total_likes'] ?></span>
                    </button>
                </form>

                <!-- COMMENT -->
                <form action="#comment-box" class="action-form">
                    <button type="submit" class="btn-action">
                        <i data-feather="message-circle"></i>
                        <span><?= $photo['total_comments'] ?></span>
                    </button>
                </form>

                <!-- DOWNLOAD BARU -->
                <form action="../actions/download.php" method="GET" class="action-form">
                    <input type="hidden" name="id" value="<?= $photoId ?>">
                    <button type="submit" class="btn-action">
                        <i data-feather="download"></i>
                        <span>Download</span>
                    </button>
                </form>

            </div>



            <!-- COMMENT SECTION -->
            <div class="comment-section" id="comment-box">

                <h4 class="comment-title">Komentar</h4>

                <?php if (mysqli_num_rows($commentsQ) == 0): ?>
                    <p class="no-comment">Belum ada komentar.</p>
                <?php endif; ?>

                <?php while ($c = mysqli_fetch_assoc($commentsQ)): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <strong><?= htmlspecialchars($c['full_name']) ?></strong>
                            <span class="comment-date"><?= date("d M Y", strtotime($c['created_at'])) ?></span>

                            <?php if ($c['user_id'] == $userId): ?>
                                <a href="../actions/comment_delete.php?id=<?= $c['id'] ?>&photo=<?= $photoId ?>" class="delete-comment-btn">
                                    <i data-feather="trash-2"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <p class="comment-text"><?= htmlspecialchars($c['comment']) ?></p>
                    </div>
                <?php endwhile; ?>

                <form action="../actions/comment_create.php" method="POST" class="comment-form">
                    <input type="hidden" name="photo_id" value="<?= $photoId ?>">
                    <textarea name="comment" class="comment-input" placeholder="Tulis komentar..." required></textarea>
                    <button type="submit" class="btn-comment-send">Kirim</button>
                </form>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>

<script>
feather.replace();

const commentButton = document.querySelector(".detail-actions form:nth-child(2) button");
const commentInput = document.querySelector(".comment-input");
const commentBox = document.querySelector("#comment-box");

if (commentButton && commentInput) {
    commentButton.addEventListener("click", function(e) {
        e.preventDefault();
        commentBox.scrollIntoView({ behavior: "smooth", block: "center" });
        setTimeout(() => {
            commentInput.focus();
        }, 350);
    });
}
</script>
