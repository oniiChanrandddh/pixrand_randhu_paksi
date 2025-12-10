<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

$user_id = $_SESSION['user']['id'];
$photo_id = isset($_POST['photo_id']) ? intval($_POST['photo_id']) : 0;

$q = mysqli_query($conn, "
    SELECT photos.id 
    FROM photos
    INNER JOIN albums ON albums.id = photos.album_id
    WHERE photos.id='$photo_id' AND albums.user_id='$user_id' LIMIT 1
");

if (!$q || mysqli_num_rows($q) == 0) {
    header("Location: /gallery_app/public/views/user/pages/photos.php");
    exit;
}

$title = mysqli_real_escape_string($conn, $_POST['title']);
$caption = isset($_POST['caption']) ? mysqli_real_escape_string($conn, $_POST['caption']) : '';

mysqli_query($conn, "
    UPDATE photos 
    SET title='$title', caption='$caption'
    WHERE id='$photo_id'
");

header("Location: /gallery_app/public/views/user/pages/photos.php?updated=1");
exit;
