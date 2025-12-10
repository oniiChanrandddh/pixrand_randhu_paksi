<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function getFlash($key) {
    if(isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

if (!function_exists('sanitize')) {
    function sanitize($str) {
        return trim(htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'));
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isUser() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

function logout() {
    session_unset();
    session_destroy();
}
