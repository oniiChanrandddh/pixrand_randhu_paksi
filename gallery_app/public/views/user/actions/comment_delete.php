<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_GET['id']) || !isset($_SESSION['user']['id'])) {
    header("Location: ../pages/photos.php?error=invalid");
    exit;
}

$comment_id = intval($_GET['id']);
$user_id = intval($_SESSION['user']['id']);

$stmt = $conn->prepare("
    SELECT photo_id, user_id 
    FROM comments 
    WHERE id = ?
");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: ../pages/photos.php?error=notfound");
    exit;
}

$photo_id = intval($data['photo_id']);
$comment_owner = intval($data['user_id']);

if ($comment_owner !== $user_id && !isset($_SESSION['user']['is_admin'])) {
    header("Location: ../pages/photo_detail.php?id=$photo_id&denied=1");
    exit;
}

$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->close();

header("Location: ../pages/photo_detail.php?id=$photo_id&deleted=1#comment-box");
exit;
