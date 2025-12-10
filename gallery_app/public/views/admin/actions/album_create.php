<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_POST['title']) || trim($_POST['title']) === "") {
    header("Location: ../pages/add_album.php?error=empty");
    exit;
}

$title = trim($_POST['title']);
$description = trim($_POST['description']);
$user_id = intval($_SESSION['user_id']); // FIXED SESSION SOURCE
$visibility = "public"; // ALWAYS PUBLIC FOR ADMIN

$stmt = $conn->prepare("
    INSERT INTO albums (user_id, title, description, visibility)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("isss", $user_id, $title, $description, $visibility);

if ($stmt->execute()) {
    header("Location: ../pages/albums.php?success=1");
} else {
    header("Location: ../pages/add_album.php?error=db&msg=" . urlencode($stmt->error));
}

$stmt->close();
exit;
