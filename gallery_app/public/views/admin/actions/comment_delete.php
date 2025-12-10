<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../pages/comments.php?error=invalid");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../pages/comments.php?success=1");
    exit;
} else {
    $stmt->close();
    header("Location: ../pages/comments.php?error=1");
    exit;
}
