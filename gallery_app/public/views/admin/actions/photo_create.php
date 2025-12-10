<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

$user_id = null;
if (isset($_SESSION['user']['id'])) {
    $user_id = intval($_SESSION['user']['id']);
} elseif (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
}

if (!$user_id) {
    header("Location: ../pages/photos.php?error=session");
    exit;
}

$title = isset($_POST['title']) ? trim($_POST['title']) : "";
$album_id = isset($_POST['album_id']) ? intval($_POST['album_id']) : 0;
$caption = isset($_POST['caption']) ? trim($_POST['caption']) : "";

if ($title === "") {
    $title = "Tanpa Judul";
}

if ($album_id <= 0) {
    header("Location: ../pages/add_photo.php?error=no_album");
    exit;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../pages/add_photo.php?error=no_file");
    exit;
}

$ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
$allowedExt = ["jpg", "jpeg", "png"];

if (!in_array($ext, $allowedExt)) {
    header("Location: ../pages/add_photo.php?error=format");
    exit;
}

$fileName = time() . "_" . uniqid() . "." . $ext;
$uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/" . $fileName;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
    header("Location: ../pages/add_photo.php?error=upload_failed");
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO photos (album_id, user_id, file_path, title, caption, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("iisss", $album_id, $user_id, $fileName, $title, $caption);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../pages/photos.php?success=1");
    exit;
} else {
    die("INSERT ERROR: " . $stmt->error);
}
