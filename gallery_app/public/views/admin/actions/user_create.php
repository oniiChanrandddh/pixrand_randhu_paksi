<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gallery_app/includes/auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/add_user.php?error=request");
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$username  = trim($_POST['username'] ?? '');
$password  = trim($_POST['password'] ?? '');
$role      = trim($_POST['role'] ?? '');

if ($full_name === '' || $username === '' || $password === '' || $role === '') {
    header("Location: ../pages/add_user.php?error=empty");
    exit;
}

$stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$stmt_check->close();

if ($result_check->num_rows > 0) {
    header("Location: ../pages/add_user.php?exists=1");
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO users (full_name, username, password, role)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("ssss", $full_name, $username, $hashedPassword, $role);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../pages/users.php?success=1");
    exit;
}

$stmt->close();
header("Location: ../pages/add_user.php?error=db");
exit;
