<?php
require_once 'database.php';

if (!isCustomer()) {
    redirect('login.php');
}

$booking_id = $_GET['id'] ?? null;
if (!$booking_id) {
    redirect('dashboard.php');
}

// Verifikasi booking milik user dan status pending
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? AND status = 'pending'");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('customer_dashboard.php');
}

// Update status menjadi dibatalkan
$stmt = $pdo->prepare("UPDATE bookings SET status = 'dibatalkan' WHERE id = ?");
$stmt->execute([$booking_id]);

redirect('customer_dashboard.php');
?>