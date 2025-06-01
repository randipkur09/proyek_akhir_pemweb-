<?php
require_once 'database.php';

if (!isCustomer()) {
    redirect('login.php');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('customer_dashboard.php');
}

$booking_id = (int) $_GET['id'];

// Pastikan booking milik user dan status selesai
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? AND status = 'selesai'");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    redirect('customer_dashboard.php');
}

// Hapus booking
$stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);

redirect('customer_dashboard.php');
