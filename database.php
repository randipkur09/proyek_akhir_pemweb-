<?php
$host = 'localhost';
$dbname = 'barbershop_booking';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['barbershop_id']);
}

function isCustomer() {
    return isset($_SESSION['user_id']);
}

function isBarbershop() {
    return isset($_SESSION['barbershop_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>