<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_SESSION['user']['id'])) {
    header("Location: /gallery_app/public/views/user/pages/photos.php?error=session");
    exit;
}

$userId = intval($_SESSION['user']['id']);
$albumId = intval($_POST['album_id'] ?? 0);
$caption = trim($_POST['caption'] ?? "");
$title = trim($_POST['title'] ?? "");
if ($title === "") $title = "Tanpa Judul";

if ($albumId <= 0) {
    header("Location: /gallery_app/public/views/user/pages/add_photo.php?error=no_album");
    exit;
}

$albumQuery = $conn->prepare("
    SELECT visibility, user_id 
    FROM albums 
    WHERE id = ? 
    LIMIT 1
");
$albumQuery->bind_param("i", $albumId);
$albumQuery->execute();
$albumResult = $albumQuery->get_result();

if ($albumResult->num_rows === 0) {
    header("Location: /gallery_app/public/views/user/pages/add_photo.php?error=album_invalid");
    exit;
}

$albumData = $albumResult->fetch_assoc();

if ($albumData['visibility'] === 'private' && intval($albumData['user_id']) !== $userId) {
    header("Location: /gallery_app/public/views/user/pages/add_photo.php?error=forbidden_album");
    exit;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== 0) {
    header("Location: /gallery_app/public/views/user/pages/add_photo.php?error=no_file");
    exit;
}

$allowedExt = ["jpg", "jpeg", "png"];
$ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExt)) {
    header("Location: /gallery_app/public/views/user/pages/add_photo.php?error=format");
    exit;
}

$fileName = time() . "_" . uniqid() . "." . $ext;
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/";
$uploadPath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
    header("Location: /gallery_app/public/views/user/pages/add_photo.php?error=upload_failed");
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO photos (album_id, user_id, file_path, title, caption, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("iisss", $albumId, $userId, $fileName, $title, $caption);
$stmt->execute();
$stmt->close();

header("Location: /gallery_app/public/views/user/pages/photos.php?success=1");
exit;
?>
