<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../init.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: " . BASE_URL . "index.php");
    exit;
}
