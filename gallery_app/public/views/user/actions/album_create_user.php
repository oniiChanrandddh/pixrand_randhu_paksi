<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

$userId = intval($_POST['user_id']);
$title = mysqli_real_escape_string($conn, trim($_POST['title']));
$description = mysqli_real_escape_string($conn, trim($_POST['description']));
$visibility = 'private';

if ($title === "") {
    header("Location: ../views/user/add_priv_album.php?error=1");
    exit;
}

$query = "
    INSERT INTO albums (user_id, title, description, visibility)
    VALUES ($userId, '$title', '$description', '$visibility')
";

mysqli_query($conn, $query);
header("Location: ../views/user/albums.php?success=1");
exit;
