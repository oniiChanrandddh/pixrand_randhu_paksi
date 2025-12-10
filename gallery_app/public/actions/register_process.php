<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../config/app.php";
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../includes/helper.php";

$full_name = sanitize($_POST['full_name'] ?? '');
$username  = sanitize($_POST['username'] ?? '');
$password  = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($username) || empty($password) || empty($confirm_password)) {
    setFlash('register_error', 'Semua field wajib diisi.');
    redirect(BASE_URL . "register.php");
}

if (strlen($username) < 3) {
    setFlash('register_error', 'Username minimal 3 karakter.');
    redirect(BASE_URL . "register.php");
}

if ($password !== $confirm_password) {
    setFlash('register_error', 'Password tidak cocok.');
    redirect(BASE_URL . "register.php");
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->fetchColumn() > 0) {
    setFlash('register_error', 'Username sudah digunakan.');
    redirect(BASE_URL . "register.php");
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$query = $pdo->prepare("INSERT INTO users (username, password, full_name, role) 
                        VALUES (?, ?, ?, 'user')");
if ($query->execute([$username, $hashedPassword, $full_name])) {
    setFlash('register_success', 'yes');
    redirect(BASE_URL . "register.php");
} else {
    setFlash('register_error', 'Terjadi kesalahan sistem.');
    redirect(BASE_URL . "register.php");
}
