<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../pages/photos.php?error=invalid");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT file_path FROM photos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: ../pages/photos.php?error=notfound");
    exit;
}

$file_path = $data['file_path'];
$full_path = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/" . $file_path;

if ($file_path && file_exists($full_path)) {
    unlink($full_path);
}

$stmt = $conn->prepare("DELETE FROM comments WHERE photo_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM likes WHERE photo_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM photos WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../pages/photos.php?success=1");
    exit;
} else {
    $stmt->close();
    header("Location: ../pages/photos.php?error=1");
    exit;
}
