<?php
require_once 'database.php';

if (!isBarbershop()) {
    redirect('login.php');
}

// Handle konfirmasi booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    if ($action == 'konfirmasi' && isset($_POST['nomor_antrian'])) {
        $nomor_antrian = intval($_POST['nomor_antrian']);

        // Update status dan nomor antrian manual
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'dikonfirmasi', nomor_antrian = ? WHERE id = ? AND barbershop_id = ?");
        $stmt->execute([$nomor_antrian, $booking_id, $_SESSION['barbershop_id']]);

    } elseif ($action == 'selesai') {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'selesai' WHERE id = ? AND barbershop_id = ?");
        $stmt->execute([$booking_id, $_SESSION['barbershop_id']]);
    }
}

// Ambil data booking
$stmt = $pdo->prepare("SELECT b.*, u.nama as customer_name, u.no_hp as customer_phone FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.barbershop_id = ? ORDER BY b.tanggal_booking DESC, b.jam_booking ASC");
$stmt->execute([$_SESSION['barbershop_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistik
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE barbershop_id = ?");
$stmt->execute([$_SESSION['barbershop_id']]);
$total_bookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE barbershop_id = ? AND status = 'pending'");
$stmt->execute([$_SESSION['barbershop_id']]);
$pending_bookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE barbershop_id = ? AND tanggal_booking = CURDATE()");
$stmt->execute([$_SESSION['barbershop_id']]);
$today_bookings = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Barbershop - BarberBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">BarberBook</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION['barbershop_name']) ?>!</span>
                <a class="nav-link" href="barbershop_dashboard.php">Dashboard</a>
                <a class="nav-link" href="barbershop_profile.php">Profil</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Dashboard Barbershop</h2>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold"><?= $total_bookings ?></h3>
                        <p class="text-muted mb-0">Total Booking</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold"><?= $pending_bookings ?></h3>
                        <p class="text-muted mb-0">Menunggu Konfirmasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold"><?= $today_bookings ?></h3>
                        <p class="text-muted mb-0">Booking Hari Ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Booking -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Daftar Booking</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($bookings)): ?>
                            <div class="text-center py-5">
                                <p class="text-muted">Belum ada booking masuk.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Pelanggan</th>
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
                                                    <strong><?= htmlspecialchars($booking['customer_name']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($booking['customer_phone']) ?></small>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($booking['tanggal_booking'])) ?></td>
                                                <td><?= date('H:i', strtotime($booking['jam_booking'])) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($booking['layanan']) ?>
                                                    <?php if ($booking['catatan']): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($booking['catatan']) ?></small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($booking['nomor_antrian'])): ?>
                                                        <br><small class="text-muted">Antrian: #<?= $booking['nomor_antrian'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $booking['status'] ?>">
                                                        <?= ucfirst($booking['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($booking['status'] == 'pending'): ?>
                                                        <form method="POST" class="d-flex align-items-center gap-2 flex-wrap">
                                                            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                            <input type="hidden" name="action" value="konfirmasi">
                                                            <input type="number" name="nomor_antrian" class="form-control form-control-sm" placeholder="No. Antrian" required style="max-width: 100px;">
                                                            <button type="submit" class="btn btn-sm btn-success">Konfirmasi</button>
                                                        </form>
                                                    <?php elseif ($booking['status'] == 'dikonfirmasi'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                            <input type="hidden" name="action" value="selesai">
                                                            <button type="submit" class="btn btn-sm btn-primary">Selesai</button>
                                                        </form>
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
