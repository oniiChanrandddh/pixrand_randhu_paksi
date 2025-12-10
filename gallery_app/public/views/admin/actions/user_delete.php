<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../pages/users.php?error=invalid");
    exit;
}

$id = intval($_GET['id']);

if ($id == $_SESSION['user']['id']) {
    header("Location: ../pages/users.php?error=selfdelete");
    exit;
}

$stmt_select = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$result = $stmt_select->get_result();
$user = $result->fetch_assoc();
$stmt_select->close();

if (!$user) {
    header("Location: ../pages/users.php?error=notfound");
    exit;
}

$stmt_del_comments = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
$stmt_del_comments->bind_param("i", $id);
$stmt_del_comments->execute();
$stmt_del_comments->close();

$stmt_del_likes = $conn->prepare("DELETE FROM likes WHERE user_id = ?");
$stmt_del_likes->bind_param("i", $id);
$stmt_del_likes->execute();
$stmt_del_likes->close();

$stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt_delete->bind_param("i", $id);

if ($stmt_delete->execute()) {
    $stmt_delete->close();
    header("Location: ../pages/users.php?success=1");
    exit;
} else {
    $stmt_delete->close();
    header("Location: ../pages/users.php?error=1");
    exit;
}
