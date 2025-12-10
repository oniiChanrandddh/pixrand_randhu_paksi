<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";

if (!isset($_GET['id'])) exit("No file");

$photoId = intval($_GET['id']);
$userId = $_SESSION['user']['id'];

$q = mysqli_query($conn, "
    SELECT photos.file_path, albums.user_id, albums.visibility
    FROM photos
    JOIN albums ON albums.id = photos.album_id
    WHERE photos.id = $photoId
");

$photo = mysqli_fetch_assoc($q);

if (!$photo) exit("File not found");

if ($photo['visibility'] === 'private' && $photo['user_id'] != $userId) {
    exit("Forbidden");
}

$file = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/" . $photo['file_path'];


if (!file_exists($file)) exit("Missing");

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=" . basename($file));
header("Content-Length: " . filesize($file));
readfile($file);
exit;
