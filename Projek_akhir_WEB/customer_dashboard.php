<?php
require_once 'database.php';

if (!isCustomer()) {
    redirect('login.php');
}

// Ambil data booking user
$stmt = $pdo->prepare("
    SELECT b.*, bs.nama_barbershop, bs.alamat as barbershop_alamat 
    FROM bookings b 
    JOIN barbershops bs ON b.barbershop_id = bs.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistik
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_bookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$_SESSION['user_id']]);
$pending_bookings = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - BarberBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">BarberBook</a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
            <a class="nav-link" href="customer_dashboard.php">Dashboard</a>
            <a class="nav-link" href="index.php">Cari Barbershop</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Dashboard Pelanggan</h2>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <h3 class="fw-bold"><?= $total_bookings ?></h3>
                    <p class="text-muted mb-0">Total Booking</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <h3 class="fw-bold"><?= $pending_bookings ?></h3>
                    <p class="text-muted mb-0">Booking Pending</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Booking -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Riwayat Booking</h5>
                    <a href="index.php" class="btn btn-dark btn-sm">Booking Baru</a>
                </div>
                <div class="card-body">
                    <?php if (empty($bookings)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted">Belum ada booking. <a href="index.php">Mulai booking sekarang!</a></p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Barbershop</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($booking['nama_barbershop']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($booking['barbershop_alamat']) ?></small>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($booking['tanggal_booking'])) ?></td>
                                            <td><?= date('H:i', strtotime($booking['jam_booking'])) ?></td>
                                            <td><?= htmlspecialchars($booking['layanan']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $booking['status'] ?>">
                                                    <?= ucfirst($booking['status']) ?>
                                                </span>
                                                <?php if ($booking['status'] === 'dikonfirmasi' && !empty($booking['nomor_antrian'])): ?>
                                                    <br><small class="text-success fw-bold">Antrian #<?= $booking['nomor_antrian'] ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($booking['status'] == 'pending'): ?>
                                                    <a href="cancel_booking.php?id=<?= $booking['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Yakin ingin membatalkan booking?')">
                                                        Batal
                                                    </a>
                                                <?php elseif ($booking['status'] == 'selesai'): ?>
                                                    <a href="delete_booking.php?id=<?= $booking['id'] ?>" 
                                                       class="btn btn-sm btn-outline-secondary"
                                                       onclick="return confirm('Yakin ingin menghapus riwayat ini?')">
                                                        Hapus
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
