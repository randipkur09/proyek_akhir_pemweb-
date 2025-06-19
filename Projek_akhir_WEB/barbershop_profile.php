<?php
require_once 'database.php';

if (!isBarbershop()) {
    redirect('login.php');
}

// Ambil data barbershop
$stmt = $pdo->prepare("SELECT * FROM barbershops WHERE id = ?");
$stmt->execute([$_SESSION['barbershop_id']]);
$barbershop = $stmt->fetch();

// Ambil data layanan yang sudah ada
$stmt = $pdo->prepare("SELECT * FROM layanan_barbershop WHERE barbershop_id = ? ORDER BY id ASC");
$stmt->execute([$_SESSION['barbershop_id']]);
$existing_layanan = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = '';
$error = '';

// Handle form actions
$action = $_POST['action'] ?? '';

// Handle layanan management
$layanan_list = [];
if (isset($_POST['layanan_id']) && is_array($_POST['layanan_id'])) {
    foreach ($_POST['layanan_id'] as $index => $layanan_id) {
        $nama_layanan = $_POST['nama_layanan'][$index] ?? '';
        $harga_layanan = $_POST['harga_layanan'][$index] ?? '';
        
        if (!empty($nama_layanan) && !empty($harga_layanan)) {
            $layanan_list[] = [
                'id' => $layanan_id, // bisa kosong untuk layanan baru
                'nama' => $nama_layanan,
                'harga' => $harga_layanan
            ];
        }
    }
} else {
    // Jika belum ada POST data, gunakan data dari database
    foreach ($existing_layanan as $layanan) {
        $layanan_list[] = [
            'id' => $layanan['id'],
            'nama' => $layanan['nama_layanan'],
            'harga' => $layanan['harga']
        ];
    }
}

