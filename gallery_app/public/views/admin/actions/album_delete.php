<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if (!isset($_GET['id'])) {
    header("Location: albums.php");
    exit;
}

$id = intval($_GET['id']);

mysqli_query($conn, "DELETE FROM photos WHERE album_id = $id");

mysqli_query($conn, "DELETE FROM albums WHERE id = $id");

header("Location: ../pages/albums.php?deleted=1");
exit;

