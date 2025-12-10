<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_SESSION['user']['id'])) {
    header("Location: /gallery_app/public/views/user/pages/albums.php?error=session");
    exit;
}

$user_id = intval($_SESSION['user']['id']);
$title = trim($_POST['title'] ?? "");
$description = trim($_POST['description'] ?? "");

if ($title === "") {
    header("Location: ../pages/add_priv_album.php?error=empty_title");
    exit;
}

$visibility = "private";

$stmt = $conn->prepare("
    INSERT INTO albums (user_id, title, description, visibility, created_at)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->bind_param("isss", $user_id, $title, $description, $visibility);
$stmt->execute();
$stmt->close();

header("Location: ../pages/albums.php?success=album_created");
exit;
