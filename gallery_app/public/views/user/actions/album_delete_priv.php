<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

$user_id = $_SESSION['user']['id'];
$album_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$q = mysqli_query($conn, "SELECT id FROM albums WHERE id='$album_id' AND user_id='$user_id' LIMIT 1");
if (!$q || mysqli_num_rows($q) == 0) {
    header("Location: /gallery_app/public/views/user/pages/albums.php");
    exit;
}

mysqli_query($conn, "DELETE FROM photos WHERE album_id='$album_id'");
mysqli_query($conn, "DELETE FROM albums WHERE id='$album_id' AND user_id='$user_id'");

header("Location: /gallery_app/public/views/user/pages/albums.php?deleted=1");
exit;
