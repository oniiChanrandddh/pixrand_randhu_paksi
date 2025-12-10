<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../views/user/photos.php?error=session");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../views/user/photos.php?error=invalid_id");
    exit;
}

$photoId = intval($_GET['id']);
$userId = intval($_SESSION['user']['id']);

$q = mysqli_query($conn, "SELECT file_path FROM photos WHERE id = $photoId AND user_id = $userId");
$p = mysqli_fetch_assoc($q);

if (!$p) {
    header("Location: ../views/user/photos.php?error=notfound");
    exit;
}

$file = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/" . $p['file_path'];
if (file_exists($file)) unlink($file);

mysqli_query($conn, "DELETE FROM likes WHERE photo_id=$photoId");
mysqli_query($conn, "DELETE FROM comments WHERE photo_id=$photoId");
mysqli_query($conn, "DELETE FROM photos WHERE id=$photoId AND user_id=$userId");

header("Location: ../views/user/photos.php?deleted=1");
exit;
