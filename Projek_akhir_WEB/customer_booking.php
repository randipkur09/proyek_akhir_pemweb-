<?php
require_once 'database.php';

if (!isCustomer()) {
    redirect('login.php');
}

$barbershop_id = $_GET['barbershop_id'] ?? null;
if (!$barbershop_id) {
    redirect('index.php');
}

// Ambil data barbershop
$stmt = $pdo->prepare("SELECT * FROM barbershops WHERE id = ? AND status = 'aktif'");
$stmt->execute([$barbershop_id]);
$barbershop = $stmt->fetch();

if (!$barbershop) {
    redirect('index.php');
}

// Ambil layanan yang tersedia di barbershop ini
$stmt = $pdo->prepare("SELECT * FROM layanan_barbershop WHERE barbershop_id = ? ORDER BY harga ASC");
$stmt->execute([$barbershop_id]);
$layanan_tersedia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika tidak ada layanan custom, gunakan layanan default
if (empty($layanan_tersedia)) {
    $layanan_tersedia = [
        ['nama_layanan' => 'Potong Rambut', 'harga' => $barbershop['harga_potong']],
        ['nama_layanan' => 'Potong + Cuci', 'harga' => $barbershop['harga_potong'] + 10000],
        ['nama_layanan' => 'Potong + Styling', 'harga' => $barbershop['harga_potong'] + 20000],
        ['nama_layanan' => 'Cukur Jenggot', 'harga' => 15000],
        ['nama_layanan' => 'Paket Lengkap', 'harga' => $barbershop['harga_potong'] + 30000]
    ];
}

$success = '';
$error = '';
$selected_layanan = $_POST['layanan'] ?? $_GET['layanan'] ?? '';
$selected_harga = 0;

// Cari harga layanan yang dipilih
if ($selected_layanan) {
    foreach ($layanan_tersedia as $layanan) {
        if ($layanan['nama_layanan'] == $selected_layanan) {
            $selected_harga = $layanan['harga'];
            break;
        }
    }
}

// Handle form submission untuk booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_booking'])) {
    $tanggal_booking = $_POST['tanggal_booking'];
    $jam_booking = $_POST['jam_booking'];
    $layanan = $_POST['layanan'];
    $catatan = $_POST['catatan'];
    
    // Validasi tanggal tidak boleh hari ini atau sebelumnya
    if (strtotime($tanggal_booking) <= strtotime('today')) {
        $error = 'Tanggal booking minimal besok!';
    } else {
        // Cek apakah jam sudah dibooking
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE barbershop_id = ? AND tanggal_booking = ? AND jam_booking = ? AND status != 'dibatalkan'");
        $stmt->execute([$barbershop_id, $tanggal_booking, $jam_booking]);
        $existing = $stmt->fetchColumn();
        
        if ($existing > 0) {
            $error = 'Jam tersebut sudah dibooking! Pilih jam lain.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO bookings (user_id, barbershop_id, tanggal_booking, jam_booking, layanan, catatan) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $barbershop_id, $tanggal_booking, $jam_booking, $layanan, $catatan]);
                
                $success = 'Booking berhasil! Menunggu konfirmasi dari barbershop.';
            } catch (PDOException $e) {
                $error = 'Terjadi kesalahan: ' . $e->getMessage();
            }
        }
    }
}

// Generate jam booking (setiap 30 menit)
$jam_buka = strtotime($barbershop['jam_buka']);
$jam_tutup = strtotime($barbershop['jam_tutup']);
$jam_options = [];

for ($time = $jam_buka; $time < $jam_tutup; $time += 1800) { // 1800 detik = 30 menit
    $jam_options[] = date('H:i', $time);
}

// Preserve form data
$form_data = [
    'tanggal_booking' => $_POST['tanggal_booking'] ?? '',
    'jam_booking' => $_POST['jam_booking'] ?? '',
    'catatan' => $_POST['catatan'] ?? ''
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - <?= htmlspecialchars($barbershop['nama_barbershop']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">BarberBook</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="customer_dashboard.php">Dashboard</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Booking di <?= htmlspecialchars($barbershop['nama_barbershop']) ?></h4>
                    </div>
                    <div class="card-body">
                        <!-- Info Barbershop -->
                        <div class="alert alert-custom mb-4">
                            <h6>Informasi Barbershop</h6>
                            <p class="mb-1"><strong>Alamat:</strong> <?= htmlspecialchars($barbershop['alamat']) ?></p>
                            <p class="mb-1"><strong>Jam Operasional:</strong> <?= $barbershop['jam_buka'] ?> - <?= $barbershop['jam_tutup'] ?></p>
                            <p class="mb-0"><strong>Harga Dasar:</strong> Rp <?= number_format($barbershop['harga_potong'], 0, ',', '.') ?></p>
                            <?php if (!empty($barbershop['deskripsi'])): ?>
                                <p class="mb-0"><strong>Deskripsi:</strong> <?= htmlspecialchars($barbershop['deskripsi']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Booking</label>
                                    <input type="date" name="tanggal_booking" class="form-control" 
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                                           value="<?= htmlspecialchars($form_data['tanggal_booking']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Booking</label>
                                    <select name="jam_booking" class="form-select" required>
                                        <option value="">Pilih Jam</option>
                                        <?php foreach ($jam_options as $jam): ?>
                                            <option value="<?= $jam ?>" <?= $form_data['jam_booking'] == $jam ? 'selected' : '' ?>>
                                                <?= $jam ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Layanan</label>
                                <select name="layanan" class="form-select" required onchange="this.form.submit()">
                                    <option value="">Pilih Layanan</option>
                                    <?php foreach ($layanan_tersedia as $layanan): ?>
                                        <option value="<?= htmlspecialchars($layanan['nama_layanan']) ?>" 
                                                <?= $selected_layanan == $layanan['nama_layanan'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($layanan['nama_layanan']) ?> - Rp <?= number_format($layanan['harga'], 0, ',', '.') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tampilkan harga yang dipilih -->
                            <?php if ($selected_layanan && $selected_harga > 0): ?>
                                <div class="mb-3">
                                    <div class="alert alert-info">
                                        <strong>Harga Layanan: </strong><span class="fw-bold text-primary">Rp <?= number_format($selected_harga, 0, ',', '.') ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-4">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="catatan" class="form-control" rows="3" 
                                          placeholder="Tambahkan catatan khusus untuk barbershop (contoh: gaya rambut yang diinginkan, preferensi khusus, dll)"><?= htmlspecialchars($form_data['catatan']) ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-outline-secondary me-md-2">Kembali</a>
                                <button type="submit" name="submit_booking" class="btn btn-dark">Konfirmasi Booking</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