if ($action == 'add_layanan') {
    $layanan_list[] = ['id' => '', 'nama' => '', 'harga' => ''];
} elseif ($action == 'remove_layanan' && isset($_POST['remove_index'])) {
    $remove_index = (int)$_POST['remove_index'];
    unset($layanan_list[$remove_index]);
    $layanan_list = array_values($layanan_list); // Re-index array
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'update_profile') {
    $nama_barbershop = $_POST['nama_barbershop'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $jam_buka = $_POST['jam_buka'];
    $jam_tutup = $_POST['jam_tutup'];
    $harga_potong = $_POST['harga_potong'];
    $deskripsi = $_POST['deskripsi'];
    
    $foto_path = $barbershop['photo_path'];

    // Proses upload foto baru jika ada
    if (!empty($_FILES['foto_barbershop']['name'])) {
        $foto_nama = $_FILES['foto_barbershop']['name'];
        $foto_tmp = $_FILES['foto_barbershop']['tmp_name'];
        $foto_path_baru = 'uploads/' . uniqid() . '_' . basename($foto_nama);

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (move_uploaded_file($foto_tmp, $foto_path_baru)) {
            // Hapus file lama jika ada
            if ($foto_path && file_exists($foto_path)) {
                unlink($foto_path);
            }
            $foto_path = $foto_path_baru;
        } else {
            $error = 'Gagal mengunggah foto baru.';
        }
    }

    if (!$error) {
        try {
            // Update data barbershop
            $stmt = $pdo->prepare("UPDATE barbershops SET nama_barbershop = ?, alamat = ?, no_hp = ?, jam_buka = ?, jam_tutup = ?, harga_potong = ?, deskripsi = ?, photo_path = ? WHERE id = ?");
            $stmt->execute([$nama_barbershop, $alamat, $no_hp, $jam_buka, $jam_tutup, $harga_potong, $deskripsi, $foto_path, $_SESSION['barbershop_id']]);

            // Update layanan
            // Hapus semua layanan lama
            $stmt = $pdo->prepare("DELETE FROM layanan_barbershop WHERE barbershop_id = ?");
            $stmt->execute([$_SESSION['barbershop_id']]);

            // Insert layanan baru
            if (!empty($layanan_list)) {
                foreach ($layanan_list as $layanan) {
                    if (!empty($layanan['nama']) && !empty($layanan['harga'])) {
                        $stmt = $pdo->prepare("INSERT INTO layanan_barbershop (barbershop_id, nama_layanan, harga) VALUES (?, ?, ?)");
                        $stmt->execute([$_SESSION['barbershop_id'], $layanan['nama'], (int)$layanan['harga']]);
                    }
                }
            }

            $_SESSION['barbershop_name'] = $nama_barbershop;
            $success = 'Profil dan layanan berhasil diperbarui!';

            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM barbershops WHERE id = ?");
            $stmt->execute([$_SESSION['barbershop_id']]);
            $barbershop = $stmt->fetch();

            // Refresh layanan
            $stmt = $pdo->prepare("SELECT * FROM layanan_barbershop WHERE barbershop_id = ? ORDER BY id ASC");
            $stmt->execute([$_SESSION['barbershop_id']]);
            $existing_layanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Update layanan_list dengan data terbaru
            $layanan_list = [];
            foreach ($existing_layanan as $layanan) {
                $layanan_list[] = [
                    'id' => $layanan['id'],
                    'nama' => $layanan['nama_layanan'],
                    'harga' => $layanan['harga']
                ];
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Ensure at least one layanan field
if (empty($layanan_list)) {
    $layanan_list[] = ['id' => '', 'nama' => '', 'harga' => ''];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Barbershop - BarberBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">BarberBook</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION['barbershop_name']) ?>!</span>
                <a class="nav-link" href="barbershop_dashboard.php">Dashboard</a>
                <a class="nav-link" href="barbershop_profile.php">Profil</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Profil Barbershop</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">Informasi Barbershop</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Nama Barbershop</label>
                                        <input type="text" name="nama_barbershop" class="form-control"
                                               value="<?= htmlspecialchars($barbershop['nama_barbershop']) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control"
                                               value="<?= htmlspecialchars($barbershop['email']) ?>" disabled>
                                        <small class="text-muted">Email tidak dapat diubah</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($barbershop['alamat']) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">No. HP</label>
                                        <input type="text" name="no_hp" class="form-control"
                                               value="<?= htmlspecialchars($barbershop['no_hp']) ?>">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Jam Buka</label>
                                            <input type="time" name="jam_buka" class="form-control"
                                                   value="<?= $barbershop['jam_buka'] ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Jam Tutup</label>
                                            <input type="time" name="jam_tutup" class="form-control"
                                                   value="<?= $barbershop['jam_tutup'] ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Harga Potong Rambut Dasar</label>
                                        <input type="number" name="harga_potong" class="form-control"
                                               value="<?= $barbershop['harga_potong'] ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($barbershop['deskripsi']) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Foto Barbershop</label><br>
                                        <?php if (!empty($barbershop['photo_path']) && file_exists($barbershop['photo_path'])): ?>
                                            <img src="<?= $barbershop['photo_path'] ?>" alt="Foto Barbershop" class="img-thumbnail mb-2" width="200">
                                        <?php else: ?>
                                            <p class="text-muted">Belum ada foto.</p>
                                        <?php endif; ?>
                                        <input type="file" name="foto_barbershop" class="form-control" accept="image/*">
                                        <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="mb-3">Layanan dan Harga</h5>
                                    
                                    <div class="mb-3">
                                        <?php foreach ($layanan_list as $index => $layanan): ?>
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <input type="hidden" name="layanan_id[]" value="<?= htmlspecialchars($layanan['id']) ?>">
                                                    <input type="text" name="nama_layanan[]" class="form-control" 
                                                           placeholder="Nama Layanan" value="<?= htmlspecialchars($layanan['nama']) ?>">
                                                </div>
                                                <div class="col-4">
                                                    <input type="number" name="harga_layanan[]" class="form-control" 
                                                           placeholder="Harga (Rp)" value="<?= htmlspecialchars($layanan['harga']) ?>">
                                                </div>
                                                <div class="col-2">
                                                    <button type="submit" name="action" value="remove_layanan" class="btn btn-danger btn-sm w-100">
                                                        <input type="hidden" name="remove_index" value="<?= $index ?>">
                                                        ×
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <button type="submit" name="action" value="add_layanan" class="btn btn-outline-primary btn-sm mt-2">
                                            + Tambah Layanan
                                        </button>
                                        <div class="form-text">Kelola layanan yang tersedia di barbershop Anda.</div>
                                    </div>

                                    <?php if (!empty($existing_layanan)): ?>
                                        <div class="alert alert-info">
                                            <h6>Layanan Saat Ini:</h6>
                                            <ul class="mb-0">
                                                <?php foreach ($existing_layanan as $layanan): ?>
                                                    <li><?= htmlspecialchars($layanan['nama_layanan']) ?> - Rp <?= number_format($layanan['harga'], 0, ',', '.') ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="barbershop_dashboard.php" class="btn btn-outline-secondary me-md-2">Kembali</a>
                                <button type="submit" name="action" value="update_profile" class="btn btn-dark">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
