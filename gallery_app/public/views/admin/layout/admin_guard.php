<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../../config/app.php";
require_once __DIR__ . "/../../../../includes/helper.php";

if (!isLoggedIn() || !isAdmin()) {
    redirect(BASE_URL . "index.php");
    exit();
}