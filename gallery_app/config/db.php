<?php

$host = '127.0.0.1';
$dbname = 'gallery_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "DB ERROR: " . $e->getMessage();
    exit();
}

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("MySQLi Connection failed: " . mysqli_connect_error());
}
