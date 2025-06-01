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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
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
            
            $stmt = $pdo->prepare("INSERT INTO barbershops (nama_barbershop, email, password, alamat, no_hp, jam_buka, jam_tutup, harga_potong, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama_barbershop, $email, $password, $alamat, $no_hp, $jam_buka, $jam_tutup, $harga_potong, $deskripsi]);
            
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
                        
                        <form method="POST" id="registerForm">
                            <div class="mb-3">
                                <label class="form-label">Daftar Sebagai</label>
                                <select name="role" class="form-select" required onchange="toggleFields()">
                                    <option value="">Pilih Role</option>
                                    <option value="customer">Pelanggan</option>
                                    <option value="barbershop">Barbershop</option>
                                </select>
                            </div>
                            
                            <!-- Customer Fields -->
                            <div id="customerFields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control">
                                </div>
                            </div>
                            
                            <!-- Barbershop Fields -->
                            <div id="barbershopFields" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Nama Barbershop</label>
                                    <input type="text" name="nama_barbershop" class="form-control">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jam Buka</label>
                                        <input type="time" name="jam_buka" class="form-control" value="08:00">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jam Tutup</label>
                                        <input type="time" name="jam_tutup" class="form-control" value="21:00">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Harga Potong Rambut</label>
                                    <input type="number" name="harga_potong" class="form-control" value="25000">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Barbershop</label>
                                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <!-- Common Fields -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-dark w-100 mb-3">Daftar</button>
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
    
    <script>
        function toggleFields() {
            const role = document.querySelector('select[name="role"]').value;
            const customerFields = document.getElementById('customerFields');
            const barbershopFields = document.getElementById('barbershopFields');
            
            if (role === 'customer') {
                customerFields.style.display = 'block';
                barbershopFields.style.display = 'none';
                document.querySelector('input[name="nama"]').required = true;
                document.querySelector('input[name="nama_barbershop"]').required = false;
            } else if (role === 'barbershop') {
                customerFields.style.display = 'none';
                barbershopFields.style.display = 'block';
                document.querySelector('input[name="nama"]').required = false;
                document.querySelector('input[name="nama_barbershop"]').required = true;
            } else {
                customerFields.style.display = 'none';
                barbershopFields.style.display = 'none';
            }
        }
    </script>
</body>
</html>