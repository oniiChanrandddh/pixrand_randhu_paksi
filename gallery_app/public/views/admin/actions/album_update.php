<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_POST['id'])) {
    header("Location: ../pages/albums.php");
    exit;
}

$id = intval($_POST['id']);
$title = trim($_POST['title']);
$description = trim($_POST['description']);

if ($title === "") {
    header("Location: ../pages/edit_album.php?id=$id&error=empty");
    exit;
}

$stmt = $conn->prepare("UPDATE albums SET title=?, description=? WHERE id=?");
$stmt->bind_param("ssi", $title, $description, $id);
$stmt->execute();
$stmt->close();

header("Location: ../pages/albums.php?updated=1");
exit;
