<?php
session_start();
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../includes/helper.php";
require_once __DIR__ . "/../../config/app.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    setFlash('login_error', 'Username atau password salah!');
    redirect(BASE_URL . "index.php");
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

if ($user['role'] === 'admin') {
    redirect(BASE_URL . "views/admin/pages/dashboard.php");
} else {
    redirect(BASE_URL . "views/user/pages/home.php");
}
