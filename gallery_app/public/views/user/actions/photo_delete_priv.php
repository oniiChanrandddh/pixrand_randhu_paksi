<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

$user_id = $_SESSION['user']['id'];
$photo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$q = mysqli_query($conn, "
    SELECT photos.file_path 
    FROM photos 
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE photos.id='$photo_id' AND albums.user_id='$user_id' LIMIT 1
");

if (!$q || mysqli_num_rows($q) == 0) {
    header("Location: /gallery_app/public/views/user/pages/photos.php");
    exit;
}

$photo = mysqli_fetch_assoc($q);
$file = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/uploads/" . $photo['file_path'];
if (file_exists($file)) unlink($file);

mysqli_query($conn, "DELETE FROM comments WHERE photo_id='$photo_id'");
mysqli_query($conn, "DELETE FROM likes WHERE photo_id='$photo_id'");
mysqli_query($conn, "DELETE FROM photos WHERE id='$photo_id'");

header("Location: /gallery_app/public/views/user/pages/photos.php?deleted=1");
exit;
