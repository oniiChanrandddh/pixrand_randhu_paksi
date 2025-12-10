<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";
require_once __DIR__ . "/../layout/admin_guard.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/users.php");
    exit;
}

$id        = intval($_POST['id'] ?? 0);
$full_name = trim($_POST['full_name'] ?? '');
$username  = trim($_POST['username'] ?? '');
$password  = trim($_POST['password'] ?? '');
$role      = trim($_POST['role'] ?? '');

if ($id <= 0 || empty($full_name) || empty($username) || empty($role)) {
    header("Location: ../pages/edit_user.php?id=$id&error=invalid");
    exit;
}

if ($id == $_SESSION['user']['id'] && $role !== $_SESSION['user']['role']) {
    header("Location: ../pages/edit_user.php?id=$id&error=selfrole");
    exit;
}

$stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt_check->bind_param("si", $username, $id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $stmt_check->close();
    header("Location: ../pages/edit_user.php?id=$id&error=exists");
    exit;
}

$stmt_check->close();

if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("
        UPDATE users 
        SET full_name = ?, username = ?, password = ?, role = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssssi", $full_name, $username, $hashedPassword, $role, $id);
} else {
    $stmt = $conn->prepare("
        UPDATE users 
        SET full_name = ?, username = ?, role = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssi", $full_name, $username, $role, $id);
}

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../pages/users.php?success=1");
    exit;
} else {
    $stmt->close();
    header("Location: ../pages/edit_user.php?id=$id&error=update");
    exit;
}
