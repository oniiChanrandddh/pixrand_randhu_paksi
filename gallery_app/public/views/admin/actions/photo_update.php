<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_POST['id'])) {
    header("Location: ../pages/photos.php?error=invalid");
    exit;
}

$id = intval($_POST['id']);
$title = trim($_POST['title']);
$caption = trim($_POST['caption']);
$album_id = intval($_POST['album_id']);

if ($title === "" || !$album_id) {
    header("Location: ../pages/photos.php?error=invalid_input");
    exit;
}

$stmt = $conn->prepare("SELECT file_path FROM photos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$photoData = $result->fetch_assoc();
$stmt->close();

if (!$photoData) {
    header("Location: ../pages/photos.php?error=notfound");
    exit;
}

$oldFile = $photoData['file_path'];
$newFileName = $oldFile;

if (!empty($_FILES['photo']['name'])) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowedExt = ["jpg", "jpeg", "png"];
    
    if (!in_array($ext, $allowedExt)) {
        header("Location: ../pages/photos.php?error=format");
        exit;
    }

    $newFileName = time() . "_" . uniqid() . "." . $ext;
    $uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/" . $newFileName;
    move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath);

    if ($oldFile) {
        $oldPath = $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/public/uploads/" . $oldFile;
        if (file_exists($oldPath)) unlink($oldPath);
    }
}

$stmt = $conn->prepare("
    UPDATE photos 
    SET title=?, caption=?, album_id=?, file_path=? 
    WHERE id=?
");
$stmt->bind_param("ssisi", $title, $caption, $album_id, $newFileName, $id);
$stmt->execute();
$stmt->close();

header("Location: ../pages/photos.php?updated=1");
exit;
