<?php
require_once 'database.php';

// Ambil keyword pencarian (jika ada)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];

// Bangun query dengan atau tanpa pencarian
$sql = "SELECT * FROM barbershops WHERE status = 'aktif'";
if (!empty($search)) {
    $sql .= " AND nama_barbershop LIKE :search";
    $params[':search'] = '%' . $search . '%';
}
$sql .= " ORDER BY nama_barbershop";

// Eksekusi query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$barbershops = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberBook - Booking Antrian Barbershop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">BarberBook</a>
            <div class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <?php if (isCustomer()): ?>
                        <a class="nav-link" href="customer_dashboard.php">Dashboard</a>
                    <?php else: ?>
                        <a class="nav-link" href="barbershop_dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Booking Antrian Barbershop Jadi Mudah</h1>
                    <p class="lead mb-4">Tidak perlu antri lama! Booking jadwal potong rambut Anda secara online di barbershop favorit.</p>
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-dark btn-lg me-3">Mulai Booking</a>
                        <a href="login.php" class="btn btn-outline-dark btn-lg">Login</a>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <div class="hero-image">
                            <div class="bg-light rounded-3 p-5">
                                <h3>🪒 Barbershop Modern</h3>
                                <p>Pelayanan profesional untuk penampilan terbaik Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Barbershop -->
    <div class="container my-5">
        <h2 class="text-center mb-5">Barbershop Tersedia</h2>

        <!-- Form Search -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Cari barbershop..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-dark" type="submit">Cari</button>
            </div>
        </form>

        <div class="row">
            <?php if (count($barbershops) > 0): ?>
                <?php foreach ($barbershops as $barbershop): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($barbershop['nama_barbershop']) ?></h5>
                                <p class="card-text text-muted"><?= htmlspecialchars($barbershop['alamat']) ?></p>
                                <p class="card-text"><?= htmlspecialchars($barbershop['deskripsi']) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-dark">Rp <?= number_format($barbershop['harga_potong'], 0, ',', '.') ?></span>
                                    <small class="text-muted"><?= $barbershop['jam_buka'] ?> - <?= $barbershop['jam_tutup'] ?></small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <?php if (isCustomer()): ?>
                                    <a href="customer_booking.php?barbershop_id=<?= $barbershop['id'] ?>" class="btn btn-dark w-100">Book Sekarang</a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-outline-dark w-100">Login untuk Book</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Barbershop tidak ditemukan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 BarberBook. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
