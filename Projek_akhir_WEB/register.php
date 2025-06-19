<?php
require_once 'database.php';

if (isLoggedIn()) {
    if (isCustomer()) {
        redirect('customer_dashboard.php');
    } else {
        redirect('barbershop_dashboard.php');
    }
}

$success = '';
$error = '';

// Handle form actions
$role = $_POST['role'] ?? $_GET['role'] ?? '';
$action = $_POST['action'] ?? '';

// Handle add/remove layanan
$layanan_list = [];
if (isset($_POST['layanan']) && is_array($_POST['layanan'])) {
    foreach ($_POST['layanan'] as $index => $nama_layanan) {
        if (!empty($nama_layanan) && !empty($_POST['harga_layanan'][$index])) {
            $layanan_list[] = [
                'nama' => $nama_layanan,
                'harga' => $_POST['harga_layanan'][$index]
            ];
        }
    }
}

if ($action == 'add_layanan') {
    $layanan_list[] = ['nama' => '', 'harga' => ''];
} elseif ($action == 'remove_layanan' && isset($_POST['remove_index'])) {
    $remove_index = (int)$_POST['remove_index'];
    unset($layanan_list[$remove_index]);
    $layanan_list = array_values($layanan_list); // Re-index array
}

// Ensure at least one layanan field for barbershop
if ($role == 'barbershop' && empty($layanan_list)) {
    $layanan_list[] = ['nama' => '', 'harga' => ''];
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'register') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        if ($role == 'customer') {
            $nama = $_POST['nama'];
            $no_hp = $_POST['no_hp'];
            $alamat = $_POST['alamat'];

            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, no_hp, alamat) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $email, $password, $no_hp, $alamat]);

            $success = 'Registrasi berhasil! Silakan login.';
        } else {
            $nama_barbershop = $_POST['nama_barbershop'];
            $alamat = $_POST['alamat'];
            $no_hp = $_POST['no_hp'];
            $jam_buka = $_POST['jam_buka'];
            $jam_tutup = $_POST['jam_tutup'];
            $harga_potong = $_POST['harga_potong'];
            $deskripsi = $_POST['deskripsi'];

            // Upload foto
            $foto_path = '';
            if (!empty($_FILES['foto_barbershop']['name'])) {
                $foto_nama = $_FILES['foto_barbershop']['name'];
                $foto_tmp = $_FILES['foto_barbershop']['tmp_name'];
                $foto_path = 'uploads/' . uniqid() . '_' . basename($foto_nama);

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                move_uploaded_file($foto_tmp, $foto_path);
            }

            // Simpan ke database barbershop
            $stmt = $pdo->prepare("INSERT INTO barbershops (nama_barbershop, email, password, alamat, no_hp, jam_buka, jam_tutup, harga_potong, deskripsi, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama_barbershop, $email, $password, $alamat, $no_hp, $jam_buka, $jam_tutup, $harga_potong, $deskripsi, $foto_path]);

            $barbershop_id = $pdo->lastInsertId();

            // Simpan layanan-layanan barbershop
            if (!empty($layanan_list)) {
                foreach ($layanan_list as $layanan) {
                    if (!empty($layanan['nama']) && !empty($layanan['harga'])) {
                        $stmt = $pdo->prepare("INSERT INTO layanan_barbershop (barbershop_id, nama_layanan, harga) VALUES (?, ?, ?)");
                        $stmt->execute([$barbershop_id, $layanan['nama'], (int)$layanan['harga']]);
                    }
                }
            }

            $success = 'Registrasi barbershop berhasil! Silakan login.';
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = 'Email sudah terdaftar!';
        } else {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Preserve form data
$form_data = [
    'nama' => $_POST['nama'] ?? '',
    'nama_barbershop' => $_POST['nama_barbershop'] ?? '',
    'email' => $_POST['email'] ?? '',
    'no_hp' => $_POST['no_hp'] ?? '',
    'alamat' => $_POST['alamat'] ?? '',
    'jam_buka' => $_POST['jam_buka'] ?? '08:00',
    'jam_tutup' => $_POST['jam_tutup'] ?? '21:00',
    'harga_potong' => $_POST['harga_potong'] ?? '25000',
    'deskripsi' => $_POST['deskripsi'] ?? ''
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - BarberBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Daftar Akun</h2>
                            <p class="text-muted">Buat akun baru untuk mulai booking</p>
                        </div>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Daftar Sebagai</label>
                                <select name="role" class="form-select" required onchange="this.form.submit()">
                                    <option value="">Pilih Role</option>
                                    <option value="customer" <?= $role == 'customer' ? 'selected' : '' ?>>Pelanggan</option>
                                    <option value="barbershop" <?= $role == 'barbershop' ? 'selected' : '' ?>>Barbershop</option>
                                </select>
                            </div>
                            
                            <?php if ($role == 'customer'): ?>
                                <!-- Customer Fields -->
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($form_data['nama']) ?>" required>
                                </div>
                            <?php elseif ($role == 'barbershop'): ?>
                                <!-- Barbershop Fields -->
                                <div class="mb-3">
                                    <label class="form-label">Nama Barbershop</label>
                                    <input type="text" name="nama_barbershop" class="form-control" value="<?= htmlspecialchars($form_data['nama_barbershop']) ?>" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jam Buka</label>
                                        <input type="time" name="jam_buka" class="form-control" value="<?= $form_data['jam_buka'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jam Tutup</label>
                                        <input type="time" name="jam_tutup" class="form-control" value="<?= $form_data['jam_tutup'] ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Harga Potong Rambut</label>
                                    <input type="number" name="harga_potong" class="form-control" value="<?= $form_data['harga_potong'] ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Barbershop</label>
                                    <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($form_data['deskripsi']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Foto Barbershop</label>
                                    <input type="file" name="foto_barbershop" class="form-control" accept="image/*" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Layanan dan Harga</label>
                                    <?php foreach ($layanan_list as $index => $layanan): ?>
                                        <div class="row mb-2">
                                            <div class="col-7">
                                                <input type="text" name="layanan[]" class="form-control" placeholder="Nama Layanan" value="<?= htmlspecialchars($layanan['nama']) ?>">
                                            </div>
                                            <div class="col-4">
                                                <input type="number" name="harga_layanan[]" class="form-control" placeholder="Harga (Rp)" value="<?= htmlspecialchars($layanan['harga']) ?>">
                                            </div>
                                            <div class="col-1">
                                                <button type="submit" name="action" value="remove_layanan" class="btn btn-danger btn-sm">
                                                    <input type="hidden" name="remove_index" value="<?= $index ?>">
                                                    ×
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <button type="submit" name="action" value="add_layanan" class="btn btn-outline-primary btn-sm mt-2">+ Tambah Layanan</button>
                                    <div class="form-text">Masukkan layanan yang tersedia dan harganya.</div>
                                </div>
                            <?php endif; ?>

                            <?php if ($role): ?>
                                <!-- Common Fields -->
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($form_data['email']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">No. HP</label>
                                    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($form_data['no_hp']) ?>">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($form_data['alamat']) ?></textarea>
                                </div>
                                
                                <button type="submit" name="action" value="register" class="btn btn-dark w-100 mb-3">Daftar</button>
                            <?php endif; ?>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Sudah punya akun? <a href="login.php" class="text-decoration-none">Login di sini</a></p>
                            <a href="index.php" class="text-muted text-decoration-none">← Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
