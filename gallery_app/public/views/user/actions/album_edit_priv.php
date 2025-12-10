<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

$user_id = $_SESSION['user']['id'];
$album_id = isset($_POST['album_id']) ? intval($_POST['album_id']) : 0;

$q = mysqli_query($conn, "SELECT id FROM albums WHERE id='$album_id' AND user_id='$user_id' LIMIT 1");
if (!$q || mysqli_num_rows($q) == 0) {
    header("Location: /gallery_app/public/views/user/pages/albums.php");
    exit;
}

$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$visibility = "private";

mysqli_query($conn, "
    UPDATE albums
    SET title='$title', description='$description', visibility='$visibility'
    WHERE id='$album_id' AND user_id='$user_id'
");

header("Location: /gallery_app/public/views/user/pages/albums.php?updated=1");
exit;
