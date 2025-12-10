<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../pages/photos.php?error=session");
    exit;
}

if (!isset($_POST['photo_id'])) {
    header("Location: ../pages/photos.php?error=invalid");
    exit;
}

$user_id = intval($_SESSION['user']['id']);
$photo_id = intval($_POST['photo_id']);

$check = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND photo_id = ?");
$check->bind_param("ii", $user_id, $photo_id);
$check->execute();
$result = $check->get_result();
$liked = $result->num_rows > 0;
$check->close();

if ($liked) {
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND photo_id = ?");
    $stmt->bind_param("ii", $user_id, $photo_id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO likes (user_id, photo_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $photo_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../pages/photo_detail.php?id=" . $photo_id);
exit;
