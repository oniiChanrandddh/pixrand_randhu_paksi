<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_POST['photo_id']) || !isset($_SESSION['user']['id'])) {
    header("Location: ../pages/photos.php?error=invalid");
    exit;
}

$photo_id = intval($_POST['photo_id']);
$user_id = intval($_SESSION['user']['id']);
$comment = trim($_POST['comment']);

if ($comment === "") {
    header("Location: ../pages/photo_detail.php?id=$photo_id&error=empty");
    exit;
}

$stmt = $conn->prepare("INSERT INTO comments (photo_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $photo_id, $user_id, $comment);
$stmt->execute();
$stmt->close();

header("Location: ../pages/photo_detail.php?id=$photo_id#comment-box");
exit;
